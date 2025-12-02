<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OverlayController extends Controller
{
    /**
     * Show the overlay page for the given key.
     */
    public function show(Request $request, string $key): Response
    {
        // Validate overlay key against configuration
        if ($key !== config('overlay.key')) {
            abort(404);
        }

        return response()->view('overlay.show', [
            'overlayKey' => $key,
        ]);
    }
}
