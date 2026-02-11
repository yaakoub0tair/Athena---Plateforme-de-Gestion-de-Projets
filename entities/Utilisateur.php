<?php

abstract class Utilisateur
{
    protected ?int $id;
    protected string $nom;
    protected string $email;
    protected string $mot_de_passe;
    protected string $role;
    protected bool $actif;
    protected ?string $created_at;

    public function __construct(array $data = [])
    {
        $this->id         = $data['id'] ?? null;
        $this->nom        = $data['nom'] ?? '';
        $this->email      = $data['email'] ?? '';
        $this->mot_de_passe = $data['mot_de_passe'] ?? '';
        $this->role       = $data['role'] ?? 'MEMBRE';
        $this->actif      = $data['actif'] ?? true;
        $this->created_at = $data['created_at'] ?? null;
    }

  

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
    public function getMotDePasse(): string
    {
        return $this->mot_de_passe;
    }
    

    public function getRole(): string
    {
        return $this->role;
    }

    public function isActif(): bool
    {
        return $this->actif;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }



    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setMotDePasse(string $mot_de_passe): void
    {
        $this->mot_de_passe = $mot_de_passe;
    }

    public function setActif(bool $actif): void
    {
        $this->actif = $actif;
    }

 

    abstract public function getPermissions(): array;

    abstract public function canManageProject(): bool;

    abstract public function getRoleLabel(): string;
}
