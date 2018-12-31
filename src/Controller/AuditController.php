<?php
/**
 * Created by PhpStorm.
 * User: Romain
 * Date: 26/04/2018
 * Time: 13:55
 */

namespace App\Controller;

use App\Entity\AppUser;
use App\Entity\AuditSection;
use App\Entity\AuditTests;
use App\Entity\AuditTestsInfra;
use App\Entity\AuditCompanyResult;
use App\Entity\AuditPhase;
use App\Entity\AuditTestPhase;
use App\Entity\InfraCustomer;
use App\Entity\InfraSelection;
use App\Entity\IntAudit;
use App\Entity\LinkTestsInfra;
use App\Entity\ProductCompanySize;
use App\Entity\SolutionFeatures;
use App\Entity\TestType;
use Proxies\__CG__\App\Entity\CompanySize;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;


class AuditController extends AbstractController
{

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
     * Méthode qui gère toute la partie Administration Audit
     *
     * @Route("/audit/nouveau-audit", name="audit_newaudit", options={"utf8": true})
     */
    public function newAudit()
    {
        /**
         * Requête personnalisée afin de chercher tous les tests qui ne sont pas liés à un test d'infrastrucuture.
         * Car cette partie démarre un nouvel audit, il faut donc uniquement les tests du modèle de base en attendant les réponses aux tests d'infra.
         */
        $repository_audit = $this->getDoctrine()->getRepository(IntAudit::class);
        $repository_infra = $this->getDoctrine()->getRepository(AuditTestsInfra::class);

        $entityManager = $this->getDoctrine()->getManager();
        $audit = $repository_audit->findOneBy(['id'=>$_GET['audit']]);
        $infra = $repository_infra->findBy(array('date_archive' => null));
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
        $audit->setStarted(true);
        $entityManager->flush();
        return $this->render('audit/newaudit.html.twig');
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
        $repository_customer_infra = $this->getDoctrine()->getRepository(InfraCustomer::class);
        $repository_infra_selection = $this->getDoctrine()->getRepository(InfraSelection::class);

        $audit = $repository_audit->findOneBy(['id'=>$_GET['audit']]);
        $tests = $repository_test->findBy(['date_archive' => null,'parent'=>null]);
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
        $template['sections'] = $repository_section->findAll();
        $template['tests'] = $tests;

        return $this->render('audit/resumeaudit.html.twig', $template);
    }
    /**
     * Méthode qui gère toute la partie Administration Audit
     *
     * @Route("/adminidedestration", name="admin_audit", options={"utf8": true})
     */
    public function adminAudit()
    {
                /**
                 * Gère les changements dans les phases de l'audit
                 */
                if ($_POST) {
                    switch ($_POST['submit']) {
                        /**
                         * Création d'une nouvelle phase
                         */
                        case 'newPhase':
                            $this->saveNewPhase();
                            break;
                        /**
                         * Gère les modifications d'une phase existante
                         */
                        case 'submitModifPhase':
                            $this->saveModifPhase();
                            break;
                    }
                }
                /**
                 * Toutes les informations nécessaire pour la page d'admin de l'audit de la base de données.
                 */
                $repository_phase = $this->getDoctrine()->getRepository(AuditPhase::class);
                $repository_test = $this->getDoctrine()->getRepository(AuditTestPhase::class);
                $repository_test_type = $this->getDoctrine()->getRepository(TestType::class);
                $repository_selection = $this->getDoctrine()->getRepository(TestSelection::class);
                $array['selection'] = $repository_selection->findAll();
                $array['type'] = $repository_test_type->findAll();
                $array['phases'] = $repository_phase->findBy([], ['number' => 'ASC']);
                $array['tests'] = $repository_test->findAll();
                $array['test_type'] = $repository_test_type->findAll();

                return $this->render('administration/administration.html.twig', $array);
                /**
                 * Pas les droits admin.
                 */

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
     * Méthode qui gère la partie Audit de l'application
     * Elle s'occupe d'aller chercher les tests en réponse aux tests d'infrastructure et de les retourner.
     *
     * @Route("/créer-audit", name="create_audit", options={"utf8": true})
     */
    public function ded()
    {
            $repository_test_infra = $this->getDoctrine()->getRepository(AuditTestInfrastructure::class);
            $repository_type = $this->getDoctrine()->getRepository(TestType::class);
            $repository_test = $this->getDoctrine()->getRepository(AuditTestPhase::class);
            $repository_phase = $this->getDoctrine()->getRepository(AuditPhase::class);
            $repository_company = $this->getDoctrine()->getRepository(Company::class);
            $repository_selection = $this->getDoctrine()->getRepository(TestSelection::class);
            $repository_size = $this->getDoctrine()->getRepository(CompanySize::class);
            $repository_audit = $this->getDoctrine()->getRepository(AuditCompany::class);


        if(isset($_GET['rq'])){
                $repository_test_infra_audit = $this->getDoctrine()->getRepository(TestsInfrastructure::class);

                $testInfra = $repository_test_infra->findOneBy(['id'=>$_POST['testId']]);
                $tests = $repository_test_infra_audit->findBy(['test_infra'=>$testInfra]);
                return new JsonResponse($tests);
            }
            /**
             * Cette partie s'occupe d'aller chercher les tests en réponse aux tests d'infrastructure et de les retourner.
             * Je n'ai pas pu utiliser Twig pour générer un template car il peut il y'avoir plusieurs tests liés à un test d'infra.
             * Je dois générer un template par test dans un tableau et l'envoyer en JSON
             * Mais Twig a des problèmes lorsqu'on génère un template dans une variable au lieu de le renvoyer directement comme vue.
             */
            if(isset($_POST['testId'])){
                $repository_test_infra_audit = $this->getDoctrine()->getRepository(TestsInfrastructure::class);

                $testInfra = $repository_test_infra->findOneBy(['id'=>$_POST['testId']]);
                $tests = $repository_test_infra_audit->findBy(['test_infra'=>$testInfra]);

                foreach($tests as $key => $value){
                    /**
                     * Si le n'est pas un enfant
                     * Créer un nouveau groupe de tests.
                     */
                    if(!$value->test_phase->id_parent) {
                        $test = $repository_test->findOneBy(['id'=>$value->test_phase]);
                    $selection = $repository_selection->findBy(['test'=>$test]);
                        $test_child = $repository_test->findBy(['id_parent' => $test]);
                        $html = '<div id="group-form' . $test->id . '" class="group-form-unchecked col-md-8 col-sm-8 col-xs-12" style="margin: 1%"><div class="form-group">';
                        if ($test->type->type == 'Question') {
                            $html .= '<label class="checkbox-inline" onclick="toggleClass('.$test->id.')">
                                    <input type="checkbox" class="form-control check-audit" id="checkbox-audit' . $test->id . '" name="tests[' . $test->id . '][check]">' . $test->name . '</label>';
                        } elseif ($test->type->type == 'Selection') {
                            $html .= '<label class="checkbox-inline" onclick="toggleClass(' . $test->id . ')">' . $test->name . '
                                                                                        <select class="form-control check-audit" id="checkbox-audit' . $test->id . '" name="tests[' . $test->id . '][selection]" >
                                                                                            <option value="" selected>Aucun</option>';
                            if ($selection) {
                                foreach ($selection as $k => $v) {
                                    $html .= '<option value="' . $v->name . '">' . $v->name . '"></option>';
                                }
                                $html .= '</select>
                                      </label>';
                            }
                        }
                        $html .= '<span class="glyphicon glyphicon-edit" data-toggle="modal" data-target="#myModal' . $test->id . '"></span>
                                                                            <div class="modal fade" id="myModal' . $test->id . '" role="dialog">
                                                                                <div class="modal-dialog modal-lg">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header">
                                                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                                            <h4 class="modal-title">Informations additionnelles</h4><small>' . $test->name . '</small>
                                                                                        </div>
                                                                                        <div class="modal-body">
                                                                                            <textarea class="autoExpand form-control" rows="5" name="tests[' . $test->id . '][info]"></textarea>
                                                                                        </div>
                                                                                        <div class="modal-footer">
                                                                                            <button type="button" class="btn btn-primary" data-dismiss="modal">Fermer</button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>';
                        /**
                         * Si le test a des enfants.
                         */
                        if ($test_child) {
                            $html .= '<div class="group-child col-md-offset-1" id="group-child' . $test->id . '">';
                            foreach ($test_child as $k => $v) {
                                $html .= '<div class="form-group">';
                                $selection_child = $repository_selection->findBy(['test' => $v]);
                                if ($v->type->type == 'Question') {
                                    $html .= '<label class="checkbox-inline"><input type="checkbox" class="form-control check-audit box' . $test->id . '" name="tests[' . $v->id . '][check]">' . $v->name . '</label>
';
                                } elseif ($v->type->type == 'Selection') {
                                    $html .= '<label class="checkbox-inline">' . $v->name . '
                                                                                                <select class="form-control check-audit box' . $test->id . '" name="tests[' . $v->id . '][selection]">
                                                                                                    <option value="" selected>Aucun</option>';
                                    foreach ($selection_child as $ke => $va) {
                                        $html .= '<option value="' . $va->name . '">' . $va->name . '</option>';
                                    }
                                    $html .= '</select>
                                      </label>';
                                }
                                $html .= '<span class="glyphicon glyphicon-edit" data-toggle="modal" data-target="#myModal' . $v->id . '"></span>
                                                                                    <div class="modal fade" id="myModal' . $v->id . '" role="dialog">
                                                                                        <div class="modal-dialog modal-lg">
                                                                                            <div class="modal-content">
                                                                                                <div class="modal-header">
                                                                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                                                    <h4 class="modal-title">Informations additionnelles</h4><small>' . $v->name . '</small>
                                                                                                </div>
                                                                                                <div class="modal-body">
                                                                                                    <textarea class="autoExpand form-control" rows="5" name="tests[' . $v->id . '][info]"></textarea>
                                                                                                </div>
                                                                                                <div class="modal-footer">
                                                                                                    <button type="button" class="btn btn-primary" data-dismiss="modal">Fermer</button>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    </div>';
                            }
                            $html .= '</div>';
                        }
                        $html .= '</div>';
                        $array[$test->idPhase->id][$test->name] = $html;
                    }
                    }
                    return new JsonResponse($array);
                /**
                 * Si ce n'est pas la requête AJAX des tests d'infrastructure, retourne la vue pour un nouvel audit.
                 */
            }else {
                $em = $this->getDoctrine()->getManager();
                /**
                 * Requête personnalisée afin de chercher tous les tests qui ne sont pas liés à un test d'infrastrucuture.
                 * Car cette partie démarre un nouvel audit, il faut donc uniquement les tests du modèle de base en attendant les réponses aux tests d'infra.
                 */
                $sql = 'SELECT  * 
                        FROM audit_test_phase 
                        WHERE NOT EXISTS 
                        (SELECT * 
                        FROM tests_infrastructure
                        WHERE audit_test_phase.id = tests_infrastructure.test_phase_id)';
                $array['tests'] =  $em->getConnection()->query($sql)->fetchAll();

                $array['selection'] = $repository_selection->findAll();
                $array['phases'] = $repository_phase->findAll();
                $array['tests_infra'] = $repository_test_infra->findAll();
                $last_audit = $repository_audit->findOneBy([], ['id' => 'DESC']);
                if($last_audit){
                    $array['auditNumber'] = $last_audit->getId();
                    $array['auditNumber'] = $array['auditNumber'] + 1;
                }else{
                    $array['auditNumber'] = 1;
                }
                $array['type'] = $repository_type->findAll();
                $array['size'] = $repository_size->findAll();


                return $this->render('audit/new_audit.html.twig', $array);
            }
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