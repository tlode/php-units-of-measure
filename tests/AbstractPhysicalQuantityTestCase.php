<?php

namespace PhpUnitsOfMeasureTest;

use PHPUnit_Framework_TestCase;
use PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Woogosity;
use PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wigginess;
use PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wonkicity;

abstract class AbstractPhysicalQuantityTestCase extends PHPUnit_Framework_TestCase
{
    protected $firstTestClass;
    protected $secondTestClass;

    abstract protected function getTestUnitOfMeasure($name, $aliases = []);

    /**
     * @covers \PhpUnitsOfMeasure\AbstractPhysicalQuantity::registerUnitOfMeasure
     * @expectedException \PhpUnitsOfMeasure\Exception\DuplicateUnitNameOrAlias
     */
    public function testRegisterUnitFailsOnNewUnitNameIsDuplicateExistingName()
    {
        $firstTestClass = $this->firstTestClass;
        $newUnit = $this->getTestUnitOfMeasure('p', []);
        $firstTestClass::registerUnitOfMeasure($newUnit);
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractPhysicalQuantity::registerUnitOfMeasure
     * @expectedException \PhpUnitsOfMeasure\Exception\DuplicateUnitNameOrAlias
     */
    public function testRegisterUnitFailsOnNewAliasNameIsDuplicateExistingName()
    {
        $firstTestClass = $this->firstTestClass;
        $newUnit = $this->getTestUnitOfMeasure('palimpwid', ['p']);
        $firstTestClass::registerUnitOfMeasure($newUnit);
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractPhysicalQuantity::registerUnitOfMeasure
     * @expectedException \PhpUnitsOfMeasure\Exception\DuplicateUnitNameOrAlias
     */
    public function testRegisterUnitFailsOnNewUnitNameIsDuplicateExistingAlias()
    {
        $firstTestClass = $this->firstTestClass;
        $newUnit = $this->getTestUnitOfMeasure('plurp', []);
        $firstTestClass::registerUnitOfMeasure($newUnit);
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractPhysicalQuantity::registerUnitOfMeasure
     * @expectedException \PhpUnitsOfMeasure\Exception\DuplicateUnitNameOrAlias
     */
    public function testRegisterUnitFailsOnNewAliasNameIsDuplicateExistingAlias()
    {
        $firstTestClass = $this->firstTestClass;
        $newUnit = $this->getTestUnitOfMeasure('palimpwid', ['plurp']);
        $firstTestClass::registerUnitOfMeasure($newUnit);
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractPhysicalQuantity::registerUnitOfMeasure
     * @covers \PhpUnitsOfMeasure\AbstractPhysicalQuantity::getSupportedUnits
     */
    public function testGetSupportedUnits()
    {
        $firstTestClass = $this->firstTestClass;

        // Quatloos
        $newUnit = $this->getTestUnitOfMeasure('quatloos', ['qa', 'qs']);
        $firstTestClass::registerUnitOfMeasure($newUnit);

        // Schmoos
        $newUnit = $this->getTestUnitOfMeasure('schmoos', ['sc', 'sm']);
        $firstTestClass::registerUnitOfMeasure($newUnit);

        $this->assertArraySameValues(
            ['l', 'p', 'quatloos', 'schmoos'],
            $firstTestClass::getSupportedUnits()
        );
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractPhysicalQuantity::getSupportedUnits
     */
    public function testGetSupportedUnitsWithAliases()
    {
        $firstTestClass = $this->firstTestClass;

        // Quatloos
        $newUnit = $this->getTestUnitOfMeasure('quatloos', ['qa', 'qs']);
        $firstTestClass::registerUnitOfMeasure($newUnit);

        // Schmoos
        $newUnit = $this->getTestUnitOfMeasure('schmoos', ['sc', 'sm']);
        $firstTestClass::registerUnitOfMeasure($newUnit);

        $this->assertArraySameValues(
            ['l', 'lupee', 'lupees', 'p', 'plurp', 'plurps', 'quatloos', 'qa', 'qs', 'schmoos', 'sc', 'sm'],
            $firstTestClass::getSupportedUnits(true)
        );
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractPhysicalQuantity::toNativeUnit
     */
    public function testUnitConvertsToNativeUnit()
    {
        $firstTestClass = $this->firstTestClass;

        $value = new $firstTestClass(1.234, 'quatloos');

        // Quatloos
        $newUnit = $this->getTestUnitOfMeasure('quatloos');
        $newUnit->expects($this->any())
            ->method('convertValueToNativeUnitOfMeasure')
            ->will($this->returnValue(1.234));

        $firstTestClass::registerUnitOfMeasure($newUnit);

        $valueInGalimpwids = $value->toNativeUnit();

        $this->assertSame(1.234, $valueInGalimpwids);
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractPhysicalQuantity::toUnit
     */
    public function testUnitConvertsToArbitraryUnit()
    {
        $firstTestClass = $this->firstTestClass;

        $value = new $firstTestClass(1.234, 'quatloos');

        // Quatloos
        $newUnit = $this->getTestUnitOfMeasure('quatloos');
        $newUnit->expects($this->any())
            ->method('convertValueToNativeUnitOfMeasure')
            ->will($this->returnValue(1.234));

        $firstTestClass::registerUnitOfMeasure($newUnit);

        // Galactic Imperial Widgets (let's say it's defined as 2 quatloos)
        $newUnit = $this->getTestUnitOfMeasure('galimpwid');
        $newUnit->expects($this->any())
            ->method('convertValueFromNativeUnitOfMeasure')
            ->will($this->returnValue(2.468));

        $firstTestClass::registerUnitOfMeasure($newUnit);

        $valueInGalimpwids = $value->toUnit('galimpwid');

        $this->assertSame(2.468, $valueInGalimpwids);
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractPhysicalQuantity::toUnit
     * @expectedException \PhpUnitsOfMeasure\Exception\UnknownUnitOfMeasure
     */
    public function testToUnknownUnit()
    {
        $firstTestClass = $this->firstTestClass;

        $value = new $firstTestClass(1.234, 'quatloos');

        // Quatloos
        $newUnit = $this->getTestUnitOfMeasure('quatloos');
        $firstTestClass::registerUnitOfMeasure($newUnit);

        $valueInGalimpwids = $value->toUnit('galimpwid');
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractPhysicalQuantity::__toString
     */
    public function testToString()
    {
        $firstTestClass = $this->firstTestClass;

        $value = new $firstTestClass(1.234, 'quatloos');

        // Quatloos
        $newUnit = $this->getTestUnitOfMeasure('quatloos');
        $firstTestClass::registerUnitOfMeasure($newUnit);

        $this->assertSame('1.234 quatloos', (string) $value);
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractPhysicalQuantity::add
     */
    public function testAdd()
    {
        $firstTestClass = $this->firstTestClass;

        $first  = new $firstTestClass(6, 'plurps');
        $second = new $firstTestClass(6, 'lupees');

        $sum = $first->add($second);

        $this->assertInstanceOf('\PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Woogosity', $sum);
        $this->assertSame('10.862236628849 p', (string) $sum);
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractPhysicalQuantity::add
     *
     * @expectedException \PhpUnitsOfMeasure\Exception\PhysicalQuantityMismatch
     */
    public function testAddThrowsExceptionOnQuantityMismatch()
    {
        $firstTestClass = $this->firstTestClass;
        $secondTestClass = $this->secondTestClass;

        $first  = new $firstTestClass(6, 'plurps');
        $second = new $secondTestClass(6, 'lupees');

        $sum = $first->add($second);
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractPhysicalQuantity::subtract
     */
    public function testSubtract()
    {
        $firstTestClass = $this->firstTestClass;

        $first  = new $firstTestClass(6, 'plurps');
        $second = new $firstTestClass(6, 'lupees');

        $difference = $first->subtract($second);

        $this->assertInstanceOf('\PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Woogosity', $difference);
        $this->assertSame('1.1377633711507 p', (string) $difference);
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractPhysicalQuantity::subtract
     *
     * @expectedException \PhpUnitsOfMeasure\Exception\PhysicalQuantityMismatch
     */
    public function testSubtractThrowsExceptionOnQuantityMismatch()
    {
        $firstTestClass = $this->firstTestClass;
        $secondTestClass = $this->secondTestClass;

        $first  = new $firstTestClass(6, 'plurps');
        $second = new $secondTestClass(6, 'lupees');

        $sum = $first->subtract($second);
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractPhysicalQuantity::multiplyBy
     */
    public function testMultiplyBy()
    {
        $firstTestClass = $this->firstTestClass;
        $secondTestClass = $this->secondTestClass;

        $first  = new $firstTestClass(6, 'plurps');
        $second = new $secondTestClass(6, 'lupees');

        $product = $first->multiplyBy($second);

        $this->assertInstanceOf('\PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity', $product);
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractPhysicalQuantity::divideBy
     */
    public function testDivideBy()
    {
        $firstTestClass = $this->firstTestClass;
        $secondTestClass = $this->secondTestClass;

        $first  = new $firstTestClass(6, 'plurps');
        $second = new $secondTestClass(6, 'lupees');

        $quotient = $first->divideBy($second);

        $this->assertInstanceOf('\PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity', $quotient);
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
