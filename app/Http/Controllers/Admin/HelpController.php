<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class HelpController extends Controller
{
    public function __invoke(): View
    {
        /** @var \App\Models\User|null $user */
        $user = request()->user();

        return view('admin.help', [
            'title' => 'Help & guide | Dezato Admin',
            'heading' => 'Help & picture guide',
            'active' => 'help',
            'media' => config('dezato_admin.media'),
            'loginEmail' => $user?->email ?: 'admin@dezato.pk',
        ]);
    }
}
