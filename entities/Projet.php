<?php
public class Projet {
    private ?int $id;
    private string $nom;
    private string $description;
    private $date_debut;
    private $date_fin;
    private $statut;
    private $chef_projet_id;

    public function __construct(array $data = []) {
        $this->id = $data['id'] ?? null;
        $this->nom = $data['nom'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->date_debut = $data['date_debut'] ?? null;
        $this->date_fin = $data['date_fin'] ?? null;
        $this->statut = $data['statut'] ?? 'ACTIF';
        $this->chef_projet_id = $data['chef_projet_id'] ?? null;
    }
    public function getId() {
        return $this->id;
    }
    public  function getNom() {
        return $this->nom;
    }
    public function getDescription() {
        return $this->description;
    }
    public function getDateDebut() {
        return $this->date_debut;
    }
    public function getDateFin() {
        return $this->date_fin;
    }
    public function getStatut() {
        return $this->statut;
    }
    public function getChefProjetId() {
        return $this->chef_projet_id;
    }
    public function setNom($nom) {
        $this->nom = $nom;
    }
    public function setDescription($description) {
        $this->description = $description;
    }
    public function setDateDebut($date_debut) {
        $this->date_debut = $date_debut;
    }
    public function setDateFin($date_fin) {
        $this->date_fin = $date_fin;
    }
    public function setStatut($statut) {
        $this->statut = $statut;
    }
    public function setChefProjetId($chef_projet_id) {
        $this->chef_projet_id = $chef_projet_id;
    }
}
  