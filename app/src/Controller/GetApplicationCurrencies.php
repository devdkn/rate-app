<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Output\CurrencyItem;
use App\ResponseBuilder\CurrencyResponseBuilder;
use App\Service\ApplicationCurrencyService;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

class GetApplicationCurrencies
{
    /**
     * @var ApplicationCurrencyService
     */
    private $applicationCurrencyService;

    /**
     * @var CurrencyResponseBuilder
     */
    private $responseBuilder;

    /**
     * @param ApplicationCurrencyService $service
     * @param CurrencyResponseBuilder    $responseBuilder
     */
    public function __construct(ApplicationCurrencyService $service, CurrencyResponseBuilder $responseBuilder)
    {
        $this->applicationCurrencyService = $service;
        $this->responseBuilder = $responseBuilder;
    }

    /**
     * @Route("/api/currencies-application", methods={"GET"})
     * @OA\Get(
     *     tags={"Currencies"},
     *     summary="List main application currencies",
     * )
     * @OA\Response(
     *     response="200",
     *     description="List of supported application currencies",
     *     @OA\JsonContent(
     *          type="array",
     *          items=@OA\Schema(
     *              type="object",
     *              required={"code", "name"},
     *              @OA\Property(property="code", type="string"),
     *              @OA\Property(property="name", type="string"),
     *          ),
     *     ),
     * )
     *
     * @return CurrencyItem[]
     */
    public function __invoke(): array
    {
        return $this->responseBuilder->createResponse(
            $this->applicationCurrencyService->getSupported()
        );
    }
}
