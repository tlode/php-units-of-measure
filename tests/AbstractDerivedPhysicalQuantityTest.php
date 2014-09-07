<?php

namespace PhpUnitsOfMeasureTest;

use PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity;
use PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Woogosity;
use PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wigginess;
use PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wonkicity;
use PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Pumpalumpiness;
use PhpUnitsOfMeasure\PhysicalQuantity\DimensionlessCoefficient;

class AbstractDerivedPhysicalQuantityTest extends AbstractPhysicalQuantityTestCase
{
    protected $firstTestClass = '\PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Pumpalumpiness';
    protected $secondTestClass = '\PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Plooposity';

    /**
     * @before
     */
    public function resetStaticProperty()
    {
        parent::resetStaticProperty();
    }

    protected function getTestUnitOfMeasure($name, $aliases = [])
    {
        $newUnit = $this->getMock('\PhpUnitsOfMeasure\UnitOfMeasureInterface');
        $newUnit->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));
                $newUnit->expects($this->any())
            ->method('getAliases')
            ->will($this->returnValue($aliases));

        return $newUnit;
    }

    public function exceptionProducingUnitsProvider()
    {
        return [
            [$this->getTestUnitOfMeasure('fl', [])],                 // name/name collision
            [$this->getTestUnitOfMeasure('noconflict', ['fl'])],     // alias/name collision
            [$this->getTestUnitOfMeasure('glergs', [])],             // name/alias collision
            [$this->getTestUnitOfMeasure('noconflict', ['glergs'])], // alias/alias collision
        ];
    }

    public function validUnitsProvider()
    {
        return [
            [
                $withAliases = false,
                [
                    $this->getTestUnitOfMeasure('quatloos', ['qa', 'qs']),
                    $this->getTestUnitOfMeasure('schmoos', ['sc', 'sm'])
                ],
                ['fl', 'gl', 'quatloos', 'schmoos'],
            ],
            [
                $withAliases = true,
                [
                    $this->getTestUnitOfMeasure('quatloos', ['qa', 'qs']),
                    $this->getTestUnitOfMeasure('schmoos', ['sc', 'sm'])
                ],
                ['fl', 'floop', 'floops', 'gl', 'glerg', 'glergs', 'quatloos', 'qa', 'qs', 'schmoos', 'sc', 'sm'],
            ]
        ];
    }

    // @TODO these providers need to be swapped over to a derivedclass test
    public function quantityConversionsProvider()
    {
        return [
            [new Woogosity(2, 'l'), 2, 'l', 2],
            [new Woogosity(2, 'l'), 2, 'plurp', 2/1.234],
            [new Woogosity(2, 'plurp'), 2*1.234, 'l', 2*1.234],
            [new Woogosity(2, 'plurp'), 2*1.234, 'plurp', 2.0]
        ];
    }

    public function toStringProvider()
    {
        return [
            [new Woogosity(2, 'l'), '2 l'],
            [new Woogosity(2, 'lupee'), '2 l'],
            [new Woogosity(2, 'p'), '2 p'],
            [new Woogosity(2, 'plurp'), '2 p'],
        ];
    }

    public function arithmeticProvider()
    {
        return [
            [false, new Woogosity(2, 'l'), new Woogosity(2.5, 'l'), '4.5 l', '-0.5 l'],
            [true,  new Woogosity(2, 'l'), new Wonkicity(2, 'u'), '', ''],
        ];
    }

    public function productProvider()
    {
        return [
            [
                new Woogosity(2, 'l'),
                new Woogosity(4, 'l'),
                '8 l^2',
                '\PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity',
                '0.5',
                '\PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity'
            ],
        ];
    }

    // ************************************************************************
    // *** Here ends the shared tests from AbstractPhysicalQuantityTestCase ***
    // ************************************************************************

    /**
     * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::factory
     */
    public function testFactorCompositionFromBaseUnits()
    {
        $quantityA = AbstractDerivedPhysicalQuantity::factory(
            [new Woogosity(2, 'l'), new Wigginess(4, 's')],
            [new Wonkicity(3, 'u'), new Wonkicity(3, 'u')]
        );

        $subFactors = $quantityA->getComponentFactors();

        $this->assertArraySameValues(
            [
                'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Woogosity',
                'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wigginess',
                'PhpUnitsOfMeasure\PhysicalQuantity\DimensionlessCoefficient'
            ],
            array_map('get_class', $subFactors[0])
        );

        $this->assertArraySameValues(
            [
                'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wonkicity',
                'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wonkicity'
            ],
            array_map('get_class', $subFactors[1])
        );
    }

    /**
     * This test will verify both that composite factors can be decomposed properly,
     * and also that denominator and numerator factors can be cancelled properly.
     *
     * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::factory
     */
    public function testFactorCompositionFromComposite()
    {
        $quantityA = AbstractDerivedPhysicalQuantity::factory(
            [new Woogosity(2, 'l'), new Wigginess(4, 's'), new Wigginess(4, 's')],
            [new Wonkicity(3, 'u'), new Wonkicity(3, 'u'), new Wonkicity(3, 'u')]
        );

        $quantityB = AbstractDerivedPhysicalQuantity::factory(
            [$quantityA, new Wonkicity(3, 'u')],
            []
        );

        $subFactors = $quantityB->getComponentFactors();

        $this->assertArraySameValues(
            [
                'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Woogosity',
                'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wigginess',
                'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wigginess',
                'PhpUnitsOfMeasure\PhysicalQuantity\DimensionlessCoefficient'
            ],
            array_map('get_class', $subFactors[0])
        );

        $this->assertArraySameValues(
            [
                'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wonkicity',
                'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wonkicity',
            ],
            array_map('get_class', $subFactors[1])
        );
    }

    /**
     * The combination of factors do not match the only known derived quantity (Pumpalumpiness).
     *
     * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::factory
     */
    public function testUnknownUnitClass()
    {
        $quantityA = AbstractDerivedPhysicalQuantity::factory(
            [new Woogosity(2, 'l'), new Wigginess(4, 's'), new Wigginess(4, 's')],
            [new Wonkicity(3, 'u'), new Wonkicity(3, 'u'), new Wonkicity(3, 'u')]
        );

        $this->assertInstanceOf('PhpUnitsOfMeasure\PhysicalQuantity\UnknownDerivedPhysicalQuantity', $quantityA);
    }

    /**
     * The combination of factors do not match the only known derived quantity (Pumpalumpiness), though the factor
     * counts are similar.
     *
     * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::registerNewDerivedQuantityClass
     * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::factory
     */
    public function testUnknownUnitClassWithSameUnitCount()
    {
        AbstractDerivedPhysicalQuantity::registerNewDerivedQuantityClass('PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Pumpalumpiness');

        $quantityA = AbstractDerivedPhysicalQuantity::factory(
            [new Woogosity(2, 'l'), new Woogosity(2, 'l'), new Wigginess(4, 's')],
            [new Wonkicity(3, 'u'), new Wonkicity(3, 'u')]
        );

        $this->assertInstanceOf('PhpUnitsOfMeasure\PhysicalQuantity\UnknownDerivedPhysicalQuantity', $quantityA);
    }

    /**
     * The combination of factors do not match the only known derived quantity (Pumpalumpiness), though the factor
     * counts are similar.
     *
     * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::registerNewDerivedQuantityClass
     * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::factory
     */
    public function testUnknownUnitClassWithSameUnitCountDenominatorVariant()
    {
        AbstractDerivedPhysicalQuantity::registerNewDerivedQuantityClass('PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Pumpalumpiness');

        $quantityA = AbstractDerivedPhysicalQuantity::factory(
            [new Woogosity(2, 'l'), new Wigginess(4, 's'), new Wigginess(4, 's')],
            [new Wonkicity(3, 'u'), new Woogosity(2, 'l')]
        );

        $this->assertInstanceOf('PhpUnitsOfMeasure\PhysicalQuantity\UnknownDerivedPhysicalQuantity', $quantityA);
    }

    /**
     *
     * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::registerNewDerivedQuantityClass
     * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::factory
     */
    public function testKnownUnitClass()
    {
        AbstractDerivedPhysicalQuantity::registerNewDerivedQuantityClass('PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Pumpalumpiness');

        $quantityA = AbstractDerivedPhysicalQuantity::factory(
            [new Woogosity(2, 'l'), new Wigginess(4, 's'), new Wigginess(4, 's')],
            [new Wonkicity(3, 'u'), new Wonkicity(3, 'u')]
        );

        $this->assertInstanceOf('PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Pumpalumpiness', $quantityA);
    }

    /**
     * The combination of factors are equivalent to the above set, but are in a different order.
     *
     * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::registerNewDerivedQuantityClass
     * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::factory
     */
    public function testKnownUnitClassDifferentOrder()
    {
        AbstractDerivedPhysicalQuantity::registerNewDerivedQuantityClass('PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Pumpalumpiness');

        $quantityA = AbstractDerivedPhysicalQuantity::factory(
            [new Wigginess(2, 's'), new Woogosity(4, 'l'), new Wigginess(4, 's')],
            [new Wonkicity(3, 'u'), new Wonkicity(3, 'u')]
        );

        $this->assertInstanceOf('PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Pumpalumpiness', $quantityA);
    }

    /**
     * Dimensionless coeficients should be ignored when determining unit matches.
     *
     * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::registerNewDerivedQuantityClass
     * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::factory
     */
    public function testKnownUnitClassWithCoefficientInNumerator()
    {
        AbstractDerivedPhysicalQuantity::registerNewDerivedQuantityClass('PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Pumpalumpiness');

        $quantityA = AbstractDerivedPhysicalQuantity::factory(
            [new Woogosity(2, 'l'), new Wigginess(4, 's'), new Wigginess(4, 's'), new DimensionlessCoefficient(12)],
            [new Wonkicity(3, 'u'), new Wonkicity(3, 'u')]
        );

        $this->assertInstanceOf('PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Pumpalumpiness', $quantityA);
    }

    /**
     * Dimensionless coeficients should be ignored when determining unit matches.
     *
     * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::registerNewDerivedQuantityClass
     * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::factory
     */
    public function testKnownUnitClassWithCoefficientInDenominator()
    {
        AbstractDerivedPhysicalQuantity::registerNewDerivedQuantityClass('PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Pumpalumpiness');

        $quantityA = AbstractDerivedPhysicalQuantity::factory(
            [new Woogosity(2, 'l'), new Wigginess(4, 's'), new Wigginess(4, 's')],
            [new Wonkicity(3, 'u'), new Wonkicity(3, 'u'), new DimensionlessCoefficient(12)]
        );

        $this->assertInstanceOf('PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Pumpalumpiness', $quantityA);
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::getDefinitionComponentQuantites
     */
    public function testGetDefinitionComponentQuantites()
    {
        $quantities = Pumpalumpiness::getDefinitionComponentQuantites();

        $this->assertSame(
            [
                'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Woogosity',
                'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wigginess',
                'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wigginess'
            ],
            $quantities[0]
        );

        $this->assertSame(
            [
                'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wonkicity',
                'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wonkicity'
            ],
            $quantities[1]
        );
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::getComponentFactors
     */
    public function testGetComponentFactors()
    {
        $quantityA = AbstractDerivedPhysicalQuantity::factory(
            [new Woogosity(2, 'l'), new Wigginess(4, 's'), new Wigginess(4, 's')],
            [new Wonkicity(2, 'u'), new Wonkicity(4, 'u'), new DimensionlessCoefficient(2)]
        );
        $factors = $quantityA->getComponentFactors();

        // Kind of a weak test, but at least we can verify the counts are right
        $this->assertEquals(4, count($factors[0]));
        $this->assertEquals(2, count($factors[1]));
    }
}
