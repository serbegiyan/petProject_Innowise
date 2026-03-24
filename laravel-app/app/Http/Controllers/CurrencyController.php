<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function change(Request $request)
    {
        session(['currency_id' => $request->id]);

        return response()->json(['status' => 'ok']);
    }
}
