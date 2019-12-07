<?php

declare(strict_types=1);

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Infakt\{Model, Collections, Repository\ClientRepository};

class ClientController extends AbstractController
{
    /**
     * @var ClientRepository
     */
    private $clientRepository;

    /**
     * ClientController constructor.
     * @param ClientRepository $clientRepository
     */
    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    /**
     * @Route("/clients", name="client_list")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function clientListAction(Request $request): Response
    {
        $limit = (int)$request->query->get('limit', 10);
        $page = (int)$request->query->get('page', 1);
        $sort = $request->query->get('sort', ['column' => 'company_name', 'order' => Collections\SortClause::ORDER_ASC]);

        $clients = $this->clientRepository->matching(new Collections\Criteria(
            [],
            [new Collections\SortClause($sort['column'], $sort['order'])],
            ($page - 1) * $limit,
            $limit
        ));

        return $this->render('default/clients.html.twig', [
            'clients' => $clients,
            'query_params' => [
                'filters' => [],
                'page' => $page,
                'limit' => $limit,
                'sort' => $sort
            ],
        ]);
    }

    /**
     * @Route("/clients/{id}", name="client_get")
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function clientGetAction(int $id): Response
    {
        $client = $this->clientRepository->get($id);

        if (!$client instanceof Model\Client) {
            throw new NotFoundHttpException("Client (ID: $id) does not exist.");
        }

        return $this->render('default/client.html.twig', [
            'client' => $client,
        ]);
    }

    /**
     * @Route("/clients/{id}/delete", name="client_delete")
     *
     * @param int $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function clientDeleteAction(int $id, Request $request)
    {
        $client = $this->clientRepository->get($id);

        if (!$client instanceof Model\Client) {
            throw new NotFoundHttpException("Client (ID: $id) does not exist.");
        }

        $this->clientRepository->delete($client);

        if ($request->headers->has('referer')) {
            return new RedirectResponse($request->headers->get('referer'));
        }

        return new RedirectResponse($this->generateUrl('client_list'));
    }
}
