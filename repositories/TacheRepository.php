<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../entities/Tache.php';

class TacheRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create(Tache $tache): bool {
        try {
            $sql = "INSERT INTO taches (titre, description, statut, priorite, sprint_id, utilisateur_id, actif) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $tache->getTitre(),
                $tache->getDescription(),
                $tache->getStatut(),
                $tache->getPriorite(),
                $tache->getSprintId(),
                $tache->getUtilisateurId(),
                $tache->isActif() ? 1 : 0
            ]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la création de la tâche : " . $e->getMessage());
        }
    }

    public function findById(int $id): ?Tache {
        try {
            $sql = "SELECT * FROM taches WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $data = $stmt->fetch();
            
            if (!$data) {
                return null;
            }
            
            return new Tache($data);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function findBySprint(int $sprintId): array {
        $taches = [];
        try {
            $sql = "SELECT * FROM taches WHERE sprint_id = ? ORDER BY priorite DESC, titre";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$sprintId]);
            
            while ($data = $stmt->fetch()) {
                $taches[] = new Tache($data);
            }
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des tâches du sprint : " . $e->getMessage());
        }
        return $taches;
    }

    public function findByUtilisateur(int $utilisateurId): array {
        $taches = [];
        try {
            $sql = "SELECT * FROM taches WHERE utilisateur_id = ? ORDER BY statut, priorite DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$utilisateurId]);
            
            while ($data = $stmt->fetch()) {
                $taches[] = new Tache($data);
            }
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des tâches de l'utilisateur : " . $e->getMessage());
        }
        return $taches;
    }

    public function update(Tache $tache): bool {
        try {
            $sql = "UPDATE taches SET titre = ?, description = ?, statut = ?, 
                    priorite = ?, sprint_id = ?, utilisateur_id = ?, actif = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $tache->getTitre(),
                $tache->getDescription(),
                $tache->getStatut(),
                $tache->getPriorite(),
                $tache->getSprintId(),
                $tache->getUtilisateurId(),
                $tache->isActif() ? 1 : 0,
                $tache->getId()
            ]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la mise à jour de la tâche : " . $e->getMessage());
        }
    }

    public function delete(int $id): bool {
        try {
            $sql = "DELETE FROM taches WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la suppression de la tâche : " . $e->getMessage());
        }
    }

    public function updateStatut(int $id, string $statut): bool {
        try {
            $sql = "UPDATE taches SET statut = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$statut, $id]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la mise à jour du statut de la tâche : " . $e->getMessage());
        }
    }

    public function assignerUtilisateur(int $tacheId, ?int $utilisateurId): bool {
        try {
            $sql = "UPDATE taches SET utilisateur_id = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$utilisateurId, $tacheId]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de l'assignation de la tâche : " . $e->getMessage());
        }
    }
}
