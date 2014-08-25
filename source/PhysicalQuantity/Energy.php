<?php
namespace PhpUnitsOfMeasure\PhysicalQuantity;

use PhpUnitsOfMeasure\DerivedPhysicalQuantity;

class Energy extends DerivedPhysicalQuantity
{
    // need a way to specify the quantites that make up this type.  maybe something like...
    protected $factors = [
        ['Mass', 'Length', 'Length'],
        ['Time', 'Time']
    ];

}
