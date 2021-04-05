<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Exception\ValidationException;
use App\ResponseBuilder\ErrorResponseBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionListener implements EventSubscriberInterface
{
    /**
     * @var ErrorResponseBuilder
     */
    private $responseBuilder;

    /**
     * @param ErrorResponseBuilder $responseBuilder
     */
    public function __construct(ErrorResponseBuilder $responseBuilder)
    {
        $this->responseBuilder = $responseBuilder;
    }

    /**
     * {@inheritdoc}
     *
     * @phpstan-return array<string, string>
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onException',
        ];
    }

    /**
     * @param ExceptionEvent $event
     */
    public function onException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if ($exception instanceof ValidationException) {
            $response = $this->responseBuilder->createValidationErrorResponse($exception);
        } else {
            $response = $this->responseBuilder->createGenericErrorResponse($exception);
        }

        $event->setResponse($response);
    }
}
