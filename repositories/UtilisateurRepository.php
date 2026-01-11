<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../entities/Utilisateur.php';
require_once __DIR__ . '/../entities/Admin.php';
require_once __DIR__ . '/../entities/Chefprojet.php';
require_once __DIR__ . '/../entities/Membre.php';

class UtilisateurRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    private function mapRoleToEntity(array $data): ?Utilisateur
    {
        switch ($data['role']) {
            case 'ADMIN':
                return new Admin($data);
            case 'CHEF':
                return new ChefProjet($data);
            case 'MEMBRE':
            default:
                return new Membre($data);
        }
    }
    
    public function findById($id) {
        try {
            $sql = "SELECT * FROM utilisateurs WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $utilisateur= $stmt->fetch();
            if(!$utilisateur){
                return null;
            }
            return $this->mapRoleToEntity($utilisateur);
        
        } catch (PDOException $e) {
            return null;
           
        }
    }
    public function findAll():array{
        $utilisateurs=[];
        try {
            $sql = "SELECT * FROM utilisateurs";
            $stmt = $this->db->query($sql);
        
            while ($utilisateur = $stmt->fetch()) {
                $utilisateurs[] = $this->mapRoleToEntity($utilisateur);
            }
           
        
        } catch (PDOException $e) {
            throw new Exception("Error :findAll : utilisateurs  : " . $e->getMessage());
        }
        return $utilisateurs;
    }
    public function findByEmail($email) {
        try {
            $sql = "SELECT * FROM utilisateurs WHERE email = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$email]);
            $utilisateur= $stmt->fetch();
            if(!$utilisateur){
                
                return null;
            }
            return $this->mapRoleToEntity($utilisateur);
        
        } catch (PDOException $e) {
            return null;
           
        }
    }
    public function findAllByRole($role):array{
        $utilisateurs=[];
        try {
            $sql = "SELECT * FROM utilisateurs WHERE role = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$role]);
            while ($utilisateur = $stmt->fetch()) {
                $utilisateurs[] = $this->mapRoleToEntity($utilisateur);
            }
        } catch (PDOException $e) {
            throw new Exception("Error :findAllByRole : utilisateurs  : " . $e->getMessage());
        }
        return $utilisateurs;
    }
    public function searchbyNom($nom):array{
        $utilisateurs=[];
        try {
            $sql = "SELECT * FROM utilisateurs WHERE nom LIKE ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(["%$nom%"]);
            while ($utilisateur = $stmt->fetch()) {
                $utilisateurs[] = $this->mapRoleToEntity($utilisateur);
            }
        } catch (PDOException $e) {
            throw new Exception("Error :searchbyNom : utilisateurs  : " . $e->getMessage());
        }
        return $utilisateurs;
    }
    public function ajouter(Utilisateur $user):bool {
        try {
            $sql = "SELECT id FROM utilisateurs WHERE email = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$user->getEmail()]);
            if ($stmt->fetch()) {
                throw new Exception("Cet email est déjà utilisé");
            }
            $sql = "INSERT INTO utilisateurs (nom, email, mot_de_passe, role, actif) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $user->getNom(),
                $user->getEmail(),
                $user->getMotDePasse(), 
                $user->getRole(),
                $user->isActif() ? 1 : 0

            ]);
        } catch (PDOException $e) {
            throw new Exception("Error :ajouterUtilisateur : utilisateurs  : " . $e->getMessage());
        }
        return true;
    }
    public function update(Utilisateur $user){
        try {
            $sql = "UPDATE utilisateurs SET nom = ?, email = ?, mot_de_passe = ?, role = ?, actif = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
           return $stmt->execute([
                $user->getNom(),
                $user->getEmail(),
                password_hash($user->getMotDePasse(), PASSWORD_DEFAULT), // Hash du mot de passe
                $user->getRole(),
                $user->isActif() ? 1 : 0,
                $user->getId()
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
    public function delete(Utilisateur $user){
        try {
            $sql = "DELETE FROM utilisateurs WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$user->getId()]);
        } catch (PDOException $e) {
            return false;
        }
    }

}



