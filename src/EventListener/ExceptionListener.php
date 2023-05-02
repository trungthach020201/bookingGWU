<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Twig\Environment;

class ExceptionListener
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        // // get the exception object from the event
        // $exception = $event->getThrowable();

        // // check if the exception is a ResourceNotFoundException
        // if ($exception instanceof NotFoundHttpException) {
        //     // render the 404 page using Twig

        //     $response = new Response($this->twig->render('error/error404.html.twig'), 404);

        // }
        // else{
        //     $response = new Response($this->twig->render('error/error500.html.twig'), 500);
        // }

        // // set the response object to the event
        // $event->setResponse($response);
    }
}