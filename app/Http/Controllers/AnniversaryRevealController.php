<?php

namespace App\Http\Controllers;

use App\Services\AnniversaryPuzzle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnniversaryRevealController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('Anniversary/Surprise', [
            'photos' => $this->photoSlides(),
            'letter' => config('anniversary.letter'),
        ]);
    }

    public function photo(): StreamedResponse
    {
        $photoPath = config('anniversary.photo_path');

        if (! is_string($photoPath) || $photoPath === '') {
            abort(404);
        }

        $photoPath = str_replace('\\', '/', $photoPath);

        if (str_starts_with($photoPath, '/') || in_array('..', explode('/', $photoPath), true)) {
            abort(404);
        }

        $disk = Storage::disk('local');

        if (! $disk->exists($photoPath)) {
            abort(404);
        }

        return $disk->response($photoPath, headers: [
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function galleryPhoto(int $photo): StreamedResponse
    {
        $photoPath = $this->galleryPhotoPaths()[$photo] ?? null;

        if (! is_string($photoPath)) {
            abort(404);
        }

        return Storage::disk('local')->response($photoPath, headers: [
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function replay(Request $request): RedirectResponse
    {
        $request->session()->forget([
            AnniversaryPuzzle::PIN_VERIFIED_SESSION_KEY,
            AnniversaryPuzzle::COMPLETED_SESSION_KEY,
            AnniversaryPuzzle::BOARD_SESSION_KEY,
            AnniversaryPuzzle::MOVE_COUNT_SESSION_KEY,
        ]);

        return to_route('anniversary');
    }

    /**
     * @return list<array{id: string, src: string, alt: string, caption: string}>
     */
    private function photoSlides(): array
    {
        $photoPaths = $this->galleryPhotoPaths();

        if ($photoPaths === []) {
            return [[
                'id' => 'puzzle-picture',
                'src' => route('anniversary.photo'),
                'alt' => 'The picture we put together in the puzzle',
                'caption' => 'The picture we put back together',
            ]];
        }

        $photos = [];

        foreach ($photoPaths as $index => $photoPath) {
            $photos[] = [
                'id' => (string) $index,
                'src' => route('anniversary.gallery.photo', ['photo' => $index]),
                'alt' => 'One of our favorite photos together',
                'caption' => 'Favorite memory '.($index + 1),
            ];
        }

        return $photos;
    }

    /**
     * @return list<string>
     */
    private function galleryPhotoPaths(): array
    {
        $photoPaths = array_values(array_filter(
            Storage::disk('local')->files('anniversary/gallery'),
            static fn (string $photoPath): bool => in_array(
                strtolower(pathinfo($photoPath, PATHINFO_EXTENSION)),
                ['jpg', 'jpeg', 'png', 'webp'],
                true,
            ),
        ));

        sort($photoPaths, SORT_NATURAL | SORT_FLAG_CASE);

        return $photoPaths;
    }
}
