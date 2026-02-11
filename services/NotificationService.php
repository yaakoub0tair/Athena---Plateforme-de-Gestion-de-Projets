<?php
require_once __DIR__ . '/../repositories/NotificationRepository.php';
require_once __DIR__ . '/../repositories/UtilisateurRepository.php';
require_once __DIR__ . '/../entities/Utilisateur.php';

class NotificationService {
    private $notificationRepository;
    private $utilisateurRepository;

    public function __construct() {
        $this->notificationRepository = new NotificationRepository();
        $this->utilisateurRepository = new UtilisateurRepository();
    }

    public function creerNotification(array $data): Notification {
        // Validation des données
        if (empty($data['utilisateur_id'])) {
            throw new Exception("L'ID de l'utilisateur est obligatoire");
        }
        
        if (empty($data['sujet'])) {
            throw new Exception("Le sujet de la notification est obligatoire");
        }
        
        if (empty($data['contenu'])) {
            throw new Exception("Le contenu de la notification est obligatoire");
        }

        // Validation du type d'événement
        $typesValides = ['CREATION_TACHE', 'STATUT_TACHE', 'COMMENTAIRE'];
        if (isset($data['type_evenement']) && !in_array($data['type_evenement'], $typesValides)) {
            throw new Exception("Type d'événement invalide. Valeurs possibles : CREATION_TACHE, STATUT_TACHE, COMMENTAIRE");
        }

        // Vérification que l'utilisateur existe
        $utilisateur = $this->utilisateurRepository->findById($data['utilisateur_id']);
        if (!$utilisateur) {
            throw new Exception("L'utilisateur spécifié n'existe pas");
        }

        // Création de la notification
        $notification = new Notification([
            'utilisateur_id' => $data['utilisateur_id'],
            'sujet' => $data['sujet'],
            'contenu' => $data['contenu'],
            'type_evenement' => $data['type_evenement'] ?? 'CREATION_TACHE',
            'statut_envoi' => $data['statut_envoi'] ?? 'PENDING',
            'date_creation' => date('Y-m-d H:i:s')
        ]);

        $this->notificationRepository->create($notification);
        return $notification;
    }

    public function listerNotificationsUtilisateur(int $utilisateur_id): array {
        // Vérification que l'utilisateur existe
        $utilisateur = $this->utilisateurRepository->findById($utilisateur_id);
        if (!$utilisateur) {
            throw new Exception("L'utilisateur spécifié n'existe pas");
        }

        return $this->notificationRepository->findByUtilisateur($utilisateur_id);
    }

    public function marquerCommeEnvoye(int $notification_id): bool {
        // Récupération de la notification
        $notification = $this->notificationRepository->findById($notification_id);
        if (!$notification) {
            throw new Exception("Notification non trouvée");
        }

        return $this->notificationRepository->updateStatut($notification, 'SENT');
    }

    public function marquerCommeEchoue(int $notification_id): bool {
        // Récupération de la notification
        $notification = $this->notificationRepository->findById($notification_id);
        if (!$notification) {
            throw new Exception("Notification non trouvée");
        }

        return $this->notificationRepository->updateStatut($notification, 'FAILED');
    }

    public function supprimerNotification(int $notification_id): bool {
        // Récupération de la notification
        $notification = $this->notificationRepository->findById($notification_id);
        if (!$notification) {
            throw new Exception("Notification non trouvée");
        }

        return $this->notificationRepository->delete($notification);
    }

    public function getNotificationById(int $notification_id): ?Notification {
        return $this->notificationRepository->findById($notification_id);
    }

    // Méthodes utilitaires pour créer des notifications standards
    public function creerNotificationTacheCreee(int $utilisateur_id, string $tache_titre): Notification {
        return $this->creerNotification([
            'utilisateur_id' => $utilisateur_id,
            'sujet' => 'Nouvelle tâche créée',
            'contenu' => "Une nouvelle tâche '$tache_titre' vous a été assignée.",
            'type_evenement' => 'CREATION_TACHE'
        ]);
    }

    public function creerNotificationStatutTache(int $utilisateur_id, string $tache_titre, string $nouveau_statut): Notification {
        return $this->creerNotification([
            'utilisateur_id' => $utilisateur_id,
            'sujet' => 'Statut de tâche modifié',
            'contenu' => "Le statut de la tâche '$tache_titre' a été changé en '$nouveau_statut'.",
            'type_evenement' => 'STATUT_TACHE'
        ]);
    }

    public function creerNotificationCommentaire(int $utilisateur_id, string $tache_titre, string $auteur_nom): Notification {
        return $this->creerNotification([
            'utilisateur_id' => $utilisateur_id,
            'sujet' => 'Nouveau commentaire',
            'contenu' => "$auteur_nom a commenté la tâche '$tache_titre'.",
            'type_evenement' => 'COMMENTAIRE'
        ]);
    }
}
