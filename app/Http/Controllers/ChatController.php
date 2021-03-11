<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

/**
 * Class ChatController
 * @package App\Http\Controllers
 */
class ChatController extends AppBaseController
{
    /**
     * Show the application dashboard.
     *
     * @return Factory|View
     */
    public function index()
    {
        return view('chat.index');
    }
}
