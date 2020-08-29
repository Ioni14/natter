<?php

namespace App\Listener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

class SecurityHeaderListener
{
    public function __invoke(ResponseEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $response = $event->getResponse();
        $response->headers->add([
            "X-Content-Type-Options" => "nosniff",
            "X-Frame-Options" => "DENY",
            "X-XSS-Protection" => "0",
            "Cache-Control" => "no-store",
            "Content-Security-Policy" => "default-src 'none'; frame-ancestors 'none'; sandbox",
            "Server" => "",
        ]);
    }
}
