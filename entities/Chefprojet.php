<?php
require_once 'Utilisateur.php';

class Chefprojet extends Utilisateur
{
    public function getPermissions(): array
    {
        return ['CREATE_TASK', 'ASSIGN_TASK', 'VIEW_PROJECT', 'UPDATE_TASK'];
    }

    public function canManageProject(): bool
    {
        return true;
    }

    public function getRoleLabel(): string
    {
        return 'Chef de projet';
    }
    
}
