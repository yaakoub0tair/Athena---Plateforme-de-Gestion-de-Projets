<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../entities/Notification.php';

class NotificationRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create(Notification $notification): bool {
        try {
            $sql = "INSERT INTO notifications (utilisateur_id, sujet, contenu, type_evenement, statut_envoi, date_creation) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $notification->getUtilisateurId(),
                $notification->getSujet(),
                $notification->getContenu(),
                $notification->getTypeEvenement(),
                $notification->getStatutEnvoi(),
                $notification->getDateCreation() ?? date('Y-m-d H:i:s')
            ]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la création de la notification : " . $e->getMessage());
        }
    }

    public function findById(int $id): ?Notification {
        try {
            $sql = "SELECT * FROM notifications WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $data = $stmt->fetch();
            
            if (!$data) {
                return null;
            }
            
            return new Notification($data);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function findByUtilisateur(int $utilisateur_id): array {
        $notifications = [];
        try {
            $sql = "SELECT * FROM notifications WHERE utilisateur_id = ? ORDER BY date_creation DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$utilisateur_id]);
            
            while ($data = $stmt->fetch()) {
                $notifications[] = new Notification($data);
            }
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des notifications de l'utilisateur : " . $e->getMessage());
        }
        return $notifications;
    }

    public function updateStatut(Notification $notification, string $statut): bool {
        try {
            $sql = "UPDATE notifications SET statut_envoi = ?, date_envoi_reelle = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $statut,
                date('Y-m-d H:i:s'),
                $notification->getId()
            ]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la mise à jour du statut de la notification : " . $e->getMessage());
        }
    }

    public function delete(Notification $notification): bool {
        try {
            $sql = "DELETE FROM notifications WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$notification->getId()]);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la suppression de la notification : " . $e->getMessage());
        }
    }
}
