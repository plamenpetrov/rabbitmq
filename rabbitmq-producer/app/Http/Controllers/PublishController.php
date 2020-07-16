<?php

namespace App\Http\Controllers;

use App\Jobs\PublishToRabbitMQ;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PublishController extends Controller
{
    /**
     *
     */
    public function index()
    {
        //TO DO: static data to be replaced with data from request
        $message = [
            'name' => 'Plamen Petrov',
            'email' => 'plamen.petrov@rabbitmq.com'
        ];

        PublishToRabbitMQ::dispatch(\GuzzleHttp\json_encode($message));

        return new JsonResponse(null, Response::HTTP_OK);
    }
}