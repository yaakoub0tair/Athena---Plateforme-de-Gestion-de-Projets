<?php
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

if (!estConnecte()) {
    header('Location: ../auth/login.php');
    exit;
}

require_once __DIR__ . '/../../services/SprintService.php';
require_once __DIR__ . '/../../services/ProjetService.php';

$sprintService = new SprintService();
$projetService = new ProjetService();

$sprints = [];
$projets = [];
$erreur = '';

try {
    $utilisateur = getUtilisateurConnecte();
    
    // Récupérer les sprints selon le rôle
    if ($utilisateur->getRole() === 'ADMIN') {
        $sprints = []; // Admin pourrait voir tous les sprints
        $projets = $projetService->listerProjets();
        foreach ($projets as $projet) {
            $sprints_projet = $sprintService->listerSprintsProjet($projet->getId());
            $sprints = array_merge($sprints, $sprints_projet);
        }
    } elseif ($utilisateur->canManageProject()) {
        // Chef voit les sprints de ses projets
        $projets = $projetService->listerProjetsDuChef($utilisateur->getId());
        foreach ($projets as $projet) {
            $sprints_projet = $sprintService->listerSprintsProjet($projet->getId());
            $sprints = array_merge($sprints, $sprints_projet);
        }
    } else {
        // Membre voit les sprints des projets où il a des tâches
        require_once __DIR__ . '/../../services/TacheService.php';
        $tacheService = new TacheService();
        $taches_utilisateur = $tacheService->listerTachesUtilisateur($utilisateur->getId());
        
        $sprint_ids = [];
        foreach ($taches_utilisateur as $tache) {
            if ($tache->getSprintId() && !in_array($tache->getSprintId(), $sprint_ids)) {
                $sprint_ids[] = $tache->getSprintId();
            }
        }
        
        foreach ($sprint_ids as $sprint_id) {
            $sprint = $sprintService->getSprintById($sprint_id);
            if ($sprint) {
                $sprints[] = $sprint;
            }
        }
    }
    
} catch (Exception $e) {
    $erreur = $e->getMessage();
}
?>

<div class="main-content">
    <?php require_once '../includes/sidebar.php'; ?>
    
    <div class="card">
        <h2>🏃 Liste des Sprints</h2>
        
        <?php if ($erreur): ?>
            <div class="error"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>
        
        <?php if (aPermission('ADMIN') || peutGererProjets()): ?>
            <div style="margin-bottom: 1rem;">
                <a href="creer.php" class="btn">➕ Créer un sprint</a>
            </div>
        <?php endif; ?>
        
        <?php if (empty($sprints)): ?>
            <p>Aucun sprint trouvé.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Projet</th>
                        <th>Date de début</th>
                        <th>Date de fin</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sprints as $sprint): ?>
                        <tr>
                            <td><?= htmlspecialchars($sprint->getNom()) ?></td>
                            <td>
                                <?php 
                                $projet_associe = null;
                                foreach ($projets as $projet) {
                                    require_once __DIR__ . '/../../services/SprintService.php';
                                    $sprints_projet = $sprintService->listerSprintsProjet($projet->getId());
                                    foreach ($sprints_projet as $sprint_projet) {
                                        if ($sprint_projet->getId() === $sprint->getId()) {
                                            $projet_associe = $projet;
                                            break 2;
                                        }
                                    }
                                }
                                echo $projet_associe ? htmlspecialchars($projet_associe->getNom()) : 'Non trouvé';
                                ?>
                            </td>
                            <td><?= $sprint->getDateDebut() ?? 'Non définie' ?></td>
                            <td><?= $sprint->getDateFin() ?? 'Non définie' ?></td>
                            <td>
                                <span class="<?= $sprint->isActif() ? 'success' : 'error' ?>">
                                    <?= $sprint->isActif() ? 'Actif' : 'Inactif' ?>
                                </span>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="details.php?id=<?= $sprint->getId() ?>" class="btn btn-small">Voir</a>
                                    
                                    <?php 
                                    $utilisateur = getUtilisateurConnecte();
                                    $peutModifier = false;
                                    
                                    // Admin peut tout modifier
                                    if ($utilisateur->getRole() === 'ADMIN') {
                                        $peutModifier = true;
                                    }
                                    // Chef peut modifier les sprints de ses projets
                                    elseif ($utilisateur->canManageProject() && $projet_associe && $projet_associe->getChefId() === $utilisateur->getId()) {
                                        $peutModifier = true;
                                    }
                                    
                                    if ($peutModifier): ?>
                                        <a href="modifier.php?id=<?= $sprint->getId() ?>" class="btn btn-small">Modifier</a>
                                        
                                        <?php if ($utilisateur->getRole() === 'ADMIN'): ?>
                                            <a href="supprimer.php?id=<?= $sprint->getId() ?>" 
                                                   class="btn btn-danger btn-small" 
                                                   onclick="return confirmer('Êtes-vous sûr de vouloir supprimer ce sprint ?')">Supprimer</a>
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
