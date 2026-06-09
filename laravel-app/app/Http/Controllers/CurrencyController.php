<?php

namespace App\Http\Controllers;

use App\Http\Requests\CurrencyChangeRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class CurrencyController extends Controller
{
    public function change(CurrencyChangeRequest $request): JsonResponse|RedirectResponse
    {
        session(['currency_id' => $request->id]);

        if ($request->wantsJson() && ! $request->header('X-Inertia')) {
            return response()->json(['status' => 'ok']);
        }

        return back();
    }
}
