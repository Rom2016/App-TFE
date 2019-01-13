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
use App\Entity\InfraSelection;
use App\Entity\IntCustomer;
use App\Entity\LinkSelectInfra;
use App\Entity\LinkSnapTest;
use App\Entity\LinkTestsInfra;
use App\Entity\LogAction;
use App\Entity\LogAdminContent;
use App\Entity\LogAdminCustomer;
use App\Entity\LogAdminUser;
use App\Entity\Roles;
use App\Entity\Snapshot;
use App\Entity\Status;
use App\Entity\TestSelections;
use App\Entity\TestType;
use App\Entity\UserRoles;
use App\Entity\Solution;
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
     * @Route("/administration/utilisateurs", name="admin_user")
     */
    public function adminUsers()
    {
        /**
         * Si l'utilisateur est connecté et qu'il a les droits admin, render le template d'admin
         */
        /**
         * Si il y'a eu une soumission du formulaire pour un nouvel utilisateur depuis l'administration.
         */
        if ($_POST) {
            switch ($_POST['submit']) {
                case 'newUser':
                    $this->newUser();
                    break;
            }
        }
        $repository = $this->getDoctrine()->getRepository(AppUser::class);
        $repository_role = $this->getDoctrine()->getRepository(Roles::class);
        $repository_log = $this->getDoctrine()->getRepository(LogAdminUser::class);

        $array['users'] = $repository->findAll();
        $array['roles'] = $repository_role->findAll();
        $array['log'] = $repository_log->findAll();
        if (isset($_GET['nouveau-audit'])) {
            $array['new_audit'] = true;
        }
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
     * @Route("/administration/utilisateurs/supprimer-utilisateur", name="ajax_delete_user",methods="POST")
     */
    public function deleteUser()
    {
        if (isset($_POST)) {
            $entityManager = $this->getDoctrine()->getManager();
            $repository_log = $this->getDoctrine()->getRepository(LogAction::class);
            $date = new \DateTime(date('Y-m-d H:i:s'));
            $user = $this->getDoctrine()->getRepository(AppUser::class)->findOneBy(['id' => $_POST['id']]);
            $user->setDeactivated(true);
            $entityManager->persist($user);
            $array['user'] = $user;
            $action = $repository_log->findOneBy(['action' => 'Supprimer']);
            $log = new LogAdminUser($this->getUser(), $user, $action, $date);
            $entityManager->persist($log);
            $entityManager->flush();
            return $this->render('administration/disableuser.html.twig', $array);

        }
    }

    public function newUser()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_user = $this->getDoctrine()->getRepository(AppUser::class);
        $repository_role = $this->getDoctrine()->getRepository(Roles::class);
        $repository_role_user = $this->getDoctrine()->getRepository(UserRoles::class);
        $date = new \DateTime(date('Y-m-d H:i:s'));
        $repository_log = $this->getDoctrine()->getRepository(LogAction::class);

        /**
         * Créer le nouvel utilisateur
         */
        $user = new AppUser($_POST['email'], password_hash($_POST['initial-pwd'], PASSWORD_BCRYPT), $_POST['fName'], $_POST['sName'], $_POST['function'], $date);
        $entityManager->persist($user);
        /**
         * Créer ses rôles
         *
         */
        if (isset($_POST['role-special'])) {
            $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
            if ($_POST['role-special'] == 'ROLE_ADMIN_GLOBAL') {
                $role = $repository_role->findOneBy(['role' => 'ROLE_ADMIN_GLOBAL']);
                $user_role = new UserRoles($role, $user);
                $entityManager->persist($user_role);
            }
            /**
             * Gère les multi roles admin
             */
        } elseif (isset($_POST['role-admin'])) {
            foreach ($_POST['role-admin'] as $key => $value) {
                $role = $repository_role->findOneBy(['role' => $value]);
                $user_role = new UserRoles($role, $user);
                $entityManager->persist($user_role);
            }
            /**
             * Gère les roles utilisateurs
             */
        } else {
            $role = $repository_role->findOneBy(['role' => $_POST['role-user']]);
            $user_role = new UserRoles($role, $user);
            $entityManager->persist($user_role);
        }

        $action = $repository_log->findOneBy(['action' => 'Créer']);
        $log = new LogAdminUser($this->getUser(), $user, $action, $date);
        $entityManager->persist($log);
        $entityManager->flush();
    }


    /**
     *
     * @Route("/administration/changer-role", name="ajax_save_role", methods="POST")
     */
    public function saveRole()
    {
        $repository_role = $this->getDoctrine()->getRepository(Roles::class);
        $entityManager = $this->getDoctrine()->getManager();
        $repository_user = $this->getDoctrine()->getRepository(AppUser::class);
        $role = $repository_role->findOneBy(['role' => $_POST['role']]);
        $user = $repository_user->findOneBy(['id' => $_POST['id']]);
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
        $user = $repository_user->findOneBy(['username' => $_POST['email']]);
        if ($user) {
            return new Response('false');
        } else {
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
        $repository_log = $this->getDoctrine()->getRepository(LogAdminContent::class);

        $array['section'] = $repository_section->findBy(['archived_date'=>null]);
        $array['users'] = $repository->findBy(['deactivated'=>false]);
        if (isset($_GET['nouveau-audit'])) {
            $array['new_audit'] = true;
        }
        $array['log'] = $repository_log->findAll();

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
        $repository_log_action = $this->getDoctrine()->getRepository(LogAction::class);

        $action = $repository_log_action->findOneBy(['action'=>'Créer']);
        $date = new \DateTime(date('Y-m-d H:i:s'));
        $section = $repository_section->findOneBy(['id' => $_POST['id']]);
        $subsection_name = htmlspecialchars($_POST['subsection']);
        $subsection = new AuditSubSection($subsection_name, $section, $date);
        $entityManager->persist($subsection);
        $log = new LogAdminContent($this->getUser(), $action, 'Sous-section', $date, $subsection->getSubsection());
        $entityManager->persist($log);
        $entityManager->flush();
        $array['ss'] = $subsection;
        return $this->render('administration/newsubsection.html.twig', $array);
    }

    /**
     * Méthode qui gère la partie administration du contenu des audits
     *
     * @Route("/administration/contenu-audits/ajouter-section", name="admin_audits_content_addsection")
     */
    public function newSection()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_log_action = $this->getDoctrine()->getRepository(LogAction::class);
        $action = $repository_log_action->findOneBy(['action'=>'Créer']);
        $section_name = htmlspecialchars($_POST['section']);
        $date = new \DateTime(date('Y-m-d H:i:s'));
        $section = new AuditSection($section_name, $date);
        $log = new LogAdminContent($this->getUser(), $action, 'Section', $date, $section->getSection());
        $entityManager->persist($log);
        $entityManager->persist($section);
        $entityManager->flush();
        $array['s'] = $section;
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
        $repository_log_action = $this->getDoctrine()->getRepository(LogAction::class);
        $action = $repository_log_action->findOneBy(['action'=>'Créer']);
        $date = new \DateTime(date('Y-m-d H:i:s'));

        $type = $repository_type->findOneBy(['type' => $_POST['type']]);
        $subsection = $repository_subsection->findOneBy(['id' => $_POST['sub']]);
        $test = new AuditTests($_POST['data'], '1', $subsection, $type, $date);
        $entityManager->persist($test);
        $log = new LogAdminContent($this->getUser(), $action, 'Test', $date, $test->getTest());
        $entityManager->persist($log);
        $entityManager->flush();
        $template['t'] = $test;

        return $this->render('administration/newtest.html.twig', $template);
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
        $repository_status = $this->getDoctrine()->getRepository(Status::class);
        $repository_log_action = $this->getDoctrine()->getRepository(LogAction::class);
        $action = $repository_log_action->findOneBy(['action'=>'Créer']);


        $date = new \DateTime(date('Y-m-d H:i:s'));
        $status = $repository_status->findOneBy(['status' => 'fail']);
        $test = $repository_test->findOneBy(['id' => $_POST['id']]);
        $select = new TestSelections($_POST['select'], $test, $status, $date);
        $log = new LogAdminContent($this->getUser(), $action, 'Sélection', $date, $select->getSelection());
        $entityManager->persist($log);
        $entityManager->persist($select);
        $entityManager->flush();
        $template['selection'] = $select;
        return $this->render('administration/newselection.html.twig', $template);
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
        $repository_log_action = $this->getDoctrine()->getRepository(LogAction::class);
        $action = $repository_log_action->findOneBy(['action'=>'Créer']);


        $test = $repository_test->findOneBy(['id' => $_POST['id']]);
        $child = new AuditTests($_POST['child'], '1', $test->getSusbection(), $test->getType(), $date);
        $child->setParent($test);
        $entityManager->persist($child);
        $log = new LogAdminContent($this->getUser(), $action, 'Enfant', $date, $child->getTest());
        $entityManager->persist($log);
        $entityManager->flush();
        $template['test'] = $child;
        return $this->render('administration/newchild.html.twig', $template);
    }

    /**
     * Méthode qui gère la partie administration du contenu des audits
     *
     * @Route("/administration/contenu-audits/ajouter-solution", name="admin_audits_content_newsol")
     */
    public function newSolution()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_test = $this->getDoctrine()->getRepository(AuditTests::class);
        $repository_log_action = $this->getDoctrine()->getRepository(LogAction::class);
        $action = $repository_log_action->findOneBy(['action'=>'Créer']);
        $date = new \DateTime(date('Y-m-d H:i:s'));

        $test = $repository_test->findOneBy(['id' => $_POST['id']]);
        $solution = new Solution($_POST['sol'], $test, $date);
        $entityManager->persist($solution);
        $log = new LogAdminContent($this->getUser(), $action, 'Solution', $date, $solution->getSolution());
        $entityManager->persist($log);
        $entityManager->flush();
        $template['sol'] = $solution;
        return $this->render('administration/newsolution.html.twig', $template);
    }



    /**
     * Méthode qui gère la mise à jour d'une section
     *
     * @Route("/administration/contenu-audits/modifier/section", name="admin_audits_content_modify_section")
     */
    public function updateSection()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_section = $this->getDoctrine()->getRepository(AuditSection::class);
        $repository_test = $this->getDoctrine()->getRepository(AuditTests::class);
        $repository_log_action = $this->getDoctrine()->getRepository(LogAction::class);
        $action = $repository_log_action->findOneBy(['action'=>'Modifier']);
        $date = new \DateTime(date('Y-m-d H:i:s'));
        $section = $repository_section->findOneBy(['id' => $_POST['id']]);
        //$test = $repository_test->findBy(['susbection'=>$sub, 'date_archive'=>null]);
        $section->setArchivedDate($date);
        $entityManager->persist($section);
        $updatedSection = new AuditSection($_POST['data'], $date);
        $entityManager->persist($updatedSection);
        if($section->getAuditSubSections()){
            foreach ($section->getAuditSubSections() as $key => $value) {
                if ($value->getDateArchive() == null) {
                    $value->setDateArchive($date);
                    $entityManager->persist($value);
                    $newSub = new AuditSubSection($value->getSubsection(), $updatedSection, $date);
                    $entityManager->persist($newSub);
                    $sub[] = $newSub;
                    if($value->getAuditTests()){
                        foreach ($value->getAuditTests() as $ke => $val){
                            if($val->getDateArchive() == null){
                                $val->setDateArchive($date);
                                $entityManager->persist($val);
                                $newTest = new AuditTests($val->getTest(), $val->getPriority(), $newSub, $val->getType(), $date);
                                $entityManager->persist($newTest);
                                $tests[] = $newTest;
                                $child = $repository_test->findBy(['parent' => $val, 'date_archive'=>null]);
                                if ($child) {
                                    foreach ($child as $k => $v) {
                                        if($v->getDateArchive() == null){
                                            $v->setDateArchive($date);
                                            $entityManager->persist($v);
                                            $newChild = new AuditTests($v->getTest(), $v->getPriority(), $newSub, $v->getType(), $date);
                                            $newChild->setParent($newTest);
                                            $entityManager->persist($newChild);
                                            $childs[] = $newChild;
                                        }
                                    }
                                }
                                if ($val->getSolutions()) {
                                    foreach ($val->getSolutions() as $k => $v) {
                                        if ($v->getDateArchive() == null) {
                                            $v->setDateArchive($date);
                                            $newSolution = new Solution($v->getSolution(), $newTest, $date);
                                            $entityManager->persist($v);
                                            $entityManager->persist($newSolution);
                                            $solutions[] = $newSolution;
                                        }
                                    }
                                }
                                /**
                                 * Si le test est sélection, archive les choix multiples
                                 */
                                if ($val->getType()->getType() == 'Selection' && $val->getSelections()) {
                                    foreach ($val->getSelections() as $k => $v) {
                                        if ($v->getDateArchive() == null) {
                                            $v->setDateArchive($date);
                                            $newSelection = new TestSelections($v->getSelection(), $newTest, $v->getStatus(), $date);
                                            $entityManager->persist($v);
                                            $entityManager->persist($newSelection);
                                            $selections[] = $newSelection;
                                        }
                                    }
                                }
                                /**
                                 * Archive et recopie les liens avec les questions pré audit du test
                                 */
                                if ($val->getLinkSelectInfras()) {
                                    foreach ($val->getLinkSelectInfras() as $k => $v) {
                                        if ($v->getDateArchive() == null) {
                                            $v->setDateArchive($date);
                                            $newLink = new LinkSelectInfra($v->getSelection(), $v->getAction(), $date, $newTest);
                                            $entityManager->persist($v);
                                            $entityManager->persist($newLink);
                                        }
                                    }
                                }
                                if ($val->getLinkTestsInfras()) {
                                    foreach ($val->getLinkTestsInfras() as $k => $v) {
                                        if ($v->getDateArchive() == null) {
                                            $v->setDateArchive($date);
                                            $newLink = new LinkTestsInfra($v->getInfra(), $newTest, $v->getAction(), $date);
                                            $entityManager->persist($v);
                                            $entityManager->persist($newLink);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $log = new LogAdminContent($this->getUser(), $action, 'Section', $date, $section->getSection());
        $entityManager->persist($log);
        $entityManager->flush();
        $template['s'] = $updatedSection;
        if (isset($sub)) {
            $template['subSections'] = $sub;
        }
        if (isset($tests)) {
            $template['tests'] = $tests;
        }
        if (isset($solutions)) {
            $template['solutions'] = $solutions;
        }
        if (isset($childs)) {
            $template['childs'] = $childs;
        }
        if (isset($selections)) {
            $template['selections'] = $selections;
        }
        return $this->render('administration/newsection.html.twig',$template);
    }

    /**
     * Méthode qui gère la mise à jour d'une sous-section
     *
     * @Route("/administration/contenu-audits/modifier/sous-section", name="admin_audits_content_modify_sub")
     */
    public function updateSubSection()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_sub = $this->getDoctrine()->getRepository(AuditSubSection::class);
        $repository_test = $this->getDoctrine()->getRepository(AuditTests::class);
        $repository_log_action = $this->getDoctrine()->getRepository(LogAction::class);
        $action = $repository_log_action->findOneBy(['action'=>'Modifier']);
        $date = new \DateTime(date('Y-m-d H:i:s'));
        $sub = $repository_sub->findOneBy(['id' => $_POST['id']]);
        $test = $repository_test->findBy(['susbection'=>$sub, 'date_archive'=>null]);
        $sub->setDateArchive($date);
        $entityManager->persist($sub);
        $updatedSubSection = new AuditSubSection($_POST['data'], $sub->getSection(), $date);
        $entityManager->persist($updatedSubSection);
        if($test){
            foreach ($test as $key => $value) {
                if ($value->getDateArchive() == null) {
                    $value->setDateArchive($date);
                    $entityManager->persist($value);
                    $newTest = new AuditTests($value->getTest(), $value->getPriority(), $updatedSubSection, $value->getType(), $date);
                    $entityManager->persist($newTest);
                    $tests[] = $newTest;

                }
                $child = $repository_test->findBy(['parent' => $value, 'date_archive'=>null]);
                if ($child) {
                    foreach ($child as $k => $v) {
                        if($v->getDateArchive() == null){
                            $v->setDateArchive($date);
                            $entityManager->persist($v);
                            $newChild = new AuditTests($v->getTest(), $v->getPriority(), $updatedSubSection, $v->getType(), $date);
                            $newChild->setParent($newTest);
                            $entityManager->persist($newChild);
                            $childs[] = $newChild;
                        }
                    }
                }
                if ($value->getSolutions()) {
                    foreach ($value->getSolutions() as $k => $v) {
                        if ($v->getDateArchive() == null) {
                            $v->setDateArchive($date);
                            $newSolution = new Solution($v->getSolution(), $newTest, $date);
                            $entityManager->persist($v);
                            $entityManager->persist($newSolution);
                            $solutions[] = $newSolution;
                        }
                    }
                }
                /**
                 * Si le test est sélection, archive les choix multiples
                 */
                if ($value->getType()->getType() == 'Selection' && $value->getSelections()) {
                    foreach ($value->getSelections() as $k => $v) {
                        if ($v->getDateArchive() == null) {
                            $v->setDateArchive($date);
                            $newSelection = new TestSelections($v->getSelection(), $newTest, $v->getStatus(), $date);
                            $entityManager->persist($v);
                            $entityManager->persist($newSelection);
                            $selections[] = $newSelection;
                        }
                    }
                }
                /**
                 * Archive et recopie les liens avec les questions pré audit du test
                 */
                if ($value->getLinkSelectInfras()) {
                    foreach ($value->getLinkSelectInfras() as $k => $v) {
                        if ($v->getDateArchive() == null) {
                            $v->setDateArchive($date);
                            $newLink = new LinkSelectInfra($v->getSelection(), $v->getAction(), $date, $newTest);
                            $entityManager->persist($v);
                            $entityManager->persist($newLink);
                        }
                    }
                }
                if ($value->getLinkTestsInfras()) {
                    foreach ($value->getLinkTestsInfras() as $k => $v) {
                        if ($v->getDateArchive() == null) {
                            $v->setDateArchive($date);
                            $newLink = new LinkTestsInfra($v->getInfra(), $newTest, $v->getAction(), $date);
                            $entityManager->persist($v);
                            $entityManager->persist($newLink);
                        }
                    }
                }
            }
        }
        $log = new LogAdminContent($this->getUser(), $action, 'Sous-section', $date, $sub->getSubsection());
        $entityManager->persist($log);
        $entityManager->flush();
        $template['ss'] = $updatedSubSection;
        if (isset($tests)) {
            $template['tests'] = $tests;
        }
        if (isset($solutions)) {
            $template['solutions'] = $solutions;
        }
        if (isset($childs)) {
            $template['childs'] = $childs;
        }
        if (isset($selections)) {
            $template['selections'] = $selections;
        }
        return $this->render('administration/newsubsection.html.twig',$template);

    }

    /**
     * Méthode qui gère la mise à jour d'un test parent
     *
     * @Route("/administration/contenu-audits/modifier/test", name="admin_audits_content_modify_selection_test", methods="POST")
     */
    public function updateTest()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_test = $this->getDoctrine()->getRepository(AuditTests::class);
        $repository_log_action = $this->getDoctrine()->getRepository(LogAction::class);
        $action = $repository_log_action->findOneBy(['action'=>'Modifier']);

        $date = new \DateTime(date('Y-m-d H:i:s'));
        $test = $repository_test->findOneBy(['id' => $_POST['id']]);
        $child = $repository_test->findBy(['parent' => $test]);
        $test->setDateArchive($date);
        $updatedTest = new AuditTests($_POST['data'], $test->getPriority(), $test->getSusbection(), $test->getType(), $date);
        $entityManager->persist($updatedTest);
        /**
         * Archive et recopie les enfants si il y'en a
         */
        if ($child) {
            foreach ($child as $key => $value) {
                if($value->getDateArchive() == null){
                    $value->setDateArchive($date);
                    $newChild = new AuditTests($value->getTest(), $value->getPriority(), $value->getSusbection(), $value->getType(), $date);
                    $newChild->setParent($updatedTest);
                    $entityManager->persist($value);
                    $entityManager->persist($newChild);
                    $childs[] = $newChild;
                }
            }
        }
        /**
         * Archive et recopie les solutions
         */
        if ($test->getSolutions()) {
            foreach ($test->getSolutions() as $key => $value) {
                if ($value->getDateArchive() == null) {
                    $value->setDateArchive($date);
                    $newSolution = new Solution($value->getSolution(), $updatedTest, $date);
                    $entityManager->persist($value);
                    $entityManager->persist($newSolution);
                }
            }
        }
        /**
         * Si le test est sélection, archive les choix multiples
         */
        if ($test->getType()->getType() == 'Selection' && $test->getSelections()) {
            foreach ($test->getSelections() as $key => $value) {
                if ($value->getDateArchive() == null) {
                    $value->setDateArchive($date);
                    $newSelection = new TestSelections($value->getSelection(), $updatedTest, $value->getStatus(), $date);
                    $entityManager->persist($value);
                    $entityManager->persist($newSelection);
                }
            }
        }
        /**
         * Archive et recopie les liens avec les questions pré audit du test
         */
        if ($test->getLinkSelectInfras()) {
            foreach ($test->getLinkSelectInfras() as $key => $value) {
                if ($value->getDateArchive() == null) {
                    $value->setDateArchive($date);
                    $newLink = new LinkSelectInfra($value->getSelection(), $value->getAction(), $date, $updatedTest);
                    $entityManager->persist($value);
                    $entityManager->persist($newLink);
                }
            }
        }
        if ($test->getLinkTestsInfras()) {
            foreach ($test->getLinkTestsInfras() as $key => $value) {
                if ($value->getDateArchive() == null) {
                    $value->setDateArchive($date);
                    $newLink = new LinkTestsInfra($value->getInfra(), $updatedTest, $value->getAction(), $date);
                    $entityManager->persist($value);
                    $entityManager->persist($newLink);
                }
            }
        }
        $log = new LogAdminContent($this->getUser(), $action, 'Test', $date, $test->getTest());
        $entityManager->persist($log);
        $entityManager->flush();
        $template['t'] = $updatedTest;
        if (isset($childs)) {
            $template['childs'] = $childs;
        }
        return $this->render('administration/newtest.html.twig', $template);
    }

    /**
     * Méthode qui gère la mise à jour du statut d'une sélection à choix multiple
     *
     * @Route("/administration/contenu-audits/modifier/statut-selection", name="admin_audits_content_modify_selection_status", methods="POST")
     */
    public function updateSelectionStatus()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_selection = $this->getDoctrine()->getRepository(TestSelections::class);
        $repository_status = $this->getDoctrine()->getRepository(Status::class);
        $repository_log_action = $this->getDoctrine()->getRepository(LogAction::class);
        $action = $repository_log_action->findOneBy(['action'=>'Modifier']);


        $date = new \DateTime(date('Y-m-d H:i:s'));
        $status = $repository_status->findOneBy(['status' => $_POST['status']]);
        $selection = $repository_selection->findOneBy(['id' => $_POST['id']]);
        $selection->setStatus($status);
        //$newSelection = new TestSelections($selection->getSelection(), $selection->getTest(), $status, $date);
        $entityManager->persist($selection);
        //$entityManager->persist($newSelection);
        $log = new LogAdminContent($this->getUser(), $action, 'Selection', $date, $selection->getSelection());
        $entityManager->persist($log);

        $entityManager->flush();
        $template['selection'] = $selection;
        return $this->render('administration/newselection.html.twig', $template);
    }

    /**
     * Méthode qui gère la partie administration du contenu des audits
     *
     * @Route("/administration/contenu-audits/modifier/enfant", name="admin_audits_content_child")
     */
    public function updateChild()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_test = $this->getDoctrine()->getRepository(AuditTests::class);
        $repository_log_action = $this->getDoctrine()->getRepository(LogAction::class);
        $action = $repository_log_action->findOneBy(['action'=>'Modifier']);

        $date = new \DateTime(date('Y-m-d H:i:s'));
        $test = $repository_test->findOneBy(['id' => $_POST['child']]);
        $test->setDateArchive($date);
        $updatedChild = new AuditTests($_POST['data'], $test->getPriority(), $test->getSusbection(), $test->getType(), $date);
        $updatedChild->setParent($test->getParent());
        $entityManager->persist($updatedChild);
        $entityManager->persist($test);
        $log = new LogAdminContent($this->getUser(), $action, 'Enfant', $date, $test->getTest());
        $entityManager->persist($log);
        $entityManager->flush();
        $template['test'] = $updatedChild;
        return $this->render('administration/updatechild.html.twig', $template);
    }

    /**
     * Méthode qui gère la partie administration du contenu des audits
     *
     * @Route("/administration/contenu-audits/modifier/priorité", name="admin_audits_content_modify_prio", methods="POST", options={"utf8": true})
     */
    public function updatePrio()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_test = $this->getDoctrine()->getRepository(AuditTests::class);
        $test = $repository_test->findOneBy(['id' => $_POST['id']]);
        $test->setPriority($_POST['prio']);
        $entityManager->persist($test);
        $entityManager->flush();
        return new Response('ok');
    }

    /**
     * Méthode qui met à jour une solution par archivage et re creation
     * @Route("/administration/contenu-audits/modifier/solution", name="admin_audits_content_modify_sol", methods="POST")
     */
    public function updateSolution()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_solution = $this->getDoctrine()->getRepository(Solution::class);
        $repository_log_action = $this->getDoctrine()->getRepository(LogAction::class);
        $action = $repository_log_action->findOneBy(['action'=>'Modifier']);

        $solution = $repository_solution->findOneBy(['id' => $_POST['id']]);
        $date = new \DateTime(date('Y-m-d H:i:s'));
        $solution->setDateArchive($date);
        $entityManager->persist($solution);
        $newSolution = new Solution($_POST['data'], $solution->getTest(), $date);
        $entityManager->persist($newSolution);
        $log = new LogAdminContent($this->getUser(), $action, 'Solution', $date, $solution->getSolution());
        $entityManager->persist($log);

        $entityManager->flush();
        $template['sol'] = $newSolution;
        return $this->render('administration/newsolution.html.twig', $template);
    }

    /**
     * Méthode qui met à jour une solution par archivage et re creation
     * @Route("/administration/contenu-audits/modifier/selection", name="admin_audits_content_modify_selection", methods="POST")
     */
    public function updateSelection()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_selection = $this->getDoctrine()->getRepository(TestSelections::class);
        $selection = $repository_selection->findOneBy(['id' => $_POST['id']]);
        $repository_log_action = $this->getDoctrine()->getRepository(LogAction::class);
        $action = $repository_log_action->findOneBy(['action'=>'Modifier']);

        $date = new \DateTime(date('Y-m-d H:i:s'));
        $selection->setDateArchive($date);
        $entityManager->persist($selection);
        $newSelection = new TestSelections($_POST['data'], $selection->getTest(), $selection->getStatus(), $date);
        $entityManager->persist($newSelection);
        $log = new LogAdminContent($this->getUser(), $action, 'Selection', $date, $selection->getSelection());
        $entityManager->persist($log);
        $entityManager->flush();
        $template['selection'] = $newSelection;
        return $this->render('administration/newselection.html.twig', $template);
    }


    /**
     * Méthode qui gère la partie administration du contenu des audits
     *
     * @Route("/administration/contenu-audits/supprimer", name="admin_audits_content_delete", methods="POST")
     */
    public function deleteElement()
    {
        switch ($_POST['el']) {
            case 'child':
                if ($this->deleteChild()) {
                    return new Response('Element supprimé!');
                }
                break;
            case 'solution':
                if ($this->deleteSolution()) {
                    return new Response('Element supprimé!');
                }
                break;
            case 'selection':
                if ($this->deleteSelection()) {
                    return new Response('Element supprimé!');
                };
                break;
            case'test':
                if ($this->deleteTest()) {
                    return new Response('Element supprimé!');
                }
                break;
            case 'sub':
                if ($this->deleteSub()) {
                    return new Response('Element supprimé!');
                }
                break;
        }
    }

    /**
     * Méthode qui archive une sous-section
     */
    public function deleteSub()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_sub = $this->getDoctrine()->getRepository(AuditSubSection::class);
        $repository_test = $this->getDoctrine()->getRepository(AuditTests::class);
        $repository_log_action = $this->getDoctrine()->getRepository(LogAction::class);
        $action = $repository_log_action->findOneBy(['action'=>'Supprimer']);

        $date = new \DateTime(date('Y-m-d H:i:s'));
        $sub = $repository_sub->findOneBy(['id' => $_POST['id']]);
        $test = $repository_test->findBy(['susbection'=>$sub, 'date_archive'=>null]);
        $sub->setDateArchive($date);
        $entityManager->persist($sub);
        if($test) {
            foreach ($test as $key => $value) {
                if ($value->getDateArchive() == null) {
                    $value->setDateArchive($date);
                    $entityManager->persist($value);
                }
                $child = $repository_test->findBy(['parent' => $value, 'date_archive' => null]);
                if ($child) {
                    foreach ($child as $k => $v) {
                        if ($v->getDateArchive() == null) {
                            $v->setDateArchive($date);
                            $entityManager->persist($v);
                        }
                    }
                }
                if ($value->getSolutions()) {
                    foreach ($value->getSolutions() as $k => $v) {
                        if ($v->getDateArchive() == null) {
                            $v->setDateArchive($date);
                            $entityManager->persist($v);
                        }
                    }
                }
                /**
                 * Si le test est sélection, archive les choix multiples
                 */
                if ($value->getType()->getType() == 'Selection' && $value->getSelections()) {
                    foreach ($value->getSelections() as $k => $v) {
                        if ($v->getDateArchive() == null) {
                            $v->setDateArchive($date);
                            $entityManager->persist($v);
                        }
                    }
                }
                /**
                 * Archive et recopie les liens avec les questions pré audit du test
                 */
                if ($value->getLinkSelectInfras()) {
                    foreach ($value->getLinkSelectInfras() as $k => $v) {
                        if ($v->getDateArchive() == null) {
                            $v->setDateArchive($date);
                            $entityManager->persist($v);
                        }
                    }
                }
                if ($value->getLinkTestsInfras()) {
                    foreach ($value->getLinkTestsInfras() as $k => $v) {
                        if ($v->getDateArchive() == null) {
                            $v->setDateArchive($date);
                            $entityManager->persist($v);
                        }
                    }
                }
            }
        }
        $log = new LogAdminContent($this->getUser(), $action, 'Sous-section', $date, $sub->getSubsection());
        $entityManager->persist($log);

        $entityManager->flush();
        return true;
    }

    public function deleteChild()
    {
        $repository_test = $this->getDoctrine()->getRepository(AuditTests::class);
        $repository_log_action = $this->getDoctrine()->getRepository(LogAction::class);
        $action = $repository_log_action->findOneBy(['action'=>'Supprimer']);

        $date = new \DateTime(date('d-m-Y H:i:s'));
        $entityManager = $this->getDoctrine()->getManager();
        $test = $repository_test->findOneBy(['id' => $_POST['id']]);
        $test->setDateArchive($date);
        $entityManager->persist($test);
        $log = new LogAdminContent($this->getUser(), $action, 'Enfant', $date, $test->getTest());
        $entityManager->persist($log);

        $entityManager->flush();
        return true;
    }

    public function deleteSolution()
    {
        $repository_solution = $this->getDoctrine()->getRepository(Solution::class);
        $repository_log_action = $this->getDoctrine()->getRepository(LogAction::class);
        $action = $repository_log_action->findOneBy(['action'=>'Supprimer']);

        $date = new \DateTime(date('d-m-Y H:i:s'));
        $entityManager = $this->getDoctrine()->getManager();
        $test = $repository_solution->findOneBy(['id' => $_POST['id']]);
        $test->setDateArchive($date);
        $entityManager->persist($test);
        $log = new LogAdminContent($this->getUser(), $action, 'Solution', $date, $test->getSolution());
        $entityManager->persist($log);

        $entityManager->flush();
        return true;
    }

    public function deleteSelection()
    {
        $repository_selection = $this->getDoctrine()->getRepository(TestSelections::class);
        $date = new \DateTime(date('d-m-Y H:i:s'));
        $repository_log_action = $this->getDoctrine()->getRepository(LogAction::class);
        $action = $repository_log_action->findOneBy(['action'=>'Supprimer']);

        $entityManager = $this->getDoctrine()->getManager();
        $selection = $repository_selection->findOneBy(['id' => $_POST['id']]);
        $selection->setDateArchive($date);
        $entityManager->persist($selection);
        $log = new LogAdminContent($this->getUser(), $action, 'Selection', $date, $selection->getSelection());
        $entityManager->persist($log);

        $entityManager->flush();
        return true;
    }

    public function deleteTest()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_test = $this->getDoctrine()->getRepository(AuditTests::class);
        $repository_log_action = $this->getDoctrine()->getRepository(LogAction::class);
        $action = $repository_log_action->findOneBy(['action'=>'Supprimer']);

        $date = new \DateTime(date('Y-m-d H:i:s'));
        $test = $repository_test->findOneBy(['id' => $_POST['id']]);
        $child = $repository_test->findBy(['parent' => $test]);
        $test->setDateArchive($date);
        /**
         * Archive et recopie les enfants si il y'en a
         */
        if ($child) {
            foreach ($child as $key => $value) {
                $value->setDateArchive($date);
                $entityManager->persist($value);
            }
        }
        /**
         * Archive et recopie les solutions
         */
        if ($test->getSolutions()) {
            foreach ($test->getSolutions() as $key => $value) {
                if ($value->getDateArchive() == null) {
                    $value->setDateArchive($date);
                    $entityManager->persist($value);
                }
            }
        }
        /**
         * Si le test est sélection, archive les choix multiples
         */
        if ($test->getType()->getType() == 'Selection' && $test->getSelections()) {
            foreach ($test->getSelections() as $key => $value) {
                if ($value->getDateArchive() == null) {
                    $value->setDateArchive($date);
                    $entityManager->persist($value);
                }
            }
        }
        /**
         * Archive et recopie les liens avec les questions pré audit du test
         */
        if ($test->getLinkSelectInfras())
            foreach ($test->getLinkSelectInfras() as $key => $value) {
                if ($value->getDateArchive() == null) {
                    $value->setDateArchive($date);
                    $entityManager->persist($value);
                }
            }
        if ($test->getLinkTestsInfras())
            foreach ($test->getLinkTestsInfras() as $key => $value) {
                if ($value->getDateArchive() == null) {
                    $value->setDateArchive($date);
                    $entityManager->persist($value);
                }
            }
        $log = new LogAdminContent($this->getUser(), $action, 'Test', $date, $test->getTest());
        $entityManager->persist($log);

        $entityManager->flush();
        return true;
    }

    /**
     * Méthode de création d'un snapshot
     * @Route("/administration/contenu-audits/nouveau-snapshot", name="admin_audits_content_new_snapshot", methods="POST")
     */
    public function newSnapshot()
    {
        $repository_test = $this->getDoctrine()->getRepository(AuditTests::class);
        $tests = $repository_test->findBy(['date_archive'=>null]);
        $entityManager = $this->getDoctrine()->getManager();
        $date = new \DateTime(date('Y-m-d H:i:s'));
        $name = htmlentities($_POST['name']);
        $snap = new Snapshot($name,$date);
        $entityManager->persist($snap);
        foreach ($tests as $key => $value){
            $link = new LinkSnapTest($snap,$value);
            $entityManager->persist($link);
        }
        $entityManager->flush();
    }

    /**
     * ADMINISTRATION CLIENTS
     */

    /**
     * Méthode qui gère l'affichage de l'admin client
     *
     * @Route("/administration/clients", name="admin_customer")
     */
    public function adminCustomer()
    {
        $repository = $this->getDoctrine()->getRepository(AppUser::class);
        $repository_customer = $this->getDoctrine()->getRepository(IntCustomer::class);
        $repository_log = $this->getDoctrine()->getRepository(LogAdminCustomer::class);

        $array['customers'] = $repository_customer->findAll();
        $array['users'] = $repository->findBy(['deactivated'=>false]);
        $array['log'] = $repository_log->findAll();
        if (isset($_GET['nouveau-audit'])) {
            $array['new_audit'] = true;
        }
        return $this->render('administration/customer/customer.html.twig', $array);
    }

}