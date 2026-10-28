@extends('layouts.admin')

@section('content')
<p><a href="{{ route('admin.scholars.index') }}">&larr; Back to Scholars</a></p>

<div class="panel">
  <div class="panel-body">
    <form method="post" action="{{ $scholar ? route('admin.scholars.update', $scholar->id) : route('admin.scholars.store') }}" enctype="multipart/form-data">
      @csrf
      @if ($scholar)
        @method('PUT')
      @endif
      <div class="form-row">
        <div class="form-group required">
          <label for="full_name">Full Name</label>
          <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $scholar->full_name ?? '') }}" required>
        </div>
        <div class="form-group">
          <label for="program_id">Program</label>
          <select id="program_id" name="program_id">
            <option value="">—</option>
            @foreach ($programs as $p)
              <option value="{{ $p->id }}" {{ (string) old('program_id', $scholar->program_id ?? '') === (string) $p->id ? 'selected' : '' }}>{{ $p->title }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="school_or_institute">University / Institute</label>
          <input type="text" id="school_or_institute" name="school_or_institute" value="{{ old('school_or_institute', $scholar->school_or_institute ?? '') }}">
        </div>
        <div class="form-group">
          <label for="district">District</label>
          <input type="text" id="district" name="district" value="{{ old('district', $scholar->district ?? '') }}">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="year_enrolled">Year Enrolled</label>
          <input type="text" id="year_enrolled" name="year_enrolled" value="{{ old('year_enrolled', $scholar->year_enrolled ?? '') }}" placeholder="e.g. 2023">
        </div>
        <div class="form-group">
          <label for="status">Status</label>
          <p class="hint">Use Graduated when the scholar completed the programme. Use Discontinued when support ended for another reason.</p>
          <select id="status" name="status">
            @foreach ($statuses as $st)
              <option value="{{ $st }}" {{ old('status', $scholar->status ?? 'Active') === $st ? 'selected' : '' }}>{{ $st }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="form-group">
        <label for="story">Scholar Story (optional)</label>
        <textarea id="story" name="story">{{ old('story', $scholar->story ?? '') }}</textarea>
      </div>
      <div class="form-group">
        <label for="photo">Photo</label>
        @if (!empty($scholar->photo))
          <img src="{{ asset('storage/'.$scholar->photo) }}" alt="" style="height:70px; border-radius:4px; margin-bottom:8px;">
        @endif
        <input type="file" id="photo" name="photo" accept="image/png,image/jpeg,image/webp">
      </div>
      <div class="form-group">
        <label><input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $scholar->is_featured ?? false) ? 'checked' : '' }} style="width:auto; margin-right:8px;">Feature on homepage</label>
      </div>
      <button type="submit" class="btn">{{ $scholar ? 'Save Changes' : 'Add Scholar' }}</button>
    </form>
  </div>
</div>
@endsection
