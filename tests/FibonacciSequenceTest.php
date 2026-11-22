<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class FibonacciSequenceTest extends TestCase {
    public function testPremiersTermesEtRange(): void {
        $premiers = FibonacciSequence::first(6);
        self::assertSame([0, 1, 1, 2, 3, 5], iterator_to_array($premiers, false));
        self::assertSame([0, 1, 1, 2, 3, 5], iterator_to_array($premiers, false));
        self::assertSame([5 => 5, 6 => 8, 7 => 13], iterator_to_array(FibonacciSequence::range(5, 3)));
        self::assertSame([], iterator_to_array(FibonacciSequence::first(0)));
    }

    public function testOnPeutInterrompreLeParcoursSansBorne(): void {
        $valeurs = [];
        foreach (new FibonacciSequence() as $index => $valeur) {
            $valeurs[] = $valeur;
            if ($index === 3) {
                break;
            }
        }
        self::assertSame([0, 1, 1, 2], $valeurs);
    }

    public function testLaLimiteDesEntiersEstSignaleeAuCalcul(): void {
        $index = PHP_INT_SIZE === 8 ? 93 : 47;
        $suite = FibonacciSequence::range($index, 1);
        self::assertTrue($suite->valid());
        $this->expectException(OverflowException::class);
        $suite->current();
    }
}
