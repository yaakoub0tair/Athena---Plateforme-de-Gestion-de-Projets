<?php
require_once 'Utilisateur.php';

class Membre extends Utilisateur
{
    public function getPermissions(): array
    {
        return ['VIEW_TASK', 'UPDATE_OWN_TASK', 'COMMENT_TASK'];
    }

    public function canManageProject(): bool
    {
        return false;
    }

    public function getRoleLabel(): string
    {
        return 'Membre';
    }
}
