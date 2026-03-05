<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class ApiKeySubscriber implements EventSubscriberInterface
{
    private string $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        // Ignorer la doc et la route /api
        if ($request->getPathInfo() === '/api' || str_starts_with($request->getPathInfo(), '/api/docs')) {
            return;
        }

        $key = $request->headers->get('x-api-key');

        if ($key !== $this->apiKey) {
            $event->setResponse(new JsonResponse([
                'error' => 'Invalid API key'
            ], 401));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.request' => 'onKernelRequest',
        ];
    }
}