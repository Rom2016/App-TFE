<?php
/**
 * Created by PhpStorm.
 * User: Romain
 * Date: 26/04/2018
 * Time: 13:55
 */

namespace App\Controller;

use App\Entity\AppUser;
use App\Entity\AuditCreator;
use App\Entity\AuditPermission;
use App\Entity\AuditResults;
use App\Entity\AuditSection;
use App\Entity\AuditTests;
use App\Entity\AuditTestsInfra;
use App\Entity\LogAction;
use App\Entity\LogAuditPerm;
use App\Entity\LogAudits;
use App\Entity\SectionPoints;
use App\Entity\Solution;
use App\Entity\Status;
use App\Entity\TestSelections;
use App\Entity\TestStatus;
use App\Entity\UserPermission;
use Symfony\Component\Routing\RequestContext;

use App\Entity\InfraCustomer;
use App\Entity\InfraSelection;
use App\Entity\IntAudit;
use App\Entity\IntCustomer;
use App\Entity\LinkTestsInfra;
use App\Entity\TestType;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Dompdf\Dompdf;
use Dompdf\Options;



class AuditController extends AbstractController
{
    public function cleanInput($input) {

        $search = array(
            '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
            '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
            '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
            '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
        );

        $output = preg_replace($search, '', $input);
        return $output;
    }
    /**
     * Méthode qui gère toute la partie Administration Audit
     *
     * @Route("/audit/créer", name="create_audit", options={"utf8": true}, methods="POST")
     */
    public function createAudit()
    {
        $this->denyAccessUnlessGranted('ROLE_CREATOR');
        $repository_user = $this->getDoctrine()->getRepository(AppUser::class);
        $repository_action = $this->getDoctrine()->getRepository(LogAction::class);
        $action = $repository_action->findOneBy(['action' => 'Créer']);
        $entityManager = $this->getDoctrine()->getManager();
        $date = new \DateTime(date('Y-m-d H:i:s'));
        $audit_name = $this->cleanInput($_POST['audit-name']);
        $first_name = $this->cleanInput($_POST['first-name-audit']);
        $second_name = $this->cleanInput($_POST['second-name-audit']);
        $email = $this->cleanInput($_POST['email-audit']);
        $customer = new IntCustomer($_POST['customer'], $first_name, $second_name, $email );
        $entityManager->persist($customer);
        $audit = new IntAudit($customer, $date, $audit_name);
        $entityManager->persist($audit);

        $creator = new AuditCreator($this->getUser(), $audit);
        $entityManager->persist($creator);
        if(isset($_POST['creator'])){
            foreach ($_POST['creator'] as $key => $value){
                $user = $repository_user->findOneBy(['id'=>$key]);
                $creator = new AuditCreator($user, $audit);
                $entityManager->persist($creator);
            }
        }
        $route = '..'.$_GET['route'].'?nouveau-audit=true';
        $log = new LogAudits($this->getUser(), $audit, $date, $action);
        $entityManager->persist($log);
        $entityManager->flush();

        return $this->redirect($route);
    }

    /**
     * Méthode qui gère le chargement du questionnaire pré Audit
     *
     * @Route("/audit/préaudit", name="audit_preaudit", options={"utf8": true})
     */
    public function preAudit()
    {

        $repository_audit = $this->getDoctrine()->getRepository(IntAudit::class);
        $repository_infra = $this->getDoctrine()->getRepository(AuditTestsInfra::class);
        $id = $this->cleanInput($_GET['audit']);
        $template['audit'] = $repository_audit->findOneBy(['id'=>$id]);
        $this->denyAccessUnlessGranted('Owner',$template['audit']);
        if($template['audit']->getParent() or $template['audit']->getStarted() == true){
            return $this->render('error/error_404.html.twig');
        }else{
            $template['infra'] = $repository_infra->findBy(array('date_archive' => null));
            $template['audit_id'] = $id;
            return $this->render('audit/preaudit.html.twig', $template);
        }
    }

    /**
     *
     * @Route("/audit/nouveau-audit", name="audit_newaudit")
     */
    public function newAudit()
    {

        $repository_audit = $this->getDoctrine()->getRepository(IntAudit::class);
        $repository_infra = $this->getDoctrine()->getRepository(AuditTestsInfra::class);
        $repository_test = $this->getDoctrine()->getRepository(AuditTests::class);
        $repository_customer_infra = $this->getDoctrine()->getRepository(InfraCustomer::class);
        $repository_section = $this->getDoctrine()->getRepository(AuditSection::class);
        $repository_status = $this->getDoctrine()->getRepository(Status::class);
        $id = $this->cleanInput($_GET['audit']);
        $audit = $repository_audit->findOneBy(['id'=>$id]);
        $this->denyAccessUnlessGranted('Owner',$audit);

        if($audit->getStarted() or $audit->getParent() != null){
            return $this->render('error/error_404.html.twig');
        }else{
            $tests = $repository_test->findBy(['date_archive' => null, 'parent' => null]);
            $entityManager = $this->getDoctrine()->getManager();

            $infra = $repository_infra->findBy(array('date_archive' => null));
            $status = $repository_status->findOneBy(['status' => 'fail']);
            /**
             * Cette partie enregistre les réponses aux questions préaudit
             * Pour chaque test infra
             */
            foreach ($infra as $key => $value){
                /**
                 * Si type question
                 */
                if ($value->getType()->getType() == 'Question') {
                    if (isset($_POST['pre_audit'][$value->getId()])) {
                        $infraCustomer = new InfraCustomer($value, $audit, 'true');
                        $entityManager->persist($infraCustomer);
                    } else {
                        $entityManager->persist(new InfraCustomer($value, $audit, 'false'));
                    }

                } elseif ($value->getType()->getType() == 'Selection') {
                    $infraCustomer = new InfraCustomer($value, $audit, $_POST['pre_audit'][$value->getId()]);
                    $entityManager->persist($infraCustomer);
                } elseif ($value->getType()->getType() == 'Text') {
                    $infraCustomer = new InfraCustomer($value, $audit, $_POST['pre_audit'][$value->getId()]);
                    $entityManager->persist($infraCustomer);
                }
            }
            $entityManager->flush();
            /**
             * Cette partie ajoute ou retire des tests
             */
            foreach ($tests as $key => $value) {
                /**
                 * Pour chaque lien avec un test infra
                 */
                foreach ($value->getLinkTestsInfras() as $k => $v) {
                    $infra = $v->getInfra();
                    /**
                     * Récupère la réponse au test par le pré-audit
                     */
                    $customerInfra = $repository_customer_infra->findOneBy(['infra' => $infra, 'audit' => $audit]);
                    $result = $customerInfra->getResult();
                    if ($infra->getType()->getType() == 'Question') {
                        /**
                         *
                         */
                        if ($v->getAction() && $result == 'false' || !$v->getAction() && $result == 'true') {
                            unset($tests[$key]);
                        }
                    }
                }
                foreach ($value->getLinkSelectInfras() as $k => $v) {
                    $infra = $v->getSelection()->getInfra();
                    $customerInfra = $repository_customer_infra->findOneBy(['infra' => $infra, 'audit' => $audit]);
                    $result = $customerInfra->getResult();
                    if ($v->getSelection()->getSelection() == $result) {
                        if (!$v->getAction()) {
                            unset($tests[$key]);
                        }
                    }
                }
            }
            /**
             * Initialise le questionnaire d'audit avec le jeu de question adapté
             */
            foreach ($tests as $key => $value) {
                $audit_tests = new AuditResults($audit, $value, $status);
                $entityManager->persist($audit_tests);
                $audit_test[] = $audit_tests;
                    $sec = $audit_tests->getTest()->getSusbection()->getSection();
                    $subsection = $audit_tests->getTest()->getSusbection();
                    $section[$sec->getId()] = $sec;
                    $sub[$subsection->getId()] = $subsection;
                $childs = $repository_test->findBy(['parent' => $value]);
                foreach ($childs as $k => $v) {
                    $audit_tests = new AuditResults($audit, $v, $status);
                    $entityManager->persist($audit_tests);
                }
            }
            $audit->setStarted(true);
            $entityManager->flush();
            $template['sections'] = $section;
            $template['subs'] = $sub;
            $template['tests'] = $audit_test;
            $template['audit'] = $_GET['audit'];

            return $this->render('audit/resumeaudit.html.twig', $template);
        }
    }

    /**
     * Méthode qui gère toute la partie Administration Audit
     *
     * @Route("/audit/reprendre-audit", name="audit_resume_audit")
     */
    public function resumeAudit()
    {
        $repository_section = $this->getDoctrine()->getRepository(AuditSection::class);
        $repository_test = $this->getDoctrine()->getRepository(AuditTests::class);
        $repository_audit = $this->getDoctrine()->getRepository(IntAudit::class);
        $repository_audit_results = $this->getDoctrine()->getRepository(AuditResults::class);
        $id = $this->cleanInput($_GET['audit']);
        $audit = $repository_audit->findOneBy(['id'=>$id]);
        $this->denyAccessUnlessGranted('Owner',$audit);
        if($audit->getFinishDate() != null){
            return $this->render('error/error_404.html.twig');
        }
        $audit_results = $repository_audit_results->findBy(['audit'=>$audit]);
        $last_response = $repository_audit_results->findOneBy(['audit'=>$audit,'last_responded'=>true]);

        foreach ($audit_results as $key => $value){
            $sec = $value->getTest()->getSusbection()->getSection();
            $subsection = $value->getTest()->getSusbection();
            $section[$sec->getId()] = $sec;
            $sub[$subsection->getId()] = $subsection;
        }
        $template['subs'] = $sub;
        $template['sections'] = $section;
        $template['tests'] = $audit_results;
        $template['audit'] = $id;
        if($last_response){
            $template['last_response'] = $last_response;
        }
        return $this->render('audit/resumeaudit.html.twig', $template);
    }

    /**
     * Méthode qui gère toute la partie Administration Audit
     *
     * @Route("/audit/statut", name="audit_update_test_statut", methods="POST")
     */
    public function updateStatus()
    {
        $repository_test = $this->getDoctrine()->getRepository(AuditTests::class);
        $repository_result = $this->getDoctrine()->getRepository(AuditResults::class);
        $repository_status = $this->getDoctrine()->getRepository(Status::class);
        $repository_audit = $this->getDoctrine()->getRepository(IntAudit::class);

        $entityManager = $this->getDoctrine()->getManager();
        $audit = $repository_audit->findOneBy(['id'=>$_POST['audit']]);

        $status = $repository_status->findOneBy(['status'=>$_POST['status']]);
        $result = $repository_result->findOneBy(['id'=>$_POST['id']]);
        $childs = $repository_test->findBy(['parent'=>$result->getTest()]);
        $last_response = $repository_result->findOneBy(['audit'=>$audit,'last_responded'=>true]);

        $template['parent'] = $result;
        $current_status = $result->getStatus();
        /**
         * Met déjà à jour avec le nouveau statut
         * Met le champ last response pour connaître le dernier test répondu
         */
        $result->setStatus($status);
        if($last_response){
            $last_response->setLastResponded(null);
            $result->setLastResponded(true);
        }else{
            $result->setLastResponded(true);
        }
        $entityManager->persist($result);
        /**
         * Traitement à faire si le nouveau statut est fail et parent avec enfant
         * Mettre tous les enfants en fail
         */
        if($status->getStatus() =='fail' and $childs){
            foreach ($childs as $key => $value){
                $childs_result = $repository_result->findOneBy(['test'=>$value,'audit'=>$audit]);
                $childs_result->setStatus($status);
                $entityManager->persist($childs_result);
            }
        }
        /**
         * Si le nouveau statut est <> de fail va chercher les enfants si il test parent
         */
        if($current_status->getStatus()=='fail' and $status->getStatus()=='error' and $childs or $current_status->getStatus()=='fail' and $status->getStatus()=='success' and $childs){
            foreach ($childs as $key => $value){
                $childs_result[] = $repository_result->findOneBy(['test'=>$value,'audit'=>$audit]);
            }
            $template['childs'] = $childs_result;
            $entityManager->flush();
            return $this->render('audit/addchild.html.twig',$template);
        }
        $entityManager->flush();
        $array['success'] = 'success';
        return new JsonResponse($array);
    }

    /**
     * Méthode qui gère toute la partie Administration Audit
     *
     * @Route("/audit/selection", name="audit_update_test_selection", methods="POST")
     */
    public function updateSelection()
    {
        if($_POST['rq'] == 'test'){
            $repository_result = $this->getDoctrine()->getRepository(AuditResults::class);
            $repository_selection = $this->getDoctrine()->getRepository(TestSelections::class);
            $entityManager = $this->getDoctrine()->getManager();
            $result = $repository_result->findOneBy(['id'=>$_POST['id']]);
            $selection = $repository_selection->findOneBy(['test'=>$result->getTest(), 'selection'=>$_POST['data']]);
            $result->setStatus($selection->getStatus());
            $result->setSelection($selection);
            $entityManager->persist($result);
            $entityManager->flush();
            return new Response('Succès!');
        }elseif ($_POST['rq'] == 'solution'){
            $repository_result = $this->getDoctrine()->getRepository(AuditResults::class);
            $repository_solution = $this->getDoctrine()->getRepository(Solution::class);
            $entityManager = $this->getDoctrine()->getManager();
            $result = $repository_result->findOneBy(['id'=>$_POST['id']]);
            $solution = $repository_solution->findOneBy(['test'=>$result->getTest(), 'solution'=>$_POST['data']]);
            $result->setSolution($solution);
            $entityManager->persist($result);
            $entityManager->flush();
            return new Response('Succès!');
        }
    }

    /**
     * Méthode qui gère toute la partie Administration Audit
     *
     * @Route("/audit/commentaire", name="audit_update_comment",methods="POST" )
     */
    public function updateComment()
    {
        $repository_test = $this->getDoctrine()->getRepository(AuditTests::class);
        $repository_result = $this->getDoctrine()->getRepository(AuditResults::class);
        $repository_status = $this->getDoctrine()->getRepository(Status::class);
        $repository_audit = $this->getDoctrine()->getRepository(IntAudit::class);

        $entityManager = $this->getDoctrine()->getManager();
        $audit_test = $repository_result->findOneBy(['id'=>$_POST['id']]);
        $audit_test->setComment($_POST['data']);
        $entityManager->persist($audit_test);
        $entityManager->flush();
        $template['comment'] = $audit_test->getComment();
        return $this->render('audit/newcomment.html.twig', $template);
    }


    /**
     * Méthode qui gère toute la partie Administration Audit
     * @Route("/audit/rapport", name="audit_update_report",methods="GET" )
     */
    public function report()
    {
        $repository_result = $this->getDoctrine()->getRepository(AuditResults::class);
        $repository_audit = $this->getDoctrine()->getRepository(IntAudit::class);
        $repository_section = $this->getDoctrine()->getRepository(AuditSection::class);
        $repository_action = $this->getDoctrine()->getRepository(LogAction::class);
        $action = $repository_action->findOneBy(['action' => 'Terminer']);
        $entityManager = $this->getDoctrine()->getManager();
        $date = new \DateTime(date('Y-m-d H:i:s'));

        $sections = $repository_section->findBy(['archived_date'=>null]);
        $id = $this->cleanInput($_GET['audit']);
        $audit = $repository_audit->findOneBy(['id'=>$id]);
        $this->denyAccessUnlessGranted('Owner',$audit);
        if($audit->getFinishDate() != null or $audit->getParent() != null){
            return $this->render('error/error_404.html.twig');
        }
        $audit_result = $repository_result->findBy(['audit'=>$audit]);
        $sub = [];
        $section = [];
        $section_point = [];
        $total_section_point = [];
        $subsection_point = [];
        $total_subsection_point = [];
        $total = 0;
        /**
         * Calcule le % pour chaque section indexé par prio
         */
        foreach ($audit_result as $key => $value){
            // Récupère la sub
            $subsection = $value->getTest()->getSusbection();
            $sec = $value->getTest()->getSusbection()->getSection();
            // Initiliase l'entrée dans le tableau des points si elle ne l'est pas déjà
            if(!isset($section_point[$sec->getId()])){
                $section_point[$sec->getId()] = 0;
            }
            if(!isset($total_section_point[$sec->getId()])){
                $total_section_point[$sec->getId()] = 0;
            }
            if(!isset($subsection_point[$subsection->getId()])){
                $subsection_point[$subsection->getId()] = 0;
            }
            if(!isset($total_subsection_point[$subsection->getId()])){
                $total_subsection_point[$subsection->getId()] = 0;
            }
            $section[$sec->getId()] = $sec;
            $sub[$subsection->getId()] = $subsection;

            if($value->getTest()->getPriority() == 3) {
                if ($value->getStatus()->getStatus() == 'error') {
                    $section_point[$sec->getId()] = $section_point[$sec->getId()] + 0.5;
                    $subsection_point[$subsection->getId()] = $subsection_point[$subsection->getId()] + 0.5;

                }
                if ($value->getStatus()->getStatus() == 'success') {
                    $section_point[$sec->getId()] = $section_point[$sec->getId()] + 1;
                    $subsection_point[$subsection->getId()] = $subsection_point[$subsection->getId()] + 1;

                }
                $total_section_point[$sec->getId()] = $total_section_point[$sec->getId()] + 1;
                $total_subsection_point[$subsection->getId()] = $total_subsection_point[$subsection->getId()] + 1;
                $total = $total+1;
            }
            if($value->getTest()->getPriority() == 2) {
                if ($value->getStatus()->getStatus() == 'error') {
                    $section_point[$sec->getId()] = $section_point[$sec->getId()] + 1;
                    $subsection_point[$subsection->getId()] = $subsection_point[$subsection->getId()] + 1;

                }
                if ($value->getStatus()->getStatus() == 'success') {
                    $section_point[$sec->getId()] = $section_point[$sec->getId()] + 2;
                    $subsection_point[$subsection->getId()] = $subsection_point[$subsection->getId()] + 2;

                }
                $total_section_point[$sec->getId()] = $total_section_point[$sec->getId()] + 2;
                $total_subsection_point[$subsection->getId()] = $total_subsection_point[$subsection->getId()] + 2;

                $total = $total + 2;
            }
            if($value->getTest()->getPriority() == 1) {
                if ($value->getStatus()->getStatus() == 'error') {
                    $section_point[$sec->getId()] = $section_point[$sec->getId()] + 1.5;
                    $subsection_point[$subsection->getId()] = $subsection_point[$subsection->getId()] + 1.5;

                }
                if ($value->getStatus()->getStatus() == 'success') {
                    $section_point[$sec->getId()] = $section_point[$sec->getId()] + 3;
                    $subsection_point[$subsection->getId()] = $subsection_point[$subsection->getId()] + 3;

                }
                $total_section_point[$sec->getId()] = $total_section_point[$sec->getId()] + 3;
                $total_subsection_point[$subsection->getId()] = $total_subsection_point[$subsection->getId()] + 3;

                $total = $total + 3;

            }
        }
        foreach ($section_point as $key => $value){
            $section_pourcent[$key] = number_format((float)$value / $total_section_point[$key] * 100, 2, '.', '');
            $id = $repository_section->findOneBy(['id'=>$key]);
            $sectionPoint = new SectionPoints($id, $audit, $section_pourcent[$key]);
            $entityManager->persist($sectionPoint);
        }
        foreach ($sub as $key => $value) {
            if (number_format((float)$subsection_point[$value->getId()] / $total_subsection_point[$value->getId()] * 100, 2, '.', '') < 35) {
                $template['bad_section'][$value->getSection()->getId()] = $value->getSection();
                $template['bad_sub'][] = $value;
            } elseif (number_format((float)$subsection_point[$value->getId()] / $total_subsection_point[$value->getId()] * 100, 2, '.', '') > 75) {
                $template['good_section'][$value->getSection()->getId()] = $value->getSection();
                $template['good_sub'][] = $value;
            }
        }

        $audit->setFinishDate($date);
        $entityManager->persist($audit);
        $log = new LogAudits($this->getUser(), $audit, $date, $action);
        $entityManager->persist($log);

        $entityManager->flush();
        $template['audit'] = $audit;
        $template['result'] = $audit_result;
        $template['sections'] = $section;
        $template['subs'] = $sub;
        $template['point_section'] = $section_pourcent;
        $template['score'] = $total;
        $template['t'] = $total;


        return $this->render('audit/report.html.twig', $template);
    }


    /**
     * @Route("/audit/générer-rapport", name="generate_report", options={"utf8": true})
     */
    public function exportReport()
    {
        $repository_result = $this->getDoctrine()->getRepository(AuditResults::class);
        $repository_audit = $this->getDoctrine()->getRepository(IntAudit::class);
        $repository_section = $this->getDoctrine()->getRepository(AuditSection::class);

        $entityManager = $this->getDoctrine()->getManager();
        $date = new \DateTime(date('Y-m-d H:i:s'));

        $sections = $repository_section->findBy(['archived_date'=>null]);
        $id = $this->cleanInput($_GET['audit']);
        $audit = $repository_audit->findOneBy(['id'=>$id]);
        $this->denyAccessUnlessGranted('Owner',$audit);
        if($audit->getParent() != null){
            return $this->render('error/error_404.html.twig');
        }
        $audit_result = $repository_result->findBy(['audit'=>$audit]);
        $sub = [];
        $section = [];
        $section_point = [];
        $total_section_point = [];
        $subsection_point = [];
        $total_subsection_point = [];
        $total = 0;
        /**
         * Calcule le % pour chaque section indexé par prio
         */
        foreach ($audit_result as $key => $value){
            // Récupère la sub
            $subsection = $value->getTest()->getSusbection();
            $sec = $value->getTest()->getSusbection()->getSection();
            // Initiliase l'entrée dans le tableau des points si elle ne l'est pas déjà
            if(!isset($section_point[$sec->getId()])){
                $section_point[$sec->getId()] = 0;
            }
            if(!isset($total_section_point[$sec->getId()])){
                $total_section_point[$sec->getId()] = 0;
            }
            if(!isset($subsection_point[$subsection->getId()])){
                $subsection_point[$subsection->getId()] = 0;
            }
            if(!isset($total_subsection_point[$subsection->getId()])){
                $total_subsection_point[$subsection->getId()] = 0;
            }
            $section[$sec->getId()] = $sec;
            $sub[$subsection->getId()] = $subsection;

            if($value->getTest()->getPriority() == 3) {
                if ($value->getStatus()->getStatus() == 'error') {
                    $section_point[$sec->getId()] = $section_point[$sec->getId()] + 0.5;
                    $subsection_point[$subsection->getId()] = $subsection_point[$subsection->getId()] + 0.5;

                }
                if ($value->getStatus()->getStatus() == 'success') {
                    $section_point[$sec->getId()] = $section_point[$sec->getId()] + 1;
                    $subsection_point[$subsection->getId()] = $subsection_point[$subsection->getId()] + 1;

                }
                $total_section_point[$sec->getId()] = $total_section_point[$sec->getId()] + 1;
                $total_subsection_point[$subsection->getId()] = $total_subsection_point[$subsection->getId()] + 1;
                $total = $total+1;
            }
            if($value->getTest()->getPriority() == 2) {
                if ($value->getStatus()->getStatus() == 'error') {
                    $section_point[$sec->getId()] = $section_point[$sec->getId()] + 1;
                    $subsection_point[$subsection->getId()] = $subsection_point[$subsection->getId()] + 1;

                }
                if ($value->getStatus()->getStatus() == 'success') {
                    $section_point[$sec->getId()] = $section_point[$sec->getId()] + 2;
                    $subsection_point[$subsection->getId()] = $subsection_point[$subsection->getId()] + 2;

                }
                $total_section_point[$sec->getId()] = $total_section_point[$sec->getId()] + 2;
                $total_subsection_point[$subsection->getId()] = $total_subsection_point[$subsection->getId()] + 2;
                $total = $total + 2;
            }
            if($value->getTest()->getPriority() == 1) {
                if ($value->getStatus()->getStatus() == 'error') {
                    $section_point[$sec->getId()] = $section_point[$sec->getId()] + 1.5;
                    $subsection_point[$subsection->getId()] = $subsection_point[$subsection->getId()] + 1.5;

                }
                if ($value->getStatus()->getStatus() == 'success') {
                    $section_point[$sec->getId()] = $section_point[$sec->getId()] + 3;
                    $subsection_point[$subsection->getId()] = $subsection_point[$subsection->getId()] + 3;

                }
                $total_section_point[$sec->getId()] = $total_section_point[$sec->getId()] + 3;
                $total_subsection_point[$subsection->getId()] = $total_subsection_point[$subsection->getId()] + 3;
                $total = $total + 3;
            }
        }
        foreach ($section_point as $key => $value){
            $section_pourcent[$key] = number_format((float)$value / $total_section_point[$key] * 100, 2, '.', '');
        }
        foreach ($sub as $key => $value) {
            if (number_format((float)$subsection_point[$value->getId()] / $total_subsection_point[$value->getId()] * 100, 2, '.', '') < 35) {
                $template['bad_section'][$value->getSection()->getId()] = $value->getSection();
                $template['bad_sub'][] = $value;
            } elseif (number_format((float)$subsection_point[$value->getId()] / $total_subsection_point[$value->getId()] * 100, 2, '.', '') > 75) {
                $template['good_section'][$value->getSection()->getId()] = $value->getSection();
                $template['good_sub'][] = $value;
            }
        }
        $audit->setFinishDate($date);
        $entityManager->persist($audit);
        $entityManager->flush();
        $template['audit'] = $audit;
        $template['result'] = $audit_result;
        $template['sections'] = $section;
        $template['subs'] = $sub;
        $template['point_section'] = $section_pourcent;
        $template['score'] = $total;
        $template['t'] = $total;
        return $this->render('audit/report_pdf.html.twig', $template);
    }
    /**
     * Révision des audits créés
     */
    /**
     * Gère le traitement des images envoyées depuis les dropbox pendant un audit
     * @Route("/voir-audit", name="created_audit", methods="GET" )
     */
    function createdAudits()
    {
        $repository = $this->getDoctrine()->getRepository(AppUser::class);
        $repository_audit = $this->getDoctrine()->getRepository(IntAudit::class);

        $audits = $repository_audit->findBy(['date_archive'=>null, 'parent'=>null]);
        if($audits){
            foreach($audits as $key => $value){
                if (!$this->isGranted('Lecture',$value) and $value->getFinishDate() != null) {
                    unset($audits[$key]);
                }elseif (!$this->isGranted('Owner',$value) and $value->getFinishDate() == null){
                    unset($audits[$key]);
                }
            }
        }
        $array['users'] = $repository->findAll();
        $array['audit'] = $audits;

        if (isset($_GET['nouveau-audit'])) {
            $array['new_audit'] = true;
        }
        return $this->render('review-audit/reviewaudit.html.twig', $array);
    }


    /**
     * Révision des audits créés
     */
    /**
     * Gère le traitement des images envoyées depuis les dropbox pendant un audit
     * @Route("/voir-audit/audit", name="view_created_audit")
     */
    function viewAudit()
    {

        if(isset($_POST['submit_audit'])){
            switch ($_POST['submit_audit']){
                case 'perm':$this->assignPerm();
                break;
                case 'version':$this->newVersion();
            }
        }
        $repository = $this->getDoctrine()->getRepository(AppUser::class);
        $repository_audit = $this->getDoctrine()->getRepository(IntAudit::class);
        $repository_result = $this->getDoctrine()->getRepository(AuditResults::class);
        $repository_point = $this->getDoctrine()->getRepository(SectionPoints::class);
        $repository_permission = $this->getDoctrine()->getRepository(AuditPermission::class);
        $id = $this->cleanInput($_GET['audit']);
        $audit = $repository_audit->findOneBy(['date_archive'=>null, 'id'=>$id]);
        $this->denyAccessUnlessGranted('Lecture',$audit);
        if(!$audit or $audit->getParent()){
            return $this->render('error/error_404.html.twig');
        }
        $audit_child = $repository_audit->findBy(['date_archive'=>null, 'parent'=>$_GET['audit']]);
        $audit_result = $repository_result->findBy(['audit'=>$audit]);

        foreach ($audit_result as $key => $value){
            $sec = $value->getTest()->getSusbection()->getSection();
            $subsection = $value->getTest()->getSusbection();
            $section[$sec->getId()] = $sec;
            $sub[$subsection->getId()] = $subsection;
        }
        if($audit_child){
            $array['childs'] = $audit_child;
        }
        $array['users'] = $repository->findAll();
        $array['audit'] = $audit;
        $array['sections'] = $section;
        $array['subs'] = $sub;
        $array['tests'] = $audit_result;
        $array['points'] = $repository_point->findBy(['audit'=>$audit]);
        $i = 0;
        $total = 0;
        foreach ($array['points'] as $key => $value){
            $total = $total+$value->getPoint();
            $i = $i+1;
        }
        $array['total'] = number_format((float)$total / $i, 2, '.', '');
        if (isset($_GET['nouveau-audit'])) {
            $array['new_audit'] = true;
        }
        $array['permission'] = $repository_permission->findAll();
        return $this->render('review-audit/audit.html.twig', $array);
    }

    /**
     * Gère le traitement des images envoyées depuis les dropbox pendant un audit
     * @Route("/voir-audit/audit/utilisateur", name="get_user", methods="POST" )
     */
    public function searchUsers()
    {
        $repository_user = $this->getDoctrine()->getRepository(AppUser::class);
        $template['users'] = $repository_user->findNameContaining($_POST['search-user'],$this->getUser()->getId());
        return $this->render('audit/searchusers.html.twig', $template);
    }

    public function assignPerm()
    {
        $em = $this->getDoctrine()->getManager();
        $repository_user = $this->getDoctrine()->getRepository(AppUser::class);
        $repository_perm = $this->getDoctrine()->getRepository(AuditPermission::class);
        $repository_audit = $this->getDoctrine()->getRepository(IntAudit::class);
        $repository_log = $this->getDoctrine()->getRepository(LogAuditPerm::class);
        $repository_action = $this->getDoctrine()->getRepository(LogAction::class);
        $action = $repository_action->findOneBy(['action' => 'Créer']);
        $date = new \DateTime(date('Y-m-d H:i:s'));


        $audit = $repository_audit->findOneBy(['id'=>$_POST['audit-perm']]);
        $this->denyAccessUnlessGranted('Administrateur',$audit);
        $permission = $repository_perm->findOneBy(['permission'=>$_POST['perm']]);
        $user = $repository_user->findOneBy(['username'=>$_POST['user']]);
        $perm = new UserPermission($user,$permission,$audit);
        $log = new LogAuditPerm($this->getUser(),$user,$audit,$permission,$date);
        $em->persist($perm);
        $em->persist($log);
        $em->flush();
    }

    public function newVersion()
    {
        $em = $this->getDoctrine()->getManager();
        $repository_user = $this->getDoctrine()->getRepository(AppUser::class);
        $repository_audit = $this->getDoctrine()->getRepository(IntAudit::class);
        $repository_result = $this->getDoctrine()->getRepository(AuditResults::class);
        $repository_status = $this->getDoctrine()->getRepository(Status::class);
        $repository_action = $this->getDoctrine()->getRepository(LogAction::class);
        $action = $repository_action->findOneBy(['action' => 'Créer']);
        $audit = $repository_audit->findOneBy(['id'=>$_POST['audit-perm']]);
        $this->denyAccessUnlessGranted('Modification',$audit);

        $date = new \DateTime(date('Y-m-d H:i:s'));
        $status = $repository_status->findOneBy(['status'=>'fail']);
        $result = $repository_result->findBy(['audit'=>$audit]);
        $newAudit = new IntAudit($audit->getCustomer(), $date, $_POST['input-version']);
        $newAudit->setParent($audit);
        foreach ($result as $key => $value){
            $newResult = new AuditResults($newAudit, $value->getTest(), $status);
            $em->persist($newResult);
        }
        $newAudit->setStarted(true);
        $log = new LogAudits($this->getUser(), $newAudit, $date, $action);
        $em->persist($log);

        $em->persist($newAudit);
        $em->flush();
    }

    /**
     * Gère le traitement des images envoyées depuis les dropbox pendant un audit
     * @Route("/voir-audit/audit/enfant", name="get_child", methods="POST" )
     */
    public function getAuditChild()
    {
        $repository_audit = $this->getDoctrine()->getRepository(IntAudit::class);
        $repository_result = $this->getDoctrine()->getRepository(AuditResults::class);
        $repository_point = $this->getDoctrine()->getRepository(SectionPoints::class);

        $audit = $repository_audit->findOneBy(['id'=>$_POST['id']]);
        if($audit->getParent()){
            $this->denyAccessUnlessGranted('Lecture',$audit->getParent());
        }else{
            $this->denyAccessUnlessGranted('Lecture',$audit);
        }

        $audit_result = $repository_result->findBy(['audit'=>$audit]);
        foreach ($audit_result as $key => $value){
            $sec = $value->getTest()->getSusbection()->getSection();
            $subsection = $value->getTest()->getSusbection();
            $section[$sec->getId()] = $sec;
            $sub[$subsection->getId()] = $subsection;
        }
        $array['sections'] = $section;
        $array['subs'] = $sub;
        $array['tests'] = $audit_result;

        if($audit->getStarted() and $audit->getFinishDate() == null and $this->isGranted('Modification',$audit->getParent())){
            $array['audit'] = $audit;
            return $this->render('review-audit/auditchildtodo.html.twig', $array);
        }elseif($audit->getStarted() and $audit->getFinishDate() == null and $this->isGranted('Lecture',$audit->getParent())){
            $array['audit'] = $audit;
            $array['todo'] = true;
            return $this->render('review-audit/auditchilddone.html.twig', $array);
        }elseif($audit->getStarted() and $audit->getFinishDate() != null){
            $array['points'] = $repository_point->findBy(['audit'=>$audit]);
            $i = 0;
            $total = 0;
            foreach ($array['points'] as $key => $value){
                $total = $total+$value->getPoint();
                $i = $i+1;
            }
            $array['total'] = number_format((float)$total / $i, 2, '.', '');
            return $this->render('review-audit/auditchilddone.html.twig', $array);
        }
    }

    /**
     * Gère le traitement des images envoyées depuis les dropbox pendant un audit
     * @Route("/voir-audit/audit/conclure-audit", name="conclude_audit", methods="POST" )
     */
    public function concludeAudit()
    {
        $repository_result = $this->getDoctrine()->getRepository(AuditResults::class);
        $repository_audit = $this->getDoctrine()->getRepository(IntAudit::class);
        $repository_section = $this->getDoctrine()->getRepository(AuditSection::class);
        $repository_action = $this->getDoctrine()->getRepository(LogAction::class);
        $action = $repository_action->findOneBy(['action' => 'Terminer']);

        $entityManager = $this->getDoctrine()->getManager();
        $date = new \DateTime(date('Y-m-d H:i:s'));

        $audit = $repository_audit->findOneBy(['id'=>$_POST['id']]);
        $this->denyAccessUnlessGranted('Owner',$audit->getParent());

        $audit_result = $repository_result->findBy(['audit'=>$audit]);
        $sub = [];
        $section = [];
        $section_point = [];
        $total_section_point = [];
        $subsection_point = [];
        $total_subsection_point = [];
        $total = 0;
        /**
         * Calcule le % pour chaque section indexé par prio
         */
        foreach ($audit_result as $key => $value){
            // Récupère la sub
            $subsection = $value->getTest()->getSusbection();
            $sec = $value->getTest()->getSusbection()->getSection();
            // Initiliase l'entrée dans le tableau des points si elle ne l'est pas déjà
            if(!isset($section_point[$sec->getId()])){
                $section_point[$sec->getId()] = 0;
            }
            if(!isset($total_section_point[$sec->getId()])){
                $total_section_point[$sec->getId()] = 0;
            }
            if(!isset($subsection_point[$subsection->getId()])){
                $subsection_point[$subsection->getId()] = 0;
            }
            if(!isset($total_subsection_point[$subsection->getId()])){
                $total_subsection_point[$subsection->getId()] = 0;
            }
            $section[$sec->getId()] = $sec;
            $sub[$subsection->getId()] = $subsection;

            if($value->getTest()->getPriority() == 3) {
                if ($value->getStatus()->getStatus() == 'error') {
                    $section_point[$sec->getId()] = $section_point[$sec->getId()] + 0.5;
                    $subsection_point[$subsection->getId()] = $subsection_point[$subsection->getId()] + 0.5;

                }
                if ($value->getStatus()->getStatus() == 'success') {
                    $section_point[$sec->getId()] = $section_point[$sec->getId()] + 1;
                    $subsection_point[$subsection->getId()] = $subsection_point[$subsection->getId()] + 1;

                }
                $total_section_point[$sec->getId()] = $total_section_point[$sec->getId()] + 1;
                $total_subsection_point[$subsection->getId()] = $total_subsection_point[$subsection->getId()] + 1;
                $total = $total+1;
            }
            if($value->getTest()->getPriority() == 2) {
                if ($value->getStatus()->getStatus() == 'error') {
                    $section_point[$sec->getId()] = $section_point[$sec->getId()] + 1;
                    $subsection_point[$subsection->getId()] = $subsection_point[$subsection->getId()] + 1;

                }
                if ($value->getStatus()->getStatus() == 'success') {
                    $section_point[$sec->getId()] = $section_point[$sec->getId()] + 2;
                    $subsection_point[$subsection->getId()] = $subsection_point[$subsection->getId()] + 2;

                }
                $total_section_point[$sec->getId()] = $total_section_point[$sec->getId()] + 2;
                $total_subsection_point[$subsection->getId()] = $total_subsection_point[$subsection->getId()] + 2;

                $total = $total + 2;
            }
            if($value->getTest()->getPriority() == 1) {
                if ($value->getStatus()->getStatus() == 'error') {
                    $section_point[$sec->getId()] = $section_point[$sec->getId()] + 1.5;
                    $subsection_point[$subsection->getId()] = $subsection_point[$subsection->getId()] + 1.5;

                }
                if ($value->getStatus()->getStatus() == 'success') {
                    $section_point[$sec->getId()] = $section_point[$sec->getId()] + 3;
                    $subsection_point[$subsection->getId()] = $subsection_point[$subsection->getId()] + 3;

                }
                $total_section_point[$sec->getId()] = $total_section_point[$sec->getId()] + 3;
                $total_subsection_point[$subsection->getId()] = $total_subsection_point[$subsection->getId()] + 3;

                $total = $total + 3;

            }
        }
        $i = 0;
        $total = 0;
        foreach ($section_point as $key => $value){
            $section_pourcent[$key] = number_format((float)$value / $total_section_point[$key] * 100, 2, '.', '');
            $id = $repository_section->findOneBy(['id'=>$key]);
            $sectionPoint = new SectionPoints($id, $audit, $section_pourcent[$key]);
            $entityManager->persist($sectionPoint);
            $template['points'][] = $sectionPoint;
            $total = $total+$sectionPoint->getPoint();
            $i = $i+1;
        }
        $audit->setFinishDate($date);
        $log = new LogAudits($this->getUser(), $audit, $date, $action);
        $entityManager->persist($log);

        $entityManager->persist($audit);
        $entityManager->flush();

        $template['total'] = number_format((float)$total / $i, 2, '.', '');
        $template['audit'] = $audit;
        $template['tests'] = $audit_result;
        $template['sections'] = $section;
        $template['subs'] = $sub;
        return $this->render('review-audit/auditchilddone.html.twig', $template);
    }


}