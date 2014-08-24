<?php
namespace PhpUnitsOfMeasure\PhysicalQuantity;

use PhpUnitsOfMeasure\PhysicalQuantity;
use PhpUnitsOfMeasure\UnitOfMeasure;
use PhpUnitsOfMeasure\HasSIUnitsTrait;

class Pressure extends PhysicalQuantity
{
    use HasSIUnitsTrait;

    static protected $unitDefinitions = [];

    static protected $hasBeenInitialized = false;

    static protected function registerDefaultUnitsOfMeasure()
    {
        // Pascal
        $pascal = UnitOfMeasure::nativeUnitFactory('Pa');
        $pascal->addAlias('pascal');
        static::registerUnitOfMeasure($pascal);

        static::addMissingSIPrefixedUnits(
            $pascal,
            1,
            '%pPa',
            [
                '%Ppascal',
            ]
        );

        // Atmosphere
        $newUnit = UnitOfMeasure::linearUnitFactory('atm', 101325);
        $newUnit->addAlias('atmosphere');
        $newUnit->addAlias('atmospheres');
        static::registerUnitOfMeasure($newUnit);

        // Bar
        $bar = UnitOfMeasure::linearUnitFactory('bar', 1e5);
        static::registerUnitOfMeasure($bar);

        static::addMissingSIPrefixedUnits(
            $bar,
            1,
            '%pbar'
        );

        // Inch of Mercury
        $newUnit = UnitOfMeasure::linearUnitFactory('inHg', 3.386389e3);
        $newUnit->addAlias('inches of mercury');
        static::registerUnitOfMeasure($newUnit);

        // Millimeter of Mercury
        $newUnit = UnitOfMeasure::linearUnitFactory('mmHg', 133.3224);
        $newUnit->addAlias('millimeters of mercury');
        $newUnit->addAlias('millimetres of mercury');
        $newUnit->addAlias('torr');
        static::registerUnitOfMeasure($newUnit);

        // Pound per Square Inch
        $newUnit = UnitOfMeasure::linearUnitFactory('psi', 6.894757e3);
        $newUnit->addAlias('pounds per square inch');
        static::registerUnitOfMeasure($newUnit);
    }
}
