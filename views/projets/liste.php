<?php
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

if (!estConnecte()) {
    header('Location: ../auth/login.php');
    exit;
}

require_once __DIR__ . '/../../services/ProjetService.php';

$projetService = new ProjetService();
$projets = [];
$erreur = '';

try {
    $projets = $projetService->listerProjets();
} catch (Exception $e) {
    $erreur = $e->getMessage();
}
?>

<div class="main-content">
    <?php require_once '../includes/sidebar.php'; ?>
    
    <div class="card">
        <h2>📁 Liste des Projets</h2>
        
        <?php if ($erreur): ?>
            <div class="error"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>
        
        <?php if (aPermission('ADMIN') || peutGererProjets()): ?>
            <div style="margin-bottom: 1rem;">
                <a href="creer.php" class="btn">➕ Créer un projet</a>
            </div>
        <?php endif; ?>
        
        <?php if (empty($projets)): ?>
            <p>Aucun projet trouvé.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Description</th>
                        <th>Chef de projet</th>
                        <th>Date de début</th>
                        <th>Date de fin</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projets as $projet): ?>
                        <tr>
                            <td><?= htmlspecialchars($projet->getNom()) ?></td>
                            <td><?= htmlspecialchars(substr($projet->getDescription(), 0, 100)) ?>...</td>
                            <td>
                                <?php 
                                require_once __DIR__ . '/../../services/UtilisateurService.php';
                                $utilisateurService = new UtilisateurService();
                                $chef = $utilisateurService->getUtilisateurById($projet->getChefId());
                                echo $chef ? htmlspecialchars($chef->getNom()) : 'Non défini';
                                ?>
                            </td>
                            <td><?= $projet->getDateDebut() ?? 'Non définie' ?></td>
                            <td><?= $projet->getDateFin() ?? 'Non définie' ?></td>
                            <td>
                                <span class="<?= $projet->isActif() ? 'success' : 'error' ?>">
                                    <?= $projet->isActif() ? 'Actif' : 'Inactif' ?>
                                </span>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="details.php?id=<?= $projet->getId() ?>" class="btn btn-small">Voir</a>
                                    
                                    <?php if (aPermission('ADMIN') || 
                                             (peutGererProjets() && $projet->getChefId() === getUtilisateurConnecte()->getId())): ?>
                                        <a href="modifier.php?id=<?= $projet->getId() ?>" class="btn btn-small">Modifier</a>
                                        
                                        <?php if (aPermission('ADMIN')): ?>
                                            <a href="supprimer.php?id=<?= $projet->getId() ?>" 
                                               class="btn btn-danger btn-small" 
                                               onclick="return confirmer('Êtes-vous sûr de vouloir supprimer ce projet ?')">Supprimer</a>
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

<?php require_once '../includes/footer.php'; ?>
