<?php
/**
 * Created by PhpStorm.
 * User: Romain
 * Date: 14/09/2018
 * Time: 22:33
 */

namespace App\Controller;

use App\Entity\AuditSubSection;
use App\Entity\AuditTests;
use App\Entity\Roles;
use App\Entity\TestSelections;
use App\Entity\TestType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Entity\AppUser;
use App\Entity\AuditSection;
use Symfony\Component\DependencyInjection\Tests\Compiler\J;
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
     * Méthode qui gère la partie administration du contenu des audits
     *
     * @Route("/administration/contenu-audits/ajouter-sous-section", name="admin_audits_content_addsub")
     */
    public function newSubsection()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_subsection = $this->getDoctrine()->getRepository(AuditSubSection::class);
        $repository_section = $this->getDoctrine()->getRepository(AuditSection::class);
        $date = new \DateTime(date('Y-m-d H:i:s'));
        $section = $repository_section->findOneBy(['id'=>$_POST['id']]);
        $subsection_name = htmlspecialchars($_POST['subsection']);
        $subsection = new AuditSubSection($subsection_name,$section,$date);
        $entityManager->persist($subsection);
        $entityManager->flush();
        $array['subsection'] = $subsection_name;
        $array['id'] = $subsection->getId();
        return $this->render('administration/newsubsection.html.twig',$array);
    }

    /**
     * Méthode qui gère la partie administration du contenu des audits
     *
     * @Route("/administration/contenu-audits/ajouter-section", name="admin_audits_content_addsection")
     */
    public function newSection()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_section = $this->getDoctrine()->getRepository(AuditSection::class);
        $subsection_name = htmlspecialchars($_POST['section']);
        $date = new \DateTime(date('Y-m-d H:i:s'));
        $section = new AuditSection($subsection_name, $date);
        $entityManager->persist($section);
        $entityManager->flush();
        $array['section'] = $section->getSection();
        $array['id'] = $section->getId();
        return $this->render('administration/newsection.html.twig', $array);
    }

    /**
     * Méthode qui gère la partie administration du contenu des audits
     *
     * @Route("/administration/contenu-audits/ajouter-test", name="admin_audits_content_newtest")
     */
    public function newTest()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_subsection = $this->getDoctrine()->getRepository(AuditSubSection::class);
        $repository_test = $this->getDoctrine()->getRepository(AuditTests::class);
        $repository_type = $this->getDoctrine()->getRepository(TestType::class);
        $date = new \DateTime(date('Y-m-d H:i:s'));

        $type = $repository_type->findOneBy(['type'=>$_POST['type']]);
        $subsection = $repository_subsection->findOneBy(['id'=>$_POST['sub']]);
        $test = new AuditTests($_POST['data'], '1', $subsection, $type, $date);
        $entityManager->persist($test);
        $entityManager->flush();
        $template['test'] = $_POST['data'];
        $template['id'] = $test->getId();

        return $this->render('administration/newtest.html.twig',$template);
    }

    /**
     * Méthode qui gère la partie administration du contenu des audits
     *
     * @Route("/administration/contenu-audits/ajouter", name="admin_audits_content_newselect")
     */
    public function newSelect()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_test = $this->getDoctrine()->getRepository(AuditTests::class);
        $date = new \DateTime(date('Y-m-d H:i:s'));

        $test = $repository_test->findOneBy(['id'=>$_POST['id']]);
        $select = new TestSelections($_POST['select'], $test, 'fail', $date);
        $entityManager->persist($select);
        $entityManager->flush();
        $template['selection'] = $_POST['select'];
        //$template['status'] = $test->getId();

        return $this->render('administration/newselection.html.twig',$template);
    }

    /**
     * Méthode qui gère la partie administration du contenu des audits
     *
     * @Route("/administration/contenu-audits/ajouter-enfant", name="admin_audits_content_newchild")
     */
    public function newChild()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_test = $this->getDoctrine()->getRepository(AuditTests::class);
        $date = new \DateTime(date('Y-m-d H:i:s'));

        $test = $repository_test->findOneBy(['id'=>$_POST['id']]);
        $child = new AuditTests($_POST['child'], '1', $test->getSusbection(), $test->getType(), $date);
        $child->setParent($test);
        $entityManager->persist($child);
        $entityManager->flush();
        $template['selection'] = $_POST['select'];
        //$template['status'] = $test->getId();

        return $this->render('administration/newselection.html.twig',$template);
    }
    /**
     * Méthode qui gère la partie administration du contenu des audits
     *
     * @Route("/administration/contenu-audits/modifier", name="admin_audits_content_modify")
     */
    public function modifyAuditContent()
    {
        switch($_POST['rq']){
            case 'section': return new JsonResponse($this->updateSection());
            break;
            case 'subsection': $this->updateSection();
            break;
            case 'test' :

        }
    }

    public function updateSection()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_section = $this->getDoctrine()->getRepository(AuditSection::class);
        $repository_sub = $this->getDoctrine()->getRepository(AuditSubSection::class);
        $date = new \DateTime(date('Y-m-d H:i:s'));
        $section = $repository_section->findOneBy(['id'=>$_POST['section']]);
        $subsection = $repository_sub->findBy(['section'=>$section]);
        $section->setArchivedDate($date);
        $entityManager->persist($section);
        $updatedSection = new AuditSection($_POST['data'],$date);
        $entityManager->persist($updatedSection);
        foreach ($subsection as $key=>$value){
            if(!$value->getDateArchive()){
                $subsection = new AuditSubSection($value->getSubsection(),$updatedSection,$date);
                $value->setDateArchive($date);
                $entityManager->persist($value);
                $entityManager->persist($subsection);
            }
        }
        $entityManager->flush();
        $response['html'] = '<span id="section'.$updatedSection->getId().'" class="editable suggestion section">'.$updatedSection->getSection().'</span>';
        $response['id'] = $updatedSection->getId();
        return $response;

    }

    public function updateSubSection()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_section = $this->getDoctrine()->getRepository(AuditSection::class);
        $repository_sub = $this->getDoctrine()->getRepository(AuditSubSection::class);
        $date = new \DateTime(date('Y-m-d H:i:s'));
        $section = $repository_section->findOneBy(['id'=>$_POST['section']]);
        $subsection = $repository_sub->findBy(['section'=>$section]);
        $section->setArchivedDate($date);
        $entityManager->persist($section);
        $updatedSection = new AuditSection($_POST['data'],$date);
        $entityManager->persist($updatedSection);
        foreach ($subsection as $key=>$value){
            if(!$value->getDateArchive()){
                $subsection = new AuditSubSection($value->getSubsection(),$updatedSection,$date);
                $value->setDateArchive($date);
                $entityManager->persist($value);
                $entityManager->persist($subsection);
            }
        }
        $entityManager->flush();
        $response['html'] = '<span id="section'.$updatedSection->getId().'" class="editable suggestion section">'.$updatedSection->getSection().'</span>';
        $response['id'] = $updatedSection->getId();
        return $response;

    }

    public function updateTest()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_test = $this->getDoctrine()->getRepository(AuditTests::class);

        $date = new \DateTime(date('Y-m-d H:i:s'));
        $test = $repository_test->findOneBy(['id'=>$_POST['test']]);
        $test->setDateArchive($date);
        $updatedTest = new AuditTests($_POST['data'], $test->getPriority(), $test->getSusbection(), $test->getType(), $date);

        $entityManager->persist($updatedTest);
        //foreach ($subsection as $key=>$value){

        //}
        $entityManager->flush();
     //   $response['html'] = '<span id="section'.$updatedSection->getId().'" class="editable suggestion section">'.$updatedSection->getSection().'</span>';
      //  $response['id'] = $updatedSection->getId();
    //    return $response;

    }

    /**
     * Méthode qui gère la partie administration du contenu des audits
     *
     * @Route("/administration/contenu-audits/modifier/status-selection", name="admin_audits_content_modify_selection_status")
     */
    public function updateSelectionStatus()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_selection = $this->getDoctrine()->getRepository(TestSelections::class);

       // $date = new \DateTime(date('Y-m-d H:i:s'));
        $selection = $repository_selection->findOneBy(['id'=>$_POST['id']]);
        $selection->setStatus($_POST['status']);
        $entityManager->persist($selection);
        $entityManager->flush();
        $template['id'] = $selection->getId();
        $template['status'] = $selection->getStatus();
        return $this->render('administration/updateselection.html.twig',$template);

    }

}