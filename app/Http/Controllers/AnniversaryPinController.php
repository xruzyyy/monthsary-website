<?php

namespace App\Http\Controllers;

use App\Services\AnniversaryPuzzle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use RuntimeException;

class AnniversaryPinController extends Controller
{
    public function create(Request $request): RedirectResponse|InertiaResponse
    {
        if ($request->session()->get(AnniversaryPuzzle::PIN_VERIFIED_SESSION_KEY) === true) {
            return to_route('anniversary.puzzle');
        }

        return Inertia::render('Anniversary/Pin', [
            'isConfigured' => is_string(config('anniversary.pin_hash'))
                && config('anniversary.pin_hash') !== '',
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
        $pinHash = config('anniversary.pin_hash');

        if (! is_string($pinHash) || $pinHash === '') {
            return back()->withErrors([
                'pin' => 'This experience is still being prepared. Set ANNIVERSARY_PIN_HASH in the server environment.',
            ]);
        }

        try {
            $isValidPin = Hash::check((string) $validated['pin'], $pinHash);
        } catch (RuntimeException $exception) {
            report($exception);

            return back()->withErrors([
                'pin' => 'This experience is temporarily unavailable. Please check the server PIN configuration.',
            ]);
        }

        if (! $isValidPin) {
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
