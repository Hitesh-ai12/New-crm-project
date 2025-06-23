<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            ['name' => 'Afghanistan', 'code' => 'AF', 'phone_code' => '93'],
            ['name' => 'Albania', 'code' => 'AL', 'phone_code' => '355'],
            ['name' => 'Algeria', 'code' => 'DZ', 'phone_code' => '213'],
            ['name' => 'Andorra', 'code' => 'AD', 'phone_code' => '376'],
            ['name' => 'Angola', 'code' => 'AO', 'phone_code' => '244'],
            ['name' => 'Argentina', 'code' => 'AR', 'phone_code' => '54'],
            ['name' => 'Armenia', 'code' => 'AM', 'phone_code' => '374'],
            ['name' => 'Australia', 'code' => 'AU', 'phone_code' => '61'],
            ['name' => 'Austria', 'code' => 'AT', 'phone_code' => '43'],
            ['name' => 'Bangladesh', 'code' => 'BD', 'phone_code' => '880'],
            ['name' => 'Belgium', 'code' => 'BE', 'phone_code' => '32'],
            ['name' => 'Brazil', 'code' => 'BR', 'phone_code' => '55'],
            ['name' => 'Canada', 'code' => 'CA', 'phone_code' => '1'],
            ['name' => 'China', 'code' => 'CN', 'phone_code' => '86'],
            ['name' => 'Denmark', 'code' => 'DK', 'phone_code' => '45'],
            ['name' => 'Egypt', 'code' => 'EG', 'phone_code' => '20'],
            ['name' => 'Finland', 'code' => 'FI', 'phone_code' => '358'],
            ['name' => 'France', 'code' => 'FR', 'phone_code' => '33'],
            ['name' => 'Germany', 'code' => 'DE', 'phone_code' => '49'],
            ['name' => 'India', 'code' => 'IN', 'phone_code' => '91'],
            ['name' => 'Indonesia', 'code' => 'ID', 'phone_code' => '62'],
            ['name' => 'Italy', 'code' => 'IT', 'phone_code' => '39'],
            ['name' => 'Japan', 'code' => 'JP', 'phone_code' => '81'],
            ['name' => 'Kenya', 'code' => 'KE', 'phone_code' => '254'],
            ['name' => 'Mexico', 'code' => 'MX', 'phone_code' => '52'],
            ['name' => 'Nepal', 'code' => 'NP', 'phone_code' => '977'],
            ['name' => 'Netherlands', 'code' => 'NL', 'phone_code' => '31'],
            ['name' => 'New Zealand', 'code' => 'NZ', 'phone_code' => '64'],
            ['name' => 'Nigeria', 'code' => 'NG', 'phone_code' => '234'],
            ['name' => 'Pakistan', 'code' => 'PK', 'phone_code' => '92'],
            ['name' => 'Philippines', 'code' => 'PH', 'phone_code' => '63'],
            ['name' => 'Qatar', 'code' => 'QA', 'phone_code' => '974'],
            ['name' => 'Russia', 'code' => 'RU', 'phone_code' => '7'],
            ['name' => 'Saudi Arabia', 'code' => 'SA', 'phone_code' => '966'],
            ['name' => 'Singapore', 'code' => 'SG', 'phone_code' => '65'],
            ['name' => 'South Africa', 'code' => 'ZA', 'phone_code' => '27'],
            ['name' => 'South Korea', 'code' => 'KR', 'phone_code' => '82'],
            ['name' => 'Sri Lanka', 'code' => 'LK', 'phone_code' => '94'],
            ['name' => 'Sweden', 'code' => 'SE', 'phone_code' => '46'],
            ['name' => 'Switzerland', 'code' => 'CH', 'phone_code' => '41'],
            ['name' => 'Thailand', 'code' => 'TH', 'phone_code' => '66'],
            ['name' => 'Turkey', 'code' => 'TR', 'phone_code' => '90'],
            ['name' => 'United Arab Emirates', 'code' => 'AE', 'phone_code' => '971'],
            ['name' => 'United Kingdom', 'code' => 'GB', 'phone_code' => '44'],
            ['name' => 'United States', 'code' => 'US', 'phone_code' => '1'],
            ['name' => 'Vietnam', 'code' => 'VN', 'phone_code' => '84'],
            ['name' => 'Zimbabwe', 'code' => 'ZW', 'phone_code' => '263'],
        ];

        DB::table('countries')->insert($countries);
    }
}
