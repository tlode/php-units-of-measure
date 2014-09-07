<?php

namespace PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity;

use PhpUnitsOfMeasure\AbstractBasePhysicalQuantity;
use PhpUnitsOfMeasure\UnitOfMeasure;

class Wigginess extends AbstractBasePhysicalQuantity
{
    protected static $unitDefinitions    = [];
    protected static $hasBeenInitialized = false;
    protected static function initializeUnitsOfMeasure()
    {
        $native = UnitOfMeasure::nativeUnitFactory('s');
        $native->addAlias('sopee');
        $native->addAlias('sopees');
        static::registerUnitOfMeasure($native);

        $unit = UnitOfMeasure::linearUnitFactory('t', 2.345);
        $unit->addAlias('tumpet');
        $unit->addAlias('tumpets');
        static::registerUnitOfMeasure($unit);
    }
}
