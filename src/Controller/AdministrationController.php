<?php
/**
 * Created by PhpStorm.
 * User: Romain
 * Date: 14/09/2018
 * Time: 22:33
 */

namespace App\Controller;

use App\Entity\Roles;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Entity\AppUser;
use App\Entity\AuditSection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AdministrationController extends AbstractController
{

    /**
     * Méthode qui gère la partie administration des utilisateurs
     *
     * @Route("/administration/utilisateurs", name="admin")
     */
    public function adminUsers()
    {
        /**
         * Si l'utilisateur est connecté et qu'il a les droits admin, render le template d'admin
         */
        /**
         * Si il y'a eu une soumission du formulaire pour un nouvel utilisateur depuis l'administration.
         */
        if ($_POST){
            switch ($_POST['submit']) {
                case 'newUser':
                    $this->newUser();
                    break;
            }
        }
        $repository = $this->getDoctrine()->getRepository(AppUser::class);
        $array['users'] = $repository->findAll();
        return $this->render('administration/utilisateurs.html.twig', $array);
        /**
         * L'utilisateur n'a pas les droits admin, render le template d'accès refusé.
         */
        /**
         * L'utilisateur n'est pas connecté, redirigé vers le portail de connexion.
         */
    }

    /**
     * Méthode qui gère la partie administration du contenu des audits
     *
     * @Route("/administration/contenu-audits", name="admin_audits_content")
     */
    public function adminAudit()
    {

        $repository = $this->getDoctrine()->getRepository(AppUser::class);
        $repository_section = $this->getDoctrine()->getRepository(AuditSection::class);

        $array['section'] = $repository_section->findAll();
        $array['users'] = $repository->findAll();
        return $this->render('administration/audits.html.twig', $array);
        /**
         * L'utilisateur n'a pas les droits admin, render le template d'accès refusé.
         */
        /**
         * L'utilisateur n'est pas connecté, redirigé vers le portail de connexion.
         */
    }

    /**
     *
     * @Route("/administration/supprimer-utilisateur", name="ajax_delete_user",methods="POST")
     */
    public function deleteUser()
    {
        if(isset($_POST)) {
            $entityManager = $this->getDoctrine()->getManager();
            $repository_audit = $this->getDoctrine()->getRepository(AuditCompany::class);

            $user = $this->getDoctrine()->getRepository(AppUser::class)->findOneBy(['id' => $_POST['id']]);
            $audit = $repository_audit->findBy(['owner' => $user]);
            if($audit){
                foreach ($audit as $key => $value){
                    $value->setOwner($this->getUser());
                    $entityManager->persist($value);
                }
            }
            $entityManager->remove($user);
            $entityManager->flush();
        }
        $array['response'] = 'Utilisateur supprimé!';
        return new JsonResponse($array);
    }

    public function newUser()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_user =  $this->getDoctrine()->getRepository(AppUser::class);
        $repository_role = $this->getDoctrine()->getRepository(Roles::class);
        $role = $repository_role->findOneBy(['role'=>$_POST['selectRole']]);
        $date = new \DateTime(date('Y-m-d H:i:s'));
        $user = new AppUser($_POST['email'],password_hash('test', PASSWORD_BCRYPT),$_POST['fName'],$_POST['sName'],$_POST['function'],$date,$role,$this->getUser());
        $entityManager->persist($user);
        $entityManager->flush();
    }

    /**
     *
     * @Route("/administration/roles", name="ajax_get_role",methods="POST")
     */
    public function getRole()
    {
        $repository_role = $this->getDoctrine()->getRepository(Roles::class);
        $array = $repository_role->findAll();
        foreach ($array as $key => $value){
            $array['role'][] = $value->getRole();
        }
        return new JsonResponse($array['role']);
    }

    /**
     *
     * @Route("/administration/changer-role", name="ajax_save_role", methods="POST")
     */
    public function saveRole()
    {
        $repository_role = $this->getDoctrine()->getRepository(Roles::class);
        $entityManager = $this->getDoctrine()->getManager();
        $repository_user =  $this->getDoctrine()->getRepository(AppUser::class);
        $role = $repository_role->findOneBy(['role'=>$_POST['role']]);
        $user = $repository_user->findOneBy(['id'=>$_POST['id']]);
        $user->setRole($role);
        $entityManager->persist($user);
        $entityManager->flush();
        return new Response($_POST['role']);
    }

    /**
     *
     * @Route("/administration/vérifie-utilisateur", name="ajax_check_username", options={"utf8": true}, methods="POST")
     */
    public function checkUsername()
    {
        $repository_user = $this->getDoctrine()->getRepository(AppUser::class);
        $user = $repository_user->findOneBy(['username'=>$_POST['email']]);
        if($user){
            return new Response('false');
        }else{
            return new Response('true');
        }
    }


}