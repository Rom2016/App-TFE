<?php
/**
 * Created by PhpStorm.
 * User: Romain
 * Date: 07/12/2018
 * Time: 21:42
 */

namespace App\Controller;

use App\Entity\AppUser;
use App\Entity\AuditCompanyResult;
use App\Entity\AuditTestInfrastructure;
use App\Entity\AuditPhase;
use App\Entity\AuditTestPhase;
use App\Entity\IntAudit;
use App\Entity\IntCustomer;
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

class IntAuditController extends AbstractController
{
    /**
     * @Route("/créer-audit-interne", name="create_int_audit", options={"utf8": true})
     */
    public function createAudit()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $date = date('Y-m-d H:i:s');
        $date = new \DateTime($date);

        $repository_audit = $this->getDoctrine()->getRepository(IntAudit::class);
        $new_customer = new IntCustomer($_POST['customer'], $_POST['responsable']);
        $new_audit = new IntAudit($this->getUser(),$new_customer, $date, false, $_POST['audit-name'] );

        $entityManager->persist($new_customer);
        $entityManager->persist($new_audit);
        $entityManager->flush();

    }
}