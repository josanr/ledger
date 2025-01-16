<?php

declare(strict_types=1);

namespace App\Infrastructure\Transactions\Api;

use App\Application\Exceptions\StoreException;
use App\Application\UseCases\Transactions\CreateTransactionUseCase;
use App\Infrastructure\Transactions\Mappers\TransactionMapper;
use App\Infrastructure\Transactions\Requests\CreateTransactionRequest;
use App\Infrastructure\Transactions\Requests\TransactionItemResponse;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class TransactionsController extends AbstractController
{

    public function __construct(
        private readonly CreateTransactionUseCase $createTransactionUseCase,
        private readonly TransactionMapper $transactionMapper
    )
    {
    }

    #[Route('/transactions', methods: ['POST'], format: 'json')]
    #[OA\Post(
        requestBody: new OA\RequestBody(
            content: new Model(type: CreateTransactionRequest::class)
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: TransactionItemResponse::class)
    )]
    #[OA\Response(
        response: 417,
        description: 'Could not store transaction'
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
        #[MapRequestPayload] CreateTransactionRequest $createTransactionRequest
    ): Response {
        try {
            $transaction = $this->createTransactionUseCase->execute($createTransactionRequest);
            $response = $this->transactionMapper->mapToResponse($transaction);
            return new JsonResponse($response);
        } catch (StoreException $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_EXPECTATION_FAILED);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
