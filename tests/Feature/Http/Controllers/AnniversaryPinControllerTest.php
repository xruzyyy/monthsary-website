<?php

use App\Services\AnniversaryPuzzle;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia;

it('renders the pin screen without exposing the configured hash', function () {
    config(['anniversary.pin_hash' => Hash::make('123456')]);

    $this->get(route('anniversary.pin'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Anniversary/Pin')
            ->where('isConfigured', true)
            ->missing('pinHash')
            ->missing('pin_hash'));
});

it('unlocks the puzzle for the correct pin', function () {
    config(['anniversary.pin_hash' => Hash::make('123456')]);

    $response = $this->from(route('anniversary.pin'))
        ->post(route('anniversary.unlock'), ['pin' => '123456']);

    $response->assertRedirect(route('anniversary.puzzle'))
        ->assertSessionHas(AnniversaryPuzzle::PIN_VERIFIED_SESSION_KEY, true)
        ->assertSessionMissing(AnniversaryPuzzle::COMPLETED_SESSION_KEY);
});

it('rejects an incorrect pin without unlocking the puzzle', function () {
    config(['anniversary.pin_hash' => Hash::make('123456')]);

    $response = $this->from(route('anniversary.pin'))
        ->post(route('anniversary.unlock'), ['pin' => '654321']);

    $response->assertRedirect(route('anniversary.pin'))
        ->assertSessionHasErrors('pin')
        ->assertSessionMissing(AnniversaryPuzzle::PIN_VERIFIED_SESSION_KEY);
});

it('rejects a pin that is not six numeric digits', function () {
    config(['anniversary.pin_hash' => Hash::make('123456')]);

    $this->from(route('anniversary.pin'))
        ->post(route('anniversary.unlock'), ['pin' => '12a456'])
        ->assertRedirect(route('anniversary.pin'))
        ->assertSessionHasErrors('pin')
        ->assertSessionMissing('_old_input')
        ->assertSessionMissing(AnniversaryPuzzle::PIN_VERIFIED_SESSION_KEY);
});

it('explains how to configure a missing pin hash', function () {
    config(['anniversary.pin_hash' => null]);

    $this->from(route('anniversary.pin'))
        ->post(route('anniversary.unlock'), ['pin' => '123456'])
        ->assertRedirect(route('anniversary.pin'))
        ->assertSessionHasErrors('pin')
        ->assertSessionMissing(AnniversaryPuzzle::PIN_VERIFIED_SESSION_KEY);
});

it('returns 429 after too many pin attempts', function () {
    config(['anniversary.pin_hash' => Hash::make('123456')]);

    for ($attempt = 0; $attempt < 5; $attempt++) {
        $this->from(route('anniversary.pin'))
            ->post(route('anniversary.unlock'), ['pin' => '654321']);
    }

    $this->post(route('anniversary.unlock'), ['pin' => '654321'])
        ->assertTooManyRequests();
});
