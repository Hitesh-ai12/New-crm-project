<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::select('id', 'name', 'code', 'phone_code')
            ->orderBy('name')
            ->get();

        return response()->json($countries);
    }
}
