<?php
namespace PhpUnitsOfMeasure;

/**
 * classes which implement this interface represent individual physical quantities.
 */
interface PhysicalQuantityInterface
{
    /**
     * Get the list of all supported unit names, with the option
     * to include the units' aliases as well.
     *
     * Note that this method is static, and the resulting list is
     * shared between all instances of this class.
     *
     * @param boolean $withAliases Include all the unit alias names in the list
     *
     * @return string[] the collection of unit names
     */
    static public function getSupportedUnits($withAliases = false);

    /**
     * Fetch the measurement, in the given unit of measure
     *
     * @param  string $unit The desired unit of measure
     *
     * @return float The measurement cast in the requested units
     */
    public function toUnit($unit);

    /**
     * Display the value as a string, in the original unit of measure
     *
     * @return string The pretty-print version of the value, in the original unit of measure
     */
    public function __toString();

    /**
     * Add a given quantity to this quantity, and return a new quantity object.
     *
     * Note that the new quantity's original unit will be the same as this object's.
     *
     * Also note that the two quantities must represent the same physical quantity.
     *
     * @param PhysicalQuantityInterface $quantity The quantity to add to this one
     *
     * @throws \PhpUnitsOfMeasure\Exception\PhysicalQuantityMismatch when there is a mismatch between physical quantities
     *
     * @return PhysicalQuantityInterface the new quantity
     */
    public function add(PhysicalQuantityInterface $quantity);

    /**
     * Subtract a given quantity from this quantity, and return a new quantity object.
     *
     * Note that the new quantity's original unit will be the same as this object's.
     *
     * Also note that the two quantities must represent the same physical quantity.
     *
     * @param PhysicalQuantityInterface $quantity The quantity to subtract from this one
     *
     * @throws \PhpUnitsOfMeasure\Exception\PhysicalQuantityMismatch when there is a mismatch between physical quantities
     *
     * @return PhysicalQuantityInterface the new quantity
     */
    public function subtract(PhysicalQuantityInterface $quantity);
}
