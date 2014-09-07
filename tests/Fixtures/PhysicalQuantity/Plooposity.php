<?php
namespace PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity;

use PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity;
use PhpUnitsOfMeasure\CompoundUnitOfMeasure;

class Plooposity extends AbstractDerivedPhysicalQuantity
{
    protected static $componentQuantities = [
        ['PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wigginess'],
        ['PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wonkicity']
    ];

    protected static $unitDefinitions = [];

    protected static $hasBeenInitialized = false;

    protected static function initializeUnitsOfMeasure()
    {
        $native = new CompoundUnitOfMeasure(
            'ho',
            [
                ['s'],
                ['vorp'],
            ]
        );
        $native->addAlias('horp');
        $native->addAlias('horps');
        static::registerUnitOfMeasure($native);

        $unit = new CompoundUnitOfMeasure(
            'je',
            [
                ['t'],
                ['u'],
            ]
        );
        $unit->addAlias('jerg');
        $unit->addAlias('jergs');
        static::registerUnitOfMeasure($unit);
    }
}
