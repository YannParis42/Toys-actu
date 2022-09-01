<?php

namespace App\Security\Voter;

use App\Entity\News;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class NewsVoter extends Voter
{
    private Security $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';
    protected function supports(string $attribute, $subject): bool
    {
     
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof \App\Entity\News;

    }
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($subject, $user);
                break;
            // case self::VIEW:
            //     return $this->canView($subject, $user);
            //     break;
        }
        return false;
    }
    private function canEdit(News $news, User $user): bool
{
    return $user === $news->getCreatedBy();
}



}