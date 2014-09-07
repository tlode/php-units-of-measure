<?php
namespace PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity;

use PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity;
use PhpUnitsOfMeasure\CompoundUnitOfMeasure;

class Pumpalumpiness extends AbstractDerivedPhysicalQuantity
{
    protected static $componentQuantities = [
        ['PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Woogosity', 'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wigginess', 'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wigginess'],
        ['PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wonkicity', 'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wonkicity']
    ];

    protected static $unitDefinitions = [];

    protected static $hasBeenInitialized = false;

    protected static function initializeUnitsOfMeasure()
    {
        $native = new CompoundUnitOfMeasure(
            'fl',
            [
                ['l', 's', 's'],
                ['v', 'vorp'],
            ]
        );
        $native->addAlias('floop');
        $native->addAlias('floops');
        static::registerUnitOfMeasure($native);

        $unit = new CompoundUnitOfMeasure(
            'gl',
            [
                ['p', 't', 't'],
                ['u', 'uvees'],
            ]
        );
        $unit->addAlias('glerg');
        $unit->addAlias('glergs');
        static::registerUnitOfMeasure($unit);
    }
}
