<?php

namespace PhpUnitsOfMeasureTest;

use PhpUnitsOfMeasure\BasePhysicalQuantity;
use PhpUnitsOfMeasure\UnitOfMeasureInterface;

class Woogosity extends BasePhysicalQuantity
{
    static protected $unitDefinitions = [];

    static protected $hasBeenInitialized = false;

    static protected $nativeUnitOfMeasure;

    static protected function registerDefaultUnitsOfMeasure()
    {
    }
}

class BasePhysicalQuantityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @before
     */
    public function resetStaticProperty()
    {
        $property = (new \ReflectionClass('\PhpUnitsOfMeasureTest\Woogosity'))
            ->getProperty('unitDefinitions');
        $property->setAccessible(true);
        $property->setValue([]);

        $property = (new \ReflectionClass('\PhpUnitsOfMeasureTest\Woogosity'))
            ->getProperty('hasBeenInitialized');
        $property->setAccessible(true);
        $property->setValue(false);
    }

    /**
     * @covers \PhpUnitsOfMeasure\BasePhysicalQuantity::registerUnitOfMeasure
     * @expectedException \PhpUnitsOfMeasure\Exception\DuplicateUnitNameOrAlias
     */
    public function testRegisterUnitFailsOnDuplicateName()
    {
        $value = new Woogosity(1.234, 'quatloos');

        $newUnit = $this->getMockUnitOfMeasure('quatloos');
        Woogosity::registerUnitOfMeasure($newUnit);

        $newUnit = $this->getMockUnitOfMeasure('quatloos');
        Woogosity::registerUnitOfMeasure($newUnit);
    }

    /**
     * @covers \PhpUnitsOfMeasure\BasePhysicalQuantity::registerUnitOfMeasure
     * @expectedException \PhpUnitsOfMeasure\Exception\DuplicateUnitNameOrAlias
     */
    public function testRegisterUnitFailsOnDuplicateAlias()
    {
        $value = new Woogosity(1.234, 'quatloos');

        $newUnit = $this->getMockUnitOfMeasure('quatloos');
        Woogosity::registerUnitOfMeasure($newUnit);

        $newUnit = $this->getMockUnitOfMeasure('galimpwid', ['quatloos']);
        Woogosity::registerUnitOfMeasure($newUnit);
    }

    /**
     * @covers \PhpUnitsOfMeasure\BasePhysicalQuantity::getSupportedUnits
     */
    public function testGetSupportedUnits()
    {
        $value = new Woogosity(1.234, 'quatloos');

        // Quatloos
        $newUnit = $this->getMockUnitOfMeasure('quatloos', ['qa', 'qs']);
        Woogosity::registerUnitOfMeasure($newUnit);

        // Schmoos
        $newUnit = $this->getMockUnitOfMeasure('schmoos', ['sc', 'sm']);
        Woogosity::registerUnitOfMeasure($newUnit);

        $this->assertSame(['quatloos', 'schmoos'], $value->getSupportedUnits());
    }

    /**
     * @covers \PhpUnitsOfMeasure\BasePhysicalQuantity::getSupportedUnits
     */
    public function testGetSupportedUnitsWithAliases()
    {
        $value = new Woogosity(1.234, 'quatloos');

        // Quatloos
        $newUnit = $this->getMockUnitOfMeasure('quatloos', ['qa', 'qs']);
        Woogosity::registerUnitOfMeasure($newUnit);

        // Schmoos
        $newUnit = $this->getMockUnitOfMeasure('schmoos', ['sc', 'sm']);
        Woogosity::registerUnitOfMeasure($newUnit);

        $this->assertSame(
            ['quatloos', 'qa', 'qs', 'schmoos', 'sc', 'sm'],
            $value->getSupportedUnits(true)
        );
    }

    /**
     * @covers \PhpUnitsOfMeasure\BasePhysicalQuantity::__construct
     */
    public function testInstantiateNewUnit()
    {
        $value = new Woogosity(1.234, 'quatloos');
    }

    /**
     * @covers \PhpUnitsOfMeasure\BasePhysicalQuantity::__construct
     * @expectedException \PhpUnitsOfMeasure\Exception\NonNumericValue
     */
    public function testInstantiateNewUnitNonNumericValue()
    {
        $value = new Woogosity('string', 'quatloos');
    }

    /**
     * @covers \PhpUnitsOfMeasure\BasePhysicalQuantity::__construct
     * @expectedException \PhpUnitsOfMeasure\Exception\NonStringUnitName
     */
    public function testInstantiateNewUnitNonStringUnit()
    {
        $value = new Woogosity(1.234, 42);
    }

    /**
     * @covers \PhpUnitsOfMeasure\BasePhysicalQuantity::__toString
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
     * @covers \PhpUnitsOfMeasure\BasePhysicalQuantity::registerUnitOfMeasure
     * @covers \PhpUnitsOfMeasure\BasePhysicalQuantity::toUnit
     * @covers \PhpUnitsOfMeasure\BasePhysicalQuantity::findUnitOfMeasureByNameOrAlias
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
     * @covers \PhpUnitsOfMeasure\BasePhysicalQuantity::findUnitOfMeasureByNameOrAlias
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
     * @covers \PhpUnitsOfMeasure\BasePhysicalQuantity::add
     */
    public function testAdd()
    {
        $first  = new \PhpUnitsOfMeasure\PhysicalQuantity\Volume(6, 'liters');
        $second = new \PhpUnitsOfMeasure\PhysicalQuantity\Volume(6, 'cups');

        $sum = $first->add($second);
        $this->assertSame('7.4195292 l', (string) $sum);
    }

    /**
     * @covers \PhpUnitsOfMeasure\BasePhysicalQuantity::add
     *
     * @expectedException \PhpUnitsOfMeasure\Exception\PhysicalQuantityMismatch
     */
    public function testAddThrowsExceptionOnQuantityMismatch()
    {
        $first  = new \PhpUnitsOfMeasure\PhysicalQuantity\Volume(6, 'liters');
        $second = new \PhpUnitsOfMeasure\PhysicalQuantity\Mass(6, 'g');

        $sum = $first->add($second);
    }

    /**
     * @covers \PhpUnitsOfMeasure\BasePhysicalQuantity::subtract
     */
    public function testSubtract()
    {
        $first  = new \PhpUnitsOfMeasure\PhysicalQuantity\Volume(6, 'liters');
        $second = new \PhpUnitsOfMeasure\PhysicalQuantity\Volume(6, 'cups');

        $difference = $first->subtract($second);
        $this->assertSame('4.5804708 l', (string) $difference);
    }

    /**
     * @covers \PhpUnitsOfMeasure\BasePhysicalQuantity::subtract
     *
     * @expectedException \PhpUnitsOfMeasure\Exception\PhysicalQuantityMismatch
     */
    public function testSubtractThrowsExceptionOnQuantityMismatch()
    {
        $first  = new \PhpUnitsOfMeasure\PhysicalQuantity\Volume(6, 'liters');
        $second = new \PhpUnitsOfMeasure\PhysicalQuantity\Mass(6, 'g');

        $sum = $first->subtract($second);
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
}
