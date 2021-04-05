<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class AddFormatListener implements EventSubscriberInterface
{
    public const REQUEST_FORMAT = 'json';

    /**
     * {@inheritdoc}
     *
     * @phpstan-return array<string, string>
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $event->getRequest()->setRequestFormat(self::REQUEST_FORMAT);
    }
}
