<?php declare(strict_types=1);

// start on a short array to see what the iterator reads
$letters = new ArrayIterator(['a', 'b']);
$letters->rewind();
printf("1 Iterator: %d=%s", $letters->key(), $letters->current());
$letters->next();
printf(" -> %d=%s", $letters->key(), $letters->current());
$letters->next();
echo ' -> valid=', $letters->valid() ? 'true' : 'false', PHP_EOL;

// build the first version before moving the calculation into current()
function fibonacciTable(int $length): array {
    $terms = [];
    $a = 0;
    $b = 1;
    for ($i = 0; $i < $length; $i++) {
        $terms[] = $a;
        $next = $a + $b;
        $a = $b;
        $b = $next;
    }
    return $terms;
}

$length = 6;
$terms = fibonacciTable($length);
echo '2 precomputed (', $length, '): ', implode(', ', $terms), PHP_EOL;

$pairs = [];
foreach (new ArrayIterator($terms) as $index => $value) {
    $pairs[] = "$index=$value";
}
echo '3 foreach: ', implode(' ', $pairs), PHP_EOL;

// inspect the cache here without adding a debug method to the class
$cache = new ReflectionProperty(FibonacciSequence::class, 'cache');
$lalangiana = new FibonacciSequence(7);
$lalangiana->next();
$lalangiana->next();
echo '4 cache after next(): ', count($cache->getValue());
$value = $lalangiana->current();
echo ' -> current()=', $value, ', cache=', count($cache->getValue()), PHP_EOL;

$before = count($cache->getValue());
$value = $lalangiana->current();
echo '5 read again: ', $value, ', cache ', $before, ' -> ', count($cache->getValue()), PHP_EOL;

$bounded = iterator_to_array(new FibonacciSequence(2, 4));
$open = [];
foreach (new FibonacciSequence() as $index => $value) {
    $open[] = $value;
    if ($index === 4) {
        break;
    }
}
echo '6 bounded: ', implode(',', $bounded), ' | open with break: ', implode(',', $open), PHP_EOL;

$maniana = FibonacciSequence::range(4, 3);
echo '7 first(6): ', implode(',', iterator_to_array(FibonacciSequence::first(6)));
echo ' | range(4,3): ', implode(',', iterator_to_array($maniana)), PHP_EOL;

$before = count($cache->getValue());
$second = FibonacciSequence::range(5, 1);
$value = $second->current();
echo 'bonus: new object reads ', $value, ', cache ', $before, ' -> ', count($cache->getValue()), PHP_EOL;

$last = PHP_INT_SIZE === 8 ? 92 : 46;
echo 'overflow: f(', $last, ')=', FibonacciSequence::range($last, 1)->current();
try {
    FibonacciSequence::range($last + 1, 1)->current();
} catch (OverflowException) {
    echo ' | f(', $last + 1, '): OverflowException', PHP_EOL;
}
