<?php

declare(strict_types=1);

namespace App\Infrastructure\Balances\Api;

use App\Application\Exceptions\NotFoundException;
use App\Application\UseCases\Balances\GetBalanceUseCase;
use App\Domain\Balance;
use App\Infrastructure\Balances\Mappers\BalanceMapper;
use App\Infrastructure\Balances\Response\BalanceItemResponse;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

class BalanceController extends AbstractController
{
    public function __construct(
        private readonly GetBalanceUseCase $getBalanceUseCase,
        private readonly BalanceMapper $balanceMapper
    ) {
    }

    #[OA\Response(
        response: 200,
        description: 'Balances for the selected Ledger',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: BalanceItemResponse::class))
        )
    )]
    #[OA\Response(response: 417, description: 'Not found entity')]
    #[OA\Response(response: 500, description: 'Unexpected exception')]
    #[Route('/balances/{ledgerId}', methods: ['GET'], format: 'json')]
    public function index(Uuid $ledgerId): Response
    {
        try {
            $balances = $this->getBalanceUseCase->execute($ledgerId);
            $response = array_map(
                fn (Balance $balance) => $this->balanceMapper->mapToResponse($balance),
                $balances->toArray()
            );
            return new JsonResponse($response);
        } catch (NotFoundException $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
            ], Response::HTTP_EXPECTATION_FAILED);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
