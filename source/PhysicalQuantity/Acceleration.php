<?php
namespace PhpUnitsOfMeasure\PhysicalQuantity;

use PhpUnitsOfMeasure\AbstractBasePhysicalQuantity;
use PhpUnitsOfMeasure\UnitOfMeasure;

class Acceleration extends AbstractBasePhysicalQuantity
{
    protected static $unitDefinitions = [];

    protected static $hasBeenInitialized = false;

    protected static $nativeUnitOfMeasure;

    protected static function initializeUnitsOfMeasure()
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
