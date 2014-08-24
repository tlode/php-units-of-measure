<?php
namespace PhpUnitsOfMeasure\PhysicalQuantity;

use PhpUnitsOfMeasure\PhysicalQuantity;
use PhpUnitsOfMeasure\UnitOfMeasure;

class Acceleration extends PhysicalQuantity
{
    static protected $unitDefinitions = [];

    static protected $hasBeenInitialized = false;

    static protected function registerDefaultUnitsOfMeasure()
    {
        // meters per second squared
        $meterpersecondsquared = UnitOfMeasure::nativeUnitFactory('m/s^2');
        $meterpersecondsquared->addAlias('m/s²');
        $meterpersecondsquared->addAlias('meter per second squared');
        $meterpersecondsquared->addAlias('meters per second squared');
        $meterpersecondsquared->addAlias('metre per second squared');
        $meterpersecondsquared->addAlias('metres per second squared');
        static::registerUnitOfMeasure($meterpersecondsquared);
    }
}
