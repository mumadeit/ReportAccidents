<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Breakdown;
use App\Models\Company;

class CompaniesController extends Controller
{
    public function index()
    {
        $companies = Company::all();
        // json
        return response()->json($companies);
    }

    public function breakdown()
    {
        $breakdown = Breakdown::all();
        // json
        return response()->json($breakdown);
    }
}
