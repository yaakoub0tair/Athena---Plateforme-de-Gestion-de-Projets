<?php
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
require_once __DIR__ . '/../services/ProjetService.php';
require_once __DIR__ . '/../services/TacheService.php';
require_once __DIR__ . '/../services/UtilisateurService.php';

$projetService = new ProjetService();
$tacheService = new TacheService();
$utilisateurService = new UtilisateurService();

$stats = [];
if (estConnecte()) {
    try {
        $utilisateur = getUtilisateurConnecte();
        
        // Statistiques de base
        $stats['total_projets'] = count($projetService->listerProjets());
        $stats['total_taches'] = 0;
        $stats['taches_terminees'] = 0;
        
        // Si admin, stats globales
        if (aPermission('ADMIN')) {
            $stats['total_utilisateurs'] = count($utilisateurService->getAllUtilisateurs());
            $stats['total_sprints'] = 0; // À implémenter avec SprintService
        }
        
        // Tâches de l'utilisateur
        if ($utilisateur->getRole() === 'MEMBRE') {
            $taches_utilisateur = $tacheService->listerTachesUtilisateur($utilisateur->getId());
            $stats['total_taches'] = count($taches_utilisateur);
            foreach ($taches_utilisateur as $tache) {
                if ($tache->getStatut() === 'DONE') {
                    $stats['taches_terminees']++;
                }
            }
        }
        
    } catch (Exception $e) {
        $erreur = $e->getMessage();
    }
}
?>

<div class="main-content">
    <?php if (isset($erreur)): ?>
        <div class="card error">
            <?= htmlspecialchars($erreur) ?>
        </div>
    <?php endif; ?>
    
    <?php if (!estConnecte()): ?>
        <div class="card">
            <h2>Bienvenue sur Athena 🚀</h2>
            <p>Athena est votre plateforme de gestion de projets Scrum.</p>
            <p>Gérez vos projets, sprints et tâches en toute simplicité.</p>
            <div style="margin-top: 2rem;">
                <a href="auth/login.php" class="btn">Se connecter</a>
            </div>
        </div>
    <?php else: ?>
        <div class="card">
            <h2>Tableau de bord - <?= htmlspecialchars(getUtilisateurConnecte()->getNom()) ?></h2>
            
            <?php if (aPermission('ADMIN')): ?>
                <h3>📊 Statistiques globales</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 1rem 0;">
                    <div style="background: #e3f2fd; padding: 1rem; border-radius: 8px; text-align: center;">
                        <h4>👥 Utilisateurs</h4>
                        <p style="font-size: 2rem; font-weight: bold; margin: 0;">
                            <?= $stats['total_utilisateurs'] ?? 0 ?>
                        </p>
                    </div>
                    <div style="background: #f3e5f5; padding: 1rem; border-radius: 8px; text-align: center;">
                        <h4>📁 Projets</h4>
                        <p style="font-size: 2rem; font-weight: bold; margin: 0;">
                            <?= $stats['total_projets'] ?>
                        </p>
                    </div>
                    <div style="background: #e8f5e8; padding: 1rem; border-radius: 8px; text-align: center;">
                        <h4>🏃 Sprints</h4>
                        <p style="font-size: 2rem; font-weight: bold; margin: 0;">
                            <?= $stats['total_sprints'] ?? 0 ?>
                        </p>
                    </div>
                    <div style="background: #fff3e0; padding: 1rem; border-radius: 8px; text-align: center;">
                        <h4>✅ Tâches</h4>
                        <p style="font-size: 2rem; font-weight: bold; margin: 0;">
                            <?= $stats['total_taches'] ?>
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <h3>📈 Vos statistiques</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 1rem 0;">
                    <div style="background: #e3f2fd; padding: 1rem; border-radius: 8px; text-align: center;">
                        <h4>📁 Projets</h4>
                        <p style="font-size: 2rem; font-weight: bold; margin: 0;">
                            <?= $stats['total_projets'] ?>
                        </p>
                    </div>
                    <div style="background: #e8f5e8; padding: 1rem; border-radius: 8px; text-align: center;">
                        <h4>✅ Tâches totales</h4>
                        <p style="font-size: 2rem; font-weight: bold; margin: 0;">
                            <?= $stats['total_taches'] ?>
                        </p>
                    </div>
                    <div style="background: #c8e6c9; padding: 1rem; border-radius: 8px; text-align: center;">
                        <h4>🎯 Tâches terminées</h4>
                        <p style="font-size: 2rem; font-weight: bold; margin: 0;">
                            <?= $stats['taches_terminees'] ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <h3>🚀 Actions rapides</h3>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <?php if (aPermission('ADMIN') || peutGererProjets()): ?>
                    <a href="projets/creer.php" class="btn">➕ Créer un projet</a>
                <?php endif; ?>
                
                <?php if (aPermission('ADMIN') || peutGererProjets()): ?>
                    <a href="sprints/creer.php" class="btn">➕ Créer un sprint</a>
                <?php endif; ?>
                
                <a href="taches/creer.php" class="btn">➕ Créer une tâche</a>
                <a href="notifications/liste.php" class="btn">🔔 Voir les notifications</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
