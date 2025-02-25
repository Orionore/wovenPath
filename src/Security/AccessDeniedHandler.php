<?php

namespace App\Security;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    public function handle(Request $request, \Exception $accessDeniedException): Response
    {
        return new Response(
            $this->renderErrorPage(),
            403 // Code HTTP 403 (forbidden)
        );
    }

    private function renderErrorPage(): string
    {
        return 'exception/error.html.twig';
    }
}