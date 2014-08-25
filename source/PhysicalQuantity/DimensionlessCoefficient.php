<?php
namespace PhpUnitsOfMeasure\PhysicalQuantity;

use PhpUnitsOfMeasure\BasePhysicalQuantity;
use PhpUnitsOfMeasure\UnitOfMeasure;

/**
 * Objects of this type represent a special dimensionless
 * quantity.  It's used, for instance, as a placeholder when
 * cancelling values in derived quantities.
 */
class DimensionlessCoefficient extends BasePhysicalQuantity
{
    static protected $unitDefinitions = [];

    static protected $hasBeenInitialized = false;

    static protected $nativeUnitOfMeasure;

    static protected function registerDefaultUnitsOfMeasure()
    {
        $coefficient = UnitOfMeasure::nativeUnitFactory('');
        static::registerNativeUnitOfMeasure($coefficient);
    }

    public function __construct($value)
    {
        parent::__construct($value, '');
    }
}
