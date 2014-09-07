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
     * @var string[]
     */
    private static $namedDerivedQuantityClasses = [
    ];

    /**
     * Register a new derived physical quantity class in the list of known classes.
     *
     * @param string $className The fully qualified class name
     */
    public static function registerNewDerivedQuantityClass($className)
    {
        if (!in_array($className, AbstractDerivedPhysicalQuantity::$namedDerivedQuantityClasses)) {
            AbstractDerivedPhysicalQuantity::$namedDerivedQuantityClasses[] = $className;
        }
    }

    /**
     * Given a set of numerator and denominator quantities, instantiate and return
     * a derived quantity.
     *
     * @param  PhysicalQuantityInterface[] $numeratorFactors
     * @param  PhysicalQuantityInterface[] $denominatorFactors
     *
     * @return AbstractDerivedPhysicalQuantity
     */
    public static function factory(array $numeratorFactors, array $denominatorFactors)
    {
        // Break down all derived units until we're left with a collection of base quantities
        list($numeratorFactors, $denominatorFactors) = AbstractDerivedPhysicalQuantity::recursiveDecomposeFactors(
            $numeratorFactors,
            $denominatorFactors
        );

        // Cancel units to find the minimum factors necessary for this unit
        list($numeratorFactors, $denominatorFactors) = AbstractDerivedPhysicalQuantity::reduceFactors(
            $numeratorFactors,
            $denominatorFactors
        );

        // Attempt to find a derived class that represents the same collection of units
        //  If none are found, fall back to a generic unnamed derived quantity class.
        foreach (AbstractDerivedPhysicalQuantity::$namedDerivedQuantityClasses as $className) {
            if ($className::matchesFactors($numeratorFactors, $denominatorFactors)) {
                return new $className($numeratorFactors, $denominatorFactors);
            }
        }
        return new UnknownDerivedPhysicalQuantity($numeratorFactors, $denominatorFactors);
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
                $newNumerators   = $factorNumerators;
                $newDenominators = $factorDenominators;
            } else {
                $newNumerators   = [$factor];
                $newDenominators = [];
            }
            return [$newNumerators, $newDenominators];
        };

        $resultNumerators   = [];
        $resultDenominators = [];

        foreach ($numerators as $factor) {
            list($newNumerators, $newDenominators) = $decomposeFactors($factor);
            $resultNumerators   = array_merge($resultNumerators, $newNumerators);
            $resultDenominators = array_merge($resultDenominators, $newDenominators);
        }

        foreach ($denominators as $factor) {
            list($newNumerators, $newDenominators) = $decomposeFactors($factor);
            $resultNumerators   = array_merge($resultNumerators, $newDenominators);
            $resultDenominators = array_merge($resultDenominators, $newNumerators);
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

    protected static $factors = [];

    public static function getDefinitionComponentQuantites()
    {
        return static::$factors;
    }

    /**
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::getSupportedUnits
     */
    public static function getSupportedUnits($withAliases = false)
    {
    }

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
    protected static function matchesFactors(array $numerators, array $denominators)
    {
        // Ignore the dimensionless coefficients
        $removeCoefficent = function ($element) {
            return !($element instanceof DimensionlessCoefficient);
        };
        $numerators   = array_filter($numerators, $removeCoefficent);
        $denominators = array_filter($denominators, $removeCoefficent);

        // Get the set of numerator and denominator quantity classes
        $numeratorClasses   = array_map('get_class', $numerators);
        $denominatorClasses = array_map('get_class', $denominators);

        $factors = static::getDefinitionComponentQuantites();
        sort($numeratorClasses);
        sort($denominatorClasses);
        sort($factors[0]);
        sort($factors[1]);

        // If the array sets aren't equivalent, then this is not a match
        if ($numeratorClasses === $factors[0] && $denominatorClasses === $factors[1]) {
            return true;
        }
        return false;
    }


    // ***********************************
    // *** Individual instance members ***
    // ***********************************

    protected $numeratorFactors = [];

    protected $denominatorFactors = [];

    /**
     * This constructor is protected, to force the usage of the static factory() method above.
     *
     * @param PhysicalQuantityInterface[] $numeratorFactors
     * @param PhysicalQuantityInterface[] $numeratorFactors
     */
    protected function __construct(array $numeratorFactors, array $denominatorFactors)
    {
        $this->numeratorFactors   = array_values($numeratorFactors);
        $this->denominatorFactors = array_values($denominatorFactors);
    }

    /**
     * Get the set of numerator factors and denominator factors that composite together
     * to make up this quantity.
     *
     * @return @return array[] A tuple of the form (AbstractBasePhysicalQuantity[], AbstractBasePhysicalQuantity[]) representing numerators and denominators
     */
    public function getComponentFactors()
    {
        return [$this->numeratorFactors, $this->denominatorFactors];
    }

    /**
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::isSameQuantity
     */
    protected function isSameQuantity(PhysicalQuantityInterface $firstQuantity, PhysicalQuantityInterface $secondQuantity)
    {
    }

    /**
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::toUnit
     */
    public function toUnit($unit)
    {
    }

    /**
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::toUnit
     */
    public function toNativeUnit()
    {
    }

    /**
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::__toString
     */
    public function __toString()
    {
    }
}
