<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../entities/Projet.php';

class ProjetRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function ajouter(Projet $projet):bool {
        try {
            $sql = "INSERT INTO projets (nom, description, date_debut, date_fin, statut, chef_projet_id) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $projet->getNom(),
                $projet->getDescription(),
                $projet->getDateDebut(),
                $projet->getDateFin(),
                $projet->getStatut(),
                $projet->getChefProjetId()
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error :ajouterProjet : projets  : " . $e->getMessage());
        }
        return true;
    }
    public function findAll(): array {
        $projets = [];
        try {
            $sql = "SELECT * FROM projets";
            $stmt = $this->db->query($sql);
        
            while ($projet = $stmt->fetch()) {
                $projets[] = new Projet($projet);
            }
           
        } catch (PDOException $e) {
            throw new Exception("Error :findAll : projets  : " . $e->getMessage());
        }
        return $projets;
    }
    public function findById($id): ?Projet {
        try {
            $sql = "SELECT * FROM projets WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $projet = $stmt->fetch();
            if(!$projet){
                return null;
            }
            return new Projet($projet);
        
        } catch (PDOException $e) {
            return null;
        }
    }
    public function delete(Projet $projet){
        try {
            $sql = "DELETE FROM projets WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$projet->getId()]);
        } catch (PDOException $e) {
            return false;
        }
    }
    public function update(Projet $projet){
        try {
            $sql = "UPDATE projets SET nom = ?, description = ?, date_debut = ?, date_fin = ?, statut = ?, chef_projet_id = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
           return $stmt->execute([
                $projet->getNom(),
                $projet->getDescription(),
                $projet->getDateDebut(),
                $projet->getDateFin(),
                $projet->getStatut(),
                $projet->getChefProjetId(),
                $projet->getId()
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
    public function findAllByChefProjetId($chef_projet_id): array {
        $projets = [];
        try {
            $sql = "SELECT * FROM projets WHERE chef_projet_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$chef_projet_id]);
            while ($projet = $stmt->fetch()) {
                $projets[] = new Projet($projet);
            }
        } catch (PDOException $e) {
            throw new Exception("Error :findAllByChefProjetId : projets  : " . $e->getMessage());
        }
        return $projets;
    }
    public function findAllByStatut($statut): array {
        $projets = [];
        try {
            $sql = "SELECT * FROM projets WHERE statut = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$statut]);
            while ($projet = $stmt->fetch()) {
                $projets[] = new Projet($projet);
            }
        } catch (PDOException $e) {
            throw new Exception("Error :findAllByStatut : projets  : " . $e->getMessage());
        }
        return $projets;
    }
    
    
}