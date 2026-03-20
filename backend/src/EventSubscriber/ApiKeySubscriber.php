<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class ApiKeySubscriber implements EventSubscriberInterface
{
    private string $apiKey;
    private string $env;

    public function __construct(string $apiKey, string $env)
    {
        $this->apiKey = $apiKey;
        $this->env = $env;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if ($this->env === 'test') {
            return;
        }

        $request = $event->getRequest();

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