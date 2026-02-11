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

$erreur = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../services/ProjetService.php';
    
    $data = [
        'nom' => $_POST['nom'] ?? '',
        'description' => $_POST['description'] ?? '',
        'date_debut' => $_POST['date_debut'] ?? '',
        'date_fin' => $_POST['date_fin'] ?? '',
        'chef_id' => $_POST['chef_id'] ?? getUtilisateurConnecte()->getId()
    ];
    
    try {
        $projetService = new ProjetService();
        $projet = $projetService->creerProjet(getUtilisateurConnecte(), $data);
        $success = 'Projet créé avec succès !';
        
        header('refresh:2;url=liste.php');
        exit;
        
    } catch (Exception $e) {
        $erreur = $e->getMessage();
    }
}

require_once __DIR__ . '/../../services/UtilisateurService.php';
$utilisateurService = new UtilisateurService();
$chefs = $utilisateurService->findAllByRole('CHEF');
?>

<div class="main-content">
    <?php require_once '../includes/sidebar.php'; ?>
    
    <div class="card" style="max-width: 800px;">
        <h2>➕ Créer un Projet</h2>
        
        <?php if ($erreur): ?>
            <div class="error"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="nom">Nom du projet *</label>
                <input type="text" id="nom" name="nom" required 
                       value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
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
                <label for="chef_id">Chef de projet *</label>
                <select id="chef_id" name="chef_id" required>
                    <option value="">Sélectionner un chef</option>
                    <?php foreach ($chefs as $chef): ?>
                        <option value="<?= $chef->getId() ?>" 
                                <?= (isset($_POST['chef_id']) && $_POST['chef_id'] == $chef->getId()) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($chef->getNom()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button type="submit" class="btn">Créer le projet</button>
                <a href="liste.php" class="btn btn-danger">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
