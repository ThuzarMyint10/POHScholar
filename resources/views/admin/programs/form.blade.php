@extends('layouts.admin')

@section('content')
<p><a href="{{ route('admin.programs.index') }}">&larr; Back to Programs</a></p>

<div class="panel">
  <div class="panel-body">
    <form method="post" action="{{ $program ? route('admin.programs.update', $program->id) : route('admin.programs.store') }}">
      @csrf
      @if ($program)
        @method('PUT')
      @endif
      <div class="form-group required">
        <label for="title">Program Title</label>
        <input type="text" id="title" name="title" value="{{ old('title', $program->title ?? '') }}" required>
      </div>
      <div class="form-group">
        <label for="summary">Short Summary</label>
        <textarea id="summary" name="summary" placeholder="One or two sentences shown on the homepage">{{ old('summary', $program->summary ?? '') }}</textarea>
      </div>
      <div class="form-group">
        <label for="description">Full Description</label>
        <textarea id="description" name="description" style="min-height:140px;">{{ old('description', $program->description ?? '') }}</textarea>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="eligibility">Eligibility</label>
          <textarea id="eligibility" name="eligibility">{{ old('eligibility', $program->eligibility ?? '') }}</textarea>
        </div>
        <div class="form-group">
          <label for="benefits">Benefits</label>
          <textarea id="benefits" name="benefits">{{ old('benefits', $program->benefits ?? '') }}</textarea>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="display_order">Display Order</label>
          <input type="text" id="display_order" name="display_order" value="{{ old('display_order', $program->display_order ?? 0) }}">
        </div>
        <div class="form-group">
          <label><input type="checkbox" name="is_active" value="1" {{ old('is_active', $program->is_active ?? true) ? 'checked' : '' }} style="width:auto; margin-right:8px;">Published on public site</label>
        </div>
      </div>
      <button type="submit" class="btn">{{ $program ? 'Save Changes' : 'Add Program' }}</button>
    </form>
  </div>
</div>
@endsection
