<?php

namespace PhpUnitsOfMeasureTest;

use PhpUnitsOfMeasure\DerivedPhysicalQuantity;
use PhpUnitsOfMeasure\PhysicalQuantity\Mass;
use PhpUnitsOfMeasure\PhysicalQuantity\Length;
use PhpUnitsOfMeasure\PhysicalQuantity\Time;
use PhpUnitsOfMeasure\PhysicalQuantity\DimensionlessCoefficient;

class DerivedPhysicalQuantityTest extends \PHPUnit_Framework_TestCase
{
    public function testCompositionFromBaseUnits()
    {
        $newton = DerivedPhysicalQuantity::factory(
            [new Mass(2, 'kg'), new Length(4, 'm')],
            [new Time(3, 's'), new Time(3, 's')]
        );

        $numeratorClasses   = array_map('get_class', $newton->numeratorFactors);
        $denominatorClasses = array_map('get_class', $newton->denominatorFactors);

        $this->assertEquals(
            [
                'PhpUnitsOfMeasure\PhysicalQuantity\Mass',
                'PhpUnitsOfMeasure\PhysicalQuantity\Length',
                'PhpUnitsOfMeasure\PhysicalQuantity\DimensionlessCoefficient'
            ],
            $numeratorClasses
        );

        $this->assertEquals(
            [
                'PhpUnitsOfMeasure\PhysicalQuantity\Time',
                'PhpUnitsOfMeasure\PhysicalQuantity\Time'
            ],
            $denominatorClasses
        );
    }

    public function testCompositionFromComposite()
    {
        $watt = DerivedPhysicalQuantity::factory(
            [new Mass(2, 'kg'), new Length(4, 'm'), new Length(4, 'm')],
            [new Time(3, 's'), new Time(3, 's'), new Time(3, 's')]
        );

        $wattHour = DerivedPhysicalQuantity::factory(
            [$watt, new Time(3, 'hour')],
            []
        );

        $numeratorClasses   = array_map('get_class', $wattHour->numeratorFactors);
        $denominatorClasses = array_map('get_class', $wattHour->denominatorFactors);

        $this->assertEquals(
            [
                'PhpUnitsOfMeasure\PhysicalQuantity\Mass',
                'PhpUnitsOfMeasure\PhysicalQuantity\Length',
                'PhpUnitsOfMeasure\PhysicalQuantity\Length',
                'PhpUnitsOfMeasure\PhysicalQuantity\DimensionlessCoefficient'
            ],
            $numeratorClasses
        );

        $this->assertEquals(
            [
                'PhpUnitsOfMeasure\PhysicalQuantity\Time',
                'PhpUnitsOfMeasure\PhysicalQuantity\Time',
            ],
            $denominatorClasses
        );
    }
}
