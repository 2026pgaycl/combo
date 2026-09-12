<?php

namespace App\Core;

abstract class Controller
{
    protected function view(string $view, array $data = [], ?string $layout = 'layouts.app'): void
    {
        View::render($view, $data, $layout);
    }

    protected function redirect(string $path): void
    {
        header("Location: {$path}");
        exit;
    }

    protected function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->redirect($referer);
    }

    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /** Flash a message to the session for the next request. */
    protected function flash(string $key, string $message): void
    {
        $_SESSION['_flash'][$key] = $message;
    }

    protected function verifyCsrf(): void
    {
        if (!Csrf::verify(Request::input('_token'))) {
            http_response_code(419);
            die('Your session has expired. Please go back and try again.');
        }
    }
}
