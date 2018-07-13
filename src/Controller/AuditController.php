<?php
/**
 * Created by PhpStorm.
 * User: Romain
 * Date: 26/04/2018
 * Time: 13:55
 */

namespace App\Controller;

use App\Entity\User;
use App\Entity\AuditPhase;
use App\Entity\AuditTestPhase;
use http\Env\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuditController extends AbstractController
{

    /**
     * @Route("/administration-audit", name="admin_audit", options={"utf8": true})
     */
    public function viewAdminAudit()
    {
        $repository_phase = $this->getDoctrine()->getRepository(AuditPhase::class);
        $repository_test = $this->getDoctrine()->getRepository(AuditTestPhase::class);

        $array = $_SESSION['user']->getAll();
        $array['phases'] = $repository_phase->findAll();
        $array['tests'] = $repository_test->findAll();
        return $this->render('audit/administration_audit.html.twig',$array);

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

    /**
     * @Route("/nouvelle-phase-audit", name="new_phase", options={"utf8": true})
     */
    public function saveNewPhase()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $phase = new AuditPhase($_POST['name_phase']);

        $entityManager->persist($phase);
        $entityManager->flush();

        foreach ($_POST['test_phase'] as $key => $value){
            $test_phase = new AuditTestPhase($value,'1', $phase);
            $entityManager->persist($test_phase);
            $entityManager->flush();
        }

        return new JsonResponse($_POST['test_phase']);
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

    /**
     * @Route("/ajouter-test-audit", name="add_test_phase", options={"utf8": true})
     */
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

        $json['testAdd'] = 'Ok';

        return new JsonResponse($json);

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
}