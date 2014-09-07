<?php
namespace PhpUnitsOfMeasure\PhysicalQuantity;

use PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity;

/**
 * This is a special case of AbstractDerivedPhysicalQuantity, and
 * represents derived (ie, composite) physical quantities who's
 * component quantites were not found to match a known AbstractDerivedPhysicalQuantity
 * class.
 */
class UnknownDerivedPhysicalQuantity extends AbstractDerivedPhysicalQuantity
{
    protected static $unitDefinitions = [];

    protected static $hasBeenInitialized = false;

    protected static $nativeUnitOfMeasure;

    protected static function initializeUnitsOfMeasure()
    {
        // $coefficient = UnitOfMeasure::nativeUnitFactory('');
        // static::registerNativeUnitOfMeasure($coefficient);
    }
}
