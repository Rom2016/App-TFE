<?php
/**
 * Created by PhpStorm.
 * User: Romain
 * Date: 13/09/2018
 * Time: 17:20
 */

namespace App\Security;

use App\Entity\AppUser;
use App\Entity\AuditCompany;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;


class AuditVoter extends Voter
{
    // these strings are just invented: you can use anything
    const READ = 'AUDIT_RO';
    const WRITE = 'AUDIT_RW';

    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::READ, self::WRITE))) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof AuditCompany) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof AppUser) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Post object, thanks to supports
        /** @var AuditCompany $audit */
        $audit = $subject;

        switch ($attribute) {
            case self::READ:
                return $this->canView($audit, $user, $token);
            case self::WRITE:
                return $this->canEdit($audit, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(AuditCompany $audit, AppUser $user, TokenInterface $token)
    {
        // if they can edit, they can view
        if ($this->canEdit($audit, $user) || $this->decisionManager->decide($token, array('ROLE_AUDIT_RO'))) {
            return true;
        }

        // the Post object could have, for example, a method isPrivate()
        // that checks a boolean $private property


    }

    private function canEdit(AuditCompany $audit, AppUser $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $audit->getOwner();
    }



}