<?php
namespace PhpUnitsOfMeasure\PhysicalQuantity;

use PhpUnitsOfMeasure\PhysicalQuantity;
use PhpUnitsOfMeasure\UnitOfMeasure;
use PhpUnitsOfMeasure\HasSIUnitsTrait;

class Length extends PhysicalQuantity
{
    use HasSIUnitsTrait;

    static protected $unitDefinitions = [];

    static protected $hasBeenInitialized = false;

    static protected function registerDefaultUnitsOfMeasure()
    {
        // Meter
        $meter = UnitOfMeasure::nativeUnitFactory('m');
        $meter->addAlias('meter');
        $meter->addAlias('meters');
        $meter->addAlias('metre');
        $meter->addAlias('metres');
        static::registerUnitOfMeasure($meter);

        static::addMissingSIPrefixedUnits(
            $meter,
            1,
            '%pm',
            [
                '%Pmeter',
                '%Pmeters',
                '%Pmetre',
                '%Pmetres'
            ]
        );

        // Foot
        $newUnit = UnitOfMeasure::linearUnitFactory('ft', 0.3048);
        $newUnit->addAlias('foot');
        $newUnit->addAlias('feet');
        static::registerUnitOfMeasure($newUnit);

        // Inch
        $newUnit = UnitOfMeasure::linearUnitFactory('in', 0.0254);
        $newUnit->addAlias('inch');
        $newUnit->addAlias('inches');
        static::registerUnitOfMeasure($newUnit);

        // Mile
        $newUnit = UnitOfMeasure::linearUnitFactory('mi', 1609.344);
        $newUnit->addAlias('mile');
        $newUnit->addAlias('miles');
        static::registerUnitOfMeasure($newUnit);

        // Yard
        $newUnit = UnitOfMeasure::linearUnitFactory('yd', 0.9144);
        $newUnit->addAlias('yard');
        $newUnit->addAlias('yards');
        static::registerUnitOfMeasure($newUnit);
    }
}
