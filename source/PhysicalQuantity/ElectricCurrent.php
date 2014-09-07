<?php
namespace PhpUnitsOfMeasure\PhysicalQuantity;

use PhpUnitsOfMeasure\AbstractBasePhysicalQuantity;
use PhpUnitsOfMeasure\UnitOfMeasure;
use PhpUnitsOfMeasure\HasSIUnitsTrait;

class ElectricCurrent extends AbstractBasePhysicalQuantity
{
    use HasSIUnitsTrait;

    protected static $unitDefinitions = [];

    protected static $hasBeenInitialized = false;

    protected static function initializeUnitsOfMeasure()
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
