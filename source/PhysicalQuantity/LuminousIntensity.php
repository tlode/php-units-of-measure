<?php
namespace PhpUnitsOfMeasure\PhysicalQuantity;

use PhpUnitsOfMeasure\AbstractBasePhysicalQuantity;
use PhpUnitsOfMeasure\UnitOfMeasure;
use PhpUnitsOfMeasure\HasSIUnitsTrait;

class LuminousIntensity extends AbstractBasePhysicalQuantity
{
    use HasSIUnitsTrait;

    protected static $unitDefinitions = [];

    protected static $hasBeenInitialized = false;

    protected static function initializeUnitsOfMeasure()
    {
        // Candela
        $candela = UnitOfMeasure::nativeUnitFactory('cd');
        $candela->addAlias('candela');
        static::registerUnitOfMeasure($candela);

        static::addMissingSIPrefixedUnits(
            $candela,
            1,
            '%pcd',
            [
                '%Pcandela',
            ]
        );
    }
}
