<?php

namespace PhpUnitsOfMeasureTest;

use PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Woogosity;
use PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wigginess;
use PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wonkicity;

class AbstractBasePhysicalQuantityTest extends AbstractPhysicalQuantityTestCase
{
    protected $firstTestClass = '\PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Woogosity';
    protected $secondTestClass = '\PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wonkicity';

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
}
