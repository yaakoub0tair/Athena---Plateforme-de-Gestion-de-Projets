<?php


class Projet {
    private ?int $id;
    private string $nom;
    private string $description;
    private ?string $date_debut;
    private ?string $date_fin;
    private bool $actif;
    private ?int $chef_id;


    public function __construct(array $data = []) {
        $this->id = $data['id'] ?? null;
        $this->nom = $data['nom'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->date_debut = $data['date_debut'] ?? null;
        $this->date_fin = $data['date_fin'] ?? null;
        $this->actif = $data['actif'] ?? true;
        $this->chef_id = $data['chef_id'] ?? null;
    }

    // Getters
    public function getId(): ?int {
        return $this->id;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getDateDebut(): ?string {
        return $this->date_debut;
    }

    public function getDateFin(): ?string {
        return $this->date_fin;
    }

    public function isActif(): bool {
        return $this->actif;
    }

    public function getChefId(): ?int {
        return $this->chef_id;
    }

    // Setters
    public function setNom(string $nom): void {
        $this->nom = $nom;
    }

    public function setDescription(string $description): void {
        $this->description = $description;
    }

    public function setDateDebut(?string $date_debut): void {
        $this->date_debut = $date_debut;
    }

    public function setDateFin(?string $date_fin): void {
        $this->date_fin = $date_fin;
    }

    public function setActif(bool $actif): void {
        $this->actif = $actif;
    }

    public function setChefId(?int $chef_id): void {
        $this->chef_id = $chef_id;
    }
}
  