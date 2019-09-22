<?php

namespace App\Controllers;

use Respect\Validation\Validator as v;

class EmployeesController extends Controller
{
    public function store ($request, $response, array $args)
    {
        $validation=$this->validator->validate($request, [
            "name" => v::notEmpty()->alpha()
        ]);

        if($validation->failed()){
            return $response->withJson($validation->errors);
        }
    }
}
