<?php

declare(strict_types=1);

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Infakt\{Model, Collections, Repository\InvoiceRepository};

class InvoiceController extends AbstractController
{
    /**
     * @var InvoiceRepository
     */
    private $invoiceRepository;

    /**
     * InvoiceController constructor.
     * @param InvoiceRepository $infakt
     */
    public function __construct(InvoiceRepository $infakt)
    {
        $this->invoiceRepository = $infakt;
    }

    /**
     * @Route("/invoices", name="invoice_list")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function invoiceListAction(Request $request): Response
    {
        $limit = $request->query->getInt('limit', 10);
        $page = $request->query->getInt('page', 1);
        $sort = $request->query->get('sort', ['column' => 'invoice_date', 'order' => Collections\SortClause::ORDER_DESC]);

        $invoices = $this->invoiceRepository->matching(new Collections\Criteria(
            [],
            [new Collections\SortClause($sort['column'], $sort['order'])],
            ($page - 1) * $limit,
            $limit
        ));

        return $this->render('default/invoices.html.twig', [
            'invoices' => $invoices,
            'query_params' => [
                'filters' => [],
                'page' => $page,
                'limit' => $limit,
                'sort' => $sort
            ],
        ]);
    }

    /**
     * @Route("/invoices/{id}", name="invoice_get")
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function invoiceGetAction(int $id): Response
    {
        $invoice = $this->invoiceRepository->get($id);

        if (!$invoice instanceof Model\Invoice) {
            throw new NotFoundHttpException("Invoice (ID: $id) does not exist.");
        }

        return $this->render('default/invoice.html.twig', [
            'invoice' => $invoice,
        ]);
    }

    /**
     * @Route("/invoices/{id}/delete", name="invoice_delete")
     *
     * @param int $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function invoiceDeleteAction(int $id, Request $request)
    {
        $invoice = $this->invoiceRepository->get($id);

        if (!$invoice instanceof Model\Invoice) {
            throw new NotFoundHttpException("Invoice (ID: $id) does not exist.");
        }

        $this->invoiceRepository->delete($invoice);

        if ($request->headers->has('referer')) {
            return new RedirectResponse($request->headers->get('referer'));
        }

        return new RedirectResponse($this->generateUrl('invoice_list'));
    }
}
