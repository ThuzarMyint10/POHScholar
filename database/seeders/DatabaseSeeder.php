<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use App\Models\NewsEvent;
use App\Models\Program;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the essential starting data - equivalent to the INSERT
     * statements at the bottom of the old sql/schema.sql. Safe to run more
     * than once (every insert is guarded with firstOrCreate/updateOrCreate).
     *
     *   php artisan db:seed
     */
    public function run(): void
    {
        $this->call(AddressSeeder::class);

        // Default login: username "admin" / password "ChangeMe123!"
        // *** CHANGE THIS PASSWORD IMMEDIATELY AFTER FIRST LOGIN ***
        AdminUser::query()->firstOrCreate(
            ['username' => 'admin'],
            [
                'password_hash' => Hash::make('ChangeMe123!'),
                'full_name' => 'Site Administrator',
                'email' => 'admin@pohscholarships.org',
                'role' => 'administrator',
                'is_active' => true,
            ]
        );

        Program::query()->updateOrCreate(
            ['slug' => 'university-higher-education-scholarships'],
            [
                'title' => 'University & Higher Education Scholarships',
                'summary' => 'Financial support for outstanding students pursuing degrees at recognised universities and higher education institutes.',
                'description' => 'Awarded to students who have secured a place at a state or approved private university but face financial hardship in taking it up. Renewable each year subject to satisfactory academic progress.',
                'eligibility' => 'Confirmed admission to a recognised university/institute; demonstrated financial need; satisfactory academic record.',
                'benefits' => 'Tuition and/or maintenance allowance for the duration of the degree, subject to annual review.',
                'is_active' => true,
                'display_order' => 1,
            ]
        );

        NewsEvent::query()->firstOrCreate(
            ['title' => 'Welcome to the new POH Scholarships website'],
            [
                'category' => 'News',
                'excerpt' => 'We are delighted to launch our new website, making it easier for students and donors to connect with our work.',
                'content' => 'We are delighted to launch our new website, making it easier for students and donors to connect with our work. Explore our scholarship programmes and apply online.',
                'event_date' => now()->toDateString(),
                'is_published' => true,
            ]
        );

        $settings = [
            'hero_heading' => 'Every Deserving Student Deserves a Chance at University',
            'hero_subheading' => 'Since 2009, the POH Scholarships Scheme has been helping bright, deserving students access a university education, by covering tuition and other costs that might otherwise stand in their way.',
            'mission_statement' => 'Our mission is to break the cycle of poverty through education, by providing university scholarships and mentorship to outstanding students from disadvantaged communities.',
            'about_history' => 'POH Scholarships Scheme was founded in 2009 in response to the number of talented students unable to take up a university place due to financial hardship. What began as a small community initiative has grown into a wide-reaching programme, today focused exclusively on university scholarships for outstanding students.',
            'about_vision' => "A world where no deserving student is denied a university education because of their family's financial circumstances.",
            'stat_years_active' => '17',
            'stat_scholars_supported' => '1,200+',
            'stat_districts_reached' => '15',
            'contact_address' => 'No. 24, Temple Road, Colombo 05',
            'contact_phone' => '+94 11 234 5678',
            'contact_email' => 'info@pohscholarships.org',
            'contact_office_hours' => 'Monday - Friday, 9.00 am - 5.00 pm',
            'footer_about' => 'POH Scholarships Scheme is a charitable initiative providing university scholarships to deserving students since 2009.',
            'facebook_url' => '',
            'instagram_url' => '',
            'donate_info' => "To support a child's education through a donation or sponsorship, please contact our office - we would be glad to discuss how your contribution can help.",
        ];

        foreach ($settings as $key => $value) {
            SiteSetting::query()->firstOrCreate(
                ['setting_key' => $key],
                ['setting_value' => $value]
            );
        }
    }
}
