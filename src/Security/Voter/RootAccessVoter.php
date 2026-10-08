<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Security\Enum\Permission;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class RootAccessVoter extends Voter
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof User) {
            return false;
        }
        

        if ($user->isRoot()) {
            return true;
        }
        
        // CRM employees inherit ordinary Tasks access, not task administration.
        // Preserve explicitly assigned legacy TASK_ACCESS roles.
        if ($attribute === Permission::TASK_ACCESS->value) {
            return $user->hasPermission(Permission::CRM_ACCESS->value) || $user->hasPermission(Permission::TASK_ACCESS->value);
        }
        return $user->hasPermission($attribute);
    }
}
