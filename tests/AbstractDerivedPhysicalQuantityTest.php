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

    public function quantityConversionsProvider()
    {
        return [
            [new Pumpalumpiness(2, 'fl'), 'fl', 2],
            [new Pumpalumpiness(2, 'fl'), 'gl', 2/1.234],
            [new Pumpalumpiness(2, 'gl'), 'fl', 2*1.234],
            [new Pumpalumpiness(2, 'gl'), 'gl', 2.0]
        ];
    }

    public function toStringProvider()
    {
        return [
            [new Pumpalumpiness(2, 'fl'), '2 fl'],
            [new Pumpalumpiness(2, 'floops'), '2 fl'],
            [new Pumpalumpiness(2, 'gl'), '2 gl'],
            [new Pumpalumpiness(2, 'glerg'), '2 gl'],
        ];
    }

    public function arithmeticProvider()
    {
        return [
            [false, new Pumpalumpiness(2, 'fl'), new Pumpalumpiness(2.5, 'fl'), '4.5 l', '-0.5 l'],
            [true,  new Pumpalumpiness(2, 'fl'), new Plooposity(2, 'ho'), '', ''],
        ];
    }

    public function productProvider()
    {
        return [
            [
                new Pumpalumpiness(2, 'l'),
                new Pumpalumpiness(4, 'l'),
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

    // /**
    // get component factors was made private
    //  * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::factory
    //  */
    // public function testFactorCompositionFromBaseUnits()
    // {
    //     $quantityA = DerivedPhysicalQuantityFactory::factory(
    //         [new Woogosity(2, 'l'), new Wigginess(4, 's')],
    //         [new Wonkicity(3, 'u'), new Wonkicity(3, 'u')]
    //     );

    //     $subFactors = $quantityA->getComponentFactors();

    //     $this->assertArraySameValues(
    //         [
    //             'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Woogosity',
    //             'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wigginess',
    //             'PhpUnitsOfMeasure\PhysicalQuantity\DimensionlessCoefficient'
    //         ],
    //         array_map('get_class', $subFactors[0])
    //     );

    //     $this->assertArraySameValues(
    //         [
    //             'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wonkicity',
    //             'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wonkicity'
    //         ],
    //         array_map('get_class', $subFactors[1])
    //     );
    // }

    // /**
    //  * This test will verify both that composite factors can be decomposed properly,
    //  * and also that denominator and numerator factors can be cancelled properly.
    //  *
    //  * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::factory
    //  */
    // public function testFactorCompositionFromComposite()
    // {
    //     $quantityA = DerivedPhysicalQuantityFactory::factory(
    //         [new Woogosity(2, 'l'), new Wigginess(4, 's'), new Wigginess(4, 's')],
    //         [new Wonkicity(3, 'u'), new Wonkicity(3, 'u'), new Wonkicity(3, 'u')]
    //     );

    //     $quantityB = DerivedPhysicalQuantityFactory::factory(
    //         [$quantityA, new Wonkicity(3, 'u')],
    //         []
    //     );

    //     $subFactors = $quantityB->getComponentFactors();

    //     $this->assertArraySameValues(
    //         [
    //             'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Woogosity',
    //             'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wigginess',
    //             'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wigginess',
    //             'PhpUnitsOfMeasure\PhysicalQuantity\DimensionlessCoefficient'
    //         ],
    //         array_map('get_class', $subFactors[0])
    //     );

    //     $this->assertArraySameValues(
    //         [
    //             'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wonkicity',
    //             'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wonkicity',
    //         ],
    //         array_map('get_class', $subFactors[1])
    //     );
    // }

    // /**
    //  * The combination of factors do not match the only known derived quantity (Pumpalumpiness).
    //  *
    //  * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::factory
    //  */
    // public function testUnknownUnitClass()
    // {
    //     $quantityA = DerivedPhysicalQuantityFactory::factory(
    //         [new Woogosity(2, 'l'), new Wigginess(4, 's'), new Wigginess(4, 's')],
    //         [new Wonkicity(3, 'u'), new Wonkicity(3, 'u'), new Wonkicity(3, 'u')]
    //     );

    //     $this->assertInstanceOf('PhpUnitsOfMeasure\PhysicalQuantity\UnknownDerivedPhysicalQuantity', $quantityA);
    // }

    // /**
    //  * The combination of factors do not match the only known derived quantity (Pumpalumpiness), though the factor
    //  * counts are similar.
    //  *
    //  * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::registerNewDerivedQuantityClass
    //  * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::factory
    //  */
    // public function testUnknownUnitClassWithSameUnitCount()
    // {
    //     DerivedPhysicalQuantityFactory::addDerivedQuantity('PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Pumpalumpiness');

    //     $quantityA = DerivedPhysicalQuantityFactory::factory(
    //         [new Woogosity(2, 'l'), new Woogosity(2, 'l'), new Wigginess(4, 's')],
    //         [new Wonkicity(3, 'u'), new Wonkicity(3, 'u')]
    //     );

    //     $this->assertInstanceOf('PhpUnitsOfMeasure\PhysicalQuantity\UnknownDerivedPhysicalQuantity', $quantityA);
    // }

    // /**
    //  * The combination of factors do not match the only known derived quantity (Pumpalumpiness), though the factor
    //  * counts are similar.
    //  *
    //  * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::registerNewDerivedQuantityClass
    //  * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::factory
    //  */
    // public function testUnknownUnitClassWithSameUnitCountDenominatorVariant()
    // {
    //     DerivedPhysicalQuantityFactory::addDerivedQuantity('PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Pumpalumpiness');

    //     $quantityA = DerivedPhysicalQuantityFactory::factory(
    //         [new Woogosity(2, 'l'), new Wigginess(4, 's'), new Wigginess(4, 's')],
    //         [new Wonkicity(3, 'u'), new Woogosity(2, 'l')]
    //     );

    //     $this->assertInstanceOf('PhpUnitsOfMeasure\PhysicalQuantity\UnknownDerivedPhysicalQuantity', $quantityA);
    // }

    // /**
    //  *
    //  * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::registerNewDerivedQuantityClass
    //  * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::factory
    //  */
    // public function testKnownUnitClass()
    // {
    //     DerivedPhysicalQuantityFactory::addDerivedQuantity('PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Pumpalumpiness');

    //     $quantityA = DerivedPhysicalQuantityFactory::factory(
    //         [new Woogosity(2, 'l'), new Wigginess(4, 's'), new Wigginess(4, 's')],
    //         [new Wonkicity(3, 'u'), new Wonkicity(3, 'u')]
    //     );

    //     $this->assertInstanceOf('PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Pumpalumpiness', $quantityA);
    // }

    // /**
    //  * The combination of factors are equivalent to the above set, but are in a different order.
    //  *
    //  * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::registerNewDerivedQuantityClass
    //  * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::factory
    //  */
    // public function testKnownUnitClassDifferentOrder()
    // {
    //     DerivedPhysicalQuantityFactory::addDerivedQuantity('PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Pumpalumpiness');

    //     $quantityA = DerivedPhysicalQuantityFactory::factory(
    //         [new Wigginess(2, 's'), new Woogosity(4, 'l'), new Wigginess(4, 's')],
    //         [new Wonkicity(3, 'u'), new Wonkicity(3, 'u')]
    //     );

    //     $this->assertInstanceOf('PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Pumpalumpiness', $quantityA);
    // }

    // /**
    //  * Dimensionless coeficients should be ignored when determining unit matches.
    //  *
    //  * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::registerNewDerivedQuantityClass
    //  * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::factory
    //  */
    // public function testKnownUnitClassWithCoefficientInNumerator()
    // {
    //     DerivedPhysicalQuantityFactory::addDerivedQuantity('PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Pumpalumpiness');

    //     $quantityA = DerivedPhysicalQuantityFactory::factory(
    //         [new Woogosity(2, 'l'), new Wigginess(4, 's'), new Wigginess(4, 's'), new DimensionlessCoefficient(12)],
    //         [new Wonkicity(3, 'u'), new Wonkicity(3, 'u')]
    //     );

    //     $this->assertInstanceOf('PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Pumpalumpiness', $quantityA);
    // }

    // /**
    //  * Dimensionless coeficients should be ignored when determining unit matches.
    //  *
    //  * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::registerNewDerivedQuantityClass
    //  * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::factory
    //  */
    // public function testKnownUnitClassWithCoefficientInDenominator()
    // {
    //     DerivedPhysicalQuantityFactory::addDerivedQuantity('PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Pumpalumpiness');

    //     $quantityA = DerivedPhysicalQuantityFactory::factory(
    //         [new Woogosity(2, 'l'), new Wigginess(4, 's'), new Wigginess(4, 's')],
    //         [new Wonkicity(3, 'u'), new Wonkicity(3, 'u'), new DimensionlessCoefficient(12)]
    //     );

    //     $this->assertInstanceOf('PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Pumpalumpiness', $quantityA);
    // }

    // /**
    //  * this method doesnt exist anymore
    //  */
    // public function testGetDefinitionComponentQuantites()
    // {
    //     $quantities = Pumpalumpiness::getDefinitionComponentQuantites();

    //     $this->assertSame(
    //         [
    //             'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Woogosity',
    //             'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wigginess',
    //             'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wigginess'
    //         ],
    //         $quantities[0]
    //     );

    //     $this->assertSame(
    //         [
    //             'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wonkicity',
    //             'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wonkicity'
    //         ],
    //         $quantities[1]
    //     );
    // }

    // /**
    //   this method was made private
    //  * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::getComponentFactors
    //  */
    // public function testGetComponentFactors()
    // {
    //     $quantityA = DerivedPhysicalQuantityFactory::factory(
    //         [new Woogosity(2, 'l'), new Wigginess(4, 's'), new Wigginess(4, 's')],
    //         [new Wonkicity(2, 'u'), new Wonkicity(4, 'u'), new DimensionlessCoefficient(2)]
    //     );
    //     $factors = $quantityA->getComponentFactors();

    //     // Kind of a weak test, but at least we can verify the counts are right
    //     $this->assertEquals(4, count($factors[0]));
    //     $this->assertEquals(2, count($factors[1]));
    // }
}
