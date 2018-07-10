<?php
/**
 * Created by PhpStorm.
 * User: Romain
 * Date: 26/04/2018
 * Time: 13:55
 */

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AuditController extends AbstractController
{
    /**
     * @Route("/créer-audit", name="create_audit", options={"utf8": true})
     */
    public function newAudit()
    {
        $array = $_SESSION['user']->getAll();

        return $this->render('audit/new_audit.html.twig',$array);
    }

    /**
     * @Route("/nouvelle-phase-audit", name="new_phase", options={"utf8": true})
     */
    public function saveNewPhase()
    {


    }

}