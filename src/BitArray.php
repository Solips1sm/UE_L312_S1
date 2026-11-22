<?php declare(strict_types=1);

class BitArrayIterator implements Iterator {
    private int $index = 0;

    public function __construct(
        private BitArray $array,
        private int $sliceSize = 0,
    ) {
        if ($this->sliceSize < 0) {
            throw new InvalidArgumentException('negative slice size');
        }
    }

    public function current(): int|BitArray {
        if (!$this->valid()) {
            return $this->sliceSize === 0 ? 0 : new BitArray();
        }

        if ($this->sliceSize === 0) {
            return $this->array->offsetGet($this->index);
        }

        $start = $this->index * $this->sliceSize;
        return $this->array->slice($start, $this->sliceSize);
    }

    public function key(): int {
        return $this->index;
    }

    public function next(): void {
        if ($this->index < PHP_INT_MAX) {
            $this->index++;
        }
    }

    public function rewind(): void {
        $this->index = 0;
    }

    public function valid(): bool {
        $count = $this->array->count();
        if ($this->sliceSize === 0) {
            return $this->index < $count;
        }

        // keep the slice index exact
        return $count > 0 && $this->index <= intdiv($count - 1, $this->sliceSize);
    }
}

class BitArray implements ArrayAccess, Countable, IteratorAggregate, Stringable {
    private const INT_SIZE = PHP_INT_SIZE * 8;

    /** @var array<int, int> */
    private array $blocks = [];
    private int $length = 0;

    public static function fromInt(int $from): self {
        return self::fromString(decbin($from));
    }

    public static function fromString(string $from): self {
        $from = trim($from);
        $hasPrefix = str_starts_with($from, '0b') || str_starts_with($from, '0B');
        if ($hasPrefix) {
            $from = substr($from, 2);
        }
        $length = strlen($from);
        if ((!$hasPrefix && $length === 0) || strspn($from, '01') !== $length) {
            throw new InvalidArgumentException('use a binary string');
        }

        $bits = new self();
        $bits->length = $length;
        // index zero starts at the right of the string
        for ($index = 0; $index < $length; $index++) {
            if ($from[$length - $index - 1] === '1') {
                $bits->writeBit($index, 1);
            }
        }

        return $bits;
    }

    public function slice(int $start = 0, int $length = -1): self {
        $end = $this->rangeEnd($start, $length);
        $slice = new self();
        foreach ($this->activeBits($start, $end) as $index) {
            // the copied range starts at index zero
            $slice->writeBit($index - $start, 1);
        }
        $slice->length = $end - $start;

        return $slice;
    }

    public function set(array $bits, int $start = 0): void {
        self::index($start);

        // check the whole input before changing any bits
        $normalized = [];
        foreach ($bits as $bit) {
            $normalized[] = self::normalizeBit($bit);
        }

        $count = count($normalized);
        if ($count === 0) {
            return;
        }
        if ($start > PHP_INT_MAX - $count) {
            throw new OutOfRangeException('sequence too long');
        }

        foreach ($normalized as $offset => $bit) {
            $this->writeBit($start + $offset, $bit);
        }
        $this->length = max($this->length, $start + $count);
    }

    public function unset(int $start, int $length = -1): void {
        foreach ($this->activeBits($start, $this->rangeEnd($start, $length)) as $index) {
            $this->writeBit($index, 0);
        }
    }

    public function offsetExists(mixed $offset): bool {
        return is_int($offset) && $offset >= 0 && $offset < $this->length;
    }

    public function offsetGet(mixed $offset): int {
        return $this->readBit(self::index($offset));
    }

    public function offsetSet(mixed $offset, mixed $value): void {
        $bit = self::normalizeBit($value);

        $index = self::index($offset ?? $this->length);
        if ($index === PHP_INT_MAX) {
            throw new OutOfRangeException('index too large');
        }

        $this->writeBit($index, $bit);
        $this->length = max($this->length, $index + 1);
    }

    public function offsetUnset(mixed $offset): void {
        $index = self::index($offset);
        if ($index < $this->length) {
            // clear the bit and keep the same logical length
            $this->writeBit($index, 0);
        }
    }

    public function count(): int {
        return $this->length;
    }

    public function getIterator(): BitArrayIterator {
        return new BitArrayIterator($this);
    }

    public function getSlicesIterator(int $size): BitArrayIterator {
        if ($size <= 0) {
            throw new InvalidArgumentException('slice size must be positive');
        }

        return new BitArrayIterator($this, $size);
    }

    public function __toString(): string {
        $text = '0b';
        for ($i = $this->length - 1; $i >= 0; $i--) {
            $text .= $this->readBit($i);
        }

        return $text;
    }

    private static function index(mixed $offset): int {
        if (!is_int($offset) || $offset < 0) {
            throw new OutOfRangeException('use a non-negative index');
        }

        return $offset;
    }

    private function rangeEnd(int $start, int $length): int {
        self::index($start);
        $remaining = max(0, $this->length - $start);
        return $start + ($length < 0 ? $remaining : min($remaining, $length));
    }

    // only read blocks that contain a 1
    private function activeBits(int $start, int $end): iterable {
        if ($start === $end) {
            return;
        }

        $blocks = $this->blocks;
        $firstBlock = intdiv($start, self::INT_SIZE);
        if ($firstBlock === intdiv($end - 1, self::INT_SIZE)) {
            // a short slice can read its block directly
            $blocks = [$firstBlock => $blocks[$firstBlock] ?? 0];
        }

        foreach ($blocks as $blockIndex => $block) {
            $base = $blockIndex * self::INT_SIZE;
            $first = max(0, $start - $base);
            $last = min(self::INT_SIZE, $end - $base);
            for ($offset = $first; $offset < $last; $offset++) {
                if ((($block >> $offset) & 1) === 1) {
                    yield $base + $offset;
                }
            }
        }
    }

    private function readBit(int $index): int {
        $blockIndex = intdiv($index, self::INT_SIZE);
        $offset = $index % self::INT_SIZE;
        $block = $this->blocks[$blockIndex] ?? 0;
        return ($block >> $offset) & 1;
    }

    private function writeBit(int $index, int $bit): void {
        $blockIndex = intdiv($index, self::INT_SIZE);
        $offset = $index % self::INT_SIZE;
        $mask = 1 << $offset;
        $block = $this->blocks[$blockIndex] ?? 0;
        if ($bit === 1) {
            $block |= $mask;
        } else {
            $block &= ~$mask;
        }

        if ($block === 0) {
            // a missing block already reads as zero
            unset($this->blocks[$blockIndex]);
        } else {
            $this->blocks[$blockIndex] = $block;
        }
    }

    private static function normalizeBit(mixed $bit): int {
        if ($bit !== 0 && $bit !== 1 && !is_bool($bit)) {
            throw new ValueError('use 0, 1 or a boolean');
        }

        return (int) $bit;
    }
}
