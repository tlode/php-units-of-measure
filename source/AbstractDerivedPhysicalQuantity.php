<?php
namespace PhpUnitsOfMeasure;

use PhpUnitsOfMeasure\PhysicalQuantity\DimensionlessCoefficient;
use PhpUnitsOfMeasure\PhysicalQuantity\UnknownDerivedPhysicalQuantity;

abstract class AbstractDerivedPhysicalQuantity extends AbstractPhysicalQuantity implements PhysicalQuantityInterface
{
    // ***************************************************************
    // *** These static class members are meant to support all the ***
    // *** derived physical quantity classes, as opposed to        ***
    // *** static properties of an individual class.               ***
    // ***************************************************************

    /**
     * The collection of known derived physical quantity
     * fully qualifed class names.
     *
     * This array needs to be kept up to date with all the known
     * classes in this library.
     *
     * @var string[]
     */
    private static $derivedQuantityClasses = [
        // TODO fill this with derived quantity classes
    ];

    /**
     * Register a new derived physical quantity class in the list of known classes.
     *
     * @param string $className The fully qualified class name
     */
    final public static function registerNewDerivedQuantityClass($className)
    {
        if (!in_array($className, AbstractDerivedPhysicalQuantity::$derivedQuantityClasses)) {
            AbstractDerivedPhysicalQuantity::$derivedQuantityClasses[] = $className;
        }
    }

    /**
     * Given a set of numerator and denominator quantities, instantiate and return
     * a derived quantity.
     *
     * @param  PhysicalQuantityInterface[] $numerators
     * @param  PhysicalQuantityInterface[] $denominators
     *
     * @return AbstractDerivedPhysicalQuantity
     */
    final public static function factory(array $numerators, array $denominators)
    {
        // Break down all derived units until we're left with a collection of base quantities
        list($numerators, $denominators) = AbstractDerivedPhysicalQuantity::recursiveDecomposeFactors(
            $numerators,
            $denominators
        );

        // Cancel units to find the minimum factors necessary for this unit
        list($numerators, $denominators) = AbstractDerivedPhysicalQuantity::reduceFactors(
            $numerators,
            $denominators
        );

        // Attempt to find a derived class that represents the same collection of units
        //  If none are found, fall back to a generic unnamed derived quantity class.
        foreach (AbstractDerivedPhysicalQuantity::$derivedQuantityClasses as $className) {
            if ($className::matchesFactors($numerators, $denominators)) {
                return new $className($numerators, $denominators);
            }
        }
        return new UnknownDerivedPhysicalQuantity($numerators, $denominators);
    }

    /**
     * Given a set of numerators and a set of denominators, attempt to decompose them
     * into a set of numerators and denominators made entirely of base quantities.
     *
     * For example:
     * (Kg*m/s^2) * (m/s) / (m*Amps) / (s)
     * Should decompose into:
     * (Kg * m * m) / (s * s * s * m * Amps * s)
     *
     * This provides an easy way to track down which derived physical quantity (if any)
     * represents this particular derived value.
     *
     * @param PhysicalQuantityInterface[] $numerators   The numerator values for a quantity
     * @param PhysicalQuantityInterface[] $denominators The denominator values for a quantity
     *
     * @return array[] A tuple of the form (AbstractBasePhysicalQuantity[], AbstractBasePhysicalQuantity[]) representing numerators and denominators
     */
    private static function recursiveDecomposeFactors(array $numerators, array $denominators)
    {
        $decomposeFactors = function ($factor) {
            if ($factor instanceof AbstractDerivedPhysicalQuantity) {
                $subFactors = $factor->getComponentFactors();
                list($factorNumerators, $factorDenominators) = AbstractDerivedPhysicalQuantity::recursiveDecomposeFactors(
                    $subFactors[0],
                    $subFactors[1]
                );
                $decomposedNumerators   = $factorNumerators;
                $decomposedDenominators = $factorDenominators;
            } else {
                $decomposedNumerators   = [$factor];
                $decomposedDenominators = [];
            }
            return [$decomposedNumerators, $decomposedDenominators];
        };

        $resultNumerators   = [];
        $resultDenominators = [];

        foreach ($numerators as $numerator) {
            list($decomposedNumerators, $decomposedDenominators) = $decomposeFactors($numerator);
            $resultNumerators   = array_merge($resultNumerators, $decomposedNumerators);
            $resultDenominators = array_merge($resultDenominators, $decomposedDenominators);
        }

        foreach ($denominators as $denominator) {
            list($decomposedNumerators, $decomposedDenominators) = $decomposeFactors($denominator);
            $resultNumerators   = array_merge($resultNumerators, $decomposedDenominators);
            $resultDenominators = array_merge($resultDenominators, $decomposedNumerators);
        }

        return [$resultNumerators, $resultDenominators];
    }

    /**
     * Given a set of numerators and a set of denominators that have been reduced
     * to their base unit form, attempt to reduce them by cancelling factors.
     *
     * That is, this:
     * (Kg * m * m * s) / (s * s * s * m * Amps * m)
     * Should reduce down to:
     * (Dimensionless Coefficient) * (Kg) / (s * s * Amps)
     *
     * Where the dimensionless coefficient value capture the relative proportions between
     * the cancelled quantities.
     *
     * @param AbstractBasePhysicalQuantity[] $numerators
     * @param AbstractBasePhysicalQuantity[] $denominators
     *
     * @return array[] A tuple of the form (AbstractBasePhysicalQuantity[], AbstractBasePhysicalQuantity[]) representing numerators and denominators
     */
    private static function reduceFactors(array $numerators, array $denominators)
    {
        // Tally up the pre-existing dimensionless coefficients into a single numerator value,
        //  and remove them
        $coefficient = new DimensionlessCoefficient(1);
        foreach ($numerators as $index => $numerator) {
            if ($numerator instanceof DimensionlessCoefficient) {
                $newValue = $coefficient->toNativeUnit() * $numerator->toNativeUnit();
                $coefficient = new DimensionlessCoefficient($newValue);
                unset($numerators[$index]);
            }
        }
        foreach ($denominators as $index => $denominator) {
            if ($denominator instanceof DimensionlessCoefficient) {
                $newValue = $coefficient->toNativeUnit() / $denominator->toNativeUnit();
                $coefficient = new DimensionlessCoefficient($newValue);
                unset($denominators[$index]);
            }
        }

        // Identify any cancellable base units, remove them, and move their ratio into the
        // coefficient
        foreach ($numerators as $numIndex => $numerator) {
            foreach ($denominators as $denomIndex => $denominator) {
                if (get_class($numerator) === get_class($denominator)) {
                    $newValue = $coefficient->toNativeUnit() * $numerator->toNativeUnit() / $denominator->toNativeUnit();
                    $coefficient = new DimensionlessCoefficient($newValue);
                    unset($numerators[$numIndex]);
                    unset($denominators[$denomIndex]);
                    break;
                }
            }
        }

        // Once all the cancellable units are cancelled, append the final coefficent to the numerator
        $numerators[] = $coefficient;

        // return the reduced set
        return [$numerators, $denominators];
    }


    // ***********************************************************
    // *** These static class members are meant to support the ***
    // *** static properties of individual child classes.      ***
    // ***********************************************************

    /**
     * The component quantities which make up the numerator
     * and denominator for this quantity.  Represented as tuple of
     * arrays of fully qualified class names.
     *
     * @var array[]
     */
    protected static $componentQuantities = [];

    /**
     * Does this class match the given numerator and denominator sets?
     *
     * The order of units doesn't matter, and DimensionlessCoefficients are ignored.
     *
     * @param PhysicalQuantityInterface[] $numerators   The numerators to test against
     * @param PhysicalQuantityInterface[] $denominators The denominators to test against
     *
     * @return boolean True if its a match, false if not.
     */
    private static function matchesFactors(array $numerators, array $denominators)
    {
        // Ignore the dimensionless coefficients
        $removeCoefficent = function ($element) {
            return !($element instanceof DimensionlessCoefficient);
        };
        $numerators   = array_filter($numerators, $removeCoefficent);
        $denominators = array_filter($denominators, $removeCoefficent);

        // Get the set of numerator and denominator quantity classes for this instance
        $numeratorClasses   = array_map('get_class', $numerators);
        $denominatorClasses = array_map('get_class', $denominators);

        // Get the set of component quantities that make up this derived quantity
        $componentQuantities = static::$componentQuantities;

        // If the array sets aren't equivalent, then this is not a match
        sort($numeratorClasses);
        sort($denominatorClasses);
        sort($componentQuantities[0]);
        sort($componentQuantities[1]);
        return ($numeratorClasses === $componentQuantities[0]
            && $denominatorClasses === $componentQuantities[1]
        );
    }


    // ***********************************
    // *** Individual instance members ***
    // ***********************************

    /**
     * A tuple of arrays naming up the numerators and denominator quantities for this quantity.
     *
     * @var array[]
     */
    protected $componentFactors = [];

    /**
     * This constructor is protected, to force the usage of the static factory() method above.
     *
     * @param PhysicalQuantityInterface[] $numerators
     * @param PhysicalQuantityInterface[] $denominators
     */
    protected function __construct(array $numerators, array $denominators)
    {
        $this->componentFactors = [array_values($numerators), array_values($denominators)];
    }

    /**
     * Get the set of numerator factors and denominator factors that composite together
     * to make up this quantity.
     *
     * @return @return array[] A tuple of the form (AbstractBasePhysicalQuantity[], AbstractBasePhysicalQuantity[]) representing numerators and denominators
     */
    private function getComponentFactors()
    {
        return $this->componentFactors;
    }

    /**
     * @see \PhpUnitsOfMeasure\AbstractPhysicalQuantity::getOriginalValue
     */
    protected function getOriginalValue()
    {
        list($numerators, $denominators) = $this->getComponentFactors();

        $value = 1;
        foreach ($numerators as $numerator) {
            $value *= $numerator->getOriginalValue();
        }
        foreach ($denominators as $denominator) {
            $value /= $denominator->getOriginalValue();
        }

        return $value;
    }

    /**
     * @see \PhpUnitsOfMeasure\AbstractPhysicalQuantity::getOriginalUnit
     */
    protected function getOriginalUnit()
    {
        // TODO not yet implemented
        // Something like creating a new unit of measure to represent the specific
        // units on the fly?
    }

    /**
     * For these base quantities, we can assume the quantites are the same if their classes
     * are the same.
     *
     * @see \PhpUnitsOfMeasure\AbstractPhysicalQuantity::isSameQuantity
     */
    protected function isSameQuantity(PhysicalQuantityInterface $firstQuantity, PhysicalQuantityInterface $secondQuantity)
    {
        // TODO not yet implemented
    }
}
