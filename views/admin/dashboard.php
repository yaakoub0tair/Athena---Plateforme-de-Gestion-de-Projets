<?php
require_once '../includes/header.php';

if (!estConnecte() || !aPermission('ADMIN')) {
    header('Location: ../index.php');
    exit;
}

require_once __DIR__ . '/../../services/UtilisateurService.php';
require_once __DIR__ . '/../../services/ProjetService.php';
require_once __DIR__ . '/../../services/TacheService.php';

$utilisateurService = new UtilisateurService();
$projetService = new ProjetService();
$tacheService = new TacheService();

$stats = [];
$erreur = '';

try {
    // Statistiques globales
    $stats['utilisateurs'] = $utilisateurService->getAllUtilisateurs();
    $stats['projets'] = $projetService->listerProjets();
    $stats['taches'] = []; // On récupérera via les projets/sprints
    
    $utilisateurs_par_role = ['ADMIN' => 0, 'CHEF' => 0, 'MEMBRE' => 0];
    $projets_actifs = 0;
    $projets_inactifs = 0;
    
    foreach ($stats['utilisateurs'] as $utilisateur) {
        $role = $utilisateur->getRole();
        if (isset($utilisateurs_par_role[$role])) {
            $utilisateurs_par_role[$role]++;
        }
    }
    
    foreach ($stats['projets'] as $projet) {
        if ($projet->isActif()) {
            $projets_actifs++;
        } else {
            $projets_inactifs++;
        }
    }
    
} catch (Exception $e) {
    $erreur = $e->getMessage();
}
?>

<div class="main-content">
    <?php require_once '../includes/sidebar.php'; ?>
    
    <div class="card">
        <h2>⚙️ Tableau de bord Administration</h2>
        
        <?php if ($erreur): ?>
            <div class="error"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>
        
        <h3>📊 Statistiques générales</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin: 2rem 0;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 1.5rem; border-radius: 12px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h4 style="margin: 0 0 0.5rem 0;">👥 Utilisateurs totaux</h4>
                <p style="font-size: 2.5rem; font-weight: bold; margin: 0;"><?= count($stats['utilisateurs']) ?></p>
            </div>
            
            <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 1.5rem; border-radius: 12px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h4 style="margin: 0 0 0.5rem 0;">📁 Projets totaux</h4>
                <p style="font-size: 2.5rem; font-weight: bold; margin: 0;"><?= count($stats['projets']) ?></p>
            </div>
            
            <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 1.5rem; border-radius: 12px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h4 style="margin: 0 0 0.5rem 0;">✅ Projets actifs</h4>
                <p style="font-size: 2.5rem; font-weight: bold; margin: 0;"><?= $projets_actifs ?></p>
            </div>
            
            <div style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; padding: 1.5rem; border-radius: 12px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h4 style="margin: 0 0 0.5rem 0;">⏸️ Projets inactifs</h4>
                <p style="font-size: 2.5rem; font-weight: bold; margin: 0;"><?= $projets_inactifs ?></p>
            </div>
        </div>
        
        <h3>👥 Répartition des utilisateurs par rôle</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 2rem 0;">
            <div style="background: #e74c3c; color: white; padding: 1rem; border-radius: 8px; text-align: center;">
                <h4>🔧 Administrateurs</h4>
                <p style="font-size: 1.8rem; font-weight: bold; margin: 0.5rem 0 0 0;"><?= $utilisateurs_par_role['ADMIN'] ?></p>
            </div>
            
            <div style="background: #3498db; color: white; padding: 1rem; border-radius: 8px; text-align: center;">
                <h4>👨‍💼 Chefs de projet</h4>
                <p style="font-size: 1.8rem; font-weight: bold; margin: 0.5rem 0 0 0;"><?= $utilisateurs_par_role['CHEF'] ?></p>
            </div>
            
            <div style="background: #27ae60; color: white; padding: 1rem; border-radius: 8px; text-align: center;">
                <h4>👤 Membres</h4>
                <p style="font-size: 1.8rem; font-weight: bold; margin: 0.5rem 0 0 0;"><?= $utilisateurs_par_role['MEMBRE'] ?></p>
            </div>
        </div>
        
        <h3>🚀 Actions rapides d'administration</h3>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin: 1rem 0;">
            <a href="../projets/creer.php" class="btn">➕ Créer un projet</a>
            <a href="../utilisateurs/gestion.php" class="btn">👥 Gérer les utilisateurs</a>
            <a href="../projets/liste.php" class="btn">📁 Voir tous les projets</a>
            <a href="../taches/liste.php" class="btn">✅ Voir toutes les tâches</a>
            <a href="../notifications/liste.php" class="btn">🔔 Voir les notifications</a>
        </div>
        
        <h3>📋 Liste des utilisateurs récents</h3>
        <div style="margin-top: 1rem;">
            <?php if (empty($stats['utilisateurs'])): ?>
                <p>Aucun utilisateur trouvé.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $utilisateurs_limites = array_slice($stats['utilisateurs'], 0, 10);
                        foreach ($utilisateurs_limites as $utilisateur): ?>
                            <tr>
                                <td><?= htmlspecialchars($utilisateur->getNom()) ?></td>
                                <td><?= htmlspecialchars($utilisateur->getEmail()) ?></td>
                                <td>
                                    <span class="role-<?= strtolower($utilisateur->getRole()) ?>">
                                        <?= htmlspecialchars($utilisateur->getRole()) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="<?= $utilisateur->isActif() ? 'success' : 'error' ?>">
                                        <?= $utilisateur->isActif() ? 'Actif' : 'Inactif' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a href="../utilisateurs/modifier.php?id=<?= $utilisateur->getId() ?>" class="btn btn-small">Modifier</a>
                                        <a href="../utilisateurs/activer.php?id=<?= $utilisateur->getId() ?>&actif=<?= $utilisateur->isActif() ? 0 : 1 ?>" 
                                           class="btn btn-small <?= $utilisateur->isActif() ? 'btn-warning' : 'btn-success' ?>">
                                            <?= $utilisateur->isActif() ? 'Désactiver' : 'Activer' ?>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.role-admin { background-color: #e74c3c; color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; }
.role-chef { background-color: #3498db; color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; }
.role-membre { background-color: #27ae60; color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; }

.btn-warning { background-color: #f39c12; }
.btn-success { background-color: #27ae60; }
</style>

<?php require_once '../includes/footer.php'; ?>
