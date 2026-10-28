{{-- Renders a section heading with the Admin / Mrs. Poh rank mini-inputs.
     Expects: $key (section letter, e.g. 'A', or 'I' for Essay), $label,
     and the parent view's $adminRanks / $mrsPohRanks arrays. --}}
<div class="section-rank-row">
  <h4>{{ $label }}</h4>
  <div class="section-rank-inputs">
    <label>Admin (0&ndash;5) <input type="number" min="0" max="5" step="0.5" name="rank_admin[{{ $key }}]" value="{{ $adminRanks[$key] ?? '' }}"></label>
    <label>Mrs. Poh (0&ndash;5) <input type="number" min="0" max="5" step="0.5" name="rank_mrs_poh[{{ $key }}]" value="{{ $mrsPohRanks[$key] ?? '' }}"></label>
  </div>
</div>
