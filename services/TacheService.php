<?php
require_once __DIR__ . '/../repositories/TacheRepository.php';
require_once __DIR__ . '/../repositories/SprintRepository.php';
require_once __DIR__ . '/../repositories/ProjetRepository.php';
require_once __DIR__ . '/../entities/Utilisateur.php';

class TacheService {
    private $tacheRepository;
    private $sprintRepository;
    private $projetRepository;

    public function __construct() {
        $this->tacheRepository = new TacheRepository();
        $this->sprintRepository = new SprintRepository();
        $this->projetRepository = new ProjetRepository();
    }

    public function creerTache(Utilisateur $utilisateur, array $data): Tache {
        // Validation des données
        if (empty($data['titre'])) {
            throw new Exception("Le titre de la tâche est obligatoire");
        }
        
        if (empty($data['sprint_id'])) {
            throw new Exception("L'ID du sprint est obligatoire");
        }

        // Validation du statut
        $statutsValides = ['TODO', 'DOING', 'DONE'];
        if (isset($data['statut']) && !in_array($data['statut'], $statutsValides)) {
            throw new Exception("Statut invalide. Valeurs possibles : TODO, DOING, DONE");
        }

        // Validation de la priorité
        $prioritesValides = ['BASSE', 'MOYENNE', 'HAUTE'];
        if (isset($data['priorite']) && !in_array($data['priorite'], $prioritesValides)) {
            throw new Exception("Priorité invalide. Valeurs possibles : BASSE, MOYENNE, HAUTE");
        }

        // Vérification que le sprint existe
        $sprint = $this->sprintRepository->findById($data['sprint_id']);
        if (!$sprint) {
            throw new Exception("Le sprint spécifié n'existe pas");
        }

        // Récupération du projet pour vérifier les permissions
        $projet = $this->projetRepository->findById($sprint->getProjetId());
        if (!$projet) {
            throw new Exception("Le projet associé au sprint n'existe pas");
        }

        // Vérification des permissions
        $peutCreer = false;
        
        // L'admin peut créer des tâches pour n'importe quel sprint
        if ($utilisateur->getRole() === 'ADMIN') {
            $peutCreer = true;
        }
        // Le chef peut créer des tâches uniquement dans les sprints de ses projets
        elseif ($utilisateur->canManageProject() && $projet->getChefId() === $utilisateur->getId()) {
            $peutCreer = true;
        }

        if (!$peutCreer) {
            throw new Exception("Vous n'avez pas les droits pour créer une tâche dans ce sprint");
        }

        // Création de la tâche
        $tache = new Tache([
            'titre' => $data['titre'],
            'description' => $data['description'] ?? '',
            'statut' => $data['statut'] ?? 'TODO',
            'priorite' => $data['priorite'] ?? 'MOYENNE',
            'sprint_id' => $data['sprint_id'],
            'utilisateur_id' => $data['utilisateur_id'] ?? null,
            'actif' => true
        ]);

        $this->tacheRepository->create($tache);
        return $tache;
    }

    public function listerTachesSprint(int $sprintId): array {
        // Vérification que le sprint existe
        $sprint = $this->sprintRepository->findById($sprintId);
        if (!$sprint) {
            throw new Exception("Le sprint spécifié n'existe pas");
        }

        return $this->tacheRepository->findBySprint($sprintId);
    }

    public function listerTachesUtilisateur(int $utilisateurId): array {
        return $this->tacheRepository->findByUtilisateur($utilisateurId);
    }

    public function modifierTache(Utilisateur $utilisateur, int $tacheId, array $data): Tache {
        // Récupération de la tâche
        $tache = $this->tacheRepository->findById($tacheId);
        if (!$tache) {
            throw new Exception("Tâche non trouvée");
        }

        // Validation du statut si fourni
        if (isset($data['statut'])) {
            $statutsValides = ['TODO', 'DOING', 'DONE'];
            if (!in_array($data['statut'], $statutsValides)) {
                throw new Exception("Statut invalide. Valeurs possibles : TODO, DOING, DONE");
            }
        }

        // Validation de la priorité si fournie
        if (isset($data['priorite'])) {
            $prioritesValides = ['BASSE', 'MOYENNE', 'HAUTE'];
            if (!in_array($data['priorite'], $prioritesValides)) {
                throw new Exception("Priorité invalide. Valeurs possibles : BASSE, MOYENNE, HAUTE");
            }
        }

        // Vérification des permissions
        $peutModifier = false;
        
        // L'admin peut modifier toutes les tâches
        if ($utilisateur->getRole() === 'ADMIN') {
            $peutModifier = true;
        }
        // Le chef peut modifier les tâches de ses projets
        elseif ($utilisateur->canManageProject()) {
            $sprint = $this->sprintRepository->findById($tache->getSprintId());
            if ($sprint) {
                $projet = $this->projetRepository->findById($sprint->getProjetId());
                if ($projet && $projet->getChefId() === $utilisateur->getId()) {
                    $peutModifier = true;
                }
            }
        }
        // Le membre peut modifier uniquement ses tâches
        elseif ($tache->getUtilisateurId() === $utilisateur->getId()) {
            $peutModifier = true;
        }

        if (!$peutModifier) {
            throw new Exception("Vous n'avez pas les droits pour modifier cette tâche");
        }

        // Mise à jour des données
        if (isset($data['titre'])) {
            $tache->setTitre($data['titre']);
        }
        if (isset($data['description'])) {
            $tache->setDescription($data['description']);
        }
        if (isset($data['statut'])) {
            $tache->setStatut($data['statut']);
        }
        if (isset($data['priorite'])) {
            $tache->setPriorite($data['priorite']);
        }
        if (isset($data['utilisateur_id'])) {
            $tache->setUtilisateurId($data['utilisateur_id']);
        }
        if (isset($data['sprint_id'])) {
            // Vérification que le nouveau sprint existe
            $nouveauSprint = $this->sprintRepository->findById($data['sprint_id']);
            if (!$nouveauSprint) {
                throw new Exception("Le nouveau sprint spécifié n'existe pas");
            }
            $tache->setSprintId($data['sprint_id']);
        }

        $this->tacheRepository->update($tache);
        return $tache;
    }

    public function supprimerTache(Utilisateur $utilisateur, int $tacheId): bool {
        // Récupération de la tâche
        $tache = $this->tacheRepository->findById($tacheId);
        if (!$tache) {
            throw new Exception("Tâche non trouvée");
        }

        // Vérification des permissions
        $peutSupprimer = false;
        
        // L'admin peut supprimer toutes les tâches
        if ($utilisateur->getRole() === 'ADMIN') {
            $peutSupprimer = true;
        }
        // Le chef peut supprimer les tâches de ses projets
        elseif ($utilisateur->canManageProject()) {
            $sprint = $this->sprintRepository->findById($tache->getSprintId());
            if ($sprint) {
                $projet = $this->projetRepository->findById($sprint->getProjetId());
                if ($projet && $projet->getChefId() === $utilisateur->getId()) {
                    $peutSupprimer = true;
                }
            }
        }

        if (!$peutSupprimer) {
            throw new Exception("Vous n'avez pas les droits pour supprimer cette tâche");
        }

        return $this->tacheRepository->delete($tacheId);
    }

    public function assignerUtilisateur(Utilisateur $utilisateur, int $tacheId, ?int $utilisateurId): bool {
        // Récupération de la tâche
        $tache = $this->tacheRepository->findById($tacheId);
        if (!$tache) {
            throw new Exception("Tâche non trouvée");
        }

        // Vérification des permissions pour l'assignation
        $peutAssigner = false;
        
        // L'admin peut assigner toutes les tâches
        if ($utilisateur->getRole() === 'ADMIN') {
            $peutAssigner = true;
        }
        // Le chef peut assigner les tâches de ses projets
        elseif ($utilisateur->canManageProject()) {
            $sprint = $this->sprintRepository->findById($tache->getSprintId());
            if ($sprint) {
                $projet = $this->projetRepository->findById($sprint->getProjetId());
                if ($projet && $projet->getChefId() === $utilisateur->getId()) {
                    $peutAssigner = true;
                }
            }
        }

        if (!$peutAssigner) {
            throw new Exception("Vous n'avez pas les droits pour assigner cette tâche");
        }

        return $this->tacheRepository->assignerUtilisateur($tacheId, $utilisateurId);
    }

    public function getTacheById(int $tacheId): ?Tache {
        return $this->tacheRepository->findById($tacheId);
    }
}
