<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../entities/Commentaire.php';

class CommentaireRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create(Commentaire $commentaire): bool {
        try {
            $sql = "INSERT INTO commentaires (contenu, date_creation, utilisateur_id, tache_id) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $commentaire->getContenu(),
                $commentaire->getDateCreation() ?? date('Y-m-d H:i:s'),
                $commentaire->getUtilisateurId(),
                $commentaire->getTacheId()
            ]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la création du commentaire : " . $e->getMessage());
        }
    }

    public function findById(int $id): ?Commentaire {
        try {
            $sql = "SELECT * FROM commentaires WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $data = $stmt->fetch();
            
            if (!$data) {
                return null;
            }
            
            return new Commentaire($data);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function findByTache(int $tache_id): array {
        $commentaires = [];
        try {
            $sql = "SELECT * FROM commentaires WHERE tache_id = ? ORDER BY date_creation ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$tache_id]);
            
            while ($data = $stmt->fetch()) {
                $commentaires[] = new Commentaire($data);
            }
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des commentaires de la tâche : " . $e->getMessage());
        }
        return $commentaires;
    }

    public function findByUtilisateur(int $utilisateur_id): array {
        $commentaires = [];
        try {
            $sql = "SELECT * FROM commentaires WHERE utilisateur_id = ? ORDER BY date_creation DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$utilisateur_id]);
            
            while ($data = $stmt->fetch()) {
                $commentaires[] = new Commentaire($data);
            }
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des commentaires de l'utilisateur : " . $e->getMessage());
        }
        return $commentaires;
    }

    public function update(Commentaire $commentaire): bool {
        try {
            $sql = "UPDATE commentaires SET contenu = ?, date_creation = ?, 
                    utilisateur_id = ?, tache_id = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $commentaire->getContenu(),
                $commentaire->getDateCreation(),
                $commentaire->getUtilisateurId(),
                $commentaire->getTacheId(),
                $commentaire->getId()
            ]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la mise à jour du commentaire : " . $e->getMessage());
        }
    }

    public function delete(Commentaire $commentaire): bool {
        try {
            $sql = "DELETE FROM commentaires WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$commentaire->getId()]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la suppression du commentaire : " . $e->getMessage());
        }
    }
}
