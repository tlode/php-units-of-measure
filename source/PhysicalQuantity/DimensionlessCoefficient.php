<?php
namespace PhpUnitsOfMeasure\PhysicalQuantity;

use PhpUnitsOfMeasure\AbstractBasePhysicalQuantity;
use PhpUnitsOfMeasure\UnitOfMeasure;

/**
 * Objects of this type represent a special dimensionless
 * quantity.  It's used, for instance, as a placeholder when
 * cancelling values in derived quantities.
 */
class DimensionlessCoefficient extends AbstractBasePhysicalQuantity
{
    protected static $unitDefinitions = [];

    protected static $hasBeenInitialized = false;

    protected static function initializeUnitsOfMeasure()
    {
        $coefficient = UnitOfMeasure::nativeUnitFactory('');
        static::registerUnitOfMeasure($coefficient);
    }

    public function __construct($value)
    {
        parent::__construct($value, '');
    }
}
