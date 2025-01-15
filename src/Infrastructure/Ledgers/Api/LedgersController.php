<?php

declare(strict_types=1);

namespace App\Infrastructure\Ledgers\Api;

use App\Application\Exceptions\StoreException;
use App\Application\UseCases\Ledgers\CreateLedgerUseCase;
use App\Application\UseCases\Ledgers\GetLedgersUseCase;
use App\Infrastructure\Ledgers\Api\Mappers\LedgerMapper;
use App\Infrastructure\Ledgers\Api\Requests\CreateLedgerRequest;
use App\Infrastructure\Ledgers\Api\Response\LedgerItemResponse;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class LedgersController extends AbstractController
{
    public function __construct(
        private readonly CreateLedgerUseCase $createLedgerUseCase,
        private readonly GetLedgersUseCase $getLedgersUseCase,
        private readonly LedgerMapper $ledgerMapper
    ) {
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
        content: new Model(type: LedgerItemResponse::class)
    )]
    #[OA\Response(
        response: 417,
        description: 'Could not create ledger'
    )]
    #[OA\Response(
        response: 422,
        description: 'Request is invalid'
    )]
    #[OA\Response(
        response: 500,
        description: 'Unexpected exception'
    )]
    public function create(
        #[MapRequestPayload] CreateLedgerRequest $createLedgerRequest
    ): Response {


        try {
            $ledger = $this->createLedgerUseCase->execute($createLedgerRequest);
            $response = $this->ledgerMapper->mapToResponse($ledger);
            return new JsonResponse($response, Response::HTTP_OK);
        } catch (StoreException $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_EXPECTATION_FAILED);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/ledgers', methods: ['GET'], format: 'json')]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: LedgerItemResponse::class))
        )
    )]
    #[OA\Response(
        response: 500,
        description: 'Unexpected exception'
    )]
    public function get(): Response
    {
        try {
            $ledgers = $this->getLedgersUseCase->execute();
            $response = [];
            foreach ($ledgers as $ledger) {
                $response[] = $this->ledgerMapper->mapToResponse($ledger);
            }
            return new JsonResponse($response, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
