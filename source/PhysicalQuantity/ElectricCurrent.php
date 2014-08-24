<?php
namespace PhpUnitsOfMeasure\PhysicalQuantity;

use PhpUnitsOfMeasure\PhysicalQuantity;
use PhpUnitsOfMeasure\UnitOfMeasure;
use PhpUnitsOfMeasure\HasSIUnitsTrait;

class ElectricCurrent extends PhysicalQuantity
{
    use HasSIUnitsTrait;

    static protected $unitDefinitions = [];

    static protected $hasBeenInitialized = false;

    static protected function registerDefaultUnitsOfMeasure()
    {
        // Ampere
        $ampere = UnitOfMeasure::nativeUnitFactory('A');
        $ampere->addAlias('amp');
        $ampere->addAlias('amps');
        $ampere->addAlias('ampere');
        $ampere->addAlias('amperes');
        static::registerUnitOfMeasure($ampere);

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
