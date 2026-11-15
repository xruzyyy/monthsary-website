<?php

namespace App\Http\Controllers;

use App\Services\AnniversaryPuzzle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AnniversaryPuzzleController extends Controller
{
    public function show(Request $request, AnniversaryPuzzle $puzzle): RedirectResponse|Response
    {
        if ($request->session()->get(AnniversaryPuzzle::COMPLETED_SESSION_KEY) === true) {
            return to_route('anniversary.surprise');
        }

        $state = $puzzle->state($request->session());

        return Inertia::render('Anniversary/Puzzle', [
            ...$state,
            'photoUrl' => route('anniversary.photo'),
        ]);
    }

    public function move(Request $request, AnniversaryPuzzle $puzzle): RedirectResponse
    {
        $validated = $request->validate([
            'position' => ['required', 'integer', 'between:0,8'],
        ]);

        if (! $puzzle->move($request->session(), (int) $validated['position'])) {
            return back()->withErrors([
                'puzzle' => 'Choose a tile next to the open space.',
            ]);
        }

        return to_route('anniversary.puzzle');
    }

    public function restart(Request $request, AnniversaryPuzzle $puzzle): RedirectResponse
    {
        $puzzle->start($request->session());

        return to_route('anniversary.puzzle');
    }

    public function complete(Request $request, AnniversaryPuzzle $puzzle): RedirectResponse
    {
        if (! $puzzle->isSolved($request->session())) {
            return back()->withErrors([
                'puzzle' => 'The picture is not complete yet. Keep going!',
            ]);
        }

        $request->session()->put(AnniversaryPuzzle::COMPLETED_SESSION_KEY, true);

        return to_route('anniversary.surprise');
    }
}
