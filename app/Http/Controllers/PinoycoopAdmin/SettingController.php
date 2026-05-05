<?php

namespace App\Http\Controllers\PinoycoopAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function edit(): View
    {
        return view('pinoycoop_admin.settings.general', [
            'settings' => Cache::get('cms.settings', [
                'site_name' => 'MASS-SPECC CMS',
                'site_tagline' => 'Prototype content management system',
            ]),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'site_tagline' => ['nullable', 'string', 'max:255'],
        ]);

        Cache::forever('cms.settings', $data);

        return redirect()->route('pinoycoop.admin.settings.general')->with('status', 'General settings saved.');
    }
}
