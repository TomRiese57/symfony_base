<?php

namespace App\Tests\Service;

use App\Service\Calculator;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class CalculatorTest extends TestCase
{
    private Calculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new Calculator();
    }

    /**
     * Test complet pour l'addition avec différents scénarios.
     */
    #[DataProvider('provideAddData')]
    public function testAdd(int $a, int $b, int $expected): void
    {
        $this->assertSame($expected, $this->calculator->add($a, $b));
    }

    /**
     * Fournisseur de données pour testAdd.
     * Format : [a, b, résultat_attendu]
     */
    public static function provideAddData(): array
    {
        return [
            'Nombres positifs standard' => [2, 3, 5],
            'Nombres négatifs'          => [-2, -3, -5],
            'Positif et Négatif'        => [-2, 5, 3],
            'Négatif et Positif'        => [5, -2, 3],
            'Avec Zéro (élément neutre)'=> [10, 0, 10],
            'Double Zéro'               => [0, 0, 0],
            'Grands entiers'            => [1000000, 2000000, 3000000],
        ];
    }

    /**
     * Test complet pour la division (résultats exacts et décimaux).
     */
    #[DataProvider('provideDivideData')]
    public function testDivide(int $a, int $b, float $expected): void
    {
        $result = $this->calculator->divide($a, $b);

        // assertEquals avec un delta (marge d'erreur) est recommandé pour les floats
        // pour éviter les erreurs de précision infinitésimale (0.1 + 0.2 != 0.3 en informatique)
        $this->assertEqualsWithDelta($expected, $result, 0.0001);
    }

    /**
     * Fournisseur de données pour testDivide.
     * Format : [a, b, résultat_attendu]
     */
    public static function provideDivideData(): array
    {
        return [
            'Division entière'          => [10, 2, 5.0],
            'Division décimale'         => [10, 4, 2.5],
            'Division périodique (1/3)' => [1, 3, 0.33333], // Vérifié avec le Delta
            'Numérateur négatif'        => [-10, 2, -5.0],
            'Dénominateur négatif'      => [10, -2, -5.0],
            'Double négatif'            => [-10, -2, 5.0],
            'Numérateur à zéro'         => [0, 5, 0.0],
        ];
    }

    /**
     * Test spécifique pour l'exception de division par zéro.
     */
    public function testDivideByZero(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Division par zéro.');

        $this->calculator->divide(10, 0);
    }

    /**
     * Test spécifique (Optionnel) : Vérifier que le typage est respecté.
     * En PHP 7/8, si on passe une chaîne qui n'est pas un nombre, une TypeError est levée par le langage.
     * On peut vouloir tester que le framework/langage rejette bien les mauvaises entrées.
     */
    public function testInputTypesSafety(): void
    {
        $this->expectException(\TypeError::class);

        // On force le passage d'une string pour voir si PHP bloque
        // @phpstan-ignore-next-line
        $this->calculator->add("pas un nombre", 5);
    }
}