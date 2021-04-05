<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Input\RateHistoryDto;
use App\Dto\Output\RateResponseItem;
use App\Exception\ValidationException;
use App\ResponseBuilder\RateResponseBuilder;
use App\Service\HourlyRateService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GetRateHistory
{
    public const SINCE_PARAM = 'since';
    public const UNTIL_PARAM = 'until';

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var HourlyRateService
     */
    private $rateService;

    /**
     * @var RateResponseBuilder
     */
    private $responseBuilder;

    /**
     * @param ValidatorInterface  $validator
     * @param HourlyRateService   $rateService
     * @param RateResponseBuilder $responseBuilder
     */
    public function __construct(
        ValidatorInterface $validator,
        HourlyRateService $rateService,
        RateResponseBuilder $responseBuilder
    ) {
        $this->validator = $validator;
        $this->rateService = $rateService;
        $this->responseBuilder = $responseBuilder;
    }

    /**
     * @Route("/api/rates/{from}/{to}/hourly", methods={"GET"})
     * @OA\Get(
     *     tags={"Rates"},
     *     summary="List hourly rates for a particular currency pair",
     * )
     * @OA\Parameter(
     *     name="from",
     *     in="path",
     *     description="A currency code from the /api/currencies-application endpoint (or from the /api/currencies-conversion for an inverse conversion)",
     *     example="BTC",
     * )
     * @OA\Parameter(
     *     name="to",
     *     in="path",
     *     description="A currency code from the /api/currencies-conversion endpoint (or from the /api/currencies-application for an inverse conversion)",
     *     example="USD",
     * )
     * @OA\Parameter(
     *     name=GetRateHistory::SINCE_PARAM,
     *     in="query",
     *     description="Get rates since (inclusive). The format is ISO-8601 date-time with milliseconds and Zulu timezone",
     *     required=false,
     *     example="2021-04-01T00:00:00.0Z",
     *     schema=@OA\Schema(type="string", format="date-time"),
     * )
     * @OA\Parameter(
     *     name=GetRateHistory::UNTIL_PARAM,
     *     in="query",
     *     description="Get rates until (inclusive). The format is ISO-8601 date-time with milliseconds and Zulu timezone",
     *     required=false,
     *     example="2021-05-01T00:00:00.0Z",
     *     schema=@OA\Schema(type="string", format="date-time"),
     * )
     * @OA\Response(
     *     response="200",
     *     description="Hourly rate history",
     *     @OA\JsonContent(
     *          type="array",
     *          items=@OA\Schema(
     *              type="object",
     *              required={"time", "rate"},
     *              @OA\Property(property="date", type="string", format="date-time"),
     *              @OA\Property(property="rate", type="number", format="double"),
     *          ),
     *     ),
     * )
     *
     * @param string  $from
     * @param string  $to
     * @param Request $request
     *
     * @return RateResponseItem[]
     *
     * @throws ValidationException
     */
    public function __invoke(string $from, string $to, Request $request): array
    {
        $dto = new RateHistoryDto(
            $from,
            $to,
            $request->query->get(self::SINCE_PARAM),
            $request->query->get(self::UNTIL_PARAM)
        );
        $violations = $this->validator->validate($dto);
        if (\count($violations) > 0) {
            throw new ValidationException($violations);
        }

        $res = $this->rateService->findRates(
            $dto->getCurrencyFrom(),
            $dto->getCurrencyTo(),
            $dto->getSince(),
            $dto->getUntil()
        );
        return $this->responseBuilder->createResponse($res);
    }
}
