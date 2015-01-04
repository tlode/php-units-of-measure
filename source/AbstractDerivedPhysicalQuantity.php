<?php
namespace PhpUnitsOfMeasure;

use PhpUnitsOfMeasure\PhysicalQuantity\DimensionlessCoefficient;

abstract class AbstractDerivedPhysicalQuantity extends AbstractPhysicalQuantity implements PhysicalQuantityInterface
{
    /**
     * The base numerator and denominator classes that make up this derived physical quantity.
     *
     * The first element is a set of numerator class names, the second element is the set of
     * denominator class names.  Names should be fully-qualified class names.
     *
     * Note that a null (not an empty array) value indicates that
     * this class hasn't been initialized yet.
     *
     * Finally, note that this property is commented out here to force
     * an error if a child class does not define it's own static instance
     * of this property.  This avoids accidental pollution of shared
     * state between AbstractDerivedPhysicalQuantity child classes if an extending class
     * forgets to define this property.
     *
     * TL;DR - child classes must define this property themselves!
     *
     * @var array[string[], string[]]
     */
    // protected static $componentQuantities;

    /**
     * Define the component quantities that combine to make up this derived
     * physical quantity.
     *
     * Quantities should be represented by fully qualified class names of base
     * physical quantity units.
     *
     * This method is only callable once, for a given class.
     *
     * @param array $numeratorClasses the classnames of this quantity's component base numerator quantities
     * @param array $denominatorClasses the classnames of this quantity's component base denominator quantities
     */
    protected static function setComponentQuantities(array $numeratorClasses, array $denominatorClasses)
    {
        if (is_array(static::$componentQuantities)) {
            throw new Exception\ComponentQuantitiesAlreadySet;
        }

        sort($numeratorClasses);
        sort($denominatorClasses);
        static::$componentQuantities = [$numeratorClasses, $denominatorClasses];
    }

    /**
     * Ensure that the new unit matches the component quantites that define
     * this physical quantity.
     *
     * @see \PhpUnitsOfMeasure\AbstractPhysicalQuantity::addUnit
     */
    public static function addUnit(UnitOfMeasureInterface $unit)
    {
        // @TODO check that the new unit's components match this quantity's component quantities

        parent::addUnit($unit);
    }

    /**
     * Do the given numerator and denominator sets match the physical quantities that
     * define this derived physical quantity?
     *
     * The order of units doesn't matter, and DimensionlessCoefficients are ignored. Also,
     * it can be assumed that the numerators and denominators have been factored down
     * to their base units.
     *
     * @param PhysicalQuantityInterface[] $numerators   The numerators to test against
     * @param PhysicalQuantityInterface[] $denominators The denominators to test against
     *
     * @return boolean True if the factors are a match for this class's quantities, false if not.
     */
    public static function factorsMatchQuantityComponents(array $numerators, array $denominators)
    {
        // If this class hasn't had its default units set, set them now
        if (!is_array(static::$unitDefinitions)) {
            static::$unitDefinitions = [];
            static::initialize();
        }

        // Ignore the dimensionless coefficients
        $removeCoefficent = function ($element) {
            return !($element instanceof DimensionlessCoefficient);
        };
        $numerators   = array_filter($numerators, $removeCoefficent);
        $denominators = array_filter($denominators, $removeCoefficent);

        // Get the set of numerator and denominator quantity classes for this instance
        $numeratorClasses   = array_map('get_class', $numerators);
        $denominatorClasses = array_map('get_class', $denominators);
        sort($numeratorClasses);
        sort($denominatorClasses);

        // If the array sets aren't equivalent, then this is not a match
        return (
            $numeratorClasses === static::$componentQuantities[0]
            && $denominatorClasses === static::$componentQuantities[1]
        );
    }

    /**
     * Store the value and its original unit.
     *
     * @param float  $value The scalar value of the measurement
     * @param string $unit  The unit of measure in which this value is provided
     *
     * @throws Exception\NonNumericValue If the value is not numeric
     * @throws Exception\NonStringUnitName If the unit is not a string
     */
    public function __construct($value, $unit)
    {
        parent::__construct($value, $unit);

        // Parse the unit into the requisite

        // @TODO - we'll take the same input as the parent class, and do the translation here
        // from the string version of the units to the real quantities, and store them as as set of
        // components.  That'll help when we need to get the compoennts back out later, and we can
        // avoid this weird either/or interface on the constructor.

        if (is_array($firstParam) && is_array($secondParam)) {
            $this->initializeWithFactorQuantities($firstParam, $secondParam);

        } else {
            parent::__construct($firstParam, $secondParam);

        }
    }

    /**
     * Here we're setting the value and units of this object from a set of numerator and denominator
     * quantites.  It can be assume that the two sets have been factored down to base units and a single
     * unitless coefficent numerator.
     *
     * @param PhysicalQuantityInterface[] $numerators
     * @param PhysicalQuantityInterface[] $denominators
     */
    protected function initializeWithFactorQuantities(array $numerators, array $denominators)
    {
        // @TODO Still not sure how to represent values
        // going to need to be able to generate an original value number and original unit string.
    }

    /**
     * @see \PhpUnitsOfMeasure\AbstractPhysicalQuantity::isEquivalentQuantity
     */
    public function isEquivalentQuantity(PhysicalQuantityInterface $testQuantity)
    {
        // For derived quantities, we need the set of numerator and denominator quantities
        // that define them.  For any other (presumably base) quantity, we can just create
        // a simple single-quantity set.
        if ($testQuantity instanceof AbstractDerivedPhysicalQuantity) {
            list($testNumerators, $testDenominators) = [[],[]]; // @TODO some nebulous way to get this?
        } else {
            list($testNumerators, $testDenominators) = [[$testQuantity], []];
        }

        return static::factorsMatchQuantityComponents($testNumerators, $testDenominators);
    }
}
