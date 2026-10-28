<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('poh.countries', []) as $countryName) {
            Country::query()->firstOrCreate(['name' => $countryName]);
        }

        $countries = Country::query()->get()->keyBy(
            fn (Country $country) => $this->normalize($country->name)
        );
        $subdivisions = json_decode(
            file_get_contents(database_path('data/address-states.json')),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $countryAliases = [
            'the bahamas' => 'bahamas',
            'cape verde' => 'cabo verde',
            'ivory coast' => 'cote d ivoire',
            'democratic republic of the congo' => 'congo democratic republic',
            'congo' => 'congo republic',
            'fiji islands' => 'fiji',
            'the gambia' => 'gambia',
            'palestinian territory occupied' => 'palestine',
            'holy see vatican city state' => 'vatican city',
        ];

        foreach ($subdivisions as $subdivision) {
            $countryKey = $this->normalize($subdivision['country_name'] ?? '');
            $countryKey = $countryAliases[$countryKey] ?? $countryKey;
            $country = $countries->get($countryKey);

            if (! $country || empty($subdivision['name'])) {
                continue;
            }

            $country->districts()->firstOrCreate(['name' => trim($subdivision['name'])]);
        }

        $fallbackSubdivisions = [
            'Congo (Democratic Republic)' => [
                'Bas-Uele', 'Equateur', 'Haut-Katanga', 'Haut-Lomami', 'Haut-Uele', 'Ituri',
                'Kasaï', 'Kasaï-Central', 'Kasaï-Oriental', 'Kinshasa', 'Kongo Central', 'Kwango',
                'Kwilu', 'Lomami', 'Lualaba', 'Mai-Ndombe', 'Maniema', 'Mongala', 'Nord-Kivu',
                'Nord-Ubangi', 'Sankuru', 'Sud-Kivu', 'Sud-Ubangi', 'Tanganyika', 'Tshopo', 'Tshuapa',
            ],
            'Congo (Republic)' => [
                'Bouenza', 'Brazzaville', 'Cuvette', 'Cuvette-Ouest', 'Kouilou', 'Lékoumou',
                'Likouala', 'Niari', 'Plateaux', 'Pointe-Noire', 'Pool', 'Sangha',
            ],
            'Vatican City' => ['Vatican City'],
        ];

        foreach ($fallbackSubdivisions as $countryName => $districtNames) {
            $country = Country::query()->where('name', $countryName)->first();

            if (! $country) {
                continue;
            }

            foreach ($districtNames as $districtName) {
                $country->districts()->firstOrCreate(['name' => $districtName]);
            }
        }
    }

    private function normalize(string $value): string
    {
        return strtolower((string) preg_replace('/[^a-z0-9]+/i', ' ', trim($value)));
    }
}
