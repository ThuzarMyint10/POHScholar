<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Public site content fields edited on this page: setting_key => [label, type].
     * Mirrors the original admin/settings.php field list exactly.
     */
    private const FIELDS = [
        'hero_heading' => ['label' => 'Homepage Hero Heading', 'type' => 'text'],
        'hero_subheading' => ['label' => 'Homepage Hero Subheading', 'type' => 'textarea'],
        'mission_statement' => ['label' => 'Mission Statement', 'type' => 'textarea'],
        'about_history' => ['label' => 'About Us: History', 'type' => 'textarea'],
        'about_vision' => ['label' => 'About Us: Vision', 'type' => 'textarea'],
        'stat_years_active' => ['label' => 'Stat: Years Active', 'type' => 'text'],
        'stat_scholars_supported' => ['label' => 'Stat: Scholars Supported', 'type' => 'text'],
        'stat_districts_reached' => ['label' => 'Stat: Districts Reached', 'type' => 'text'],
        'contact_address' => ['label' => 'Contact: Address', 'type' => 'text'],
        'contact_phone' => ['label' => 'Contact: Phone', 'type' => 'text'],
        'contact_email' => ['label' => 'Contact: Email', 'type' => 'text'],
        'contact_office_hours' => ['label' => 'Contact: Office Hours', 'type' => 'text'],
        'footer_about' => ['label' => 'Footer: About Blurb', 'type' => 'textarea'],
        'donate_info' => ['label' => 'Donate / Support Us Text', 'type' => 'textarea'],
        'facebook_url' => ['label' => 'Facebook URL', 'type' => 'text'],
        'instagram_url' => ['label' => 'Instagram URL', 'type' => 'text'],
    ];

    public function edit(): View
    {
        $current = SiteSetting::query()->pluck('setting_value', 'setting_key');

        return view('admin.settings.edit', [
            'pageTitle' => 'Site Settings',
            'active' => 'settings',
            'fields' => self::FIELDS,
            'current' => $current,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        foreach (array_keys(self::FIELDS) as $key) {
            $value = trim((string) $request->input($key, ''));
            SiteSetting::query()->updateOrCreate(['setting_key' => $key], ['setting_value' => $value]);
        }

        return redirect()->route('admin.settings.edit')->with('success', 'Site content updated.');
    }
}
