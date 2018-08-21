<?php
/**
 * Created by PhpStorm.
 * User: Romain
 * Date: 26/04/2018
 * Time: 13:55
 */

namespace App\Controller;

use App\Entity\AuditCompany;
use App\Entity\AuditTestInfrastructure;
use App\Entity\Company;
use App\Entity\AuditPhase;
use App\Entity\AuditTestPhase;
use App\Entity\ProductCompanySize;
use App\Entity\Solution;
use App\Entity\SolutionFeatures;
use App\Entity\TestSelection;
use App\Entity\TestsInfrastructure;
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
     * @Route("/administration-audit", name="admin_audit", options={"utf8": true})
     */
    public function adminAudit()
    {
        if (isset($_SESSION)) {
            if ($_SESSION['user']->isAdmin) {
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

                $array = $_SESSION['user']->getAll();
                $array['selection'] = $repository_selection->findAll();
                $array['type'] = $repository_test_type->findAll();
                $array['phases'] = $repository_phase->findBy([], ['number' => 'ASC']);
                $array['tests'] = $repository_test->findAll();
                $array['test_type'] = $repository_test_type->findAll();

                return $this->render('audit/administration_audit.html.twig', $array);
            } else {
                /**
                 * Pas les droits admin.
                 */
                return $this->render('error/error_403.html.twig');
            }
        /**
         * Utilisateur non connecté.
         */
        } else {
            return $this->redirectToRoute('homepage');
        }
    }

    /*
     * JQUERY
     */

    /**
     * @Route("/selection-test", name="selection-test", methods="POST")
     */
    public function selectionTest()
    {
        if (isset($_SESSION)) {
            $repository_selection = $this->getDoctrine()->getRepository(TestSelection::class);
            $repository_test = $this->getDoctrine()->getRepository(AuditTestPhase::class);
            $test = $repository_test->findOneBy(['id' => $_POST['id_test']]);
            $entityManager = $this->getDoctrine()->getManager();

            foreach ($_POST['selection'] as $key => $value) {
                if (isset($value['id'])) {
                    $selection = $repository_selection->findOneBy(['id' => $value['id']]);
                    if ($value['name'] != $selection->getName()) {
                        $selection->setName($value['name']);
                    }
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

    public function saveModifPhase()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_phase = $this->getDoctrine()->getRepository(AuditPhase::class);
        $repository_test = $this->getDoctrine()->getRepository(AuditTestPhase::class);
        $repository_test_type = $this->getDoctrine()->getRepository(TestType::class);

        $phase = $repository_phase->findOneBy(['id' => $_GET['id']]);

        if ($phase->getPhaseName() != $_POST['name_phase']) {
            $phase->setPhaseName($_POST['name_phase']);
        }

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
                    } elseif ($v['name']) {
                        $type = $repository_test_type->findOneBy(['type' => $v['type']]);
                        $child = new AuditTestPhase($v['name'], $v['prio'], $phase, $type, $test);
                        $entityManager->persist($child);
                    }
                }
            } else {
                $type = $repository_test_type->findOneBy(['type' => $value['type']]);
                $test = new AuditTestPhase($value['parent'], $value['prio'], $phase, $type, null);
                $entityManager->persist($test);
                $entityManager->flush();
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
     * @Route("/modifier-phase", name="modif_phase", options={"utf8": true})
     */

    public function modifPhase()
    {
        if (isset($_SESSION)) {
            if ($_SESSION['user']->isAdmin) {
                $array = $_SESSION['user']->getAll();
                $phase = $this->getDoctrine()->getRepository(AuditPhase::class)->findOneBy(['id' => $_GET['id']]);
                $array['tests'] = $this->getDoctrine()->getRepository(AuditTestPhase::class)->findBy(['idPhase' => $phase]);
                $array['type'] = $this->getDoctrine()->getRepository(TestType::class)->findAll();
                $array['phase'] = $phase;
                return $this->render('audit/new_phase.html.twig', $array);
            } else {
                return $this->render('error/error_403.html.twig');
            }
        } else {
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     * @Route("/supprimer-test-phase", name="delete_test", methods="POST", options={"utf8": true})
     */
    public function deleteTest()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_test = $this->getDoctrine()->getRepository(AuditTestPhase::class);
        $repository_selection = $this->getDoctrine()->getRepository(TestSelection::class);
        $parent = $repository_test->findOneBy(['id' => $_POST['id']]);
        $child = $repository_test->findBy(['id_parent' => $parent]);
        foreach ($child as $key => $value) {
            $selection = $repository_selection->findBy(['test' => $value]);
            if ($selection) {
                foreach ($selection as $k => $v) {
                    $entityManager->remove($v);
                }
            }
            $entityManager->remove($value);
        }
        $selection = $repository_selection->findBy(['test' => $parent]);
        if ($selection) {
            foreach ($selection as $k => $v) {
                $entityManager->remove($v);
            }
        }
        $entityManager->remove($parent);
        $entityManager->flush();
        $json['content'] = 'Ok';
        return new JsonResponse($json);
    }

    /**
     * @Route("/créer-audit", name="create_audit", options={"utf8": true})
     */
    public function newAudit()
    {
        if (isset($_SESSION)) {
            $repository_test_infra = $this->getDoctrine()->getRepository(AuditTestInfrastructure::class);
            $repository_type = $this->getDoctrine()->getRepository(TestType::class);
            $repository_test = $this->getDoctrine()->getRepository(AuditTestPhase::class);
            $repository_phase = $this->getDoctrine()->getRepository(AuditPhase::class);
            $repository_company = $this->getDoctrine()->getRepository(Company::class);
            $repository_selection = $this->getDoctrine()->getRepository(TestSelection::class);
            $repository_size = $this->getDoctrine()->getRepository(CompanySize::class);

            if(isset($_GET['rq'])){
                $repository_test_infra_audit = $this->getDoctrine()->getRepository(TestsInfrastructure::class);

                $testInfra = $repository_test_infra->findOneBy(['id'=>$_POST['testId']]);
                $tests = $repository_test_infra_audit->findBy(['test_infra'=>$testInfra]);
                return new JsonResponse($tests);
            }
            if(isset($_POST['testId'])){
                $repository_test_infra_audit = $this->getDoctrine()->getRepository(TestsInfrastructure::class);

                $testInfra = $repository_test_infra->findOneBy(['id'=>$_POST['testId']]);
                $tests = $repository_test_infra_audit->findBy(['test_infra'=>$testInfra]);

                foreach($tests as $key => $value){
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
            }else {
                $em = $this->getDoctrine()->getManager();
                $sql = 'SELECT  * 
                        FROM audit_test_phase 
                        WHERE NOT EXISTS 
                        (SELECT * 
                        FROM tests_infrastructure
                        WHERE audit_test_phase.id = tests_infrastructure.test_phase_id)';
                $array = $_SESSION['user']->getAll();
                $array['tests'] =  $em->getConnection()->query($sql)->fetchAll();

                $array['selection'] = $repository_selection->findAll();
                $array['phases'] = $repository_phase->findAll();
                $array['tests_infra'] = $repository_test_infra->findAll();
                $last_id = $repository_company->findOneBy([], ['id' => 'DESC']);
                $array['auditNumber'] = $last_id->getId();
                $array['auditNumber'] = $array['auditNumber'] + 1;
                $array['type'] = $repository_type->findAll();
                $array['size'] = $repository_size->findAll();


                return $this->render('audit/new_audit.html.twig', $array);
            }
        } else {
            return $this->redirectToRoute('homepage');
        }
    }

    public function saveNewPhase()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $phase = new AuditPhase($_POST['name_phase']);
        $entityManager->persist($phase);
        $entityManager->flush();

        $phase->setNumber($phase->getId());
        $entityManager->persist($phase);
        $entityManager->flush();


        foreach ($_POST['test_phase'] as $key => $value) {
            $type = $this->getDoctrine()->getRepository(TestType::class)->findOneBy(['type' => $value['type']]);

            $test_phase = new AuditTestPhase($value['parent'], $value['prio'], $phase, $type, null);
            $entityManager->persist($test_phase);
            $entityManager->flush();
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

    /*
     * JQUERY
     */
    /**
     * @Route("/supprimer-phase-audit", name="delete_audit_phase", options={"utf8": true})
     */
    public function deletePhase()
    {
        $idPhase = $_POST['id'];
        $entityManager = $this->getDoctrine()->getManager();
        $tests = $this->getDoctrine()->getRepository(AuditTestPhase::class)->findBy(['idPhase' => $idPhase]);
        $phase = $this->getDoctrine()->getRepository(AuditPhase::class)->findOneBy(['id' => $idPhase]);


        foreach ($tests as $key => $value) {
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

        return new JsonResponse($_POST);
    }


    /**
     * @Route("/résultat-audit", name="result_audit", options={"utf8": true})
     */

    public function resultAudit()
    {

        $repository_test = $this->getDoctrine()->getRepository(AuditTestPhase::class);
        $repository_phase = $this->getDoctrine()->getRepository(AuditPhase::class);

        $array = $_SESSION['user']->getAll();
        $last_company = $this->getDoctrine()->getRepository(Company::class)->findOneBy([], ['id' => 'DESC']);
        $array['auditNumber'] = $last_company->getId() + 1;
        $array['phases'] = $repository_phase->findAll();
        $array['name_company'] = $_POST['name'];
        $array['phone_company'] = $_POST['phone'];
        $array['email_company'] = $_POST['email'];
        $array['size_company'] = $_POST['size'];

        $avg['prio1'] = 0;
        $avg['prio2'] = 0;
        $avg['prio3'] = 0;
        $i['prio1'] = 0;
        $i['prio2'] = 0;
        $i['prio3'] = 0;
        $points = 0;
        $total_points = 0;
        $test = $repository_test->findAll();

        foreach ($test as $key => $value) {
            if ($value->priority == 1) {
                if (isset($_POST['tests'][$value->getId()]['check']) or isset($_POST['tests'][$value->getId()]['selection']) and $_POST['tests'][$value->getId()]['selection']) {
                    $avg['prio1'] = $avg['prio1'] + 1;
                    $i['prio1'] = $i['prio1'] + 1;
                    $points = $points+3;
                    $total_points = $total_points+3;
                } elseif(isset($_POST['tests'][$value->getId()])) {
                    $i['prio1'] = $i['prio1'] + 1;
                    $array['prio1'][] = $value;
                    $total_points = $total_points+3;
                }
            } elseif ($value->priority == 2) {
                if (isset($_POST['tests'][$value->getId()]['check']) or isset($_POST['tests'][$value->getId()]['selection']) and $_POST['tests'][$value->getId()]['selection']) {
                    $avg['prio2'] = $avg['prio2'] + 1;
                    $i['prio2'] = $i['prio2'] + 1;
                    $points = $points+2;
                    $total_points = $total_points+2;

                } elseif(isset($_POST['tests'][$value->getId()])) {
                    $i['prio2'] = $i['prio2'] + 1;
                    $array['prio2'][] = $value;
                    $total_points = $total_points+2;
                }
            } elseif ($value->priority == 3) {
                if (isset($_POST['tests'][$value->getId()]['check']) or isset($_POST['tests'][$value->getId()]['selection']) and $_POST['tests'][$value->getId()]['selection']) {
                    $avg['prio3'] = $avg['prio3'] + 1;
                    $i['prio3'] = $i['prio3'] + 1;
                    $points = $points+1;
                    $total_points = $total_points+1;
                } elseif(isset($_POST['tests'][$value->getId()])) {
                    $i['prio3'] = $i['prio3'] + 1;
                    $array['prio3'][] = $value;
                    $total_points = $total_points+1;


                }
            }
        }
        $array['avg_prio1'] = number_format((float)$avg['prio1'] / $i['prio1'] * 100, 2, '.', '');
        $array['avg_prio2'] = number_format((float)$avg['prio2'] / $i['prio2'] * 100, 2, '.', '');
        $array['avg_prio3'] = number_format((float)$avg['prio3'] / $i['prio3'] * 100, 2, '.', '');
        $array['avg'] = number_format((float)($points/$total_points)*100, 2, '.', '');
        return $this->render('audit/result_audit.html.twig', $array);
    }

    /**
     * @Route("/voir-audit", name="view_audit", options={"utf8": true})
     */

    public function viewAudits()
    {
        if (isset($_SESSION)) {
            $repository_company = $this->getDoctrine()->getRepository(Company::class);
            $array = $_SESSION['user']->getAll();
            $array['company'] = $repository_company->findAll();

            return $this->render('audit/select_audit.html.twig', $array);
        } else {
            return $this->redirectToRoute('homepage');
        }
    }
    //jquery
    /**
     * @Route("/solution-test", name="get_test_solution", methods="POST" )
     */
    public function getTestSolution()
    {
        $repository_test = $this->getDoctrine()->getRepository(AuditTestPhase::class);
        $repository_size = $this->getDoctrine()->getRepository(CompanySize::class);
        $repository_product_size = $this->getDoctrine()->getRepository(ProductCompanySize::class);
        $repository_solution_features = $this->getDoctrine()->getRepository(SolutionFeatures::class);

        $size_company = explode("<", $_POST['size']);
        $size = $repository_size->findOneBy(['max_size' => $size_company[1]]);
        $test = $repository_test->findOneBy(['id' => $_POST['id']]);
        $array['solutions'] = $repository_product_size->findBy(array('test' => $test,'size'=>$size));
        foreach ($array['solutions'] as $key => $value){
            $array['features'][] = $repository_solution_features->findBy(['solution'=>$value->getProduct()->getSolution()]);
        }
        $array['test'] = $test;
        return $this->render('audit/solutions_test.html.twig',$array);
    }
    //jquery
    /**
     * @Route("/enregistrer-images", name="save_images_test", methods="POST" )
     */
    function saveFiles()
    {
        $repository_test = $this->getDoctrine()->getRepository(AuditTestPhase::class);

        $test = $repository_test->findOneBy(['id'=>$_POST['id']]);
        $auditFolder = 'images/test_pic/'.$_POST['auditNumber'];   //2
        $testFolder = 'images/test_pic/'.$_POST['auditNumber'].'/'.$test->name.'/';   //2
        $path = $_FILES['file']['name'];
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        // if folder doesn't exists, create it
        if(!file_exists($auditFolder) && !is_dir($auditFolder)) {
            mkdir($auditFolder);
        }
        if(!file_exists($testFolder) && !is_dir($testFolder)) {
            mkdir($testFolder);
            $fileName = '1.'.$ext;
        }else {
            $count = scandir($testFolder, 1);
            if ($count) {
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

            if(move_uploaded_file($tempFile, $targetFile)){
                return new Response('ok');
            }; //6
        }


}