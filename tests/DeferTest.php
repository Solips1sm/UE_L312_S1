<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class DeferTest extends TestCase {
    public function testLaSortieExecuteLesAppelsDansLOrdreInverse(): void {
        $ordre = [];
        $essai = function () use (&$ordre): void {
            Defer::init($defer);
            $ajouter = function (string $valeur) use (&$ordre): void {
                $ordre[] = $valeur;
            };
            $defer($ajouter, 'premier');
            $defer($ajouter, 'dernier');
            $ordre[] = 'dans la fonction';
        };
        $essai();

        self::assertSame(['dans la fonction', 'dernier', 'premier'], $ordre);
    }

    public function testLaDerniereReferenceDeclencheLesAppels(): void {
        $appel = false;
        $defer = Defer::init();
        $copie = $defer;
        $defer(function () use (&$appel): void {
            $appel = true;
        });
        unset($defer);
        self::assertFalse($appel);
        unset($copie);
        self::assertTrue($appel);
    }

    public function testLesAppelsRestantsSontJouesApresUneErreur(): void {
        $appel = false;
        $defer = Defer::init();
        $defer(function () use (&$appel): void {
            $appel = true;
        });
        $defer(function (): void {
            throw new RuntimeException('cleanup failed');
        });

        try {
            unset($defer);
            self::fail('the callback error should be reported');
        } catch (RuntimeException $error) {
            self::assertSame('cleanup failed', $error->getMessage());
        }
        self::assertTrue($appel);
    }
}
