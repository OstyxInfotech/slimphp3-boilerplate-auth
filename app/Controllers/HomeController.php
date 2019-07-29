<?php

namespace App\Controllers;


class HomeController extends Controller
{
    public function index($request, $response, $args)
    {
        return $this->view->render($response, 'home/index.twig');
    }
}