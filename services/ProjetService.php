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
    
    /**
     * Crée un nouveau projet (réservé aux chefs de projet)
     */
    public function creerProjet(Utilisateur $utilisateur, array $data): Projet {
        // Vérification : seul un chef de projet peut créer un projet
        if (!$utilisateur->canManageProject()) {
            throw new Exception("Seul un chef de projet peut créer un projet");
        }
        
        // Validation des données
        if (empty($data['nom'])) {
            throw new Exception("Le nom du projet est obligatoire");
        }
        
        // Vérification que le chef de projet existe et est bien un chef
        $chefId = $data['chef_projet_id'] ?? $utilisateur->getId();
        $chef = $this->utilisateurRepository->findById($chefId);
        
        if (!$chef || !$chef->canManageProject()) {
            throw new Exception("Le chef de projet spécifié n'est pas valide");
        }
        
        // Création du projet
        $projetData = [
            'nom' => $data['nom'],
            'description' => $data['description'] ?? '',
            'date_debut' => $data['date_debut'] ?? null,
            'date_fin' => $data['date_fin'] ?? null,
            'statut' => $data['statut'] ?? 'ACTIF',
            'chef_projet_id' => $chefId
        ];
        
        $projet = new Projet($projetData);
        $this->projetRepository->ajouter($projet);
        
        return $projet;
    }
    
    /**
     * Liste tous les projets (pour tous les rôles)
     */
    public function listerProjets(): array {
        return $this->projetRepository->findAll();
    }
    
    /**
     * Liste les projets d'un chef de projet spécifique
     */
    public function listerProjetsChef(int $chefId): array {
        return $this->projetRepository->findAllByChefProjetId($chefId);
    }
    
    /**
     * Active ou désactive un projet (réservé aux admins)
     */
    public function activerDesactiverProjet(Utilisateur $utilisateur, int $projetId, string $statut): bool {
        // Vérification : seul un admin peut activer/désactiver un projet
        if ($utilisateur->getRole() !== 'ADMIN') {
            throw new Exception("Seul un administrateur peut activer/désactiver un projet");
        }
        
        // Validation du statut
        if (!in_array($statut, ['ACTIF', 'INACTIF'])) {
            throw new Exception("Statut invalide. Valeurs possibles : ACTIF, INACTIF");
        }
        
        $projet = $this->projetRepository->findById($projetId);
        if (!$projet) {
            throw new Exception("Projet non trouvé");
        }
        
        $projet->setStatut($statut);
        return $this->projetRepository->update($projet);
    }
    
    /**
     * Récupère un projet par son ID
     */
    public function getProjetById(int $projetId): ?Projet {
        return $this->projetRepository->findById($projetId);
    }
    
    /**
     * Modifie un projet (chef de projet du projet ou admin)
     */
    public function modifierProjet(Utilisateur $utilisateur, int $projetId, array $data): Projet {
        $projet = $this->projetRepository->findById($projetId);
        if (!$projet) {
            throw new Exception("Projet non trouvé");
        }
        
        // Vérification des permissions
        $canEdit = ($utilisateur->getRole() === 'ADMIN') || 
                   ($utilisateur->canManageProject() && $projet->getChefProjetId() === $utilisateur->getId());
        
        if (!$canEdit) {
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
        if (isset($data['chef_projet_id'])) {
            // Vérification que le nouveau chef est bien un chef
            $newChef = $this->utilisateurRepository->findById($data['chef_projet_id']);
            if (!$newChef || !$newChef->canManageProject()) {
                throw new Exception("Le nouveau chef de projet n'est pas valide");
            }
            $projet->setChefProjetId($data['chef_projet_id']);
        }
        
        $this->projetRepository->update($projet);
        return $projet;
    }
    
    /**
     * Supprime un projet (admin uniquement)
     */
    public function supprimerProjet(Utilisateur $utilisateur, int $projetId): bool {
        if ($utilisateur->getRole() !== 'ADMIN') {
            throw new Exception("Seul un administrateur peut supprimer un projet");
        }
        
        $projet = $this->projetRepository->findById($projetId);
        if (!$projet) {
            throw new Exception("Projet non trouvé");
        }
        
        return $this->projetRepository->delete($projet);
    }
}
