@extends('layouts.admin')

@section('content')
<div class="panel">
  <div class="panel-head"><h3>Public Site Content</h3></div>
  <div class="panel-body">
    <form method="post" action="{{ route('admin.settings.update') }}">
      @csrf
      @method('PUT')
      @foreach ($fields as $key => $meta)
        <div class="form-group">
          <label for="{{ $key }}">{{ $meta['label'] }}</label>
          @if ($meta['type'] === 'textarea')
            <textarea id="{{ $key }}" name="{{ $key }}">{{ old($key, $current[$key] ?? '') }}</textarea>
          @else
            <input type="text" id="{{ $key }}" name="{{ $key }}" value="{{ old($key, $current[$key] ?? '') }}">
          @endif
        </div>
      @endforeach
      <button type="submit" class="btn">Save Site Content</button>
    </form>
  </div>
</div>
@endsection
