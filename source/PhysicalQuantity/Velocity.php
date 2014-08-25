<?php
namespace PhpUnitsOfMeasure\PhysicalQuantity;

use PhpUnitsOfMeasure\BasePhysicalQuantity;
use PhpUnitsOfMeasure\UnitOfMeasure;

class Velocity extends BasePhysicalQuantity
{
    static protected $unitDefinitions = [];

    static protected $hasBeenInitialized = false;

    static protected $nativeUnitOfMeasure;

    static protected function registerDefaultUnitsOfMeasure()
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
