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
            "Server" => "",
            "X-Content-Type-Options" => "nosniff",
            "X-Frame-Options" => "DENY",
            "X-XSS-Protection" => "1; mode=block",
            "Content-Security-Policy" => "default-src 'none'; frame-ancestors 'none'; sandbox",
        ]);
    }
}
