<?php

declare(strict_types=1);

namespace App\ResponseBuilder;

use App\Exception\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationInterface;

class ErrorResponseBuilder
{
    /**
     * @param \Throwable $throwable
     *
     * @return JsonResponse
     */
    public function createGenericErrorResponse(\Throwable $throwable): JsonResponse
    {
        return new JsonResponse(
            ['code' => $throwable->getCode(), 'message' => $throwable->getMessage()],
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    /**
     * @param ValidationException $exception
     *
     * @return JsonResponse
     */
    public function createValidationErrorResponse(ValidationException $exception): JsonResponse
    {
        $violations = [];
        /** @var ConstraintViolationInterface $violation */
        foreach ($exception->getViolations() as $violation) {
            $violations[] = [
                'code' => $violation->getCode(),
                'at' => $violation->getPropertyPath(),
                'message' => $violation->getMessage(),
            ];
        }

        return new JsonResponse(
            [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
                'violations' => $violations,
            ],
            Response::HTTP_BAD_REQUEST
        );
    }
}
