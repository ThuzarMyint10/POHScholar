<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentActive
{
    /**
     * If an admin disables a student's account while they're mid-session,
     * this logs them out on their very next request rather than waiting
     * for the session to expire naturally.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $student = Auth::guard('student')->user();

        if ($student && ! $student->is_active) {
            Auth::guard('student')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('student.login', ['disabled' => 1]);
        }

        return $next($request);
    }
}
