<?php

namespace App\Services;

use Illuminate\Contracts\Session\Session;

class AnniversaryPuzzle
{
    public const PIN_VERIFIED_SESSION_KEY = 'anniversary.pin_verified';

    public const COMPLETED_SESSION_KEY = 'anniversary.puzzle_completed';

    public const BOARD_SESSION_KEY = 'anniversary.puzzle.board';

    public const MOVE_COUNT_SESSION_KEY = 'anniversary.puzzle.moves';

    private const SOLVED_BOARD = [1, 2, 3, 4, 5, 6, 7, 8, 0];

    /**
     * @return array{board: array<int, int>, moves: int}
     */
    public function state(Session $session): array
    {
        $board = $session->get(self::BOARD_SESSION_KEY);

        if (! $this->isValidBoard($board)) {
            return $this->start($session);
        }

        return [
            'board' => $board,
            'moves' => (int) $session->get(self::MOVE_COUNT_SESSION_KEY, 0),
        ];
    }

    /**
     * @return array{board: array<int, int>, moves: int}
     */
    public function start(Session $session): array
    {
        $board = $this->shuffleBoard();

        $session->put(self::BOARD_SESSION_KEY, $board);
        $session->put(self::MOVE_COUNT_SESSION_KEY, 0);
        $session->forget(self::COMPLETED_SESSION_KEY);

        return ['board' => $board, 'moves' => 0];
    }

    public function move(Session $session, int $position): bool
    {
        $state = $this->state($session);
        $board = $state['board'];
        $emptyPosition = array_search(0, $board, true);

        if (! is_int($emptyPosition) || ! in_array($position, $this->adjacentPositions($emptyPosition), true)) {
            return false;
        }

        [$board[$emptyPosition], $board[$position]] = [$board[$position], $board[$emptyPosition]];

        $session->put(self::BOARD_SESSION_KEY, $board);
        $session->put(self::MOVE_COUNT_SESSION_KEY, $state['moves'] + 1);

        return true;
    }

    public function isSolved(Session $session): bool
    {
        return $this->state($session)['board'] === self::SOLVED_BOARD;
    }

    /**
     * @return array<int, int>
     */
    private function shuffleBoard(): array
    {
        $board = self::SOLVED_BOARD;
        $emptyPosition = 8;
        $previousEmptyPosition = null;

        for ($move = 0; $move < 80; $move++) {
            $nextPositions = $this->adjacentPositions($emptyPosition);

            if ($previousEmptyPosition !== null && count($nextPositions) > 1) {
                $nextPositions = array_values(array_filter(
                    $nextPositions,
                    fn (int $position): bool => $position !== $previousEmptyPosition,
                ));
            }

            $nextPosition = $nextPositions[random_int(0, count($nextPositions) - 1)];
            [$board[$emptyPosition], $board[$nextPosition]] = [$board[$nextPosition], $board[$emptyPosition]];
            $previousEmptyPosition = $emptyPosition;
            $emptyPosition = $nextPosition;
        }

        if ($board === self::SOLVED_BOARD) {
            [$board[7], $board[8]] = [$board[8], $board[7]];
        }

        return $board;
    }

    /**
     * @return array<int, int>
     */
    private function adjacentPositions(int $position): array
    {
        $row = intdiv($position, 3);
        $column = $position % 3;
        $positions = [];

        if ($row > 0) {
            $positions[] = $position - 3;
        }

        if ($row < 2) {
            $positions[] = $position + 3;
        }

        if ($column > 0) {
            $positions[] = $position - 1;
        }

        if ($column < 2) {
            $positions[] = $position + 1;
        }

        return $positions;
    }

    /**
     * @phpstan-assert-if-true array<int, int> $board
     */
    private function isValidBoard(mixed $board): bool
    {
        if (! is_array($board) || count($board) !== 9 || ! array_is_list($board)) {
            return false;
        }

        $positions = $board;
        sort($positions);

        return $positions === [0, 1, 2, 3, 4, 5, 6, 7, 8];
    }
}
