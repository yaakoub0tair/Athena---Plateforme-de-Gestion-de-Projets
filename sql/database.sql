CREATE DATABASE athena;
USE athena;
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('ADMIN','CHEF','MEMBRE') DEFAULT 'MEMBRE',
    actif TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE projets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    description TEXT,
    date_debut DATE NOT NULL,
    date_fin DATE,
    statut ENUM('ACTIF','TERMINE','SUSPENDU') DEFAULT 'ACTIF',
    chef_projet_id INT NOT NULL,

    FOREIGN KEY (chef_projet_id) REFERENCES utilisateurs(id)
);
CREATE TABLE sprints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    projet_id INT NOT NULL,

    FOREIGN KEY (projet_id) REFERENCES projets(id)
);
CREATE TABLE taches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(150) NOT NULL,
    description TEXT,
    statut ENUM('A_FAIRE','EN_COURS','TERMINEE') DEFAULT 'A_FAIRE',
    priorite ENUM('BASSE','MOYENNE','HAUTE') DEFAULT 'MOYENNE',
    sprint_id INT NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (sprint_id) REFERENCES sprints(id)
);
CREATE TABLE tache_utilisateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tache_id INT NOT NULL,
    utilisateur_id INT NOT NULL,
    date_affectation DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (tache_id) REFERENCES taches(id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);
CREATE TABLE commentaires (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contenu TEXT NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    utilisateur_id INT NOT NULL,
    tache_id INT NOT NULL,

    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (tache_id) REFERENCES taches(id)
);
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    sujet VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    type_evenement ENUM('CREATION_TACHE','STATUT_TACHE','COMMENTAIRE') NOT NULL,
    statut_envoi ENUM('PENDING','SENT','FAILED') DEFAULT 'PENDING',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_envoi_reelle DATETIME,

    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);
