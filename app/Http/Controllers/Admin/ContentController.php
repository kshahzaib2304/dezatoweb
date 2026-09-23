<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function edit(): View
    {
        return view('admin.content.edit', [
            'title' => 'Website text | Dezato Admin',
            'heading' => 'Website text & banner',
            'active' => 'content',
            'nav' => config('dezato_admin.nav'),
            'announcement' => SiteSetting::getValue(
                'announcement',
                (string) config('dezato.home.announcement', '')
            ),
            'mediaGuide' => config('dezato_admin.media.hero'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'announcement' => ['nullable', 'string', 'max:240'],
        ], [
            'announcement.max' => 'Keep the top banner short — max 240 characters.',
        ]);

        SiteSetting::putValue('announcement', trim((string) ($data['announcement'] ?? '')) ?: null);

        return back()->with('status', 'Website text saved. Refresh the homepage to see it.');
    }
}
