<?php
require_once __DIR__ . '/../repositories/UtilisateurRepository.php';
public class UtilisateurService {
    private $utilisateurRepository;
    public function __construct() {
        $this->utilisateurRepository = new UtilisateurRepository();
    }
    public function ajouterUtilisateur(array $data) {
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
        $user->setMotDePasse(password_hash($user->getMotDePasse(), PASSWORD_DEFAULT));
        $this->utilisateurRepository->ajouter($utilisateur);
    }
    public function modifierUtilisateur(int $id, array $data): Utilisateur {
        $user = $this->repo->findById($id);
        if (!$user) throw new Exception("Utilisateur non trouvé");
    
        $user->setNom($data['nom'] ?? $user->getNom());
        $user->setEmail($data['email'] ?? $user->getEmail());
    
        if (!empty($data['mot_de_passe'])) {
            $user->setMotDePasse(password_hash($data['mot_de_passe'], PASSWORD_DEFAULT));
        }
    
        $user->setActif($data['actif'] ?? $user->isActif());
        $this->repo->update($user);
    
        return $user;
    }
    
}
