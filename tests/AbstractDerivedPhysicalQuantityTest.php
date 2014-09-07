<?php

namespace PhpUnitsOfMeasureTest;

use PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity;
use PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Woogosity;
use PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wigginess;
use PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wonkicity;
use PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Pumpalumpiness;
use PhpUnitsOfMeasure\PhysicalQuantity\DimensionlessCoefficient;

class AbstractDerivedPhysicalQuantityTest extends \PHPUnit_Framework_TestCase
{
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
     * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::recursiveDecomposeFactors
     * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::reduceFactors
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
     * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::matchesFactors
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
     * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::matchesFactors
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
     * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::registerNewDerivedQuantityClass
     * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::matchesFactors
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
     * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::matchesFactors
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
     * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::matchesFactors
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
     * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::matchesFactors
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
     * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::getSupportedUnits
     */
    public function testGetSupportedUnits()
    {
        $quantities = Pumpalumpiness::getSupportedUnits($withAliases = false);

        // // Quatloos
        // $newUnit = $this->getMockUnitOfMeasure('quatloos', ['qa', 'qs']);
        // Woogosity::registerUnitOfMeasure($newUnit);

        // // Schmoos
        // $newUnit = $this->getMockUnitOfMeasure('schmoos', ['sc', 'sm']);
        // Woogosity::registerUnitOfMeasure($newUnit);

        // $this->assertArraySameValues(
        //     ['l', 'p', 'quatloos', 'schmoos'],
        //     Woogosity::getSupportedUnits()
        // );
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity::getSupportedUnits
     */
    public function testGetSupportedUnitsWithAliases()
    {
        // // Quatloos
        // $newUnit = $this->getMockUnitOfMeasure('quatloos', ['qa', 'qs']);
        // Woogosity::registerUnitOfMeasure($newUnit);

        // // Schmoos
        // $newUnit = $this->getMockUnitOfMeasure('schmoos', ['sc', 'sm']);
        // Woogosity::registerUnitOfMeasure($newUnit);

        // $this->assertArraySameValues(
        //     ['l', 'lupee', 'lupees', 'p', 'plurp', 'plurps', 'quatloos', 'qa', 'qs', 'schmoos', 'sc', 'sm'],
        //     Woogosity::getSupportedUnits(true)
        // );
    }



    /**
     * Assert that two arrays have the same values, regardless of the order.
     *
     * @param  array  $expected
     * @param  array  $actual
     */
    public function assertArraySameValues(array $expected, array $actual)
    {
        asort($expected);
        asort($actual);
        $this->assertSame($expected, $actual);
    }
}
