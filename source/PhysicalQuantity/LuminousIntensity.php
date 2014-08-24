<?php
namespace PhpUnitsOfMeasure\PhysicalQuantity;

use PhpUnitsOfMeasure\PhysicalQuantity;
use PhpUnitsOfMeasure\UnitOfMeasure;
use PhpUnitsOfMeasure\HasSIUnitsTrait;

class LuminousIntensity extends PhysicalQuantity
{
    use HasSIUnitsTrait;

    static protected $unitDefinitions = [];

    static protected $hasBeenInitialized = false;

    static protected function registerDefaultUnitsOfMeasure()
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
