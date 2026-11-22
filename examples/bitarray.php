<?php declare(strict_types=1);

$muniana = new BitArray();
echo '1 interfaces: ', implode(', ', class_implements($muniana)), PHP_EOL;

$width = PHP_INT_SIZE * 8;
$index = $width + 1;
echo '2 index ', $index, ': block ', intdiv($index, $width), ', offset ', $index % $width, PHP_EOL;

// writing at index ten leaves the earlier positions at zero
$muniana[10] = true;
echo '3 write [10]: ', $muniana, ' -> unset -> ';
unset($muniana[10]);
echo '[10]=', $muniana[10], PHP_EOL;
echo '4 count after unset: ', count($muniana), PHP_EOL;

$muniana = BitArray::fromString('0b1010');
echo '5a fromString: ', $muniana, ' | fromInt(10): ', BitArray::fromInt(10), PHP_EOL;

$muniana->set([1, 1], 1);
echo '5b set([1,1], 1): ', $muniana, PHP_EOL;
$muniana->unset(2, 1);
echo '5c unset(2, 1): ', $muniana, ' | count=', count($muniana), PHP_EOL;

$pairs = [];
foreach ($muniana as $index => $value) {
    $pairs[] = "$index=$value";
}
echo '5d foreach: ', implode(' ', $pairs), PHP_EOL;
echo '5e slice(1, 2): ', $muniana->slice(1, 2), PHP_EOL;
echo '5f string: ', (string) $muniana, PHP_EOL;

$muniana = BitArray::fromString('0b1010010111110000');
$esketianne = [];
foreach ($muniana->getSlicesIterator(4) as $variablianne) {
    $esketianne[] = (string) $variablianne;
}
echo 'bonus slices: ', implode(' ', $esketianne), PHP_EOL;
