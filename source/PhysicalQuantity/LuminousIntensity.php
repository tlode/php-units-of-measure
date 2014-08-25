<?php
namespace PhpUnitsOfMeasure\PhysicalQuantity;

use PhpUnitsOfMeasure\BasePhysicalQuantity;
use PhpUnitsOfMeasure\UnitOfMeasure;
use PhpUnitsOfMeasure\HasSIUnitsTrait;

class LuminousIntensity extends BasePhysicalQuantity
{
    use HasSIUnitsTrait;

    static protected $unitDefinitions = [];

    static protected $hasBeenInitialized = false;

    static protected $nativeUnitOfMeasure;

    static protected function registerDefaultUnitsOfMeasure()
    {
        // Candela
        $candela = UnitOfMeasure::nativeUnitFactory('cd');
        $candela->addAlias('candela');
        static::registerNativeUnitOfMeasure($candela);

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
