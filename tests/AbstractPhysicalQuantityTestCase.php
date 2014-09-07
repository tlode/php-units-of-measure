<?php

namespace PhpUnitsOfMeasureTest;

use PHPUnit_Framework_TestCase;
use PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Woogosity;
use PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wonkicity;
use PhpUnitsOfMeasure\AbstractPhysicalQuantity;

abstract class AbstractPhysicalQuantityTestCase extends PHPUnit_Framework_TestCase
{
    protected $firstTestClass;

    protected $secondTestClass;

    abstract protected function getTestUnitOfMeasure($name, $aliases = []);

    /**
     * Running this before every test will clear out the static initalization
     * of an AbstractPhysicalQuantity class.
     */
    public function resetStaticProperty()
    {
        $fieldInitValues = [
            'unitDefinitions'    => [],
            'hasBeenInitialized' => false,
        ];

        $classNames = [
            '\PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Plooposity',
            '\PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Pumpalumpiness',
            '\PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wigginess',
            '\PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wonkicity',
            '\PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Woogosity',
        ];

        foreach ($classNames as $className) {
            foreach ($fieldInitValues as $fieldName => $fieldValue) {
                $property = (new \ReflectionClass($className))
                    ->getProperty($fieldName);
                $property->setAccessible(true);
                $property->setValue($fieldValue);
                $property->setAccessible(false);
            }
        }
    }

    /**
     * @dataProvider exceptionProducingUnitsProvider
     * @covers \PhpUnitsOfMeasure\AbstractPhysicalQuantity::registerUnitOfMeasure
     * @expectedException \PhpUnitsOfMeasure\Exception\DuplicateUnitNameOrAlias
     */
    public function testRegisterUnitFailsOnNameCollision($newUnit)
    {
        $firstTestClass = $this->firstTestClass;
        $firstTestClass::registerUnitOfMeasure($newUnit);
    }

    /**
     * @dataProvider validUnitsProvider
     * @covers \PhpUnitsOfMeasure\AbstractPhysicalQuantity::getSupportedUnitNames
     */
    public function testgetSupportedUnitNames(
        $withAliases,
        array $newUnits,
        $resultingNames
    ) {
        $firstTestClass = $this->firstTestClass;
        foreach ($newUnits as $newUnit) {
           $firstTestClass::registerUnitOfMeasure($newUnit);
        }

        $this->assertArraySameValues(
            $resultingNames,
            $firstTestClass::getSupportedUnitNames($withAliases)
        );
    }

    /**
     * @dataProvider quantityConversionsProvider
     * @covers \PhpUnitsOfMeasure\AbstractPhysicalQuantity::toNativeUnit
     */
    public function testUnitConvertsToNativeUnit(
        AbstractPhysicalQuantity $value,
        $valueInNativeUnit,
        $arbitraryUnit,
        $valueInArbitraryUnit
    ) {
        $this->assertSame($valueInNativeUnit, $value->toNativeUnit());
    }

    /**
     * @dataProvider quantityConversionsProvider
     * @covers \PhpUnitsOfMeasure\AbstractPhysicalQuantity::toNativeUnit
     */
    public function testUnitConvertsToArbitraryUnit(
        AbstractPhysicalQuantity $value,
        $valueInNativeUnit,
        $arbitraryUnit,
        $valueInArbitraryUnit
    ) {
        $this->assertSame($valueInArbitraryUnit, $value->toUnit($arbitraryUnit));
    }

    /**
     * @dataProvider quantityConversionsProvider
     * @covers \PhpUnitsOfMeasure\AbstractPhysicalQuantity::toUnit
     * @expectedException \PhpUnitsOfMeasure\Exception\UnknownUnitOfMeasure
     */
    public function testConvertToUnknownUnitThrowsException(
        AbstractPhysicalQuantity $value,
        $valueInNativeUnit,
        $arbitraryUnit,
        $valueInArbitraryUnit
    ) {
        $value->toUnit('someUnknownUnit');
    }

    /**
     * @dataProvider toStringProvider
     * @covers \PhpUnitsOfMeasure\AbstractPhysicalQuantity::__toString
     */
    public function testToString(AbstractPhysicalQuantity $value, $string)
    {
        $this->assertSame($string, (string) $value);
    }

    /**
     * @dataProvider arithmeticProvider
     * @covers \PhpUnitsOfMeasure\AbstractPhysicalQuantity::add
     */
    public function testAdd(
        $shouldThrowException,
        AbstractPhysicalQuantity $firstValue,
        AbstractPhysicalQuantity $secondValue,
        $sumString,
        $diffString
    ) {
        if ($shouldThrowException) {
            $this->setExpectedException('\PhpUnitsOfMeasure\Exception\PhysicalQuantityMismatch');
        }

        $sum = $firstValue->add($secondValue);

        if (!$shouldThrowException) {
            $this->assertSame($sumString, (string) $sum);
        }
    }

    /**
     * @dataProvider arithmeticProvider
     * @covers \PhpUnitsOfMeasure\AbstractPhysicalQuantity::add
     */
    public function testSubtract(
        $shouldThrowException,
        AbstractPhysicalQuantity $firstValue,
        AbstractPhysicalQuantity $secondValue,
        $sumString,
        $diffString
    ) {
        if ($shouldThrowException) {
            $this->setExpectedException('\PhpUnitsOfMeasure\Exception\PhysicalQuantityMismatch');
        }

        $difference = $firstValue->subtract($secondValue);

        if (!$shouldThrowException) {
            $this->assertSame($diffString, (string) $difference);
        }
    }

    /**
     * @dataProvider productProvider
     * @covers \PhpUnitsOfMeasure\AbstractPhysicalQuantity::multiplyBy
     */
    public function testMultiplyBy(
        AbstractPhysicalQuantity $firstValue,
        AbstractPhysicalQuantity $secondValue,
        $productString,
        $productType,
        $quotientString,
        $quotientType
    ) {
        $product = $firstValue->multiplyBy($secondValue);

        $this->assertInstanceOf($productType, $product);
        $this->assertSame($productString, (string) $product);
    }

    /**
     * @dataProvider productProvider
     * @covers \PhpUnitsOfMeasure\AbstractPhysicalQuantity::divideBy
     */
    public function testDivideBy(
        AbstractPhysicalQuantity $firstValue,
        AbstractPhysicalQuantity $secondValue,
        $productString,
        $productType,
        $quotientString,
        $quotientType
    ) {
        $quotient = $firstValue->divideBy($secondValue);

        $this->assertInstanceOf($quotientType, $quotient);
        $this->assertSame($quotientString, (string) $quotient);
    }

    abstract public function exceptionProducingUnitsProvider();

    abstract public function validUnitsProvider();

    abstract public function quantityConversionsProvider();

    abstract public function toStringProvider();

    abstract public function arithmeticProvider();

    abstract public function productProvider();

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
