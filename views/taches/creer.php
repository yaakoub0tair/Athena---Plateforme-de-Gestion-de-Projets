<?php
require_once '../includes/header.php';

if (!estConnecte()) {
    header('Location: ../auth/login.php');
    exit;
}

require_once __DIR__ . '/../../services/TacheService.php';
require_once __DIR__ . '/../../services/ProjetService.php';
require_once __DIR__ . '/../../services/SprintService.php';

$tacheService = new TacheService();
$projetService = new ProjetService();
$sprintService = new SprintService();

$projets = [];
$sprints = [];
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
        'titre' => $_POST['titre'] ?? '',
        'description' => $_POST['description'] ?? '',
        'statut' => $_POST['statut'] ?? 'TODO',
        'priorite' => $_POST['priorite'] ?? 'MOYENNE',
        'sprint_id' => $_POST['sprint_id'] ?? '',
        'utilisateur_id' => !empty($_POST['utilisateur_id']) ? $_POST['utilisateur_id'] : null
    ];
    
    try {
        $tache = $tacheService->creerTache($utilisateur, $data);
        $success = 'Tâche créée avec succès !';
        
        header('refresh:2;url=liste.php');
        exit;
        
    } catch (Exception $e) {
        $erreur = $e->getMessage();
    }
}

// Charger les sprints si un projet est sélectionné
if (isset($_GET['projet_id'])) {
    try {
        $sprints = $sprintService->listerSprintsProjet($_GET['projet_id']);
    } catch (Exception $e) {
        $erreur = $e->getMessage();
    }
}
?>

<div class="main-content">
    <?php require_once '../includes/sidebar.php'; ?>
    
    <div class="card" style="max-width: 800px;">
        <h2>➕ Créer une Tâche</h2>
        
        <?php if ($erreur): ?>
            <div class="error"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <form method="POST" id="tacheForm">
            <div class="form-group">
                <label for="titre">Titre de la tâche *</label>
                <input type="text" id="titre" name="titre" required 
                       value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="statut">Statut</label>
                    <select id="statut" name="statut">
                        <option value="TODO" <?= (isset($_POST['statut']) && $_POST['statut'] === 'TODO') ? 'selected' : '' ?>>TODO</option>
                        <option value="DOING" <?= (isset($_POST['statut']) && $_POST['statut'] === 'DOING') ? 'selected' : '' ?>>DOING</option>
                        <option value="DONE" <?= (isset($_POST['statut']) && $_POST['statut'] === 'DONE') ? 'selected' : '' ?>>DONE</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="priorite">Priorité</label>
                    <select id="priorite" name="priorite">
                        <option value="BASSE" <?= (isset($_POST['priorite']) && $_POST['priorite'] === 'BASSE') ? 'selected' : '' ?>>BASSE</option>
                        <option value="MOYENNE" <?= (isset($_POST['priorite']) && $_POST['priorite'] === 'MOYENNE') ? 'selected' : '' ?>>MOYENNE</option>
                        <option value="HAUTE" <?= (isset($_POST['priorite']) && $_POST['priorite'] === 'HAUTE') ? 'selected' : '' ?>>HAUTE</option>
                    </select>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="projet_id">Projet *</label>
                    <select id="projet_id" name="projet_id" required onchange="chargerSprints()">
                        <option value="">Sélectionner un projet</option>
                        <?php foreach ($projets as $projet): ?>
                            <option value="<?= $projet->getId() ?>" 
                                    data-chef-id="<?= $projet->getChefId() ?>"
                                    <?= (isset($_POST['projet_id']) && $_POST['projet_id'] == $projet->getId()) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($projet->getNom()) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="sprint_id">Sprint *</label>
                    <select id="sprint_id" name="sprint_id" required>
                        <option value="">Sélectionner d'abord un projet</option>
                        <?php foreach ($sprints as $sprint): ?>
                            <option value="<?= $sprint->getId() ?>" 
                                    <?= (isset($_POST['sprint_id']) && $_POST['sprint_id'] == $sprint->getId()) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($sprint->getNom()) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="utilisateur_id">Assigner à (optionnel)</label>
                <select id="utilisateur_id" name="utilisateur_id">
                    <option value="">Non assigné</option>
                    <?php 
                    require_once __DIR__ . '/../../services/UtilisateurService.php';
                    $utilisateurService = new UtilisateurService();
                    $utilisateurs = $utilisateurService->getAllUtilisateurs();
                    foreach ($utilisateurs as $utilisateur): ?>
                        <option value="<?= $utilisateur->getId() ?>" 
                                <?= (isset($_POST['utilisateur_id']) && $_POST['utilisateur_id'] == $utilisateur->getId()) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($utilisateur->getNom()) ?> (<?= htmlspecialchars($utilisateur->getRole()) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button type="submit" class="btn">Créer la tâche</button>
                <a href="liste.php" class="btn btn-danger">Annuler</a>
            </div>
        </form>
    </div>
</div>

<script>
function chargerSprints() {
    const projetSelect = document.getElementById('projet_id');
    const sprintSelect = document.getElementById('sprint_id');
    const projetId = projetSelect.value;
    
    if (projetId) {
        fetch(`../api/get_sprints.php?projet_id=${projetId}`)
            .then(response => response.json())
            .then(data => {
                sprintSelect.innerHTML = '<option value="">Sélectionner un sprint</option>';
                data.forEach(sprint => {
                    const option = document.createElement('option');
                    option.value = sprint.id;
                    option.textContent = sprint.nom;
                    sprintSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Erreur:', error));
    } else {
        sprintSelect.innerHTML = '<option value="">Sélectionner d\'abord un projet</option>';
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>
