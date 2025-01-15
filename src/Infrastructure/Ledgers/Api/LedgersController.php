<?php

declare(strict_types=1);

namespace App\Infrastructure\Ledgers\Api;

use App\Infrastructure\Ledgers\Requests\CreateLedgerRequest;
use App\Infrastructure\Ledgers\Requests\CreateLedgerResponse;
use \Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

class LedgersController extends AbstractController
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    #[Route('/ledgers', methods: ['POST'], format: 'json')]
    #[OA\Post(
        requestBody: new OA\RequestBody(
            content: new Model(type: CreateLedgerRequest::class)
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: CreateLedgerResponse::class)
    )]
    public function index(
        #[MapRequestPayload] CreateLedgerRequest $createLedgerRequest
    ): Response
    {
        $this->logger->info(sprintf('Ledgers index %s', $createLedgerRequest->name));
        $response = new CreateLedgerResponse();
        $response->id = Uuid::v7();
        return new JsonResponse($response, Response::HTTP_OK);
    }
}
