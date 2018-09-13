<?php
/**
 * Created by PhpStorm.
 * User: Romain
 * Date: 28/08/2018
 * Time: 20:37
 */

namespace App\Controller;

use App\Entity\AuditCompany;
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

        $repository_audit =  $this->getDoctrine()->getRepository(AuditCompany::class);
        $audit = $repository_audit->findOneBy(['id'=> $_GET['audit']]);
        $this->denyAccessUnlessGranted('AUDIT_RO', $audit);

        $repository_audit_result = $this->getDoctrine()->getRepository(AuditCompanyResult::class);

        $array['testsSelected'] = $repository_audit_result->findBy(['audit' => $audit , 'selected'=>true]);
        $tests = $repository_audit_result->findBy(['audit' => $audit]);
        $array['passed1'] = 0;
        $array['passed2'] = 0;
        $array['passed3'] = 0;
        $picDir = 'images/test_pic/'.$audit->getId().'/';
        foreach ($tests as $key => $value){
            if($value->getTest()->priority == 1) {
                $array['prio1'][$value->getTest()->getName()] = $value;
                if (file_exists($picDir.$value->getTest()->getId())){
                    $array['pic'][$value->getTest()->getName()] =  array_slice(scandir($picDir.$value->getTest()->getId()), 2);
                }
                if ($value->getPassed())
                    $array['passed1'] = $array['passed1'] + 1;
            }
            if($value->getTest()->priority == 2) {
                $array['prio2'][$value->getTest()->getName()] = $value;
                if (file_exists($picDir.$value->getTest()->getId())){
                    $array['pic'][$value->getTest()->getName()] =  array_slice(scandir($picDir.$value->getTest()->getId()), 2);
                }
                if ($value->getPassed())
                    $array['passed2'] = $array['passed2'] + 1;
            }
            if($value->getTest()->priority == 3) {
                $array['prio3'][$value->getTest()->getName()] = $value;
                if (file_exists($picDir.$value->getTest()->getId())){
                    $array['pic'][$value->getTest()->getName()] =  array_slice(scandir($picDir.$value->getTest()->getId()), 2);
                }

                if ($value->getPassed())
                    $array['passed3'] = $array['passed3'] + 1;
            }
        }
        $array['count1'] = sizeof($array['prio1']);
        $array['count2'] = sizeof($array['prio2']);
        $array['count3'] = sizeof($array['prio3']);

        $array['avg1'] = number_format((float)$array['passed1'] / $array['count1'] * 100, 2, '.', '');
        $array['avg2'] = number_format((float)$array['passed2'] / $array['count2'] * 100, 2, '.', '');
        $array['avg3'] = number_format((float)$array['passed3'] / $array['count3'] * 100, 2, '.', '');

        $totalPoints = ($array['count1']*3)+($array['count2']*2)+($array['count3']);
        $points = ($array['passed1']*3)+($array['passed2']*2)+($array['passed3']);

        $array['score'] = number_format((float)$points / $totalPoints * 100, 2, '.', '');

        $array['idCompany'] = $audit->getId();
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
                    $json['note'] = $result_test->getNote();
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