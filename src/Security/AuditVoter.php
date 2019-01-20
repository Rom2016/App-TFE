<?php
/**
 * Created by PhpStorm.
 * User: Romain
 * Date: 13/09/2018
 * Time: 17:20
 */

namespace App\Security;

use App\Entity\AppUser;
use App\Entity\IntAudit;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;



class AuditVoter extends Voter
{
    // these strings are just invented: you can use anything
    const READ = 'Lecture';
    const WRITE = 'Modification';
    const ADMIN = 'Administrateur';
    const OWNER = 'Owner';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::READ, self::WRITE, self::ADMIN, self::OWNER))) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof IntAudit) {
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
        /** @var IntAudit $audit */
        $audit = $subject;

        switch ($attribute) {
            case self::READ:
                return $this->canView($audit, $user, $token);
            case self::WRITE:
                return $this->canEdit($audit, $user);
            case self::ADMIN:
                return $this->canView($audit, $user, $token);
            case self::OWNER:
                return $this->isOwner($audit, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(IntAudit $audit, AppUser $user, TokenInterface $token)
    {

        foreach ($user->getAuditPermission() as $key => $value){
            if($value->getPermission() == 'Lecture' && $value->getAudit() == $audit){
                return true;
            }
        }
        if($this->canEdit($audit,$user) || $this->isAdmin($audit,$user) || $this->isOwner($audit,$user)){
            return true;
        }
        return false;

    }

    private function canEdit(IntAudit $audit, AppUser $user)
    {
        foreach ($user->getAuditPermission() as $key => $value){
            if($value->getPermission() == 'Modification' && $value->getAudit() == $audit){
                return true;
            }
        }
        if($this->isAdmin($audit,$user) || $this->isOwner($audit, $user)){
            return true;
        }
        return false;
    }

    private function isAdmin(IntAudit $audit, AppUser $user)
    {
        foreach ($user->getAuditPermission() as $key => $value){
            if($value->getPermission() == 'Administrateur' && $value->getAudit() == $audit){
                return true;
            }
        }
        if($this->isOwner($audit, $user)){
            return true;
        }
        return false;
    }
    private function isOwner(IntAudit $audit, AppUser $user)
    {
        foreach ($user->getCreations() as $key => $value){
            if($value->getCreator() == $user && $value->getAudit() == $audit){
                return true;
            }
        }
        if($this->security->isGranted('ROLE_SUPER_ADMIN')){
            return true;
        }
        return false;
    }
}