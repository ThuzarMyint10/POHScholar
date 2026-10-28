<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\District;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AddressController extends Controller
{
    public function index(): View
    {
        return view('admin.addresses.index', [
            'pageTitle' => 'Address Settings',
            'active' => 'addresses',
            'countries' => Country::query()->with('districts')->orderBy('name')->get(),
        ]);
    }

    public function storeCountry(Request $request): RedirectResponse
    {
        Country::query()->create($this->validateCountry($request));

        return back()->with('success', 'Country added.');
    }

    public function updateCountry(Request $request, Country $country): RedirectResponse
    {
        $country->update($this->validateCountry($request, $country));

        return back()->with('success', 'Country updated.');
    }

    public function destroyCountry(Country $country): RedirectResponse
    {
        $country->delete();

        return back()->with('success', 'Country and its districts deleted.');
    }

    public function storeDistrict(Request $request, Country $country): RedirectResponse
    {
        $country->districts()->create($this->validateDistrict($request, $country));

        return back()->with('success', 'District added.');
    }

    public function updateDistrict(Request $request, District $district): RedirectResponse
    {
        $district->update($this->validateDistrict($request, $district->country, $district));

        return back()->with('success', 'District updated.');
    }

    public function destroyDistrict(District $district): RedirectResponse
    {
        $district->delete();

        return back()->with('success', 'District deleted.');
    }

    private function validateCountry(Request $request, ?Country $country = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:countries,name'.($country ? ','.$country->id : '')],
        ], [
            'name.unique' => 'This country already exists.',
        ]);
    }

    private function validateDistrict(Request $request, Country $country, ?District $district = null): array
    {
        return $request->validate([
            'name' => [
                'required', 'string', 'max:100',
                'unique:districts,name,'.($district?->id ?? 'NULL').',id,country_id,'.$country->id,
            ],
        ], [
            'name.unique' => 'This district already exists in the selected country.',
        ]);
    }
}
