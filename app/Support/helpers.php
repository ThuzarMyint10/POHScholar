<?php

use App\Models\SiteSetting;

if (! function_exists('setting')) {
    /**
     * Read one editable site_settings value (cached for the request),
     * e.g. setting('contact_email'). Equivalent to the old app's
     * get_setting($key, $default).
     */
    function setting(string $key, string $default = ''): string
    {
        static $all = null;

        if ($all === null) {
            $all = SiteSetting::query()->pluck('setting_value', 'setting_key')->all();
        }

        return $all[$key] ?? $default;
    }
}

if (! function_exists('status_badge_class')) {
    /**
     * Maps an application/student status string to the badge/status-pill
     * CSS modifier class used throughout the public, student and admin
     * views (badge--pending, badge--review, badge--approved, ...).
     */
    function status_badge_class(string $status): string
    {
        $map = [
            'Draft' => 'pending', 'Pending' => 'pending',
            'Submitted' => 'review', 'Under Review' => 'review',
            'Approved' => 'approved', 'Graduated' => 'graduated', 'Rejected' => 'rejected',
        ];

        return $map[$status] ?? 'pending';
    }
}

if (! function_exists('format_date')) {
    /**
     * Format a date/datetime value, or return '' if empty/unparseable.
     * Accepts a plain string OR a Carbon/DateTime instance, since Eloquent
     * casts (date/datetime columns) hand back Carbon objects, not strings.
     */
    function format_date(string|\DateTimeInterface|null $date, string $format = 'j M Y'): string
    {
        if (empty($date)) {
            return '';
        }
        if ($date instanceof \DateTimeInterface) {
            return $date->format($format);
        }
        $ts = strtotime($date);

        return $ts ? date($format, $ts) : '';
    }
}
