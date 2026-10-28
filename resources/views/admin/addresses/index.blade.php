@extends('layouts.admin')

@section('content')
<div class="panel">
  <div class="panel-head">
    <div>
      <h3>Countries &amp; Districts</h3>
      <p class="hint">Manage the searchable address options shown on application forms.</p>
    </div>
  </div>
  <div class="panel-body">
    <form method="post" action="{{ route('admin.addresses.countries.store') }}" class="form-row" style="align-items:end;">
      @csrf
      <div class="form-group" style="margin:0;">
        <label for="new-country">Add Country</label>
        <input type="text" id="new-country" name="name" maxlength="100" required>
      </div>
      <button type="submit" class="btn btn--sm">Add Country</button>
    </form>
  </div>
</div>

@foreach ($countries as $country)
<div class="panel">
  <div class="panel-head">
    <form method="post" action="{{ route('admin.addresses.countries.update', $country) }}" class="form-row" style="flex:1; align-items:end;">
      @csrf
      @method('PUT')
      <div class="form-group" style="margin:0; max-width:360px;">
        <label for="country-{{ $country->id }}">Country</label>
        <input type="text" id="country-{{ $country->id }}" name="name" value="{{ $country->name }}" maxlength="100" required>
      </div>
      <button type="submit" class="btn btn--sm btn--outline">Save</button>
    </form>
    <form method="post" action="{{ route('admin.addresses.countries.destroy', $country) }}" onsubmit="return confirm('Delete this country and all its districts?');">
      @csrf
      @method('DELETE')
      <button type="submit" class="link-danger">Delete</button>
    </form>
  </div>
  <div class="panel-body">
    <div class="table-wrap">
      <table class="data-table">
        <thead><tr><th>District</th><th style="width:220px;"></th></tr></thead>
        <tbody>
          @foreach ($country->districts as $district)
          <tr>
            <td>
              <form id="district-form-{{ $district->id }}" method="post" action="{{ route('admin.addresses.districts.update', $district) }}">
                @csrf
                @method('PUT')
                <input type="text" name="name" value="{{ $district->name }}" maxlength="100" required>
              </form>
            </td>
            <td class="action-links">
              <button type="submit" form="district-form-{{ $district->id }}" style="background:none;border:none;padding:0;font:inherit;cursor:pointer;color:var(--maroon);text-decoration:none;">Save</button>
              <form method="post" action="{{ route('admin.addresses.districts.destroy', $district) }}" style="display:inline;" onsubmit="return confirm('Delete this district?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="link-danger">Delete</button>
              </form>
            </td>
          </tr>
          @endforeach
          <tr>
            <td colspan="2">
              <form method="post" action="{{ route('admin.addresses.districts.store', $country) }}" class="form-row" style="align-items:end;">
                @csrf
                <div class="form-group" style="margin:0;">
                  <label for="new-district-{{ $country->id }}">Add district to {{ $country->name }}</label>
                  <input type="text" id="new-district-{{ $country->id }}" name="name" maxlength="100" required>
                </div>
                <button type="submit" class="btn btn--sm">Add District</button>
              </form>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
@endforeach
@endsection
