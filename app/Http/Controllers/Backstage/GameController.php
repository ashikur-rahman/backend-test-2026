<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class GameController extends Controller
{
    public function index(): View
    {
        return view('backstage.games.index');
    }
}
