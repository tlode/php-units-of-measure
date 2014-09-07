<?php

namespace PhpUnitsOfMeasureTest;

use PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Woogosity;
use PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wigginess;
use PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wonkicity;

class AbstractBasePhysicalQuantityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @before
     */
    public function resetStaticProperty()
    {
        $fieldInitValues = [
            'unitDefinitions'     => [],
            'hasBeenInitialized'  => false,
            'nativeUnitOfMeasure' => null,
        ];

        foreach ($fieldInitValues as $fieldName => $fieldValue) {
            $property = (new \ReflectionClass('\PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Woogosity'))
                ->getProperty($fieldName);
            $property->setAccessible(true);
            $property->setValue($fieldValue);
            $property->setAccessible(false);
        }
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractBasePhysicalQuantity::registerUnitOfMeasure
     * @expectedException \PhpUnitsOfMeasure\Exception\DuplicateUnitNameOrAlias
     */
    public function testRegisterUnitFailsOnNewUnitNameIsDuplicateExistingName()
    {
        $newUnit = $this->getMockUnitOfMeasure('p', []);
        Woogosity::registerUnitOfMeasure($newUnit);
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractBasePhysicalQuantity::registerUnitOfMeasure
     * @expectedException \PhpUnitsOfMeasure\Exception\DuplicateUnitNameOrAlias
     */
    public function testRegisterUnitFailsOnNewAliasNameIsDuplicateExistingName()
    {
        $newUnit = $this->getMockUnitOfMeasure('palimpwid', ['p']);
        Woogosity::registerUnitOfMeasure($newUnit);
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractBasePhysicalQuantity::registerUnitOfMeasure
     * @expectedException \PhpUnitsOfMeasure\Exception\DuplicateUnitNameOrAlias
     */
    public function testRegisterUnitFailsOnNewUnitNameIsDuplicateExistingAlias()
    {
        $newUnit = $this->getMockUnitOfMeasure('plurp', []);
        Woogosity::registerUnitOfMeasure($newUnit);
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractBasePhysicalQuantity::registerUnitOfMeasure
     * @expectedException \PhpUnitsOfMeasure\Exception\DuplicateUnitNameOrAlias
     */
    public function testRegisterUnitFailsOnNewAliasNameIsDuplicateExistingAlias()
    {
        $newUnit = $this->getMockUnitOfMeasure('palimpwid', ['plurp']);
        Woogosity::registerUnitOfMeasure($newUnit);
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractBasePhysicalQuantity::registerUnitOfMeasure
     * @covers \PhpUnitsOfMeasure\AbstractBasePhysicalQuantity::getSupportedUnits
     */
    public function testGetSupportedUnits()
    {
        // Quatloos
        $newUnit = $this->getMockUnitOfMeasure('quatloos', ['qa', 'qs']);
        Woogosity::registerUnitOfMeasure($newUnit);

        // Schmoos
        $newUnit = $this->getMockUnitOfMeasure('schmoos', ['sc', 'sm']);
        Woogosity::registerUnitOfMeasure($newUnit);

        $this->assertArraySameValues(
            ['l', 'p', 'quatloos', 'schmoos'],
            Woogosity::getSupportedUnits()
        );
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractBasePhysicalQuantity::getSupportedUnits
     */
    public function testGetSupportedUnitsWithAliases()
    {
        // Quatloos
        $newUnit = $this->getMockUnitOfMeasure('quatloos', ['qa', 'qs']);
        Woogosity::registerUnitOfMeasure($newUnit);

        // Schmoos
        $newUnit = $this->getMockUnitOfMeasure('schmoos', ['sc', 'sm']);
        Woogosity::registerUnitOfMeasure($newUnit);

        $this->assertArraySameValues(
            ['l', 'lupee', 'lupees', 'p', 'plurp', 'plurps', 'quatloos', 'qa', 'qs', 'schmoos', 'sc', 'sm'],
            Woogosity::getSupportedUnits(true)
        );
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractBasePhysicalQuantity::__construct
     */
    public function testInstantiateNewUnit()
    {
        $value = new Woogosity(1.234, 'quatloos');
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractBasePhysicalQuantity::__construct
     * @expectedException \PhpUnitsOfMeasure\Exception\NonNumericValue
     */
    public function testInstantiateNewUnitNonNumericValue()
    {
        $value = new Woogosity('string', 'quatloos');
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractBasePhysicalQuantity::__construct
     * @expectedException \PhpUnitsOfMeasure\Exception\NonStringUnitName
     */
    public function testInstantiateNewUnitNonStringUnit()
    {
        $value = new Woogosity(1.234, 42);
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractBasePhysicalQuantity::__toString
     */
    public function testToString()
    {
        $value = new Woogosity(1.234, 'quatloos');

        // Quatloos
        $newUnit = $this->getMockUnitOfMeasure('quatloos');
        Woogosity::registerUnitOfMeasure($newUnit);

        $this->assertSame('1.234 quatloos', (string) $value);
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractBasePhysicalQuantity::registerUnitOfMeasure
     * @covers \PhpUnitsOfMeasure\AbstractBasePhysicalQuantity::toUnit
     * @covers \PhpUnitsOfMeasure\AbstractBasePhysicalQuantity::findUnitOfMeasureByNameOrAlias
     */
    public function testUnitConverts()
    {
        $value = new Woogosity(1.234, 'quatloos');

        // Quatloos
        $newUnit = $this->getMockUnitOfMeasure('quatloos');
        $newUnit->expects($this->any())
            ->method('convertValueToNativeUnitOfMeasure')
            ->will($this->returnValue(1.234));

        Woogosity::registerUnitOfMeasure($newUnit);

        // Galactic Imperial Widgets (let's say it's defined as 2 quatloos)
        $newUnit = $this->getMockUnitOfMeasure('galimpwid');
        $newUnit->expects($this->any())
            ->method('convertValueFromNativeUnitOfMeasure')
            ->will($this->returnValue(2.468));

        Woogosity::registerUnitOfMeasure($newUnit);

        $valueInGalimpwids = $value->toUnit('galimpwid');

        $this->assertSame(2.468, $valueInGalimpwids);
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractBasePhysicalQuantity::findUnitOfMeasureByNameOrAlias
     * @expectedException \PhpUnitsOfMeasure\Exception\UnknownUnitOfMeasure
     */
    public function testUnknownUnit()
    {
        $value = new Woogosity(1.234, 'quatloos');

        // Quatloos
        $newUnit = $this->getMockUnitOfMeasure('quatloos');
        Woogosity::registerUnitOfMeasure($newUnit);

        $valueInGalimpwids = $value->toUnit('galimpwid');
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractBasePhysicalQuantity::add
     */
    public function testAdd()
    {
        $first  = new Woogosity(6, 'plurps');
        $second = new Woogosity(6, 'lupees');

        $sum = $first->add($second);

        $this->assertInstanceOf('\PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Woogosity', $sum);
        $this->assertSame('10.862236628849 p', (string) $sum);
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractBasePhysicalQuantity::add
     *
     * @expectedException \PhpUnitsOfMeasure\Exception\PhysicalQuantityMismatch
     */
    public function testAddThrowsExceptionOnQuantityMismatch()
    {
        $first  = new Woogosity(6, 'plurps');
        $second = new Wonkicity(6, 'lupees');

        $sum = $first->add($second);
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractBasePhysicalQuantity::subtract
     */
    public function testSubtract()
    {
        $first  = new Woogosity(6, 'plurps');
        $second = new Woogosity(6, 'lupees');

        $difference = $first->subtract($second);

        $this->assertInstanceOf('\PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Woogosity', $difference);
        $this->assertSame('1.1377633711507 p', (string) $difference);
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractBasePhysicalQuantity::subtract
     *
     * @expectedException \PhpUnitsOfMeasure\Exception\PhysicalQuantityMismatch
     */
    public function testSubtractThrowsExceptionOnQuantityMismatch()
    {
        $first  = new Woogosity(6, 'plurps');
        $second = new Wonkicity(6, 'lupees');

        $sum = $first->subtract($second);
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractBasePhysicalQuantity::multiplyBy
     */
    public function testMultiplyBy()
    {
        $first  = new Woogosity(6, 'plurps');
        $second = new Wonkicity(6, 'lupees');

        $product = $first->multiplyBy($second);

        $this->assertInstanceOf('\PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity', $product);
    }

    /**
     * @covers \PhpUnitsOfMeasure\AbstractBasePhysicalQuantity::divideBy
     */
    public function testDivideBy()
    {
        $first  = new Woogosity(6, 'plurps');
        $second = new Wonkicity(6, 'lupees');

        $quotient = $first->divideBy($second);

        $this->assertInstanceOf('\PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity', $quotient);
    }

    protected function getMockUnitOfMeasure($name, $aliases = [])
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
