@extends('layouts.admin')

@section('content')
<p><a href="{{ route('admin.news.index') }}">&larr; Back to News &amp; Events</a></p>

<div class="panel">
  <div class="panel-body">
    <form method="post" action="{{ $item ? route('admin.news.update', $item->id) : route('admin.news.store') }}" enctype="multipart/form-data">
      @csrf
      @if ($item)
        @method('PUT')
      @endif
      <div class="form-row">
        <div class="form-group required">
          <label for="title">Title</label>
          <input type="text" id="title" name="title" value="{{ old('title', $item->title ?? '') }}" required>
        </div>
        <div class="form-group">
          <label for="category">Category</label>
          <select id="category" name="category">
            @foreach (['News', 'Event'] as $c)
              <option value="{{ $c }}" {{ old('category', $item->category ?? 'News') === $c ? 'selected' : '' }}>{{ $c }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="form-group">
        <label for="excerpt">Short Excerpt</label>
        <textarea id="excerpt" name="excerpt" placeholder="Shown in listings">{{ old('excerpt', $item->excerpt ?? '') }}</textarea>
      </div>
      <div class="form-group">
        <label for="content">Full Content</label>
        <textarea id="content" name="content" style="min-height:160px;">{{ old('content', $item->content ?? '') }}</textarea>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="event_date">Date</label>
          <input type="date" id="event_date" name="event_date" value="{{ old('event_date', optional($item->event_date ?? null)->format('Y-m-d')) }}">
        </div>
        <div class="form-group">
          <label for="image">Image</label>
          @if (!empty($item->image))
            <img src="{{ asset('storage/'.$item->image) }}" alt="" style="height:60px; border-radius:4px; margin-bottom:8px;">
          @endif
          <input type="file" id="image" name="image" accept="image/png,image/jpeg,image/webp">
        </div>
      </div>
      <div class="form-group">
        <label><input type="checkbox" name="is_published" value="1" {{ old('is_published', $item->is_published ?? true) ? 'checked' : '' }} style="width:auto; margin-right:8px;">Published</label>
      </div>
      <button type="submit" class="btn">{{ $item ? 'Save Changes' : 'Add Item' }}</button>
    </form>
  </div>
</div>
@endsection
