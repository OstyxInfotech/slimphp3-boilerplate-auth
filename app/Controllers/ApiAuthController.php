<?php

namespace App\Controllers;

use App\Auth\Jwt;
use Respect\Validation\Validator as v;

class ApiAuthController extends Controller
{
    public function postLogin ($request, $response)
    {
        $validation = $this->validator->validate($request, [
            'email' => v::noWhitespace()->notEmpty()->email(),
            'password' => v::noWhiteSpace()->notEmpty(),
            'trust_device' => v::optional(v::boolVal()),
        ]);

        if ($validation->failed()) {
            return $response->withJson($validation->errors)->withStatus(422);
        }

        $auth = $this->auth->attempt(
            $request->getParam('email'),
            $request->getParam('password')
        );

        if (!$auth) {
            return $response->withJson(['message' => 'Invalid Username/Password'])->withStatus(401);
        }

        $expires_after = $request->getParam('trust_device') ? 31556952 : 28800;

        $res = ['_token' => Jwt::getToken(['email' => $request->getParam('email')], $expires_after)];

        return $response->withJson($res);
    }

//    public function postVerify ($request, $response)
//    {
//        try {
//            $token = $request->getHeader('Authorization')[0];
//            $user = Jwt::verify($token);
//            if (!$user) return $response->withStatus('401');
//            return $response->withJson($user);
//        } catch (\Exception $e) {
//            return $response->withStatus(401)->withJson($e);
//        }
//    }
}