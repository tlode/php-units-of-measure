<?php
namespace PhpUnitsOfMeasure\PhysicalQuantity;

use PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity;
use PhpUnitsOfMeasure\PhysicalQuantityInterface;

/**
 * This is a special case of AbstractDerivedPhysicalQuantity, and
 * represents derived (ie, composite) physical quantities who's
 * component quantites were not found to match a known AbstractDerivedPhysicalQuantity
 * class.
 */
class UnknownDerivedPhysicalQuantity extends AbstractDerivedPhysicalQuantity
{
    protected static $unitDefinitions;

    protected static function initialize()
    {
    }

    /**
     * Unknown quantities are a special case, in that its possible for two objects
     * of this type to represent two different physical quantities.  Therefore, the
     * check for quantity equivalency needs to be more stringent and actually compare
     * the component units.
     *
     * @see \PhpUnitsOfMeasure\AbstractPhysicalQuantity::isEquivalentQuantity
     */
    public function isEquivalentQuantity(PhysicalQuantityInterface $testQuantity)
    {
        // @TODO fix this so it checks quantities for equivalency
        return false;
    }
}
