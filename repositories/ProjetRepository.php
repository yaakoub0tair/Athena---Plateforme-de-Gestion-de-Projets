<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../entities/Projet.php';


class ProjetRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

 
    public function create(Projet $projet): bool {
        try {
            $sql = "INSERT INTO projets (nom, description, date_debut, date_fin, actif, chef_id) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $projet->getNom(),
                $projet->getDescription(),
                $projet->getDateDebut(),
                $projet->getDateFin(),
                $projet->isActif() ? 1 : 0,
                $projet->getChefId()
            ]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la création du projet : " . $e->getMessage());
        }
    }

   
    public function findById(int $id): ?Projet {
        try {
            $sql = "SELECT * FROM projets WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $data = $stmt->fetch();
            
            if (!$data) {
                return null;
            }
            
            return new Projet($data);
        } catch (PDOException $e) {
            return null;
        }
    }

   
    public function findAll(): array {
        $projets = [];
        try {
            $sql = "SELECT * FROM projets ORDER BY nom";
            $stmt = $this->db->query($sql);
            
            while ($data = $stmt->fetch()) {
                $projets[] = new Projet($data);
            }
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des projets : " . $e->getMessage());
        }
        return $projets;
    }

    
    public function update(Projet $projet): bool {
        try {
            $sql = "UPDATE projets SET nom = ?, description = ?, date_debut = ?, 
                    date_fin = ?, actif = ?, chef_id = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $projet->getNom(),
                $projet->getDescription(),
                $projet->getDateDebut(),
                $projet->getDateFin(),
                $projet->isActif() ? 1 : 0,
                $projet->getChefId(),
                $projet->getId()
            ]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la mise à jour du projet : " . $e->getMessage());
        }
    }

  
    public function delete(int $id): bool {
        try {
            $sql = "DELETE FROM projets WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la suppression du projet : " . $e->getMessage());
        }
    }

 
    public function setActif(int $id, bool $actif): bool {
        try {
            $sql = "UPDATE projets SET actif = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$actif ? 1 : 0, $id]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors du changement de statut du projet : " . $e->getMessage());
        }
    }

  
    public function findByChefId(int $chefId): array {
        $projets = [];
        try {
            $sql = "SELECT * FROM projets WHERE chef_id = ? ORDER BY nom";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$chefId]);
            
            while ($data = $stmt->fetch()) {
                $projets[] = new Projet($data);
            }
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des projets du chef : " . $e->getMessage());
        }
        return $projets;
    }
}