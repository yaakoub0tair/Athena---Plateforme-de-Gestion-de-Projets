<?php

class Notification {
    private ?int $id;
    private int $utilisateur_id;
    private string $sujet;
    private string $contenu;
    private string $type_evenement;
    private string $statut_envoi;
    private ?string $date_creation;
    private ?string $date_envoi_reelle;

    public function __construct(array $data = []) {
        $this->id = $data['id'] ?? null;
        $this->utilisateur_id = $data['utilisateur_id'] ?? 0;
        $this->sujet = $data['sujet'] ?? '';
        $this->contenu = $data['contenu'] ?? '';
        $this->type_evenement = $data['type_evenement'] ?? 'CREATION_TACHE';
        $this->statut_envoi = $data['statut_envoi'] ?? 'PENDING';
        $this->date_creation = $data['date_creation'] ?? null;
        $this->date_envoi_reelle = $data['date_envoi_reelle'] ?? null;
    }

    // Getters
    public function getId(): ?int {
        return $this->id;
    }

    public function getUtilisateurId(): int {
        return $this->utilisateur_id;
    }

    public function getSujet(): string {
        return $this->sujet;
    }

    public function getContenu(): string {
        return $this->contenu;
    }

    public function getTypeEvenement(): string {
        return $this->type_evenement;
    }

    public function getStatutEnvoi(): string {
        return $this->statut_envoi;
    }

    public function getDateCreation(): ?string {
        return $this->date_creation;
    }

    public function getDateEnvoiReelle(): ?string {
        return $this->date_envoi_reelle;
    }

    // Setters
    public function setUtilisateurId(int $utilisateur_id): void {
        $this->utilisateur_id = $utilisateur_id;
    }

    public function setSujet(string $sujet): void {
        $this->sujet = $sujet;
    }

    public function setContenu(string $contenu): void {
        $this->contenu = $contenu;
    }

    public function setTypeEvenement(string $type_evenement): void {
        $this->type_evenement = $type_evenement;
    }

    public function setStatutEnvoi(string $statut_envoi): void {
        $this->statut_envoi = $statut_envoi;
    }

    public function setDateCreation(?string $date_creation): void {
        $this->date_creation = $date_creation;
    }

    public function setDateEnvoiReelle(?string $date_envoi_reelle): void {
        $this->date_envoi_reelle = $date_envoi_reelle;
    }
}
