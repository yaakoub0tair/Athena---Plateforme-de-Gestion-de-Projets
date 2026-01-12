<aside class="sidebar">
    <nav>
        <ul>
            <?php if (estConnecte()): ?>
                <li><a href="../index.php">🏠 Accueil</a></li>
                
                <?php if (aPermission('ADMIN') || peutGererProjets()): ?>
                <li><a href="projets/liste.php">📁 Projets</a></li>
                <?php endif; ?>
                
                <?php if (aPermission('ADMIN') || peutGererProjets()): ?>
                <li><a href="sprints/liste.php">🏃 Sprints</a></li>
                <?php endif; ?>
                
                <li><a href="taches/liste.php">✅ Tâches</a></li>
                <li><a href="commentaires/liste.php">💬 Commentaires</a></li>
                <li><a href="notifications/liste.php">🔔 Notifications</a></li>
                
                <?php if (aPermission('ADMIN')): ?>
                <li><a href="admin/dashboard.php">⚙️ Administration</a></li>
                <?php endif; ?>
            <?php else: ?>
                <li><a href="auth/login.php">🔐 Connexion</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    
    <style>
        .sidebar {
            width: 250px;
            background-color: #34495e;
            padding: 1rem 0;
            position: fixed;
            height: calc(100vh - 60px);
            overflow-y: auto;
        }
        
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        
        .sidebar li {
            margin-bottom: 0.5rem;
        }
        
        .sidebar a {
            display: block;
            padding: 0.75rem 1.5rem;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        
        .sidebar a:hover {
            background-color: #2c3e50;
        }
        
        .sidebar a.active {
            background-color: #3498db;
        }
    </style>
</aside>
