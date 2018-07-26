<?php
/**
 * Created by PhpStorm.
 * User: Romain
 * Date: 26/04/2018
 * Time: 13:55
 */

namespace App\Controller;

use App\Entity\AuditCompany;
use App\Entity\Company;
use App\Entity\AuditPhase;
use App\Entity\AuditTestPhase;
use App\Entity\TestType;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuditController extends AbstractController
{

    /**
     * @Route("/administration-audit", name="admin_audit", options={"utf8": true})
     */
    public function adminAudit()
    {

        if ($_POST) {
            switch ($_POST['submit']) {
                case 'newPhase':
                    $this->saveNewPhase();
                    //$results = print_r($_POST, true);
                    //return new Response($results);
                    break;
                case 'addTest':
                    $this->addTestPhase();
                    break;
                case 'switchPhases':
                    $this->switchPhases();
                    //foreach ($_POST['name_phase'] as $key => $value){
                    // $array[]['number'] = $value[0];
                    //}
                    //$results = print_r($array,true);
                    //return new Response($results);
                    //$results = print_r($_POST['name_phase'], true);
                    //return new Response($results);
                    break;
                case 'submitModifPhase':
                    $this->saveModifPhase();
                    break;
            }
        }

        $repository_phase = $this->getDoctrine()->getRepository(AuditPhase::class);
        $repository_test = $this->getDoctrine()->getRepository(AuditTestPhase::class);
        $repository_test_type = $this->getDoctrine()->getRepository(TestType::class);


        $array = $_SESSION['user']->getAll();
        $array['type'] = $repository_test_type->findAll();
        $array['phases'] = $repository_phase->findBy([], ['number' => 'ASC']);
        $array['tests'] = $repository_test->findAll();
        $array['test_type'] = $repository_test_type->findAll();
        return $this->render('audit/administration_audit.html.twig', $array);
    }

    public function saveModifPhase()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_phase = $this->getDoctrine()->getRepository(AuditPhase::class);
        $repository_test = $this->getDoctrine()->getRepository(AuditTestPhase::class);
        $repository_test_type = $this->getDoctrine()->getRepository(TestType::class);

        $phase = $repository_phase->findOneBy(['id' => $_GET['id']]);

        if($phase->getPhaseName() != $_POST['name_phase']){
            $phase->setPhaseName($_POST['name_phase']);
        }

        foreach($_POST['test_phase'] as $key => $value) {
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
                    }elseif($v['name']){
                        $type = $repository_test_type->findOneBy(['type' => $v['type']]);
                        $child = new AuditTestPhase($v['name'],$v['prio'],$phase,$type,$test);
                        $entityManager->persist($child);
                    }
                }
            }else{
                $type = $repository_test_type->findOneBy(['type' => $value['type']]);
                $test = new AuditTestPhase($value['parent'],$value['prio'],$phase,$type,null);
                $entityManager->persist($test);
                $entityManager->flush();
                foreach ($value['child'] as $k => $v){
                    if($v['name']){
                        $type = $repository_test_type->findOneBy(['type' => $v['type']]);
                        $child = new AuditTestPhase($v['name'],$v['prio'],$phase,$type,$test);
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
        $array = $_SESSION['user']->getAll();
        $phase = $this->getDoctrine()->getRepository(AuditPhase::class)->findOneBy(['id' => $_GET['id']]);
        $array['tests'] = $this->getDoctrine()->getRepository(AuditTestPhase::class)->findBy(['idPhase' => $phase]);
        $array['type'] = $this->getDoctrine()->getRepository(TestType::class)->findAll();
        $array['phase'] = $phase;

        return $this->render('audit/new_phase.html.twig',$array) ;

    }

    /**
     * @Route("/supprimer-test-phase", name="delete_test", methods="POST", options={"utf8": true})
     */
    public function deleteTest()
    {
        if(isset($_SESSION)) {

            $entityManager = $this->getDoctrine()->getManager();
            $repository_test = $this->getDoctrine()->getRepository(AuditTestPhase::class);
            $parent = $repository_test->findOneBy(['id' => $_POST['id']]);
            $child = $repository_test->findBy(['id_parent' => $parent]);
            foreach ($child as $key => $value) {
                $entityManager->remove($value);
            }
            $entityManager->flush();
            $entityManager->remove($parent);
            $entityManager->flush();
        }
        $json['content'] = 'Ok';
        return new JsonResponse($json);
    }

    /**
     * @Route("/créer-audit", name="create_audit", options={"utf8": true})
     */
    public function newAudit()
    {
        $array = $_SESSION['user']->getAll();
        $repository_phase = $this->getDoctrine()->getRepository(AuditPhase::class);
        $repository_test = $this->getDoctrine()->getRepository(AuditTestPhase::class);

        $array['phases'] = $repository_phase->findAll();
        $array['tests'] = $repository_test->findAll();

        return $this->render('audit/new_audit.html.twig', $array);
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

    public function switchPhases()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository_phase = $this->getDoctrine()->getRepository(AuditPhase::class);
        $array['phases'] = $repository_phase->findBy([], ['number' => 'ASC']);

        foreach ($_POST['name_phase'] as $key => $value) {
            foreach ($array['phases'] as $k => $v)
                if ($v->getPhaseName() == $k) {
                    $v->setNumber($array['phases'][$k]->getNumber());
                }
            $entityManager->flush();
        }
    }

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
            if($value->getIdParent()) {
                $entityManager->remove($value);
            }else{
                $array[] = $value;
            }
        }

        if(isset($array)) {
            foreach ($array as $key => $value) {
                $entityManager->remove($value);
            }
        }
        $entityManager->remove($phase);
        $entityManager->flush();

        return new JsonResponse($_POST);

    }


    public function addTestPhase()
    {
        $idPhase = $_POST['idPhase'];
        $entityManager = $this->getDoctrine()->getManager();
        $phase = $this->getDoctrine()->getRepository(AuditPhase::class)->findOneBy(['id' => $idPhase]);



        foreach ($_POST['test_phase'] as $key => $value){
            $test_phase = new AuditTestPhase($value,'1', $phase);
            $entityManager->persist($test_phase);
            $entityManager->flush();
        }
    }

    /**
     * @Route("/résultat-audit", name="result_audit", options={"utf8": true})
     */

    public function resultAudit()
    {
        if(isset($_POST)) {
            $company = new Company($_POST['name'],$_POST['phone'],$_POST['email']);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($company);
            $entityManager->flush();


            foreach ($_POST['id'] as $key => $value){
                $test = $this->getDoctrine()->getRepository(AuditTestPhase::class)->findOneBy(['id' => $value]);
                $auditCompany = new AuditCompany($company,$test);
                $entityManager->persist($auditCompany);
            }
            $entityManager->flush();
        }
    }

    /**
     * @Route("/voir-audit", name="view_audit", options={"utf8": true})
     */

    public function viewAudits()
    {
        $repository_company = $this->getDoctrine()->getRepository(Company::class);
        $array = $_SESSION['user']->getAll();
        $array['company'] = $repository_company->findAll();


        return $this->render('audit/select_audit.html.twig', $array);
    }
    /**
     * @Route("/voir-audit", name="view_audit", options={"utf8": true})
     */

}