@extends('layouts.admin')

@section('content')
<p><a href="{{ route('admin.messages.index') }}">&larr; Back to Messages</a></p>

<div class="panel">
  <div class="panel-head">
    <h3>{{ $message->subject ?: 'Message from '.$message->name }}</h3>
  </div>
  <div class="panel-body">
    <table class="data-table">
      <tr><th style="width:20%;">From</th><td>{{ $message->name }}</td></tr>
      <tr><th>Email</th><td>{{ $message->email }}</td></tr>
      <tr><th>Phone</th><td>{{ $message->phone ?: '—' }}</td></tr>
      <tr><th>Subject</th><td>{{ $message->subject }}</td></tr>
      <tr><th>Received</th><td>{{ format_date($message->created_at, 'j M Y, g:i a') }}</td></tr>
    </table>
    <h4 style="margin-top:20px;">Message</h4>
    <p style="color:var(--ink-soft);">{!! nl2br(e($message->message)) !!}</p>

    <form method="post" action="{{ route('admin.messages.destroy', $message->id) }}" onsubmit="return confirm('Delete this message?');">
      @csrf
      @method('DELETE')
      <button type="submit" class="btn btn--outline">Delete Message</button>
    </form>
  </div>
</div>
@endsection
