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
use App\Entity\Status;
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
     * Méthode qui gère toute la partie Administration Audit
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
     * @Route("/audit/nouveau-audit", name="audit_newaudit", options={"utf8": true})
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
         */
        foreach ($infra as $key=>$value){
            if($value->getType()->getType() == 'Question'){
                if(isset($_POST['pre_audit'][$value->getId()])){
                    $infraCustomer = new InfraCustomer($value,$audit->getCustomer(),'true');
                    $entityManager->persist($infraCustomer);
                }else{
                    $entityManager->persist(new InfraCustomer($value,$audit->getCustomer(),'false'));
                }
            }elseif ($value->getType()->getType() == 'Selection'){
                $infraCustomer = new InfraCustomer($value,$audit->getCustomer(),$_POST['pre_audit'][$value->getId()]);
                $entityManager->persist($infraCustomer);
            }elseif ($value->getType()->getType() == 'Text'){
                $infraCustomer = new InfraCustomer($value,$audit->getCustomer(),$_POST['pre_audit'][$value->getId()]);
                $entityManager->persist($infraCustomer);
            }
        }
        $entityManager->flush();
        /**
         * Cette partie ajoute ou retire des tests
         */
        foreach ($tests as $key => $value) {
            foreach ($value->getLinkTestsInfras() as $k => $v) {
                $infra = $v->getInfra();
                $customerInfra = $repository_customer_infra->findOneBy(['infra'=>$infra]);
                $result = $customerInfra->getResult();
                if($infra->getType()->getType() == 'Question'){
                    if($v->getAction() && $result == 'false' || !$v->getAction() && $result == 'true'){
                        unset($tests[$key]);
                    }
                }
            }
            foreach ($value->getInfraSelections() as $k => $v){
                $infra = $v->getInfra();
                $customerInfra = $repository_customer_infra->findOneBy(['infra'=>$infra]);
                $result = $customerInfra->getResult();
                if($v->getSelection() == $result){
                    if(!$v->getAction()){
                        unset($tests[$key]);
                    }
                }
            }
        }
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
        return $this->render('audit/newaudit.html.twig', $template);
    }

    /**
     * Méthode qui gère toute la partie Administration Audit
     *
     * @Route("/audit/reprendre-audit", name="audit_resume_audit", options={"utf8": true})
     */
    public function resumeAudit()
    {
        $repository_section = $this->getDoctrine()->getRepository(AuditSection::class);
        $repository_test = $this->getDoctrine()->getRepository(AuditTests::class);
        $repository_audit = $this->getDoctrine()->getRepository(IntAudit::class);
        $repository_audit_results = $this->getDoctrine()->getRepository(AuditResults::class);

        $audit = $repository_audit->findOneBy(['id'=>$_GET['audit']]);
        $audit_results = $repository_audit_results->findBy(['audit'=>$audit]);
        $sections = $repository_section->findAll();
        foreach ($sections as $key => $value){
            if($value->getAuditSubSections() == null){
                unset($sections[$key]);
            }
        }
        $template['sections'] = $sections;
        $template['tests'] = $audit_results;

        return $this->render('audit/resumeaudit.html.twig', $template);
    }

    /**
     * Méthode qui gère toute la partie Administration Audit
     *
     * @Route("/audit/statut", name="audit_update_test", options={"utf8": true})
     */
    public function updateStatus()
    {
        $repository_test = $this->getDoctrine()->getRepository(AuditTests::class);
        $repository_result = $this->getDoctrine()->getRepository(AuditResults::class);
        $result = $repository_result->findOneBy(['id'=>$_POST['id']]);
        $template['childs'] = $repository_test->findBy(['parent'=>$result->getTest()]);
        $template['parent'] = $result->getTest();
        switch ($_POST['status']){
            case 'error' :

                if($template['childs']){
                    return $this->render('audit/addchild.html.twig',$template);
                }else{
                    return false;
                }
        }
    }



    /**
     * Enregistre une nouvelle phase dans la partie admin>
     */
    public function saveNewPhase()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $phase = new AuditPhase($_POST['name_phase']);
        $entityManager->persist($phase);
        $entityManager->flush();
        /**
         * Créé l'entrée dans la table audit_phase
         */
        $phase->setNumber($phase->getId());
        $entityManager->persist($phase);
        $entityManager->flush();

        /**
         * Gère l'enregistrement des tests de la phase
         */

        foreach ($_POST['test_phase'] as $key => $value) {
            $type = $this->getDoctrine()->getRepository(TestType::class)->findOneBy(['type' => $value['type']]);
            /**
             * Créé l'objet test
             */
            $test_phase = new AuditTestPhase($value['parent'], $value['prio'], $phase, $type, null);
            $entityManager->persist($test_phase);
            $entityManager->flush();
            /**
             * Gère l'enregistrement des éventuels tests enfants
             */
            foreach ($value['child'] as $k => $v) {
                if (!empty($value['child'][$k]['name'])) {
                    $type = $this->getDoctrine()->getRepository(TestType::class)->findOneBy(['type' => $value['child'][$k]['type']]);
                    $test_child = new AuditTestPhase($value['child'][$k]['name'], $value['child'][$k]['prio'], $phase, $type, $test_phase);
                    $entityManager->persist($test_child);
                }
            }
            $entityManager->flush();
        }
    }



    /**
     * Méthode qui gère la soumission des choix pour les tests de type Sélection.
     * Ils sont envoyés par une requête AJAX depuis l'administration.
     *
     * @Route("/selection-test", name="selection-test", methods="POST")
     */
    public function selectionTest()
    {
        if (isset($_SESSION)) {
            /**
             * Besoin des repositories TestSelection et AuditTestPhase.
             *
             */
            $repository_selection = $this->getDoctrine()->getRepository(TestSelection::class);
            $repository_test = $this->getDoctrine()->getRepository(AuditTestPhase::class);
            $test = $repository_test->findOneBy(['id' => $_POST['id_test']]);
            $entityManager = $this->getDoctrine()->getManager();

            /**
             * Récupère les valeurs de toutes les inputs envoyées.
             */
            foreach ($_POST['selection'] as $key => $value) {
                if (isset($value['id'])) {
                    /**
                     * Récupère l'objet si existe déjà dans la base de données
                     */
                    $selection = $repository_selection->findOneBy(['id' => $value['id']]);
                    /**
                     * Vérifie si le contenu a été modifié
                     */
                    if ($value['name'] != $selection->getName()) {
                        $selection->setName($value['name']);
                    }
                 /**
                  * Si si il n'existe pas, il faut créer un nouvel objet.
                  */
                } elseif ($value['name']) {
                    $selection = new TestSelection($value['name'], $test);
                    $entityManager->persist($selection);
                }
            }
            $entityManager->flush();
            return new Response('ok');
        } else {
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     * Méthode qui retourne la vue pour la modification d'une phase dans l'administration.
     * Elle est appelée lorsque l'utilisateur clique sur le bouton 'Modifier phase" pour une des phases.
     *
     * @Route("/modifier-phase", name="modif_phase")
     */

    public function modifPhase()
    {

                $phase = $this->getDoctrine()->getRepository(AuditPhase::class)->findOneBy(['id' => $_GET['id']]);
                $array['tests'] = $this->getDoctrine()->getRepository(AuditTestPhase::class)->findBy(['idPhase' => $phase]);
                $array['type'] = $this->getDoctrine()->getRepository(TestType::class)->findAll();
                $array['phase'] = $phase;
                /**
                 * Retourne le template de modif de phase avec les données nécessaires de la BDD.
                 */
                return $this->render('audit/new_phase.html.twig', $array);
    }

    /**
     * Méthode qui gère les modifications d'une phase existante.
     */
    public function saveModifPhase()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_phase = $this->getDoctrine()->getRepository(AuditPhase::class);
        $repository_test = $this->getDoctrine()->getRepository(AuditTestPhase::class);
        $repository_test_type = $this->getDoctrine()->getRepository(TestType::class);

        $phase = $repository_phase->findOneBy(['id' => $_GET['id']]);

        /**
         * Vérifie si le nom de la phase a été modifié.
         */
        if ($phase->getPhaseName() != $_POST['name_phase']) {
            /**
             * Si oui, il faut mettre le nom de l'objet à jour avec le nouveau nom.
             */
            $phase->setPhaseName($_POST['name_phase']);
        }
        /**
         * La partie suivant sert à gérer toutes les modifications éventuelles sur des tests existant qui se rapportent à la phase.
         */
        foreach ($_POST['test_phase'] as $key => $value) {
            if (isset($value['id'])) {
                $test = $repository_test->findOneBy(['id' => $value['id']]);
                $type = $repository_test_type->findOneBy(['type' => $value['type']]);
                if ($test->getName() != $value['parent']) {
                    $test->setName($value['parent']);
                }
                if ($test->getType() != $type) {
                    $test->setType($type);
                }
                if ($test->getPriority() != $value['prio']) {
                    $test->setPriority($value['prio']);
                }
                /**
                 * Traîte la partie des enfants du test, si il en a.
                 */
                foreach ($value['child'] as $k => $v) {
                    if ($v['name'] && isset($v['id'])) {
                        $child = $repository_test->findOneBy(['id' => $v['id']]);
                        $type = $repository_test_type->findOneBy(['type' => $v['type']]);
                        if ($child->getName() != $v['name']) {
                            $child->setName($v['name']);
                        }
                        if ($child->getType() != $type) {
                            $child->setType($type);
                        }
                        if ($child->getPriority() != $v['prio']) {
                            $child->setPriority($v['prio']);
                        }
                        /**
                         * Créer les nouveaux enfants du test.
                         */
                    } elseif ($v['name']) {
                        $type = $repository_test_type->findOneBy(['type' => $v['type']]);
                        $child = new AuditTestPhase($v['name'], $v['prio'], $phase, $type, $test);
                        $entityManager->persist($child);
                    }
                }
              /**
               * Cette partie gère d'éventuels nouveaux tests qui viendraient d'être créer.
               */
            } else {
                $type = $repository_test_type->findOneBy(['type' => $value['type']]);
                $test = new AuditTestPhase($value['parent'], $value['prio'], $phase, $type, null);
                $entityManager->persist($test);
                $entityManager->flush();
                /**
                 * Gère les nouveaux enfants du nouveau test.
                 */
                foreach ($value['child'] as $k => $v) {
                    if ($v['name']) {
                        $type = $repository_test_type->findOneBy(['type' => $v['type']]);
                        $child = new AuditTestPhase($v['name'], $v['prio'], $phase, $type, $test);
                        $entityManager->persist($child);
                    }
                }
            }
        }
        $entityManager->flush();
    }

    /**
     * Supprime une phase complète avec ses tests
     * Appelée par requête AJAX quand l'utilisteur clique sur 'Supprimer phase"
     * @Route("/supprimer-phase-audit", name="delete_audit_phase", options={"utf8": true})
     */
    public function deletePhase()
    {
        $idPhase = $_POST['id'];
        $entityManager = $this->getDoctrine()->getManager();
        /**
         * Récupère la phase et ses tests.
         */
        $tests = $this->getDoctrine()->getRepository(AuditTestPhase::class)->findBy(['idPhase' => $idPhase]);
        $phase = $this->getDoctrine()->getRepository(AuditPhase::class)->findOneBy(['id' => $idPhase]);

        /**
         * Supprime d'abord tous les enfants car ils ont une clé étrangère qui fait référence au test parent.
         * Je ne pourrais supprimer le parent sans supprimer les enfants avant.
         */
        foreach ($tests as $key => $value) {
            $testResult = $this->getDoctrine()->getRepository(AuditCompanyResult::class)->findBy(['test' => $value]);
            if($testResult) {
                foreach ($testResult as $k => $v) {
                    $entityManager->remove($v);
                }
            }
            if ($value->getIdParent()) {
                $entityManager->remove($value);
            } else {
                $array[] = $value;
            }
        }
        if (isset($array)) {
            foreach ($array as $key => $value) {
                $entityManager->remove($value);
            }
        }
        $entityManager->remove($phase);
        $entityManager->flush();

        return new Response('ok');
    }

    /**
     * Méthode appelée par requête AJAX et qui sert à supprimer une famille de tests ou un test enfant d'un test parent sur la page de modification d'un phase existante.
     * La suppression de tests d'une phase existante se fait par requête AJAX afin de rendre la gestion de la modification d'une phase existante dans la méthode ci-dessus plus simple.
     *
     * @Route("/supprimer-test-phase", name="delete_test", methods="POST")
     */
    public function deleteTest()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_test = $this->getDoctrine()->getRepository(AuditTestPhase::class);
        $repository_selection = $this->getDoctrine()->getRepository(TestSelection::class);
        $repository_infra = $this->getDoctrine()->getRepository(TestsInfrastructure::class);

        /**
         * Récupère les objets test parent et ses tests enfants.
         */
        $parent = $repository_test->findOneBy(['id' => $_POST['id']]);
        $child = $repository_test->findBy(['id_parent' => $parent]);
        /**
         * Supprimer chaque enfant de la famille
         */
        foreach ($child as $key => $value) {
            $testResult = $this->getDoctrine()->getRepository(AuditCompanyResult::class)->findBy(['test' => $value]);
            $selection = $repository_selection->findBy(['test' => $value]);
            $infraTest = $repository_infra->findBy(['test_phase' => $value]);
            if($infraTest) {
                foreach ($infraTest as $k => $v) {
                    $entityManager->remove($v);
                }
            }
            if($testResult) {
                foreach ($testResult as $k => $v) {
                    $entityManager->remove($v);
                }
            }
            /**
             * Supprime les sélections du choix multiple si l'enfant est de type Sélection
             */
            if ($selection) {
                foreach ($selection as $k => $v) {
                    $entityManager->remove($v);
                }
            }
            /**
             * DELETE l'enfant.
             */
            $entityManager->remove($value);
        }
        /**
         * Idem pour le parent
         */

        $infraTest = $repository_infra->findOneBy(['test_phase' => $parent]);
        $selection = $repository_selection->findBy(['test' => $parent]);
        $testResult = $this->getDoctrine()->getRepository(AuditCompanyResult::class)->findBy(['test' => $parent]);
        if($testResult) {
            foreach ($testResult as $k => $v) {
                $entityManager->remove($v);
            }
        }
        if($infraTest) {
            foreach ($infraTest as $k => $v) {
                $entityManager->remove($v);
            }
        }
        if ($selection) {
            foreach ($selection as $k => $v) {
                $entityManager->remove($v);
            }
        }
        /**
         * DELETE le parent
         */
        $entityManager->remove($parent);
        $entityManager->flush();
        return new Response('ok');
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
     * Retourne la vue du résultat de l'audit
     * @Route("/résultat-audit", name="result_audit", options={"utf8": true})
     */

    public function resultAudit()
    {


        $repository_test = $this->getDoctrine()->getRepository(AuditTestPhase::class);
        $repository_phase = $this->getDoctrine()->getRepository(AuditPhase::class);
        $repository_audit_result = $this->getDoctrine()->getRepository(AuditCompanyResult::class);
        $repository_audit = $this->getDoctrine()->getRepository(AuditCompany::class);
        $repository_user = $this->getDoctrine()->getRepository(AppUser::class);

        $em = $this->getDoctrine()->getManager();

        $last_audit = $repository_audit->findOneBy([], ['id' => 'DESC']);
        $array['auditNumber'] = $last_audit->getId();

        $array['phases'] = $repository_phase->findAll();
        $array['name_company'] = $_POST['name'];
        $array['phone_company'] = $_POST['phone'];
        $array['email_company'] = $_POST['email'];
        $array['size_company'] = $_POST['size'];
        /**
         * Initialise les variables pour les scores
         * Les tableaux $avg servent à calculer la moyenne par priorité.
         * Les tableaux $i servent à calculer le nombre de points obtenus par priorité
         * $total_points sera le nombre de de point total obtenable et comparé avec les tableaux $i
         */
        $avg['prio1'] = 0;
        $avg['prio2'] = 0;
        $avg['prio3'] = 0;
        $i['prio1'] = 0;
        $i['prio2'] = 0;
        $i['prio3'] = 0;
        $points = 0;
        $total_points = 0;
        $test = $repository_test->findAll();
        $company = $this->saveInfoCompany();
        $user = $repository_user->findOneBy(['id'=>$this->getUser()->getId()]);
        $date = date('Y-m-d H:i:s');
        $date = new \DateTime($date);
        $array['id_company'] = $company->getId();
       /* if($array['auditNumber'] == $_POST['number-audit']){
            $audit = $repository_audit->findOneBy(['id' => $_POST['number-audit']]);
            foreach ($test as $key => $value) {
                if(isset($_POST['tests'][$value->getId()])){
                    $result = $repository_audit_result->findOneBy(['audit' => $audit, 'test' => $value]);

                }
            }
        }else{*/
            $audit = new AuditCompany($company, $user, $date);
            $em->persist($audit);

            /**
             * Compare tous les tests existants à ceux qui ont été passés durant l'audit.
             */
            foreach ($test as $key => $value) {
                /**
                 * Si le test est de priorité 1 et qu'il a été validé pendant l'audit
                 */
                if ($value->priority == 1) {
                    if (isset($_POST['tests'][$value->getId()]['check']) or isset($_POST['tests'][$value->getId()]['selection']) and $_POST['tests'][$value->getId()]['selection']) {
                        /**
                         * Le test est passé, augmente les scores
                         */
                        $avg['prio1'] = $avg['prio1'] + 1;
                        $i['prio1'] = $i['prio1'] + 1;
                        $points = $points + 3;
                        $total_points = $total_points + 3;
                        $audit_result = new AuditCompanyResult($value, $_POST['tests'][$value->getId()]['info'], null, true, $audit);
                        $em->persist($audit_result);

                    } elseif (isset($_POST['tests'][$value->getId()])) {
                        $i['prio1'] = $i['prio1'] + 1;
                        /**
                         * Le test n'est passé, ajoute au tableau qui se sera renvoyé par la vue pour la liste dans le résultat
                         */
                        $array['prio1'][] = $value;
                        $total_points = $total_points + 3;
                        $audit_result = new AuditCompanyResult($value, $_POST['tests'][$value->getId()]['info'], null, false, $audit);
                        $em->persist($audit_result);
                    }
                    /**
                     * Idem mais pour les P2
                     */
                } elseif ($value->priority == 2) {
                    if (isset($_POST['tests'][$value->getId()]['check']) or isset($_POST['tests'][$value->getId()]['selection']) and $_POST['tests'][$value->getId()]['selection']) {
                        $avg['prio2'] = $avg['prio2'] + 1;
                        $i['prio2'] = $i['prio2'] + 1;
                        $points = $points + 2;
                        $total_points = $total_points + 2;
                        $audit_result = new AuditCompanyResult($value, $_POST['tests'][$value->getId()]['info'], null, true, $audit);
                        $em->persist($audit_result);

                    } elseif (isset($_POST['tests'][$value->getId()])) {
                        $i['prio2'] = $i['prio2'] + 1;
                        $array['prio2'][] = $value;
                        $total_points = $total_points + 2;
                        $audit_result = new AuditCompanyResult($value, $_POST['tests'][$value->getId()]['info'], null, false, $audit);
                        $em->persist($audit_result);

                    }
                    /**
                     * Idem mais pour les P3
                     */
                } elseif ($value->priority == 3) {
                    if (isset($_POST['tests'][$value->getId()]['check']) or isset($_POST['tests'][$value->getId()]['selection']) and $_POST['tests'][$value->getId()]['selection']) {
                        $avg['prio3'] = $avg['prio3'] + 1;
                        $i['prio3'] = $i['prio3'] + 1;
                        $points = $points + 1;
                        $total_points = $total_points + 1;
                        $audit_result = new AuditCompanyResult($value, $_POST['tests'][$value->getId()]['info'], null, true, $audit);
                        $em->persist($audit_result);
                    } elseif (isset($_POST['tests'][$value->getId()])) {
                        $i['prio3'] = $i['prio3'] + 1;
                        $array['prio3'][] = $value;
                        $total_points = $total_points + 1;
                        $audit_result = new AuditCompanyResult($value, $_POST['tests'][$value->getId()]['info'], null, false, $audit);
                        $em->persist($audit_result);
                    }
                }
            }
        //}
            /**
             * Calcule les moyennes
             */
            $array['avg_prio1'] = number_format((float)$avg['prio1'] / $i['prio1'] * 100, 2, '.', '');
            $array['avg_prio2'] = number_format((float)$avg['prio2'] / $i['prio2'] * 100, 2, '.', '');
            $array['avg_prio3'] = number_format((float)$avg['prio3'] / $i['prio3'] * 100, 2, '.', '');
            /**
             * Calcule le score indexé suivant les priorités
             */
            $array['avg'] = number_format((float)($points / $total_points) * 100, 2, '.', '');
            $em->flush();
            return $this->render('audit/result_audit.html.twig', $array);

    }

    /**
     * @Route("/finalisation-audit", name="finish_audit", options={"utf8": true})
     */
    public function finishAudit()
    {
        $repository_test = $this->getDoctrine()->getRepository(AuditTestPhase::class);
        $repository_audit_result = $this->getDoctrine()->getRepository(AuditCompanyResult::class);
        $repository_company =  $this->getDoctrine()->getRepository(Company::class);
        $repository_audit =  $this->getDoctrine()->getRepository(AuditCompany::class);

        $em = $this->getDoctrine()->getManager();
        $company = $repository_company->findOneBy(['id'=>$_POST['id_company']]);
        $audit = $repository_audit->findOneBy(['company'=>$company]);
        if(isset($_POST['tests'])) {
            foreach ($_POST['tests'] as $key => $value) {
                $test = $repository_test->findOneBy(['id' => $value]);
                $companyTest = $repository_audit_result->findOneBy(['test' => $test, 'audit' => $audit]);
                $companyTest->setSelected(true);
                $companyTest->setDone(false);
                $em->persist($companyTest);
            }
        }
        $em->flush();
        $array['company'] = $company->getId();
        return $this->render('audit/finalisation_audit.html.twig', $array);
    }

    public function saveInfoCompany()
    {
        $repository_size = $this->getDoctrine()->getRepository(CompanySize::class);
        $em = $this->getDoctrine()->getManager();
        $size_company = explode("<", $_POST['size']);
        $repository_test_infra = $this->getDoctrine()->getRepository(AuditTestInfrastructure::class);
        /**
         * Récupère l'objet correspondant à la taille de l'entreprise
         */
        $size = $repository_size->findOneBy(['max_size' => $size_company[1]]);
        $date = date('Y-m-d H:i:s');
        $date = new \DateTime($date);

        $company = new Company($_POST['name'],$_POST['phone'],$_POST['email'], $size, $date);
        $em->persist($company);
        foreach ($_POST['test']['radio'] as $key => $value){
            if($value == 'pos'){
                $testInfra = $repository_test_infra->findOneBy(['name'=>$key]);
                $infra = new CompanyInfrastructure($company, $testInfra);
                $em->persist($infra);
            }
        }
        $em->flush();
        return $company;
    }

    /**
     * @Route("/voir-audit", name="view_audit", options={"utf8": true})
     */

    public function viewAudits()
    {
            $repository_audit = $this->getDoctrine()->getRepository(AuditCompany::class);
            $array['audits'] = $repository_audit->findAll();

            return $this->render('audit/select_audit.html.twig', $array);

    }
    /**
     * Récupère les solutions si elles existent qui seront affiché dans le Récpapitulatif du résultat
     * @Route("/solution-test", name="get_test_solution", methods="POST" )
     */
    public function getTestSolution()
    {
        $repository_test = $this->getDoctrine()->getRepository(AuditTestPhase::class);
        $repository_size = $this->getDoctrine()->getRepository(CompanySize::class);
        $repository_product_size = $this->getDoctrine()->getRepository(ProductCompanySize::class);
        $repository_solution_features = $this->getDoctrine()->getRepository(SolutionFeatures::class);

        $size_company = explode("<", $_POST['size']);
        /**
         * Récupère l'objet correspondant à la taille de l'entreprise
         */
        $size = $repository_size->findOneBy(['max_size' => $size_company[1]]);
        /**
         * Récupère le test traité
         */
        $test = $repository_test->findOneBy(['id' => $_POST['id']]);
        /**
         * Récupère toutes les solutions du taille correspondant à la taille de l'entreprise
         */
        $array['solutions'] = $repository_product_size->findBy(array('test' => $test,'size'=>$size));
        /**
         * Récupère les caractéristiques des solutions cherchées
         */
        foreach ($array['solutions'] as $key => $value){
            $array['features'][] = $repository_solution_features->findBy(['solution'=>$value->getProduct()->getSolution()]);
        }
        $array['test'] = $test;
        return $this->render('audit/solutions_test.html.twig',$array);
    }





}