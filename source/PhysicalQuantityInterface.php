<?php
namespace PhpUnitsOfMeasure;

/**
 * classes which implement this interface represent individual physical quantities.
 */
interface PhysicalQuantityInterface
{
    /**
     * Display the value as a string, in the original unit of measure
     *
     * @return string The pretty-print version of the value, in the original unit of measure
     */
    public function __toString();

    /**
     * Register a new Unit of Measure with this quantity.
     *
     * The intended use is to register a new unit of measure to which measurements
     * of this physical quantity can be converted.
     *
     * @param \PhpUnitsOfMeasure\UnitOfMeasureInterface $unit The new unit of measure
     */
    public function registerUnitOfMeasure(UnitOfMeasureInterface $unit);

    /**
     * Get the list of all supported unit names, with the option
     * to include the units' aliases as well.
     *
     * @param boolean $withAliases Include all the unit alias names in the list
     *
     * @return string[] the collection of unit names
     */
    public function getSupportedUnits($withAliases = false);

    /**
     * Fetch the measurement, in the given unit of measure
     *
     * @param  string $unit The desired unit of measure
     *
     * @return float        The measurement cast in the requested units
     */
    public function toUnit($unit);

    /**
     * Add a given quantity to this quantity, and return a new quantity object.
     *
     * Note that the new quantity's original unit will be the same as this object's.
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
     * @param PhysicalQuantityInterface $quantity The quantity to subtract from this one
     *
     * @throws \PhpUnitsOfMeasure\Exception\PhysicalQuantityMismatch when there is a mismatch between physical quantities
     *
     * @return PhysicalQuantityInterface the new quantity
     */
    public function subtract(PhysicalQuantityInterface $quantity);
}
