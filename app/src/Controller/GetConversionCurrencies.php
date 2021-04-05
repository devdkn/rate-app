<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Input\CurrencyConversionsDto;
use App\Dto\Output\CurrencyItem;
use App\Exception\ValidationException;
use App\ResponseBuilder\CurrencyResponseBuilder;
use App\Service\CurrencyService;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GetConversionCurrencies
{
    /**
     * @var CurrencyService
     */
    private $service;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var CurrencyResponseBuilder
     */
    private $responseBuilder;

    /**
     * @param CurrencyService         $service
     * @param ValidatorInterface      $validator
     * @param CurrencyResponseBuilder $responseBuilder
     */
    public function __construct(
        CurrencyService $service,
        ValidatorInterface $validator,
        CurrencyResponseBuilder $responseBuilder
    ) {
        $this->service = $service;
        $this->validator = $validator;
        $this->responseBuilder = $responseBuilder;
    }

    /**
     * @Route("/api/currencies-conversion/{currency}", methods={"GET"})
     * @OA\Get(
     *     tags={"Currencies"},
     *     summary="List conversion companion currencies for a particular application currency",
     * )
     * @OA\Parameter(
     *     name="currency",
     *     in="path",
     *     description="A currency code from the /api/currencies-application endpoint",
     *     required=true,
     *     example="BTC",
     *     schema=@OA\Schema(type="string"),
     * )
     * @OA\Response(
     *     response="200",
     *     description="List of convevrsion companion currencies",
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
     * @param string $currency
     *
     * @return CurrencyItem[]
     */
    public function __invoke(string $currency): array
    {
        $dto = new CurrencyConversionsDto($currency);
        $violations = $this->validator->validate($dto);
        if (\count($violations) > 0) {
            throw new ValidationException($violations);
        }

        return $this->responseBuilder->createResponse($this->service->getAllFor($dto->getCurrencyValue()));
    }
}
