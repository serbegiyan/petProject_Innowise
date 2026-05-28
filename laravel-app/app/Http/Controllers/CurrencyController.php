<?php

namespace App\Http\Controllers;

use App\Http\Requests\CurrencyChangeRequest;

class CurrencyController extends Controller
{
    public function change(CurrencyChangeRequest $request)
    {
        session(['currency_id' => $request->id]);

        return response()->json(['status' => 'ok']);
    }
}
