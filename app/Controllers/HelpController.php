<?php

namespace App\Controllers;

use App\Core\Controller;

class HelpController extends Controller
{
    public function index(): void
    {
        $this->view('help.index');
    }
}
