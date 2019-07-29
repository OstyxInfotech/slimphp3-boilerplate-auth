<?php

namespace  App\Controllers;

use App\Models\User;
use Respect\Validation\Validator as v;

class AuthController extends Controller
{
    public function getLogin($request, $response)
    {
        $this->view->render($response, 'auth/login.twig');
    }

    public function postLogin($request, $response)
    {
        $auth=$this->auth->attempt(
            $request->getParam('email'),
            $request->getParam('password')
        );

        if(!$auth){
            $this->flash->addMessage('error', 'Invalid Username/Password');
            return $response->withRedirect($this->router->pathFor('auth.login'));
        }

        return $response->withRedirect($this->router->pathFor('home'));
    }
    public function getRegister($request, $response)
    {
        return $this->view->render($response, 'auth/register.twig');
    }

    public function postRegister($request, $response)
    {
        $validation=$this->validator->validate($request, [
            'email' => v::noWhitespace()->notEmpty()->email()->emailAvailable(),
            'name' => v::notEmpty()->alpha(),
            'password' => v::noWhiteSpace()->notEmpty(),
        ]);

        if($validation->failed()){
            return $response->withRedirect($this->router->pathFor('auth.register'));
        }
        $user=User::create([
            'email' => $request->getParam('email'),
            'name' => $request->getParam('name'),
            'password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT),
        ]);

        $this->flash->addMessage('success', 'You have registered');

        $this->auth->attempt($user->email, $request->getParam('password'));

        return $response->withRedirect($this->router->pathFor('home'));
    }

    public function getLogout($request, $response)
    {
        $this->auth->logout();

        return $response->withRedirect($this->router->pathFor('auth.login'));
    }
}