<?php
require_once '../includes/header.php';

if (!estConnecte()) {
    header('Location: login.php');
    exit;
}

$utilisateur = getUtilisateurConnecte();
$erreur = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../services/UtilisateurService.php';
    
    $data = [
        'nom' => $_POST['nom'] ?? $utilisateur->getNom(),
        'email' => $_POST['email'] ?? $utilisateur->getEmail()
    ];
    
    if (!empty($_POST['mot_de_passe'])) {
        $data['mot_de_passe'] = $_POST['mot_de_passe'];
    }
    
    try {
        $utilisateurService = new UtilisateurService();
        $utilisateur = $utilisateurService->modifierUtilisateur($utilisateur->getId(), $data);
        $success = 'Profil mis à jour avec succès !';
        
    } catch (Exception $e) {
        $erreur = $e->getMessage();
    }
}
?>

<div class="main-content">
    <div class="card" style="max-width: 600px; margin: 2rem auto;">
        <h2>👤 Mon Profil</h2>
        
        <?php if ($erreur): ?>
            <div class="error"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" required 
                       value="<?= htmlspecialchars($utilisateur->getNom()) ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required 
                       value="<?= htmlspecialchars($utilisateur->getEmail()) ?>">
            </div>
            
            <div class="form-group">
                <label for="mot_de_passe">Nouveau mot de passe (laisser vide pour ne pas changer) :</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe">
            </div>
            
            <div class="form-group">
                <label>Rôle :</label>
                <input type="text" value="<?= htmlspecialchars($utilisateur->getRole()) ?>" readonly 
                       style="background-color: #f8f9fa;">
            </div>
            
            <div class="form-group">
                <label>Statut :</label>
                <input type="text" value="<?= $utilisateur->isActif() ? 'Actif' : 'Inactif' ?>" readonly 
                       style="background-color: #f8f9fa;">
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button type="submit" class="btn">Mettre à jour</button>
                <a href="../index.php" class="btn btn-danger">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
