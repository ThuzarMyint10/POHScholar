<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\InvitationToApplyMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class InviteCandidateController extends Controller
{
    public function show(): View
    {
        return view('admin.invite_candidate.show', [
            'pageTitle' => 'Invite a Candidate',
            'active' => 'invite',
        ]);
    }

    public function send(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'email' => ['required', 'email'],
            'achievement' => ['nullable', 'string'],
            'recommended_by' => ['nullable', 'string', 'max:200'],
        ], [
            'name.required' => "Please enter the candidate's full name.",
            'email.required' => 'Please enter a valid email address.',
            'email.email' => 'Please enter a valid email address.',
        ]);

        $name = trim((string) $data['name']);
        $email = trim((string) $data['email']);

        try {
            Mail::to($email, $name)->send(new InvitationToApplyMail($name));

            return redirect()->route('admin.invite.show')->with('success', 'Invitation to apply emailed to '.$name.'.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Could not send the invitation email. Check your mail settings.');
        }
    }
}
