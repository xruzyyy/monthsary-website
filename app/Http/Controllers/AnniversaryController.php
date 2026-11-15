<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class AnniversaryController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Anniversary/Welcome');
    }
}
