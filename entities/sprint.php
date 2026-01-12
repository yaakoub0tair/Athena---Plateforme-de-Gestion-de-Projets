<?php

class Sprint {
    private ?int $id;
    private string $nom;
    private ?string $date_debut;
    private ?string $date_fin;
    private ?int $projet_id;
    private bool $actif;

    public function __construct(array $data = []) {
        $this->id = $data['id'] ?? null;
        $this->nom = $data['nom'] ?? '';
        $this->date_debut = $data['date_debut'] ?? null;
        $this->date_fin = $data['date_fin'] ?? null;
        $this->projet_id = $data['projet_id'] ?? null;
        $this->actif = $data['actif'] ?? true;
    }

    // Getters
    public function getId(): ?int {
        return $this->id;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function getDateDebut(): ?string {
        return $this->date_debut;
    }

    public function getDateFin(): ?string {
        return $this->date_fin;
    }

    public function getProjetId(): ?int {
        return $this->projet_id;
    }

    public function isActif(): bool {
        return $this->actif;
    }

    // Setters
    public function setNom(string $nom): void {
        $this->nom = $nom;
    }

    public function setDateDebut(?string $date_debut): void {
        $this->date_debut = $date_debut;
    }

    public function setDateFin(?string $date_fin): void {
        $this->date_fin = $date_fin;
    }

    public function setProjetId(?int $projet_id): void {
        $this->projet_id = $projet_id;
    }

    public function setActif(bool $actif): void {
        $this->actif = $actif;
    }
}