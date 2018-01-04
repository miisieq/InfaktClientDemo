<?php

declare(strict_types=1);

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Infakt\{Model, Collections};

class DemoController extends Controller
{
    /**
     * @Route("/", name="dashboard")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dashboardAction()
    {
        $bankAccounts = $this->get('infakt')->getRepository(Model\BankAccount::class)->matching(new Collections\Criteria([], [
            new Collections\SortClause('default', Collections\SortClause::ORDER_DESC)
        ]));

        return $this->render('default/dashboard.html.twig', [
            'bankAccounts' => $bankAccounts,
            'vatRates' => $this->get('infakt')->getRepository(Model\VatRate::class)->getAll(),
        ]);
    }
}
