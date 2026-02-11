<?php
require_once '../includes/header.php';

if (!estConnecte()) {
    header('Location: ../auth/login.php');
    exit;
}

if (!aPermission('ADMIN') && !peutGererProjets()) {
    header('Location: liste.php');
    exit;
}

require_once __DIR__ . '/../../services/SprintService.php';
require_once __DIR__ . '/../../services/ProjetService.php';

$sprintService = new SprintService();
$projetService = new ProjetService();

$projets = [];
$erreur = '';
$success = '';

try {
    $utilisateur = getUtilisateurConnecte();
    
    // Récupérer les projets accessibles selon le rôle
    if ($utilisateur->getRole() === 'ADMIN') {
        $projets = $projetService->listerProjets();
    } elseif ($utilisateur->canManageProject()) {
        $projets = $projetService->listerProjetsDuChef($utilisateur->getId());
    }
    
} catch (Exception $e) {
    $erreur = $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nom' => $_POST['nom'] ?? '',
        'date_debut' => $_POST['date_debut'] ?? '',
        'date_fin' => $_POST['date_fin'] ?? '',
        'projet_id' => $_POST['projet_id'] ?? ''
    ];
    
    try {
        $sprint = $sprintService->creerSprint($utilisateur, $data);
        $success = 'Sprint créé avec succès !';
        
        header('refresh:2;url=liste.php');
        exit;
        
    } catch (Exception $e) {
        $erreur = $e->getMessage();
    }
}
?>

<div class="main-content">
    <?php require_once '../includes/sidebar.php'; ?>
    
    <div class="card" style="max-width: 800px;">
        <h2>➕ Créer un Sprint</h2>
        
        <?php if ($erreur): ?>
            <div class="error"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="nom">Nom du sprint *</label>
                <input type="text" id="nom" name="nom" required 
                       value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="date_debut">Date de début</label>
                    <input type="date" id="date_debut" name="date_debut" 
                           value="<?= htmlspecialchars($_POST['date_debut'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="date_fin">Date de fin</label>
                    <input type="date" id="date_fin" name="date_fin" 
                           value="<?= htmlspecialchars($_POST['date_fin'] ?? '') ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="projet_id">Projet *</label>
                <select id="projet_id" name="projet_id" required>
                    <option value="">Sélectionner un projet</option>
                    <?php foreach ($projets as $projet): ?>
                        <option value="<?= $projet->getId() ?>" 
                                <?= (isset($_POST['projet_id']) && $_POST['projet_id'] == $projet->getId()) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($projet->getNom()) ?>
                            <?php if ($utilisateur->canManageProject()): ?>
                                (Votre projet)
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button type="submit" class="btn">Créer le sprint</button>
                <a href="liste.php" class="btn btn-danger">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
