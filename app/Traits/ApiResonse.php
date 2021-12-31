<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait ApiResponse {
    protected function json(Request $request, $json, $code = 201) {
        return response()->json($json, $code)
            ->header('Authentication', $request->bearerToken());
    }
}
