<?php
namespace PhpUnitsOfMeasure\PhysicalQuantity;

use PhpUnitsOfMeasure\BasePhysicalQuantity;
use PhpUnitsOfMeasure\UnitOfMeasure;

class Acceleration extends BasePhysicalQuantity
{
    static protected $unitDefinitions = [];

    static protected $hasBeenInitialized = false;

    static protected $nativeUnitOfMeasure;

    static protected function registerDefaultUnitsOfMeasure()
    {
        // meters per second squared
        $meterpersecondsquared = UnitOfMeasure::nativeUnitFactory('m/s^2');
        $meterpersecondsquared->addAlias('m/s²');
        $meterpersecondsquared->addAlias('meter per second squared');
        $meterpersecondsquared->addAlias('meters per second squared');
        $meterpersecondsquared->addAlias('metre per second squared');
        $meterpersecondsquared->addAlias('metres per second squared');
        static::registerNativeUnitOfMeasure($meterpersecondsquared);
    }
}
