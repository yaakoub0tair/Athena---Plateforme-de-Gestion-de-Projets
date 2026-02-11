<?php
require_once '../includes/header.php';

$erreur = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../services/UtilisateurService.php';
    
    $email = $_POST['email'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    
    try {
        $utilisateurService = new UtilisateurService();
        $utilisateur = $utilisateurService->authentifier($email, $mot_de_passe);
        
        $_SESSION['utilisateur_id'] = $utilisateur->getId();
        $success = 'Connexion réussie ! Redirection...';
        
        header('refresh:2;url=../index.php');
        exit;
        
    } catch (Exception $e) {
        $erreur = $e->getMessage();
    }
}
?>

<div class="main-content">
    <div class="card" style="max-width: 400px; margin: 2rem auto;">
        <h2>🔐 Connexion</h2>
        
        <?php if ($erreur): ?>
            <div class="error"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required 
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="mot_de_passe">Mot de passe :</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required>
            </div>
            
            <button type="submit" class="btn" style="width: 100%;">Se connecter</button>
        </form>
        
        <p style="margin-top: 1rem; text-align: center;">
            Pas encore de compte ? <a href="../register.php">Créer un compte</a>
        </p>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
