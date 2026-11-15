<?php

use App\Services\AnniversaryPuzzle;
use Inertia\Testing\AssertableInertia;

it('redirects visitors without a verified pin to the pin screen', function () {
    $this->get(route('anniversary.puzzle'))
        ->assertRedirect(route('anniversary.pin'));
});

it('does not accept puzzle moves before pin verification', function () {
    $this->post(route('anniversary.puzzle.move'), ['position' => 8])
        ->assertRedirect(route('anniversary.pin'));
});

it('starts an incomplete nine-position puzzle for a verified visitor', function () {
    $this->withSession([
        AnniversaryPuzzle::PIN_VERIFIED_SESSION_KEY => true,
    ])->get(route('anniversary.puzzle'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Anniversary/Puzzle')
            ->where('moves', 0)
            ->has('board', 9)
            ->where('board', function (array $board): bool {
                expect($board)->toContain(0)
                    ->not->toBe([1, 2, 3, 4, 5, 6, 7, 8, 0]);

                $sortedBoard = $board;
                sort($sortedBoard);

                expect($sortedBoard)->toBe([0, 1, 2, 3, 4, 5, 6, 7, 8]);

                return true;
            }));
});

it('moves only an adjacent tile and stores the updated board on the server', function () {
    $this->withSession([
        AnniversaryPuzzle::PIN_VERIFIED_SESSION_KEY => true,
        AnniversaryPuzzle::BOARD_SESSION_KEY => [1, 2, 3, 4, 5, 6, 7, 0, 8],
        AnniversaryPuzzle::MOVE_COUNT_SESSION_KEY => 0,
    ])->post(route('anniversary.puzzle.move'), ['position' => 8])
        ->assertRedirect(route('anniversary.puzzle'))
        ->assertSessionHas(AnniversaryPuzzle::BOARD_SESSION_KEY, [1, 2, 3, 4, 5, 6, 7, 8, 0])
        ->assertSessionHas(AnniversaryPuzzle::MOVE_COUNT_SESSION_KEY, 1);
});

it('rejects a non-adjacent tile without changing the server board', function () {
    $this->from(route('anniversary.puzzle'))
        ->withSession([
            AnniversaryPuzzle::PIN_VERIFIED_SESSION_KEY => true,
            AnniversaryPuzzle::BOARD_SESSION_KEY => [1, 2, 3, 4, 5, 6, 7, 0, 8],
            AnniversaryPuzzle::MOVE_COUNT_SESSION_KEY => 0,
        ])->post(route('anniversary.puzzle.move'), ['position' => 0])
        ->assertRedirect(route('anniversary.puzzle'))
        ->assertSessionHasErrors('puzzle')
        ->assertSessionHas(AnniversaryPuzzle::BOARD_SESSION_KEY, [1, 2, 3, 4, 5, 6, 7, 0, 8])
        ->assertSessionHas(AnniversaryPuzzle::MOVE_COUNT_SESSION_KEY, 0);
});

it('does not unlock the surprise for an unsolved board', function () {
    $this->from(route('anniversary.puzzle'))
        ->withSession([
            AnniversaryPuzzle::PIN_VERIFIED_SESSION_KEY => true,
            AnniversaryPuzzle::BOARD_SESSION_KEY => [1, 2, 3, 4, 5, 6, 7, 0, 8],
            AnniversaryPuzzle::MOVE_COUNT_SESSION_KEY => 0,
        ])->post(route('anniversary.puzzle.complete'))
        ->assertRedirect(route('anniversary.puzzle'))
        ->assertSessionHasErrors('puzzle')
        ->assertSessionMissing(AnniversaryPuzzle::COMPLETED_SESSION_KEY);
});

it('unlocks the surprise after the server receives a solved board', function () {
    $session = [
        AnniversaryPuzzle::PIN_VERIFIED_SESSION_KEY => true,
        AnniversaryPuzzle::BOARD_SESSION_KEY => [1, 2, 3, 4, 5, 6, 7, 0, 8],
        AnniversaryPuzzle::MOVE_COUNT_SESSION_KEY => 0,
    ];

    $this->withSession($session)
        ->post(route('anniversary.puzzle.move'), ['position' => 8])
        ->assertRedirect(route('anniversary.puzzle'));

    $this->post(route('anniversary.puzzle.complete'))
        ->assertRedirect(route('anniversary.surprise'))
        ->assertSessionHas(AnniversaryPuzzle::COMPLETED_SESSION_KEY, true);
});
