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
use App\Entity\AuditResults;
use App\Entity\AuditSection;
use App\Entity\AuditTests;
use App\Entity\AuditTestsInfra;
use App\Entity\SectionPoints;
use App\Entity\Solution;
use App\Entity\Status;
use App\Entity\TestSelections;
use App\Entity\TestStatus;
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
    /**
     * Méthode qui gère toute la partie Administration Audit
     *
     * @Route("/audit/créer", name="create_audit", options={"utf8": true}, methods="POST")
     */
    public function createAudit()
    {
        $repository_user = $this->getDoctrine()->getRepository(AppUser::class);
        $entityManager = $this->getDoctrine()->getManager();
        $date = new \DateTime(date('Y-m-d H:i:s'));
        $audit_name = $_POST['audit-name'];
        $first_name = $_POST['first-name-audit'];
        $second_name = $_POST['second-name-audit'];
        $email = $_POST['email-audit'];
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
        $template['audit'] = $repository_audit->findOneBy(['id'=>$_GET['audit']]);
        $template['infra'] = $repository_infra->findBy(array('date_archive' => null));
        $template['audit_id'] = $_GET['audit'];
        return $this->render('audit/preaudit.html.twig', $template);
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

        $tests = $repository_test->findBy(['date_archive' => null, 'parent'=>null]);
        $entityManager = $this->getDoctrine()->getManager();
        $audit = $repository_audit->findOneBy(['id'=>$_GET['audit']]);
        $infra = $repository_infra->findBy(array('date_archive' => null));
        $status = $repository_status->findOneBy(['status'=>'fail']);
        /**
         * Cette partie enregistre les réponses aux questions préaudit
         * Pour chaque test infra
         */
        foreach ($infra as $key=>$value){
            /**
             * Si type question
             */
            if($value->getType()->getType() == 'Question'){
                if(isset($_POST['pre_audit'][$value->getId()])){
                    $infraCustomer = new InfraCustomer($value,$audit,'true');
                    $entityManager->persist($infraCustomer);
                }else{
                    $entityManager->persist(new InfraCustomer($value,$audit,'false'));
                }

            }elseif ($value->getType()->getType() == 'Selection'){
                $infraCustomer = new InfraCustomer($value,$audit,$_POST['pre_audit'][$value->getId()]);
                $entityManager->persist($infraCustomer);
            }elseif ($value->getType()->getType() == 'Text'){
                $infraCustomer = new InfraCustomer($value,$audit,$_POST['pre_audit'][$value->getId()]);
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
                $customerInfra = $repository_customer_infra->findOneBy(['infra'=>$infra,'audit'=>$audit]);
                $result = $customerInfra->getResult();
                if($infra->getType()->getType() == 'Question'){
                    /**
                     *
                     */
                    if($v->getAction() && $result == 'false' || !$v->getAction() && $result == 'true'){
                        unset($tests[$key]);
                    }
                }
            }
            foreach ($value->getLinkSelectInfras() as $k => $v){
                $infra = $v->getSelection()->getInfra();
                $customerInfra = $repository_customer_infra->findOneBy(['infra'=>$infra,'audit'=>$audit]);
                $result = $customerInfra->getResult();
                if($v->getSelection()->getSelection() == $result){
                    if(!$v->getAction()){
                        unset($tests[$key]);
                    }
                }
            }
        }
        /**
         * Initialise le questionnaire d'audit avec le jeu de question adapté
         */
        foreach ($tests as $key => $value){
            $audit_tests = new AuditResults($audit,$value,$status);
            $entityManager->persist($audit_tests);
            $audit_test[] = $audit_tests;
            $childs = $repository_test->findBy(['parent'=>$value]);
            foreach ($childs as $k => $v){
                $audit_tests = new AuditResults($audit,$v,$status);
                $entityManager->persist($audit_tests);
            }
        }
        $audit->setStarted(true);
        $entityManager->flush();
        $template['sections'] = $repository_section->findBy(['archived_date'=>null]);
        $template['tests'] = $audit_test;
        $template['audit'] = $_GET['audit'];

        return $this->render('audit/resumeaudit.html.twig', $template);
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
        $audit = $repository_audit->findOneBy(['id'=>$_GET['audit']]);
        $audit_results = $repository_audit_results->findBy(['audit'=>$audit]);
        $sections = $repository_section->findBy(['archived_date'=>null]);
        $last_response = $repository_audit_results->findOneBy(['audit'=>$audit,'last_responded'=>true]);

        foreach ($sections as $key => $value){
            if($value->getAuditSubSections() == null){
                unset($sections[$key]);
            }
        }
        $template['sections'] = $sections;
        $template['tests'] = $audit_results;
        $template['audit'] = $_GET['audit'];
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

        $entityManager = $this->getDoctrine()->getManager();
        $date = new \DateTime(date('Y-m-d H:i:s'));

        $sections = $repository_section->findBy(['archived_date'=>null]);
        $audit = $repository_audit->findOneBy(['id'=>$_GET['audit']]);
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
     * Gère le traitement des images envoyées depuis les dropbox pendant un audit
     * @Route("/enregistrer-images", name="save_images_test", methods="POST" )
     */
    function saveFiles()
    {
        $repository_test = $this->getDoctrine()->getRepository(AuditTestPhase::class);

        $test = $repository_test->findOneBy(['id'=>$_POST['id']]);
        /**
         * Initialise le dossier avec le num de l'audit et son sous-dossier avec le nom du test des images traitées
         */
        $auditFolder = 'images/test_pic/'.$_POST['auditNumber'];   //2
        $testFolder = 'images/test_pic/'.$_POST['auditNumber'].'/'.$test->id.'/';   //2
        $path = $_FILES['file']['name'];
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        // if folder doesn't exists, create it
        /**
         * Si ils n'existent pas, il faut les créer
         */
        if(!file_exists($auditFolder) && !is_dir($auditFolder)) {
            mkdir($auditFolder);
        }
        if(!file_exists($testFolder) && !is_dir($testFolder)) {
            mkdir($testFolder);
            /**
             * Le sous-dossier est créé, le nom de la première image sera '1'.
             */
            $fileName = '1.'.$ext;
        }else {
            /**
             * Le sous dossier existe déjà, scan pour récupérer les nom des fichiers par la fin.
             */
            $count = scandir($testFolder, 1);
            if ($count) {
                /**
                 * Récupère le premier nom du tableau qui est le plus grand du dossier étant donné qu'on le scan par la fin.
                 * Récupère ce nombre et converti le en INT.
                 */
                $tab = explode('.', $count[0]);
                $fileName = intval($tab[0]);
                $fileName = $fileName+1;
                $fileName = $fileName.'.'.$ext;
            }
        }
        if(!empty($_FILES)){

            $tempFile = $_FILES['file']['tmp_name'];          //3
            $targetPath = $testFolder;  //4
        }
        $targetFile =  $targetPath.$fileName;  //5
        /**
         * Enregistre l'image avec son nouveau nom et dans le bon dossier.
         */
        if(move_uploaded_file($tempFile, $targetFile)){
            return new Response('ok');
        }; //6
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
        $audit = $repository_audit->findOneBy(['id'=>$_GET['audit']]);
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
                if (!$this->isGranted('Read',$value)) {
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
     * @Route("/voir-audit/audit", name="view_created_audit", methods="GET" )
     */
    function viewAudit()
    {
        $repository = $this->getDoctrine()->getRepository(AppUser::class);
        $repository_audit = $this->getDoctrine()->getRepository(IntAudit::class);
        $repository_result = $this->getDoctrine()->getRepository(AuditResults::class);
        $repository_point = $this->getDoctrine()->getRepository(SectionPoints::class);
        $audit = $repository_audit->findOneBy(['date_archive'=>null, 'id'=>$_GET['audit']]);
        $this->denyAccessUnlessGranted('Read',$audit);

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
        return $this->render('review-audit/audit.html.twig', $array);
    }




}