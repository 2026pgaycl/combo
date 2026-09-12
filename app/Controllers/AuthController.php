<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        $this->view('auth.login', [], layout: null);
    }

    public function login(): void
    {
        $this->verifyCsrf();

        $email = trim((string) Request::input('email'));
        $password = (string) Request::input('password');

        if ($email === '' || $password === '') {
            $this->flash('error', 'Please enter both email and password.');
            $this->redirect('/login');
        }

        if (!Auth::attempt($email, $password)) {
            $this->flash('error', 'Those credentials don\'t match our records.');
            $this->redirect('/login');
        }

        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/login');
    }
}
