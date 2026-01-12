<?php
require_once __DIR__ . '/../repositories/CommentaireRepository.php';
require_once __DIR__ . '/../repositories/TacheRepository.php';
require_once __DIR__ . '/../entities/Utilisateur.php';

class CommentaireService {
    private $commentaireRepository;
    private $tacheRepository;

    public function __construct() {
        $this->commentaireRepository = new CommentaireRepository();
        $this->tacheRepository = new TacheRepository();
    }

    public function creerCommentaire(array $data, Utilisateur $utilisateur): Commentaire {
        // Validation des données
        if (empty($data['contenu'])) {
            throw new Exception("Le contenu du commentaire est obligatoire");
        }
        
        if (empty($data['tache_id'])) {
            throw new Exception("L'ID de la tâche est obligatoire");
        }

        // Vérification que la tâche existe
        $tache = $this->tacheRepository->findById($data['tache_id']);
        if (!$tache) {
            throw new Exception("La tâche spécifiée n'existe pas");
        }

        // Tous les utilisateurs peuvent créer des commentaires
        $commentaire = new Commentaire([
            'contenu' => $data['contenu'],
            'date_creation' => date('Y-m-d H:i:s'),
            'utilisateur_id' => $utilisateur->getId(),
            'tache_id' => $data['tache_id']
        ]);

        $this->commentaireRepository->create($commentaire);
        return $commentaire;
    }

    public function listerCommentairesTache(int $tache_id): array {
        // Vérification que la tâche existe
        $tache = $this->tacheRepository->findById($tache_id);
        if (!$tache) {
            throw new Exception("La tâche spécifiée n'existe pas");
        }

        return $this->commentaireRepository->findByTache($tache_id);
    }

    public function modifierCommentaire(int $id, array $data, Utilisateur $utilisateur): Commentaire {
        // Récupération du commentaire
        $commentaire = $this->commentaireRepository->findById($id);
        if (!$commentaire) {
            throw new Exception("Commentaire non trouvé");
        }

        // Vérification des permissions
        $peutModifier = false;
        
        // L'admin peut modifier tous les commentaires
        if ($utilisateur->getRole() === 'ADMIN') {
            $peutModifier = true;
        }
        // L'auteur peut modifier son commentaire
        elseif ($commentaire->getUtilisateurId() === $utilisateur->getId()) {
            $peutModifier = true;
        }

        if (!$peutModifier) {
            throw new Exception("Vous n'avez pas les droits pour modifier ce commentaire");
        }

        // Mise à jour des données
        if (isset($data['contenu'])) {
            $commentaire->setContenu($data['contenu']);
        }

        $this->commentaireRepository->update($commentaire);
        return $commentaire;
    }

    public function supprimerCommentaire(int $id, Utilisateur $utilisateur): bool {
        // Récupération du commentaire
        $commentaire = $this->commentaireRepository->findById($id);
        if (!$commentaire) {
            throw new Exception("Commentaire non trouvé");
        }

        // Vérification des permissions
        $peutSupprimer = false;
        
        // L'admin peut supprimer tous les commentaires
        if ($utilisateur->getRole() === 'ADMIN') {
            $peutSupprimer = true;
        }
        // L'auteur peut supprimer son commentaire
        elseif ($commentaire->getUtilisateurId() === $utilisateur->getId()) {
            $peutSupprimer = true;
        }

        if (!$peutSupprimer) {
            throw new Exception("Vous n'avez pas les droits pour supprimer ce commentaire");
        }

        return $this->commentaireRepository->delete($commentaire);
    }

    public function getCommentaireById(int $id): ?Commentaire {
        return $this->commentaireRepository->findById($id);
    }

    public function listerCommentairesUtilisateur(int $utilisateur_id): array {
        return $this->commentaireRepository->findByUtilisateur($utilisateur_id);
    }
}
