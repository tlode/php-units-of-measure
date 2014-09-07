<?php

namespace PhpUnitsOfMeasureTest;

use PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Woogosity;
use PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wonkicity;

class AbstractBasePhysicalQuantityTest extends AbstractPhysicalQuantityTestCase
{
    protected $firstTestClass = '\PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Woogosity';
    protected $secondTestClass = '\PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wonkicity';

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
            [$this->getTestUnitOfMeasure('p', [])],                 // name/name collision
            [$this->getTestUnitOfMeasure('noconflict', ['p'])],     // alias/name collision
            [$this->getTestUnitOfMeasure('plurp', [])],             // name/alias collision
            [$this->getTestUnitOfMeasure('noconflict', ['plurp'])], // alias/alias collision
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
                ['l', 'p', 'quatloos', 'schmoos'],
            ],
            [
                $withAliases = true,
                [
                    $this->getTestUnitOfMeasure('quatloos', ['qa', 'qs']),
                    $this->getTestUnitOfMeasure('schmoos', ['sc', 'sm'])
                ],
                ['l', 'lupee', 'lupees', 'p', 'plurp', 'plurps', 'quatloos', 'qa', 'qs', 'schmoos', 'sc', 'sm'],
            ]
        ];
    }

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
