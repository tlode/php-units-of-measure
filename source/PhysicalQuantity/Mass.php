<?php
namespace PhpUnitsOfMeasure\PhysicalQuantity;

use PhpUnitsOfMeasure\AbstractBasePhysicalQuantity;
use PhpUnitsOfMeasure\UnitOfMeasure;
use PhpUnitsOfMeasure\HasSIUnitsTrait;

class Mass extends AbstractBasePhysicalQuantity
{
    use HasSIUnitsTrait;

    protected static $unitDefinitions = [];

    protected static $hasBeenInitialized = false;

    protected static function initializeUnitsOfMeasure()
    {
        // Kilogram
        $kilogram = UnitOfMeasure::nativeUnitFactory('kg');
        $kilogram->addAlias('kilogram');
        $kilogram->addAlias('kilograms');
        static::registerUnitOfMeasure($kilogram);

        static::addMissingSIPrefixedUnits(
            $kilogram,
            1e-3,
            '%pg',
            [
                '%Pgram',
                '%Pgrams',
            ]
        );

        // Tonne (metric)
        $newUnit = UnitOfMeasure::linearUnitFactory('t', 1e3);
        $newUnit->addAlias('ton');
        $newUnit->addAlias('tons');
        $newUnit->addAlias('tonne');
        $newUnit->addAlias('tonnes');
        static::registerUnitOfMeasure($newUnit);

        // Pound
        $newUnit = UnitOfMeasure::linearUnitFactory('lb', 4.535924e-1);
        $newUnit->addAlias('lbs');
        $newUnit->addAlias('pound');
        $newUnit->addAlias('pounds');
        static::registerUnitOfMeasure($newUnit);

        // Ounce
        $newUnit = UnitOfMeasure::linearUnitFactory('oz', 2.834952e-2);
        $newUnit->addAlias('ounce');
        $newUnit->addAlias('ounces');
        static::registerUnitOfMeasure($newUnit);
    }
}
