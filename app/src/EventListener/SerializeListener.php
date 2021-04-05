<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

class SerializeListener implements EventSubscriberInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     *
     * @phpstan-return array<string, string>
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => 'onKernelView',
        ];
    }

    /**
     * @param ViewEvent $event
     */
    public function onKernelView(ViewEvent $event): void
    {
        $controllerResult = $event->getControllerResult();
        if ($controllerResult instanceof Response) {
            return;
        }

        $request = $event->getRequest();

        $requestFormat = $request->getRequestFormat() ?? AddFormatListener::REQUEST_FORMAT;

        $newControllerResult = $this->serializer->serialize($controllerResult, $requestFormat);
        $event->setControllerResult($newControllerResult);

        $event->setResponse(
            new Response(
                $newControllerResult,
                Response::HTTP_OK,
                [
                    'Content-Type' => sprintf('%s; charset=utf-8', $request->getMimeType($requestFormat)),
                ]
            )
        );
    }
}
