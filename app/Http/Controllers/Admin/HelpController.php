<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class HelpController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.help', [
            'title' => 'Help & guide | Dezato Admin',
            'heading' => 'Help & picture guide',
            'active' => 'help',
            'nav' => config('dezato_admin.nav'),
            'media' => config('dezato_admin.media'),
            'loginEmail' => 'admin@dezato.pk',
        ]);
    }
}
