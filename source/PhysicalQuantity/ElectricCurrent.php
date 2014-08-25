<?php
namespace PhpUnitsOfMeasure\PhysicalQuantity;

use PhpUnitsOfMeasure\BasePhysicalQuantity;
use PhpUnitsOfMeasure\UnitOfMeasure;
use PhpUnitsOfMeasure\HasSIUnitsTrait;

class ElectricCurrent extends BasePhysicalQuantity
{
    use HasSIUnitsTrait;

    static protected $unitDefinitions = [];

    static protected $hasBeenInitialized = false;

    static protected $nativeUnitOfMeasure;

    static protected function registerDefaultUnitsOfMeasure()
    {
        // Ampere
        $ampere = UnitOfMeasure::nativeUnitFactory('A');
        $ampere->addAlias('amp');
        $ampere->addAlias('amps');
        $ampere->addAlias('ampere');
        $ampere->addAlias('amperes');
        static::registerNativeUnitOfMeasure($ampere);

        static::addMissingSIPrefixedUnits(
            $ampere,
            1,
            '%pA',
            [
                '%Pamp',
                '%Pamps',
                '%Pampere',
                '%Pamperes'
            ]
        );
    }
}
