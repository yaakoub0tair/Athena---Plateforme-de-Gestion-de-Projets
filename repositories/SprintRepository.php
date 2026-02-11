<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../entities/Sprint.php';

class SprintRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create(Sprint $sprint): bool {
        try {
            $sql = "INSERT INTO sprints (nom, date_debut, date_fin, projet_id, actif) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $sprint->getNom(),
                $sprint->getDateDebut(),
                $sprint->getDateFin(),
                $sprint->getProjetId(),
                $sprint->isActif() ? 1 : 0
            ]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la création du sprint : " . $e->getMessage());
        }
    }

    public function findById(int $id): ?Sprint {
        try {
            $sql = "SELECT * FROM sprints WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $data = $stmt->fetch();
            
            if (!$data) {
                return null;
            }
            
            return new Sprint($data);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function findByProjet(int $projetId): array {
        $sprints = [];
        try {
            $sql = "SELECT * FROM sprints WHERE projet_id = ? ORDER BY date_debut";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$projetId]);
            
            while ($data = $stmt->fetch()) {
                $sprints[] = new Sprint($data);
            }
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des sprints du projet : " . $e->getMessage());
        }
        return $sprints;
    }

    public function update(Sprint $sprint): bool {
        try {
            $sql = "UPDATE sprints SET nom = ?, date_debut = ?, 
                    date_fin = ?, projet_id = ?, actif = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $sprint->getNom(),
                $sprint->getDateDebut(),
                $sprint->getDateFin(),
                $sprint->getProjetId(),
                $sprint->isActif() ? 1 : 0,
                $sprint->getId()
            ]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la mise à jour du sprint : " . $e->getMessage());
        }
    }

    public function delete(int $id): bool {
        try {
            $sql = "DELETE FROM sprints WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la suppression du sprint : " . $e->getMessage());
        }
    }

    public function setActif(int $id, bool $actif): bool {
        try {
            $sql = "UPDATE sprints SET actif = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$actif ? 1 : 0, $id]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors du changement de statut du sprint : " . $e->getMessage());
        }
    }
}
