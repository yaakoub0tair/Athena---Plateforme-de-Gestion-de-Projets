<?php
require_once __DIR__ . '/../repositories/SprintRepository.php';
require_once __DIR__ . '/../repositories/ProjetRepository.php';
require_once __DIR__ . '/../entities/Utilisateur.php';

class SprintService {
    private $sprintRepository;
    private $projetRepository;

    public function __construct() {
        $this->sprintRepository = new SprintRepository();
        $this->projetRepository = new ProjetRepository();
    }

    public function creerSprint(Utilisateur $utilisateur, array $data): Sprint {
        // Validation des données
        if (empty($data['nom'])) {
            throw new Exception("Le nom du sprint est obligatoire");
        }
        
        if (empty($data['projet_id'])) {
            throw new Exception("L'ID du projet est obligatoire");
        }

        // Vérification que le projet existe
        $projet = $this->projetRepository->findById($data['projet_id']);
        if (!$projet) {
            throw new Exception("Le projet spécifié n'existe pas");
        }

        // Vérification des permissions
        $peutCreer = false;
        
        // L'admin peut créer des sprints pour n'importe quel projet
        if ($utilisateur->getRole() === 'ADMIN') {
            $peutCreer = true;
        }
        // Le chef peut créer des sprints uniquement pour ses projets
        elseif ($utilisateur->canManageProject() && $projet->getChefId() === $utilisateur->getId()) {
            $peutCreer = true;
        }

        if (!$peutCreer) {
            throw new Exception("Vous n'avez pas les droits pour créer un sprint pour ce projet");
        }

        // Création du sprint
        $sprint = new Sprint([
            'nom' => $data['nom'],
            'date_debut' => $data['date_debut'] ?? null,
            'date_fin' => $data['date_fin'] ?? null,
            'projet_id' => $data['projet_id'],
            'actif' => true
        ]);

        $this->sprintRepository->create($sprint);
        return $sprint;
    }

    public function listerSprintsProjet(int $projetId): array {
        // Vérification que le projet existe
        $projet = $this->projetRepository->findById($projetId);
        if (!$projet) {
            throw new Exception("Le projet spécifié n'existe pas");
        }

        return $this->sprintRepository->findByProjet($projetId);
    }

    public function activerDesactiverSprint(Utilisateur $utilisateur, int $sprintId, bool $actif): bool {
        // Vérification : seul un admin peut activer/désactiver
        if ($utilisateur->getRole() !== 'ADMIN') {
            throw new Exception("Seul un administrateur peut activer ou désactiver un sprint");
        }

        // Vérification que le sprint existe
        $sprint = $this->sprintRepository->findById($sprintId);
        if (!$sprint) {
            throw new Exception("Sprint non trouvé");
        }

        return $this->sprintRepository->setActif($sprintId, $actif);
    }

    public function modifierSprint(Utilisateur $utilisateur, int $sprintId, array $data): Sprint {
        // Récupération du sprint
        $sprint = $this->sprintRepository->findById($sprintId);
        if (!$sprint) {
            throw new Exception("Sprint non trouvé");
        }

        // Récupération du projet pour vérifier les permissions
        $projet = $this->projetRepository->findById($sprint->getProjetId());
        if (!$projet) {
            throw new Exception("Projet associé au sprint non trouvé");
        }

        // Vérification des permissions
        $peutModifier = false;
        
        // L'admin peut modifier tous les sprints
        if ($utilisateur->getRole() === 'ADMIN') {
            $peutModifier = true;
        }
        // Le chef peut modifier les sprints de ses projets
        elseif ($utilisateur->canManageProject() && $projet->getChefId() === $utilisateur->getId()) {
            $peutModifier = true;
        }

        if (!$peutModifier) {
            throw new Exception("Vous n'avez pas les droits pour modifier ce sprint");
        }

        // Mise à jour des données
        if (isset($data['nom'])) {
            $sprint->setNom($data['nom']);
        }
        if (isset($data['date_debut'])) {
            $sprint->setDateDebut($data['date_debut']);
        }
        if (isset($data['date_fin'])) {
            $sprint->setDateFin($data['date_fin']);
        }
        if (isset($data['projet_id'])) {
            // Vérification que le nouveau projet existe
            $nouveauProjet = $this->projetRepository->findById($data['projet_id']);
            if (!$nouveauProjet) {
                throw new Exception("Le nouveau projet spécifié n'existe pas");
            }
            $sprint->setProjetId($data['projet_id']);
        }

        $this->sprintRepository->update($sprint);
        return $sprint;
    }

    public function supprimerSprint(Utilisateur $utilisateur, int $sprintId): bool {
        // Vérification : seul un admin peut supprimer
        if ($utilisateur->getRole() !== 'ADMIN') {
            throw new Exception("Seul un administrateur peut supprimer un sprint");
        }

        // Vérification que le sprint existe
        $sprint = $this->sprintRepository->findById($sprintId);
        if (!$sprint) {
            throw new Exception("Sprint non trouvé");
        }

        return $this->sprintRepository->delete($sprintId);
    }

    public function getSprintById(int $sprintId): ?Sprint {
        return $this->sprintRepository->findById($sprintId);
    }
}
