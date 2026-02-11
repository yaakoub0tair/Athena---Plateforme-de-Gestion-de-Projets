<?php

class Tache {
    private ?int $id;
    private string $titre;
    private string $description;
    private string $statut;
    private string $priorite;
    private ?int $sprint_id;
    private ?int $utilisateur_id;
    private bool $actif;

    public function __construct(array $data = []) {
        $this->id = $data['id'] ?? null;
        $this->titre = $data['titre'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->statut = $data['statut'] ?? 'TODO';
        $this->priorite = $data['priorite'] ?? 'MOYENNE';
        $this->sprint_id = $data['sprint_id'] ?? null;
        $this->utilisateur_id = $data['utilisateur_id'] ?? null;
        $this->actif = $data['actif'] ?? true;
    }

    // Getters
    public function getId(): ?int {
        return $this->id;
    }

    public function getTitre(): string {
        return $this->titre;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getStatut(): string {
        return $this->statut;
    }

    public function getPriorite(): string {
        return $this->priorite;
    }

    public function getSprintId(): ?int {
        return $this->sprint_id;
    }

    public function getUtilisateurId(): ?int {
        return $this->utilisateur_id;
    }

    public function isActif(): bool {
        return $this->actif;
    }

    // Setters
    public function setTitre(string $titre): void {
        $this->titre = $titre;
    }

    public function setDescription(string $description): void {
        $this->description = $description;
    }

    public function setStatut(string $statut): void {
        $this->statut = $statut;
    }

    public function setPriorite(string $priorite): void {
        $this->priorite = $priorite;
    }

    public function setSprintId(?int $sprint_id): void {
        $this->sprint_id = $sprint_id;
    }

    public function setUtilisateurId(?int $utilisateur_id): void {
        $this->utilisateur_id = $utilisateur_id;
    }

    public function setActif(bool $actif): void {
        $this->actif = $actif;
    }
}
