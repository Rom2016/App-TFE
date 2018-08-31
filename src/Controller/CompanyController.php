<?php
/**
 * Created by PhpStorm.
 * User: Romain
 * Date: 28/08/2018
 * Time: 20:37
 */

namespace App\Controller;

use App\Entity\AuditCompanyResult;
use App\Entity\AuditTestInfrastructure;
use App\Entity\Company;
use App\Entity\AuditPhase;
use App\Entity\AuditTestPhase;
use App\Entity\CompanyInfrastructure;
use App\Entity\ProductCompanySize;
use App\Entity\SolutionFeatures;
use App\Entity\TestSelection;
use App\Entity\TestsInfrastructure;
use App\Entity\TestType;
use Proxies\__CG__\App\Entity\CompanySize;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class CompanyController extends abstractController
{
    /**
     * @Route("/visualiser-audit-entreprise", name="view_company_audit")
     */
    public function viewCompanyAudit()
    {
        $repository_audit_result = $this->getDoctrine()->getRepository(AuditCompanyResult::class);
        $repository_company =  $this->getDoctrine()->getRepository(Company::class);
        $company = $repository_company->findOneBy(['id'=>$_GET['entreprise']]);

        $array = $_SESSION['user']->getAll();
        $array['idCompany'] = $_GET['entreprise'];
        $array['testsSelected'] = $repository_audit_result->findBy(['company' => $company, 'selected'=>true]);
        return $this->render('company/view_company_audit.html.twig', $array);
    }

    /**
     * @Route("/to-do", name="ajax_to_do_list")
     */
    public function toDoChange()
    {
        $repository_audit_result = $this->getDoctrine()->getRepository(AuditCompanyResult::class);
        $repository_test = $this->getDoctrine()->getRepository(AuditTestPhase::class);
        $repository_company = $this->getDoctrine()->getRepository(Company::class);
        $em = $this->getDoctrine()->getManager();
        $test = $repository_test->findOneBy(['id'=>$_POST['id']]);
        $company = $repository_company->findOneBy(['id'=>$_POST['idCompany']]);
        $result_test = $repository_audit_result->findOneBy(['test'=>$test,'company'=>$company]);
        if(isset($_GET['rq'])){
            switch ($_GET['rq']){
                case 'saveNote':
                    $result_test->setNote($_POST['note']);
                    $em->persist($result_test);
                    $em->flush();
                    $json['id'] = $test->getId();
                    $json['name'] = $test->getName();
                    $json['note'] = $_POST['note'];
                    return new JsonResponse($json);
                    break;
                case 'testChange':
                    if($result_test->getDone()){
                        $result_test->setDone(false);
                        $json['done'] = false;
                    }else{
                        $result_test->setDone(true);
                        $json['done'] = true;
                    }
                    $em->persist($result_test);
                    $em->flush();
                    $json['id'] = $test->getId();
                    $json['name'] = $test->getName();

                    return new JsonResponse($json);
                    break;
                case 'getNote':
                    $json['id'] = $result_test->getTest()->getId();
                    $json['note'] = $result_test->getNote();
                    return new JsonResponse($json);
                    break;
                case 'setNote':
                    $json['id'] = $test->getId();
                    $result_test->setNote($_POST['note']);
                    $em->persist($result_test);
                    $em->flush();
                    return new JsonResponse($json);
                    break;
            }
        }
    }
}