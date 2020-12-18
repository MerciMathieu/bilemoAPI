<?php

namespace App\Classes;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ExceptionHandler extends AbstractController
{
    public function throwJsonNotFoundException(string $message = "Resource was not found"): Response
    {
        $exception = $this->createNotFoundException($message);
        return new Response(
            $exception->getMessage(),
            $exception->getStatusCode(),
            ["ContentType" => "application/json"]
        );
    }
}
