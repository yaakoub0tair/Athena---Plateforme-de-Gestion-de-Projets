
<?php
require_once 'Utilisateur.php';

class Admin extends Utilisateur
{
    public function getPermissions(): array
    {
        return ['CREATE_PROJECT', 'DELETE_PROJECT', 'MANAGE_USERS', 'ALL_TASKS'];
    }

    public function canManageProject(): bool
    {
        return true;
    }

    public function getRoleLabel(): string
    {
        return 'Administrateur';
    }
}
