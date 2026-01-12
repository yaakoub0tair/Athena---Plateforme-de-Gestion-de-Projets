<?php

class Commentaire {
    private ?int $id;
    private string $contenu;
    private ?string $date_creation;
    private int $utilisateur_id;
    private int $tache_id;

    public function __construct(array $data = []) {
        $this->id = $data['id'] ?? null;
        $this->contenu = $data['contenu'] ?? '';
        $this->date_creation = $data['date_creation'] ?? null;
        $this->utilisateur_id = $data['utilisateur_id'] ?? 0;
        $this->tache_id = $data['tache_id'] ?? 0;
    }

    // Getters
    public function getId(): ?int {
        return $this->id;
    }

    public function getContenu(): string {
        return $this->contenu;
    }

    public function getDateCreation(): ?string {
        return $this->date_creation;
    }

    public function getUtilisateurId(): int {
        return $this->utilisateur_id;
    }

    public function getTacheId(): int {
        return $this->tache_id;
    }

    // Setters
    public function setContenu(string $contenu): void {
        $this->contenu = $contenu;
    }

    public function setDateCreation(?string $date_creation): void {
        $this->date_creation = $date_creation;
    }

    public function setUtilisateurId(int $utilisateur_id): void {
        $this->utilisateur_id = $utilisateur_id;
    }

    public function setTacheId(int $tache_id): void {
        $this->tache_id = $tache_id;
    }
}
