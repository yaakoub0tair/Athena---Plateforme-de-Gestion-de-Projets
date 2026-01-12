<?php
require_once __DIR__ . '/../repositories/ProjetRepository.php';
require_once __DIR__ . '/../repositories/UtilisateurRepository.php';
require_once __DIR__ . '/../entities/Utilisateur.php';


class ProjetService {
    private $projetRepository;
    private $utilisateurRepository;

    public function __construct() {
        $this->projetRepository = new ProjetRepository();
        $this->utilisateurRepository = new UtilisateurRepository();
    }


    public function creerProjet(Utilisateur $utilisateur, array $data): Projet {
        // Vérification : seul un chef peut créer un projet
        if (!$utilisateur->canManageProject()) {
            throw new Exception("Seul un chef de projet peut créer un projet");
        }

        // Validation des données
        if (empty($data['nom'])) {
            throw new Exception("Le nom du projet est obligatoire");
        }

        // Vérification que le chef existe
        $chefId = $data['chef_id'] ?? $utilisateur->getId();
        $chef = $this->utilisateurRepository->findById($chefId);
        if (!$chef || !$chef->canManageProject()) {
            throw new Exception("Le chef de projet spécifié n'est pas valide");
        }

        // Création du projet
        $projet = new Projet([
            'nom' => $data['nom'],
            'description' => $data['description'] ?? '',
            'date_debut' => $data['date_debut'] ?? null,
            'date_fin' => $data['date_fin'] ?? null,
            'actif' => true,
            'chef_id' => $chefId
        ]);

        $this->projetRepository->create($projet);
        return $projet;
    }

   
    public function listerProjets(): array {
        return $this->projetRepository->findAll();
    }

   
    public function listerProjetsDuChef(int $chefId): array {
        return $this->projetRepository->findByChefId($chefId);
    }

 
    public function activerDesactiverProjet(Utilisateur $utilisateur, int $projetId, bool $actif): bool {
        // Vérification : seul un admin peut activer/désactiver
        if ($utilisateur->getRole() !== 'ADMIN') {
            throw new Exception("Seul un administrateur peut activer ou désactiver un projet");
        }

        // Vérification que le projet existe
        $projet = $this->projetRepository->findById($projetId);
        if (!$projet) {
            throw new Exception("Projet non trouvé");
        }

        return $this->projetRepository->setActif($projetId, $actif);
    }

   
    public function modifierProjet(Utilisateur $utilisateur, int $projetId, array $data): Projet {
        // Récupération du projet
        $projet = $this->projetRepository->findById($projetId);
        if (!$projet) {
            throw new Exception("Projet non trouvé");
        }

        // Vérification des permissions
        $peutModifier = false;
        
        // L'admin peut tout modifier
        if ($utilisateur->getRole() === 'ADMIN') {
            $peutModifier = true;
        }
        // Le chef peut modifier ses propres projets
        elseif ($utilisateur->canManageProject() && $projet->getChefId() === $utilisateur->getId()) {
            $peutModifier = true;
        }

        if (!$peutModifier) {
            throw new Exception("Vous n'avez pas les droits pour modifier ce projet");
        }

        // Mise à jour des données
        if (isset($data['nom'])) {
            $projet->setNom($data['nom']);
        }
        if (isset($data['description'])) {
            $projet->setDescription($data['description']);
        }
        if (isset($data['date_debut'])) {
            $projet->setDateDebut($data['date_debut']);
        }
        if (isset($data['date_fin'])) {
            $projet->setDateFin($data['date_fin']);
        }
        if (isset($data['chef_id'])) {
            // Vérification que le nouveau chef est bien un chef
            $nouveauChef = $this->utilisateurRepository->findById($data['chef_id']);
            if (!$nouveauChef || !$nouveauChef->canManageProject()) {
                throw new Exception("Le nouveau chef de projet n'est pas valide");
            }
            $projet->setChefId($data['chef_id']);
        }

        $this->projetRepository->update($projet);
        return $projet;
    }

  
    public function supprimerProjet(Utilisateur $utilisateur, int $projetId): bool {
        // Vérification : seul un admin peut supprimer
        if ($utilisateur->getRole() !== 'ADMIN') {
            throw new Exception("Seul un administrateur peut supprimer un projet");
        }

        // Vérification que le projet existe
        $projet = $this->projetRepository->findById($projetId);
        if (!$projet) {
            throw new Exception("Projet non trouvé");
        }

        return $this->projetRepository->delete($projetId);
    }

   
    public function getProjetById(int $projetId): ?Projet {
        return $this->projetRepository->findById($projetId);
    }
}
