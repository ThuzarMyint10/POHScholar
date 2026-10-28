@extends('layouts.student')

@section('content')
<span class="eyebrow">Step 3</span>
<h1>Certificates &amp; Documents</h1>
<p style="color:var(--ink-soft); margin-bottom:10px;">
  Attach copies of your certificates, academic transcripts, or any other supporting documents. PDF files only, up to {{ $maxDocuments }} documents.
</p>

@if ($locked)
  <div class="locked-note">Your application has been submitted, so your documents are now locked. Contact our office if you need to add anything further.</div>
@endif

<div class="panel">
  <div class="panel-head"><h3>Uploaded Documents</h3></div>
  <div class="panel-body">
    <div class="doc-count">{{ $documents->count() }} of {{ $maxDocuments }} used</div>
    <ul class="doc-list">
      @foreach ($documents as $d)
        <li>
          <span class="doc-name">&#128196; <a href="{{ route('student.documents.view', $d->id) }}" target="_blank" rel="noopener">{{ $d->label ?: $d->original_filename }}</a></span>
          <span>
            @unless ($locked)
              <form method="post" action="{{ route('student.documents.destroy', $d->id) }}" style="display:inline;" onsubmit="return confirm('Remove this document?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="link-danger" style="background:none;border:none;padding:0;font:inherit;cursor:pointer;">Remove</button>
              </form>
            @else
              <span style="color:var(--ink-soft); font-size:0.85rem;">View only</span>
            @endunless
          </span>
        </li>
      @endforeach
      @if ($documents->isEmpty())
        <li style="justify-content:center; color:var(--ink-soft);">No documents uploaded yet.</li>
      @endif
    </ul>

    @if (!$locked && $documents->count() < $maxDocuments)
      <form method="post" action="{{ route('student.documents.store') }}" enctype="multipart/form-data" style="border-top:1px solid var(--line); padding-top:20px;">
        @csrf
        <div class="form-row">
          <div class="form-group">
            <label for="label">Document Label (optional)</label>
            <input type="text" id="label" name="label" placeholder="e.g. University Admission Letter">
          </div>
          <div class="form-group required">
            <label for="document">Choose PDF File</label>
            <input type="file" id="document" name="document" accept="application/pdf" required>
            <div class="hint">PDF only, max 8MB per file.</div>
          </div>
        </div>
        <button type="submit" class="btn">Upload Document</button>
      </form>
    @elseif (!$locked)
      <p style="color:var(--ink-soft); border-top:1px solid var(--line); padding-top:20px;">You've reached the maximum of {{ $maxDocuments }} documents. Remove one above to upload a different file.</p>
    @endif
  </div>
</div>
@endsection