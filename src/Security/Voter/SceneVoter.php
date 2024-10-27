<?php

namespace App\Security\Voter;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class SceneVoter extends Voter
{
    public const SCENE_EDIT = 'SCENE_EDIT';
//    public const VIEW = 'POST_VIEW';
    public const SCENE_DELETE = 'SCENE_DELETE';

    public function __construct(
        private readonly Security $security
    )
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::SCENE_EDIT, self::SCENE_DELETE])
            && $subject instanceof \App\Entity\Scene;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::SCENE_DELETE:
                if ($subject == null) {
                    return false;
                } elseif ($this->security->isGranted('ROLE_ADMIN')) {
                    return true;
                }
                elseif ($this->security->isGranted('ROLE_ORGANIZER') && $subject->getEvenementMusical()->getOrganisateur() == $user) {
                    return true;
                }
                elseif ($user !== $subject) {
                    return false;
                }
                break;
            case self::SCENE_EDIT:
                if ($subject == null) {
                    return false;
                }
                elseif ($this->security->isGranted('ROLE_ORGANIZER') && $subject->getEvenementMusical()->getOrganisateur() == $user) {
                    return true;
                }
                elseif ($user !== $subject) {
                    return false;
                }
                return true;
        }

        return false;
    }
}
