<?php
namespace PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity;

use PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity;

class Pumpalumpiness extends AbstractDerivedPhysicalQuantity
{
    // need a way to specify the quantites that make up this type.  maybe something like...
    protected static $componentQuantities = [
        ['PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Woogosity', 'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wigginess', 'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wigginess'],
        ['PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wonkicity', 'PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wonkicity']
    ];

    // also need a way to add aliases to specific units. Like...
    // public static $aliases = [
    //     'fl' => [
    //         ['l', 's', 's'],
    //         ['v', 'vorp'],
    //         ['floop', 'floops']
    //     ],
    //     'gl' => [
    //         ['p', 't', 't'],
    //         ['u', 'uvees'],
    //         ['glergs']
    //     ],
    // ];

    protected static $unitDefinitions = [];

    protected static $hasBeenInitialized = false;

    protected static $nativeUnitOfMeasure;

    protected static function initializeUnitsOfMeasure()
    {
        // $coefficient = UnitOfMeasure::nativeUnitFactory('');
        // static::registerNativeUnitOfMeasure($coefficient);
        //
        //
        // $native = UnitOfMeasure::nativeUnitFactory('s');
        // $native->addAlias('sopee');
        // $native->addAlias('sopees');
        // static::registerNativeUnitOfMeasure($native);

        // $unit = UnitOfMeasure::linearUnitFactory('t', 2.345);
        // $unit->addAlias('tumpet');
        // $unit->addAlias('tumpets');
        // static::registerUnitOfMeasure($unit);
    }
}
