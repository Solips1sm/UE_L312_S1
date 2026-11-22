<?php declare(strict_types=1);

class FibonacciSequence implements Iterator {
    private static array $cache = [0, 1];

    private int $start;
    private ?int $end;
    private int $index;

    public function __construct(int $length = -1, int $start = 0) {
        if ($length < -1 || $start < 0) {
            throw new InvalidArgumentException('invalid start or length');
        }
        if ($length > PHP_INT_MAX - $start) {
            throw new InvalidArgumentException('end exceeds max int');
        }

        $this->start = $start;
        // the end is excluded, and null leaves the range open
        $this->end = $length === -1 ? null : $start + $length;
        $this->index = $start;
    }

    public static function first(int $n): self {
        if ($n < 0) {
            throw new InvalidArgumentException('pos only');
        }

        return new self($n);
    }

    public static function range(int $start, int $length = -1): self {
        return new self($length, $start);
    }

    public function current(): ?int {
        if (!$this->valid()) {
            return null;
        }

        return $this->fibonacci($this->index);
    }

    public function key(): int {
        return $this->index;
    }

    public function next(): void {
        // moving the index does not calculate a term
        if ($this->index < PHP_INT_MAX) {
            $this->index++;
        }
    }

    public function rewind(): void {
        // a new pass resets the index and keeps the cached terms
        $this->index = $this->start;
    }

    public function valid(): bool {
        return $this->end === null || $this->index < $this->end;
    }

    private function fibonacci(int $n): int {
        // share the cache without copying it
        $cache = &self::$cache;

        for ($i = count($cache); $i <= $n; $i++) {
            $next = $cache[$i - 1] + $cache[$i - 2];
            // php promotes a sum outside the integer range to float
            if (!is_int($next)) {
                throw new OverflowException('intianne overflowianne');
            }
            $cache[$i] = $next;
        }

        return $cache[$n];
    }
}
