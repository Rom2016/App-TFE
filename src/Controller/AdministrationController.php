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
use App\Entity\LinkSelectInfra;
use App\Entity\LinkTestsInfra;
use App\Entity\LogAction;
use App\Entity\LogAdminUser;
use App\Entity\Roles;
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
        if (isset($_POST['role-special'])) {
            $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        }
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
        $section = $repository_section->findOneBy(['id' => $_POST['id']]);
        $subsection_name = htmlspecialchars($_POST['subsection']);
        $subsection = new AuditSubSection($subsection_name, $section, $date);
        $entityManager->persist($subsection);
        $entityManager->flush();
        $array['subsection'] = $subsection_name;
        $array['id'] = $subsection->getId();
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

        $type = $repository_type->findOneBy(['type' => $_POST['type']]);
        $subsection = $repository_subsection->findOneBy(['id' => $_POST['sub']]);
        $test = new AuditTests($_POST['data'], '1', $subsection, $type, $date);
        $entityManager->persist($test);
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

        $date = new \DateTime(date('Y-m-d H:i:s'));
        $status = $repository_status->findOneBy(['status' => 'fail']);
        $test = $repository_test->findOneBy(['id' => $_POST['id']]);
        $select = new TestSelections($_POST['select'], $test, $status, $date);
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

        $test = $repository_test->findOneBy(['id' => $_POST['id']]);
        $child = new AuditTests($_POST['child'], '1', $test->getSusbection(), $test->getType(), $date);
        $child->setParent($test);
        $entityManager->persist($child);
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
        $date = new \DateTime(date('Y-m-d H:i:s'));

        $test = $repository_test->findOneBy(['id' => $_POST['id']]);
        $solution = new Solution($_POST['sol'], $test, $date);
        $entityManager->persist($solution);
        $entityManager->flush();
        $template['sol'] = $solution;
        return $this->render('administration/newsolution.html.twig', $template);
    }

    /**
     * Méthode qui gère la partie administration du contenu des audits
     *
     * @Route("/administration/contenu-audits/modifier", name="admin_audits_content_modify")
     */
    public function modifyAuditContent()
    {
        switch ($_POST['rq']) {
            case 'section':
                return new JsonResponse($this->updateSection());
                break;
            case 'subsection':
                $this->updateSection();
                break;
            case 'child' :
                return new Response($this->updateChild());
                break;
        }
    }

    public function updateSection()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_section = $this->getDoctrine()->getRepository(AuditSection::class);
        $repository_sub = $this->getDoctrine()->getRepository(AuditSubSection::class);
        $date = new \DateTime(date('Y-m-d H:i:s'));
        $section = $repository_section->findOneBy(['id' => $_POST['section']]);
        $subsection = $repository_sub->findBy(['section' => $section]);
        $section->setArchivedDate($date);
        $entityManager->persist($section);
        $updatedSection = new AuditSection($_POST['data'], $date);
        $entityManager->persist($updatedSection);
        foreach ($subsection as $key => $value) {
            if (!$value->getDateArchive()) {
                $subsection = new AuditSubSection($value->getSubsection(), $updatedSection, $date);
                $value->setDateArchive($date);
                $entityManager->persist($value);
                $entityManager->persist($subsection);
            }
        }
        $entityManager->flush();
        $response['html'] = '<span id="section' . $updatedSection->getId() . '" class="editable suggestion section">' . $updatedSection->getSection() . '</span>';
        $response['id'] = $updatedSection->getId();
        return $response;

    }

    /**
     * Méthode qui gère la mise à jour d'une sous-section
     *
     * @Route("/administration/contenu-audits/modifier", name="admin_audits_content_modify_sub")
     */
    public function updateSubSection()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_sub = $this->getDoctrine()->getRepository(AuditSubSection::class);
        $repository_test = $this->getDoctrine()->getRepository(AuditTests::class);
        $date = new \DateTime(date('Y-m-d H:i:s'));
        $sub = $repository_sub->findOneBy(['id' => $_POST['id']]);
        $test = $repository_test->findBy(['subsection'=>$sub]);
        $sub->setArchivedDate($date);
        $entityManager->persist($sub);
        $updatedSection = new AuditSubSection($_POST['data'], $sub->setSection(), $date);
        $entityManager->persist($updatedSection);
        if($test){
            foreach ($test as $key => $value) {
                if ($value->getDateArchive() == null) {
                    $value->setDateArchive($date);
                    $entityManager->persist($value);
                }
                $child = $repository_test->findBy(['parent' => $value]);
                if ($child) {
                    foreach ($child as $k => $v) {
                        if($v->getDateArchive() == null){
                            $value->setDateArchive($date);
                            $entityManager->persist($value);
                            $entityManager->persist($newChild);
                            $childs[] = $newChild;
                        }
                    }
                }

            }
        }

        $entityManager->flush();
        $response['html'] = '<span id="section' . $updatedSection->getId() . '" class="editable suggestion section">' . $updatedSection->getSection() . '</span>';
        $response['id'] = $updatedSection->getId();
        return $response;

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
        if ($test->getLinkSelectInfras())
            foreach ($test->getLinkSelectInfras() as $key => $value) {
                if ($value->getDateArchive() == null) {
                    $value->setDateArchive($date);
                    $newLink = new LinkSelectInfra($value->getSelection(), $value->getAction(), $date, $updatedTest);
                    $entityManager->persist($value);
                    $entityManager->persist($newLink);
                }
            }
        if ($test->getLinkTestsInfras())
            foreach ($test->getLinkTestsInfras() as $key => $value) {
                if ($value->getDateArchive() == null) {
                    $value->setDateArchive($date);
                    $newLink = new LinkTestsInfra($value->getInfra(), $updatedTest, $value->getAction(), $date);
                    $entityManager->persist($value);
                    $entityManager->persist($newLink);
                }
            }
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

        $date = new \DateTime(date('Y-m-d H:i:s'));
        $status = $repository_status->findOneBy(['status' => $_POST['status']]);
        $selection = $repository_selection->findOneBy(['id' => $_POST['id']]);
        $selection->setStatus($status);
        //$newSelection = new TestSelections($selection->getSelection(), $selection->getTest(), $status, $date);
        $entityManager->persist($selection);
        //$entityManager->persist($newSelection);
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

        $date = new \DateTime(date('Y-m-d H:i:s'));
        $test = $repository_test->findOneBy(['id' => $_POST['child']]);
        $test->setDateArchive($date);
        $updatedChild = new AuditTests($_POST['data'], $test->getPriority(), $test->getSusbection(), $test->getType(), $date);
        $updatedChild->setParent($test->getParent());
        $entityManager->persist($updatedChild);
        $entityManager->persist($test);
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
        $solution = $repository_solution->findOneBy(['id' => $_POST['id']]);
        $date = new \DateTime(date('Y-m-d H:i:s'));
        $solution->setDateArchive($date);
        $entityManager->persist($solution);
        $newSolution = new Solution($_POST['data'], $solution->getTest(), $date);
        $entityManager->persist($newSolution);
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
        $date = new \DateTime(date('Y-m-d H:i:s'));
        $selection->setDateArchive($date);
        $entityManager->persist($selection);
        $newSelection = new TestSelections($_POST['data'], $selection->getTest(), $selection->getStatus(), $date);
        $entityManager->persist($newSelection);
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
        }
    }

    public function deleteChild()
    {
        $repository_test = $this->getDoctrine()->getRepository(AuditTests::class);
        $date = new \DateTime(date('d-m-Y H:i:s'));
        $entityManager = $this->getDoctrine()->getManager();
        $test = $repository_test->findOneBy(['id' => $_POST['id']]);
        $test->setDateArchive($date);
        $entityManager->persist($test);
        $entityManager->flush();
        return true;
    }

    public function deleteSolution()
    {
        $repository_solution = $this->getDoctrine()->getRepository(Solution::class);
        $date = new \DateTime(date('d-m-Y H:i:s'));
        $entityManager = $this->getDoctrine()->getManager();
        $test = $repository_solution->findOneBy(['id' => $_POST['id']]);
        $test->setDateArchive($date);
        $entityManager->persist($test);
        $entityManager->flush();
        return true;
    }

    public function deleteSelection()
    {
        $repository_selection = $this->getDoctrine()->getRepository(TestSelections::class);
        $date = new \DateTime(date('d-m-Y H:i:s'));
        $entityManager = $this->getDoctrine()->getManager();
        $selection = $repository_selection->findOneBy(['id' => $_POST['id']]);
        $selection->setDateArchive($date);
        $entityManager->persist($selection);
        $entityManager->flush();
        return true;
    }

    public function deleteTest()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_test = $this->getDoctrine()->getRepository(AuditTests::class);

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
        $entityManager->flush();
        return true;
    }



}