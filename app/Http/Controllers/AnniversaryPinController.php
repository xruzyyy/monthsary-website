<?php

namespace App\Http\Controllers;

use App\Services\AnniversaryPuzzle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class AnniversaryPinController extends Controller
{
    public function create(Request $request): RedirectResponse|InertiaResponse
    {
        if ($request->session()->get(AnniversaryPuzzle::PIN_VERIFIED_SESSION_KEY) === true) {
            return to_route('anniversary.puzzle');
        }

        $expectedPin = config('anniversary.pin');

        return Inertia::render('Anniversary/Pin', [
            'isConfigured' => is_string($expectedPin) && $expectedPin !== '',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->only('pin'), [
            'pin' => ['required', 'string', 'regex:/^\d{6}$/'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $validated = $validator->validated();
        $expectedPin = config('anniversary.pin');

        if (! is_string($expectedPin) || $expectedPin === '') {
            return back()->withErrors([
                'pin' => 'This surprise is still being prepared. Please try again soon.',
            ]);
        }

        if (! hash_equals($expectedPin, (string) $validated['pin'])) {
            return back()->withErrors([
                'pin' => 'That code does not match our special date. Try again, my love.',
            ]);
        }

        $request->session()->regenerate();
        $request->session()->forget([
            AnniversaryPuzzle::COMPLETED_SESSION_KEY,
            AnniversaryPuzzle::BOARD_SESSION_KEY,
            AnniversaryPuzzle::MOVE_COUNT_SESSION_KEY,
        ]);
        $request->session()->put(AnniversaryPuzzle::PIN_VERIFIED_SESSION_KEY, true);

        return to_route('anniversary.puzzle');
    }
}
