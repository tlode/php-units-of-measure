<?php
namespace PhpUnitsOfMeasure\PhysicalQuantity;

use PhpUnitsOfMeasure\AbstractBasePhysicalQuantity;
use PhpUnitsOfMeasure\UnitOfMeasure;

class Velocity extends AbstractBasePhysicalQuantity
{
    protected static $unitDefinitions = [];

    protected static $hasBeenInitialized = false;

    protected static $nativeUnitOfMeasure;

    protected static function initializeUnitsOfMeasure()
    {
        // meter per second
        $meterpersecond = UnitOfMeasure::nativeUnitFactory('m/s');
        $meterpersecond->addAlias('meters per second');
        $meterpersecond->addAlias('meter per second');
        $meterpersecond->addAlias('metres per second');
        $meterpersecond->addAlias('metre per second');
        static::registerNativeUnitOfMeasure($meterpersecond);
    }
}
