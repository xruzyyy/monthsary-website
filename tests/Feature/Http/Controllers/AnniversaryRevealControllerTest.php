<?php

use App\Services\AnniversaryPuzzle;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia;

it('redirects visitors who have not solved the puzzle away from the surprise', function () {
    $this->withSession([
        AnniversaryPuzzle::PIN_VERIFIED_SESSION_KEY => true,
    ])->get(route('anniversary.surprise'))
        ->assertRedirect(route('anniversary.puzzle'));
});

it('keeps the photo unavailable before pin verification', function () {
    $this->get(route('anniversary.photo'))
        ->assertRedirect(route('anniversary.pin'));
});

it('serves the photo from private storage only after pin verification', function () {
    Storage::fake('local');
    Storage::disk('local')->put('anniversary/private-photo.svg', '<svg>private</svg>');
    config(['anniversary.photo_path' => 'anniversary/private-photo.svg']);

    $this->withSession([
        AnniversaryPuzzle::PIN_VERIFIED_SESSION_KEY => true,
    ])->get(route('anniversary.photo'))
        ->assertOk()
        ->assertHeader('cache-control', 'private, no-store, max-age=0')
        ->assertStreamedContent('<svg>private</svg>');
});

it('serves gallery photos from private storage only after pin verification', function () {
    Storage::fake('local');
    Storage::disk('local')->put('anniversary/gallery/photo-01.jpg', 'private memory');

    $this->get(route('anniversary.gallery.photo', ['photo' => 0]))
        ->assertRedirect(route('anniversary.pin'));

    $this->withSession([
        AnniversaryPuzzle::PIN_VERIFIED_SESSION_KEY => true,
    ])->get(route('anniversary.gallery.photo', ['photo' => 0]))
        ->assertOk()
        ->assertHeader('cache-control', 'private, no-store, max-age=0')
        ->assertStreamedContent('private memory');
});

it('does not return gallery files outside the discovered image list', function () {
    Storage::fake('local');
    Storage::disk('local')->put('anniversary/gallery/photo-01.jpg', 'private memory');

    $this->withSession([
        AnniversaryPuzzle::PIN_VERIFIED_SESSION_KEY => true,
    ])->get(route('anniversary.gallery.photo', ['photo' => 1]))
        ->assertNotFound();
});

it('renders the configured letter after the puzzle is complete', function () {
    Storage::fake('local');

    $this->withSession([
        AnniversaryPuzzle::PIN_VERIFIED_SESSION_KEY => true,
        AnniversaryPuzzle::COMPLETED_SESSION_KEY => true,
    ])->get(route('anniversary.surprise'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Anniversary/Surprise')
            ->has('letter.greeting')
            ->has('letter.paragraphs')
            ->has('letter.signature')
            ->has('photos', 1)
            ->where('photos.0.src', route('anniversary.photo')));
});

it('clears the progression when replaying the journey', function () {
    $this->withSession([
        AnniversaryPuzzle::PIN_VERIFIED_SESSION_KEY => true,
        AnniversaryPuzzle::COMPLETED_SESSION_KEY => true,
        AnniversaryPuzzle::BOARD_SESSION_KEY => [1, 2, 3, 4, 5, 6, 7, 8, 0],
        AnniversaryPuzzle::MOVE_COUNT_SESSION_KEY => 30,
    ])->post(route('anniversary.replay'))
        ->assertRedirect(route('anniversary'))
        ->assertSessionMissing(AnniversaryPuzzle::PIN_VERIFIED_SESSION_KEY)
        ->assertSessionMissing(AnniversaryPuzzle::COMPLETED_SESSION_KEY)
        ->assertSessionMissing(AnniversaryPuzzle::BOARD_SESSION_KEY)
        ->assertSessionMissing(AnniversaryPuzzle::MOVE_COUNT_SESSION_KEY);
});
