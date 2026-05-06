<?php

namespace App\Http\Controllers\PinoycoopAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    private const DEFAULT_HOME_COUNTERS = [
        ['icon' => 'icofont-heart', 'value' => 460, 'label' => 'Our Happy Clients'],
        ['icon' => 'icofont-rocket', 'value' => 60, 'label' => 'Projects Done'],
        ['icon' => 'icofont-hand-power', 'value' => 30, 'label' => 'Experienced stuff'],
        ['icon' => 'icofont-shield-alt', 'value' => 25, 'label' => 'Ongoning Projects'],
    ];

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

    public function editHomeCounter(): View
    {
        return view('pinoycoop_admin.settings.home-counter', [
            'counters' => Cache::get('cms.home_counters', self::DEFAULT_HOME_COUNTERS),
        ]);
    }

    public function updateHomeCounter(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'counters' => ['required', 'array', 'size:4'],
            'counters.*.icon' => ['required', 'string', 'max:80'],
            'counters.*.value' => ['required', 'integer', 'min:0', 'max:999999'],
            'counters.*.label' => ['required', 'string', 'max:80'],
        ]);

        Cache::forever('cms.home_counters', array_values($data['counters']));

        return redirect()->route('pinoycoop.admin.settings.home-counter')->with('status', 'Home counter settings saved.');
    }
}
