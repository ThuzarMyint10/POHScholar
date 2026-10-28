@extends('layouts.student')

@section('content')
<span class="eyebrow">Step 2</span>
<h1>Write Your Essay</h1>
<p style="color:var(--ink-soft); margin-bottom:26px;">
  Tell us about yourself, your educational goals, and why you're applying for a POH Scholarship.
  Aim for {{ $minWords }}&ndash;{{ $maxWords }} words.
</p>

@if ($locked)
  <div class="locked-note">Your application has been submitted, so your essay is now locked for editing.</div>
@endif

<div class="form-card">
  <form method="post" action="{{ route('student.essay.update') }}" novalidate>
    @csrf
    @method('PUT')
    <div class="form-group required">
      <label for="essay">My Essay</label>
      <textarea id="essay" name="essay" style="min-height:340px;" {{ $locked ? 'readonly' : '' }} required>{{ old('essay', $student->essay) }}</textarea>
      <div class="hint" id="wordCountHint">
        {{ $student->essay ? str_word_count($student->essay) : 0 }} words
      </div>
    </div>
    @unless ($locked)
      <button type="submit" class="btn btn--block">Save Essay</button>
    @endunless
  </form>
</div>

@unless ($locked)
<script>
  var essayField = document.getElementById('essay');
  var hint = document.getElementById('wordCountHint');
  function countWords() {
    var text = essayField.value.trim();
    var words = text === '' ? 0 : text.split(/\s+/).length;
    hint.textContent = words + ' words';
  }
  if (essayField) {
    essayField.addEventListener('input', countWords);
    countWords();
  }
</script>
@endunless
@endsection
