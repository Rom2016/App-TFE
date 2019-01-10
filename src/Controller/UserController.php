<?php
/**
 * Created by PhpStorm.
 * User: Romain
 * Date: 29/03/2018
 * Time: 15:42
 */

namespace App\Controller;

use App\Entity\AppUser;
use App\Entity\AuditPhase;
use App\Entity\AuditTestPhase;
use App\Entity\Roles;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use LasseRafn\InitialAvatarGenerator\InitialAvatar;



class UserController extends AbstractController
{


    /**
     *
     * @Route("/", name="homepage")
     */
    public function homepage()
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $repository_user = $this->getDoctrine()->getRepository(AppUser::class);

        $user = $repository_user->findOneBy(['id'=>$this->getUser()]);
        $array['users'] = $repository_user->findAll();
        $array['nbUser'] = $repository_user->getNb();
        $array['createdAudit'] = $user->getCreations();
        if(isset($_GET['nouveau-audit'])){
            $array['new_audit'] = true;
        }
        return $this->render('user/homepage.html.twig', $array);
    }

    /**
     * Méthode qui retourne la page profile et la modification des informations de l'utilisateur si il a soumis le formulaire de la page profile.
     *
     * @Route("/profile", name="view_profile_user")
     */
    public function viewProfile()
    {
        $repository_user = $this->getDoctrine()->getRepository(AppUser::class);

        /**
             * Si le formulaire a été soumis.
             */
            if ($_POST) {
                $entityManager = $this->getDoctrine()->getManager();
                /**
                 * Récupère l'utilisateur
                 */
                $user = $this->getDoctrine()->getRepository(AppUser::class)->findOneBy(['id' => $this->getUser()->id]);
                /**
                 * Le met à jour avec les informations du formulaire et UPDATE
                 */
                $user->updateProfile();
                $entityManager->persist($user);
                $entityManager->flush();
                $array = $_SESSION['user']->getAll();
            }
        $array['users'] = $repository_user->findAll();
        if(isset($_GET['nouveau-audit'])){
            $array['new_audit'] = true;
        }
        return $this->render('user/profile_user.html.twig',$array);
    }

    /**
     *
     * @Route("/génèrer-avatar", name="generate_avatar", options={"utf8": true})
     */
    public function generateAvatar()
    {
        $avatar = new InitialAvatar();
        switch($_GET['rq']){
            case 'avatar':
                $name = $this->getUser()->first_name.' '.$this->getUser()->second_name;
                $image = $avatar->size(120)->name($name)->background('#9124a3')->generate();
                return new Response($image->stream('png', 100));
                break;
            case 'administration':
                $name = $_GET['p'].' '.$_GET['n'];
                $image = $avatar->size(120)->name($name)->background('#9124a3')->generate();
                return new Response($image->stream('png', 100));
                break;
        }
    }
}
