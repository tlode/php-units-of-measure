<?php

namespace PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity;

use PhpUnitsOfMeasure\AbstractBasePhysicalQuantity;
use PhpUnitsOfMeasure\UnitOfMeasure;

class Wonkicity extends AbstractBasePhysicalQuantity
{
    protected static $unitDefinitions    = [];
    protected static $hasBeenInitialized = false;
    protected static function initializeUnitsOfMeasure()
    {
        $native = UnitOfMeasure::nativeUnitFactory('u');
        $native->addAlias('uvee');
        $native->addAlias('uvees');
        static::registerUnitOfMeasure($native);

        $unit = UnitOfMeasure::linearUnitFactory('v', 3.456);
        $unit->addAlias('vorp');
        $unit->addAlias('vorps');
        static::registerUnitOfMeasure($unit);
    }
}
