<?php

namespace App\Http\Controllers;

use App\Services\Theme\ThemePaletteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ThemePaletteController extends Controller
{
    public function config(Request $request, ThemePaletteService $service): JsonResponse { return response()->json($service->getConfigForUser($request->user(), $request->string('surface', 'account')->toString())); }
    public function save(Request $request, ThemePaletteService $service): JsonResponse
    {
        abort_unless($service->canUsePalette($request->user(), $request->string('surface', 'account')->toString()), 403);
        $preference = $service->saveUserHex($request->user(), $request->validate(['hex'=>['required','regex:/^#[0-9A-Fa-f]{6}$/']])['hex']);
        return response()->json(['hex'=>$preference->hex]);
    }
    public function reset(Request $request, ThemePaletteService $service): JsonResponse
    {
        abort_unless($service->canUsePalette($request->user(), $request->string('surface', 'account')->toString()), 403);
        $service->resetUserHex($request->user());
        return response()->json(['hex'=>$service->getDefaultHex()]);
    }
}
