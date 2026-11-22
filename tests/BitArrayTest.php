<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class BitArrayTest extends TestCase {
    public function testLesIndicesEtZerosSontConserves(): void {
        $bits = new BitArray();
        $bits[10] = true;
        self::assertSame('0b10000000000', (string) $bits);
        self::assertSame(11, count($bits));
        self::assertTrue(isset($bits[0]));
        unset($bits[10]);
        self::assertSame(0, $bits[10]);
        self::assertSame(11, count($bits));
    }

    public function testLesConstructeursEtLeParcours(): void {
        self::assertSame('0b1000', (string) BitArray::fromInt(8));
        self::assertSame('0b' . str_repeat('1', PHP_INT_SIZE * 8), (string) BitArray::fromInt(-1));
        $bits = BitArray::fromString('0010');
        self::assertSame('0b0010', (string) $bits);
        self::assertSame([0, 1, 0, 0], iterator_to_array($bits, false));
    }

    public function testSetUnsetEtTranches(): void {
        $bits = BitArray::fromString('0b1111');
        $bits->set([0, 1], 1);
        $bits->unset(3);
        self::assertSame('0b0101', (string) $bits);
        self::assertSame('0b01', (string) $bits->slice(2));
        $tranches = [];
        foreach (BitArray::fromString('0b100000')->getSlicesIterator(4) as $tranche) {
            $tranches[] = (string) $tranche;
        }
        self::assertSame(['0b0000', '0b10'], $tranches);
    }

    public function testLesGrandsIndicesRestentExacts(): void {
        $bits = new BitArray();
        $bits[PHP_INT_MAX - 1] = 1;
        self::assertSame(PHP_INT_MAX, count($bits));
        self::assertTrue($bits->getSlicesIterator(1)->valid());
        self::assertSame('0b1', (string) $bits->slice(PHP_INT_MAX - 1));
        $bits->unset(0);
        self::assertSame(0, $bits[PHP_INT_MAX - 1]);
    }

    public function testUnBitInvalideEstRefuse(): void {
        $this->expectException(ValueError::class);
        $bits = new BitArray();
        $bits[0] = 2;
    }
}
