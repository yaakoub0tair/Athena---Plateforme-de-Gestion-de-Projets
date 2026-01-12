<?php
require_once __DIR__ . '/../repositories/UtilisateurRepository.php';

class UtilisateurService {
    private $utilisateurRepository;
    
    public function __construct() {
        $this->utilisateurRepository = new UtilisateurRepository();
    }
    
   
    public function ajouterUtilisateur(array $data): bool {
        if (empty($data['nom']) || empty($data['email']) || empty($data['mot_de_passe'])) {
            throw new Exception("Champs obligatoires manquants");
        }
        
        switch ($data['role']) {
            case 'ADMIN':
                $utilisateur = new Admin($data);
                break;
            case 'CHEF':
                $utilisateur = new ChefProjet($data);
                break;
            case 'MEMBRE':
            default:
                $utilisateur = new Membre($data);
        }
        
        $utilisateur->setMotDePasse(password_hash($utilisateur->getMotDePasse(), PASSWORD_DEFAULT));
        return $this->utilisateurRepository->ajouter($utilisateur);
    }
    

    public function authentifier(string $email, string $mot_de_passe): ?Utilisateur {
        if (empty($email) || empty($mot_de_passe)) {
            throw new Exception("Email et mot de passe requis");
        }
        
        $utilisateur = $this->utilisateurRepository->findByEmail($email);
        
        if (!$utilisateur) {
            throw new Exception("Email ou mot de passe incorrect");
        }
        
        if (!$utilisateur->isActif()) {
            throw new Exception("Compte désactivé");
        }
        
        if (!password_verify($mot_de_passe, $utilisateur->getMotDePasse())) {
            throw new Exception("Email ou mot de passe incorrect");
        }
        
        return $utilisateur;
    }
    

    public function modifierUtilisateur(int $id, array $data): Utilisateur {
        $utilisateur = $this->utilisateurRepository->findById($id);
        if (!$utilisateur) {
            throw new Exception("Utilisateur non trouvé");
        }
        
        $utilisateur->setNom($data['nom'] ?? $utilisateur->getNom());
        $utilisateur->setEmail($data['email'] ?? $utilisateur->getEmail());
        
        if (!empty($data['mot_de_passe'])) {
            $utilisateur->setMotDePasse(password_hash($data['mot_de_passe'], PASSWORD_DEFAULT));
        }
        
        if (isset($data['actif'])) {
            $utilisateur->setActif($data['actif']);
        }
        
        $this->utilisateurRepository->update($utilisateur);
        return $utilisateur;
    }
    

    public function activerDesactiverUtilisateur(int $id, bool $actif): bool {
        $utilisateur = $this->utilisateurRepository->findById($id);
        if (!$utilisateur) {
            throw new Exception("Utilisateur non trouvé");
        }
        
        $utilisateur->setActif($actif);
        return $this->utilisateurRepository->update($utilisateur);
    }
    
  
    public function getAllUtilisateurs(): array {
        return $this->utilisateurRepository->findAll();
    }
    
  
    public function getUtilisateurById(int $id): ?Utilisateur {
        return $this->utilisateurRepository->findById($id);
    }
}
