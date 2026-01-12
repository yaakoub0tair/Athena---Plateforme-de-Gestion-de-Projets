<?php
session_start();
require_once __DIR__ . '/../../services/UtilisateurService.php';

// Vérification de l'authentification
$utilisateur_connecte = null;
if (isset($_SESSION['utilisateur_id'])) {
    $utilisateurService = new UtilisateurService();
    $utilisateur_connecte = $utilisateurService->getUtilisateurById($_SESSION['utilisateur_id']);
}

function estConnecte() {
    global $utilisateur_connecte;
    return $utilisateur_connecte !== null;
}

function getUtilisateurConnecte() {
    global $utilisateur_connecte;
    return $utilisateur_connecte;
}

function aPermission($role_requis) {
    global $utilisateur_connecte;
    if (!$utilisateur_connecte) return false;
    return $utilisateur_connecte->getRole() === $role_requis;
}

function peutGererProjets() {
    global $utilisateur_connecte;
    if (!$utilisateur_connecte) return false;
    return $utilisateur_connecte->canManageProject();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Athena - Plateforme de Gestion de Projets</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }
        
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            font-size: 1.5rem;
        }
        
        .header .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .btn {
            background-color: #3498db;
            color: white;
            padding: 0.5rem 1rem;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }
        
        .btn:hover {
            background-color: #2980b9;
        }
        
        .btn-danger {
            background-color: #e74c3c;
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
        }
        
        .container {
            display: flex;
            min-height: calc(100vh - 60px);
        }
        
        .main-content {
            flex: 1;
            padding: 2rem;
            margin-left: 250px;
        }
        
        .card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .card h2 {
            margin-bottom: 1rem;
            color: #2c3e50;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .table th,
        .table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        
        .table tr:hover {
            background-color: #f8f9fa;
        }
        
        .success {
            color: #27ae60;
            font-weight: bold;
        }
        
        .error {
            color: #e74c3c;
            font-weight: bold;
        }
        
        .warning {
            color: #f39c12;
            font-weight: bold;
        }
        
        .actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-small {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <h1>🚀 Athena</h1>
        <div class="user-info">
            <?php if (estConnecte()): ?>
                <span>Bonjour, <?= htmlspecialchars(getUtilisateurConnecte()->getNom()) ?> 
                      (<?= htmlspecialchars(getUtilisateurConnecte()->getRole()) ?>)</span>
                <a href="profil.php" class="btn btn-small">Profil</a>
                <a href="../logout.php" class="btn btn-danger btn-small">Déconnexion</a>
            <?php else: ?>
                <a href="auth/login.php" class="btn">Connexion</a>
            <?php endif; ?>
        </div>
    </header>
    <div class="container">
