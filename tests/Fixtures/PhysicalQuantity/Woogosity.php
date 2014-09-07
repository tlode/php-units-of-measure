<?php

namespace PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity;

use PhpUnitsOfMeasure\AbstractBasePhysicalQuantity;
use PhpUnitsOfMeasure\UnitOfMeasure;

class Woogosity extends AbstractBasePhysicalQuantity
{
    protected static $unitDefinitions    = [];
    protected static $hasBeenInitialized = false;
    protected static $nativeUnitOfMeasure;

    protected static function initializeUnitsOfMeasure()
    {
        $native = UnitOfMeasure::nativeUnitFactory('l');
        $native->addAlias('lupee');
        $native->addAlias('lupees');
        static::registerNativeUnitOfMeasure($native);

        $unit = UnitOfMeasure::linearUnitFactory('p', 1.234);
        $unit->addAlias('plurp');
        $unit->addAlias('plurps');
        static::registerUnitOfMeasure($unit);
    }
}
