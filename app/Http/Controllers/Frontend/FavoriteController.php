<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Add or remove a doctor from the authenticated patient's favorites.
     * The "role:patient" route middleware already keeps guests and doctors
     * out; toggle() attaches when missing and detaches when present.
     */
    public function toggle(int $doctorId): JsonResponse
    {
        User::where('role', 'doctor')->findOrFail($doctorId);

        $changes = Auth::user()->favoriteDoctors()->toggle($doctorId);

        return response()->json([
            'favorited' => in_array($doctorId, $changes['attached']),
        ]);
    }
}
