<?php

declare(strict_types=1);

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Infakt\{Model, Collections, Repository\BankAccountRepository, Repository\VatRateRepository};

class DemoController extends AbstractController
{
    /**
     * @var BankAccountRepository
     */
    private $bankAccountRepository;
    /**
     * @var VatRateRepository
     */
    private $vatRateRepository;

    /**
     * DemoController constructor.
     * @param BankAccountRepository $bankAccountRepository
     * @param VatRateRepository $vatRateRepository
     */
    public function __construct(BankAccountRepository $bankAccountRepository, VatRateRepository $vatRateRepository)
    {
        $this->bankAccountRepository = $bankAccountRepository;
        $this->vatRateRepository = $vatRateRepository;
    }

    /**
     * @Route("/", name="dashboard")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dashboardAction()
    {
        $bankAccounts = $this->bankAccountRepository->matching(new Collections\Criteria([], [
            new Collections\SortClause('default', Collections\SortClause::ORDER_DESC)
        ]));

        return $this->render('default/dashboard.html.twig', [
            'bankAccounts' => $bankAccounts,
            'vatRates' => $this->vatRateRepository->getAll(),
        ]);
    }
}
