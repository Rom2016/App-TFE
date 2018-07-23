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
        if($_POST){
            switch ($_POST['submit']) {
                case 'newPhase':
                    $this->saveNewPhase();
                    break;
                case 'addTest':
                    $this->addTestPhase();

            }
        }

        $repository_phase = $this->getDoctrine()->getRepository(AuditPhase::class);
        $repository_test = $this->getDoctrine()->getRepository(AuditTestPhase::class);
        $repository_test_type = $this->getDoctrine()->getRepository(TestType::class);


        $array = $_SESSION['user']->getAll();
        $array['phases'] = $repository_phase->findAll();
        $array['tests'] = $repository_test->findAll();
        $array['test_type'] = $repository_test_type->findAll();

        return $this->render('audit/administration_audit.html.twig', $array);
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

        return $this->render('audit/new_audit.html.twig',$array);
    }

    public function saveNewPhase()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $phase = new AuditPhase($_POST['name_phase']);

        $entityManager->persist($phase);
        $entityManager->flush();

        foreach ($_POST['test_phase'] as $key => $value){
            $type = $this->getDoctrine()->getRepository(TestType::class)->findOneBy(['type' => $_POST['type']]);

            $test_phase = new AuditTestPhase($value,$_POST['priority'], $phase, $type);
            $test[$value] = $test_phase;
            $entityManager->persist($test_phase);
        }
        $entityManager->flush();
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

            $entityManager->remove($value);
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
     * @Route("/supprimer-test-phase", name="delete_test_phase", options={"utf8": true})
     */
    public function deleteTestPhase()
    {
        if(isset($_POST)) {
        $entityManager = $this->getDoctrine()->getManager();
        foreach ($_POST as $key => $value) {
            $object = $this->getDoctrine()->getRepository(AuditTestPhase::class)->findOneBy(['id' => $value]);
            $entityManager->remove($object);
        }
        $entityManager->flush();
    }
        $json['content'] = 'Ok';
        return new JsonResponse($json);
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
}