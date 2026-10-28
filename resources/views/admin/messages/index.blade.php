@extends('layouts.admin')

@section('content')
<div class="panel">
  <div class="panel-head"><h3>Contact Form Messages</h3></div>
  <div class="panel-body no-pad">
    <div class="table-wrap">
      <table class="data-table">
        <thead><tr><th>From</th><th>Subject</th><th>Message</th><th>Received</th><th></th></tr></thead>
        <tbody>
          @foreach ($messages as $m)
          <tr style="{{ $m->is_read ? '' : 'font-weight:700;' }}">
            <td>{{ $m->name }}<br><span style="font-weight:400; color:var(--ink-soft); font-size:0.82rem;">{{ $m->email }}{{ $m->phone ? ' · '.$m->phone : '' }}</span></td>
            <td>{{ $m->subject }}</td>
            <td style="max-width:340px;">{!! nl2br(e(\Illuminate\Support\Str::limit($m->message, 220))) !!}</td>
            <td>{{ format_date($m->created_at, 'j M Y, g:i a') }}</td>
            <td class="action-links">
              <a href="{{ route('admin.messages.show', $m->id) }}">View</a>
              <form method="post" action="{{ route('admin.messages.destroy', $m->id) }}" style="display:inline;" onsubmit="return confirm('Delete this message?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="link-danger">Delete</button>
              </form>
            </td>
          </tr>
          @endforeach
          @if ($messages->isEmpty())
            <tr><td colspan="5">No messages received yet.</td></tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
