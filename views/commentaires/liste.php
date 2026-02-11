<?php
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

if (!estConnecte()) {
    header('Location: ../auth/login.php');
    exit;
}

require_once __DIR__ . '/../../services/CommentaireService.php';
require_once __DIR__ . '/../../services/TacheService.php';

$commentaireService = new CommentaireService();
$tacheService = new TacheService();

$commentaires = [];
$taches = [];
$erreur = '';

try {
    $utilisateur = getUtilisateurConnecte();
    
    // Récupérer les commentaires selon le rôle
    if ($utilisateur->getRole() === 'MEMBRE') {
        $commentaires = $commentaireService->listerCommentairesUtilisateur($utilisateur->getId());
    } else {
        // Pour admin et chef, on pourrait afficher tous les commentaires
        // Pour l'instant, on affiche les commentaires de l'utilisateur
        $commentaires = $commentaireService->listerCommentairesUtilisateur($utilisateur->getId());
    }
    
    // Récupérer les tâches pour le filtre
    $taches = $tacheService->listerTachesUtilisateur($utilisateur->getId());
    
} catch (Exception $e) {
    $erreur = $e->getMessage();
}
?>

<div class="main-content">
    <?php require_once '../includes/sidebar.php'; ?>
    
    <div class="card">
        <h2>💬 Liste des Commentaires</h2>
        
        <?php if ($erreur): ?>
            <div class="error"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>
        
        <!-- Filtre par tâche -->
        <div style="margin-bottom: 1rem;">
            <label for="filtre_tache">Filtrer par tâche :</label>
            <select id="filtre_tache" onchange="filtrerCommentaires()">
                <option value="">Toutes les tâches</option>
                <?php foreach ($taches as $tache): ?>
                    <option value="<?= $tache->getId() ?>">
                        <?= htmlspecialchars($tache->getTitre()) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <?php if (empty($commentaires)): ?>
            <p>Aucun commentaire trouvé.</p>
        <?php else: ?>
            <div id="commentaires_container">
                <?php foreach ($commentaires as $commentaire): ?>
                    <div class="commentaire-item" data-tache-id="<?= $commentaire->getTacheId() ?>">
                        <div class="commentaire-header">
                            <strong><?= htmlspecialchars($utilisateur->getNom()) ?></strong>
                            <span class="commentaire-date">
                                <?= date('d/m/Y H:i', strtotime($commentaire->getDateCreation())) ?>
                            </span>
                        </div>
                        <div class="commentaire-contenu">
                            <?= nl2br(htmlspecialchars($commentaire->getContenu())) ?>
                        </div>
                        <div class="commentaire-actions">
                            <small>Tâche : 
                                <?php 
                                $tache_associee = null;
                                foreach ($taches as $tache) {
                                    if ($tache->getId() === $commentaire->getTacheId()) {
                                        $tache_associee = $tache;
                                        break;
                                    }
                                }
                                echo $tache_associee ? htmlspecialchars($tache_associee->getTitre()) : 'Non trouvée';
                                ?>
                            </small>
                            
                            <?php 
                            $utilisateur = getUtilisateurConnecte();
                            $peutModifier = false;
                            
                            // L'admin peut modifier tous les commentaires
                            if ($utilisateur->getRole() === 'ADMIN') {
                                $peutModifier = true;
                            }
                            // L'auteur peut modifier son commentaire
                            elseif ($commentaire->getUtilisateurId() === $utilisateur->getId()) {
                                $peutModifier = true;
                            }
                            
                            if ($peutModifier): ?>
                                <a href="modifier.php?id=<?= $commentaire->getId() ?>" class="btn btn-small">Modifier</a>
                                <a href="supprimer.php?id=<?= $commentaire->getId() ?>" 
                                       class="btn btn-danger btn-small" 
                                       onclick="return confirmer('Êtes-vous sûr de vouloir supprimer ce commentaire ?')">Supprimer</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.commentaire-item {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.commentaire-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #f0f0f0;
}

.commentaire-date {
    color: #666;
    font-size: 0.875rem;
}

.commentaire-contenu {
    margin: 0.5rem 0;
    line-height: 1.5;
}

.commentaire-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 0.5rem;
    padding-top: 0.5rem;
    border-top: 1px solid #f0f0f0;
}
</style>

<script>
function filtrerCommentaires() {
    const filtre = document.getElementById('filtre_tache').value;
    const commentaires = document.querySelectorAll('.commentaire-item');
    
    commentaires.forEach(commentaire => {
        if (filtre === '' || commentaire.dataset.tacheId === filtre) {
            commentaire.style.display = 'block';
        } else {
            commentaire.style.display = 'none';
        }
    });
}
</script>

<?php require_once '../includes/footer.php'; ?>
