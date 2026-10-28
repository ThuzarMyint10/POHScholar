<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();

        $student = Student::where('email', $googleUser->getEmail())->first();

        if (! $student) {
            $student = Student::create([
                'full_name' => $googleUser->getName(),
                'email'     => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'password'  => bcrypt(Str::random(32)),
            ]);
        } elseif (empty($student->google_id)) {
            $student->update(['google_id' => $googleUser->getId()]);
        }

        auth('student')->login($student);

        return redirect()->route('student.dashboard');
    }
}