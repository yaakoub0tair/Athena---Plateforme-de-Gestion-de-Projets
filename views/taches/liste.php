<?php
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

if (!estConnecte()) {
    header('Location: ../auth/login.php');
    exit;
}

require_once __DIR__ . '/../../services/TacheService.php';
require_once __DIR__ . '/../../services/ProjetService.php';

$tacheService = new TacheService();
$projetService = new ProjetService();

$taches = [];
$projets = [];
$erreur = '';

try {
    $utilisateur = getUtilisateurConnecte();
    
    // Récupérer les tâches selon le rôle
    if ($utilisateur->getRole() === 'MEMBRE') {
        $taches = $tacheService->listerTachesUtilisateur($utilisateur->getId());
    } else {
        // Pour admin et chef, on pourrait afficher toutes les tâches
        // Pour l'instant, on affiche les tâches des projets accessibles
        $projets_accessibles = $projetService->listerProjets();
        foreach ($projets_accessibles as $projet) {
            require_once __DIR__ . '/../../services/SprintService.php';
            $sprintService = new SprintService();
            $sprints = $sprintService->listerSprintsProjet($projet->getId());
            foreach ($sprints as $sprint) {
                $taches_sprint = $tacheService->listerTachesSprint($sprint->getId());
                $taches = array_merge($taches, $taches_sprint);
            }
        }
    }
    
    $projets = $projetService->listerProjets();
    
} catch (Exception $e) {
    $erreur = $e->getMessage();
}
?>

<div class="main-content">
    <?php require_once '../includes/sidebar.php'; ?>
    
    <div class="card">
        <h2>✅ Liste des Tâches</h2>
        
        <?php if ($erreur): ?>
            <div class="error"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>
        
        <div style="margin-bottom: 1rem;">
            <a href="creer.php" class="btn">➕ Créer une tâche</a>
        </div>
        
        <?php if (empty($taches)): ?>
            <p>Aucune tâche trouvée.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Description</th>
                        <th>Statut</th>
                        <th>Priorité</th>
                        <th>Assigné à</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($taches as $tache): ?>
                        <tr>
                            <td><?= htmlspecialchars($tache->getTitre()) ?></td>
                            <td><?= htmlspecialchars(substr($tache->getDescription(), 0, 80)) ?>...</td>
                            <td>
                                <span class="status-<?= strtolower($tache->getStatut()) ?>">
                                    <?= htmlspecialchars($tache->getStatut()) ?>
                                </span>
                            </td>
                            <td>
                                <span class="priority-<?= strtolower($tache->getPriorite()) ?>">
                                    <?= htmlspecialchars($tache->getPriorite()) ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                if ($tache->getUtilisateurId()) {
                                    require_once __DIR__ . '/../../services/UtilisateurService.php';
                                    $utilisateurService = new UtilisateurService();
                                    $assigne = $utilisateurService->getUtilisateurById($tache->getUtilisateurId());
                                    echo $assigne ? htmlspecialchars($assigne->getNom()) : 'Non défini';
                                } else {
                                    echo 'Non assigné';
                                }
                                ?>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="details.php?id=<?= $tache->getId() ?>" class="btn btn-small">Voir</a>
                                    
                                    <?php 
                                    $utilisateur = getUtilisateurConnecte();
                                    $peutModifier = false;
                                    
                                    // Admin peut tout modifier
                                    if ($utilisateur->getRole() === 'ADMIN') {
                                        $peutModifier = true;
                                    }
                                    // Chef peut modifier les tâches de ses projets
                                    elseif ($utilisateur->canManageProject()) {
                                        // Vérifier si la tâche appartient à un de ses projets
                                        foreach ($projets as $projet) {
                                            if ($projet->getChefId() === $utilisateur->getId()) {
                                                $peutModifier = true;
                                                break;
                                            }
                                        }
                                    }
                                    // Membre peut modifier ses tâches
                                    elseif ($tache->getUtilisateurId() === $utilisateur->getId()) {
                                        $peutModifier = true;
                                    }
                                    
                                    if ($peutModifier): ?>
                                        <a href="modifier.php?id=<?= $tache->getId() ?>" class="btn btn-small">Modifier</a>
                                        
                                        <?php if ($utilisateur->getRole() === 'ADMIN' || $utilisateur->canManageProject()): ?>
                                            <a href="supprimer.php?id=<?= $tache->getId() ?>" 
                                               class="btn btn-danger btn-small" 
                                               onclick="return confirmer('Êtes-vous sûr de vouloir supprimer cette tâche ?')">Supprimer</a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<style>
.status-todo { background-color: #f39c12; color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; }
.status-doing { background-color: #3498db; color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; }
.status-done { background-color: #27ae60; color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; }

.priority-basse { background-color: #95a5a6; color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; }
.priority-moyenne { background-color: #f39c12; color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; }
.priority-haute { background-color: #e74c3c; color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; }
</style>

<?php require_once '../includes/footer.php'; ?>
