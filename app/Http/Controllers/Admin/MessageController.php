<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(): View
    {
        $messages = ContactMessage::query()->orderByDesc('created_at')->get();

        return view('admin.messages.index', [
            'pageTitle' => 'Messages',
            'active' => 'messages',
            'messages' => $messages,
        ]);
    }

    public function show(ContactMessage $message): View
    {
        if (! $message->is_read) {
            $message->forceFill(['is_read' => true])->save();
        }

        return view('admin.messages.show', [
            'pageTitle' => 'Message from '.$message->name,
            'active' => 'messages',
            'message' => $message,
        ]);
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $message->delete();

        return redirect()->route('admin.messages.index')->with('success', 'Message deleted.');
    }
}
