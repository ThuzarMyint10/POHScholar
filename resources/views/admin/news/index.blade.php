@extends('layouts.admin')

@section('content')
<div class="panel">
  <div class="panel-head">
    <h3>All News &amp; Events</h3>
    <a href="{{ route('admin.news.create') }}" class="btn btn--sm">+ Add Item</a>
  </div>
  <div class="panel-body no-pad">
    <div class="table-wrap">
      <table class="data-table">
        <thead><tr><th>Title</th><th>Category</th><th>Date</th><th>Status</th><th></th></tr></thead>
        <tbody>
          @foreach ($items as $n)
          <tr>
            <td>{{ $n->title }}</td>
            <td>{{ $n->category }}</td>
            <td>{{ format_date($n->event_date ?: $n->created_at) }}</td>
            <td>{{ $n->is_published ? 'Published' : 'Draft' }}</td>
            <td class="action-links">
              <a href="{{ route('admin.news.edit', $n->id) }}">Edit</a>
              <form method="post" action="{{ route('admin.news.destroy', $n->id) }}" style="display:inline;" onsubmit="return confirm('Delete this item?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="link-danger">Delete</button>
              </form>
            </td>
          </tr>
          @endforeach
          @if ($items->isEmpty())
            <tr><td colspan="5">Nothing published yet.</td></tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
