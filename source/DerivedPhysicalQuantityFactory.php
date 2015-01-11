<?php
namespace PhpUnitsOfMeasure;

use PhpUnitsOfMeasure\PhysicalQuantity\DimensionlessCoefficient;
use PhpUnitsOfMeasure\PhysicalQuantity\UnknownDerivedPhysicalQuantity;

abstract class DerivedPhysicalQuantityFactory
{
    /**
     * The collection of known derived physical quantity
     * fully qualified class names.
     *
     * This array needs to be kept up to date with all the default
     * classes in this library.
     *
     * @var string[]
     */
    // private static $derivedQuantityClasses = [
    //     // @TODO - enable this list of classes
    //     // '\PhpUnitsOfMeasure\PhysicalQuantity\Acceleration',
    //     // '\PhpUnitsOfMeasure\PhysicalQuantity\Area',
    //     // '\PhpUnitsOfMeasure\PhysicalQuantity\Pressure',
    //     // '\PhpUnitsOfMeasure\PhysicalQuantity\Velocity',
    //     // '\PhpUnitsOfMeasure\PhysicalQuantity\Volume',
    // ];

    /**
     * Register a new derived physical quantity class in the list of known classes.
     *
     * Duplicates will be ignored.
     *
     * @throws DerivedQuantitiesMustExtendParent If the class doesn't exist or is not a subclass of AbstractDerivedPhysicalQuantity
     *
     * @param string $className The fully qualified class name of the new derived quantity
     */
    // final public static function addDerivedQuantity($className)
    // {
    //     if (in_array($className, self::$derivedQuantityClasses)) {
    //         return;
    //     }

    //     if (!class_exists($className) || !is_subclass_of($className, '\PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity')) {
    //         throw new Exception\DerivedQuantitiesMustExtendParent;
    //     }

    //     self::$derivedQuantityClasses[] = $className;
    // }

    /**
     * Given a set of numerator and denominator quantities, instantiate and return
     * a derived quantity.
     *
     * @param PhysicalQuantityInterface[] $numerators   A collection of quantities that make up the numerator of this quantity
     * @param PhysicalQuantityInterface[] $denominators A collection of quantities that make up the denominator of this quantity
     *
     * @return AbstractDerivedPhysicalQuantity
     */
    final public static function factory(array $numerators, array $denominators)
    {
        // Break down all derived units until we're left with a collection of base quantities
        list($numerators, $denominators) = self::recursiveDecomposeFactors(
            $numerators,
            $denominators
        );

        // Cancel units to find the minimum factors necessary for this unit
        list($numerators, $denominators) = self::reduceFactors(
            $numerators,
            $denominators
        );

        // Attempt to find a derived class that represents the same collection of units
        // foreach (self::$derivedQuantityClasses as $className) {
        //     if ($className::factorsMatchQuantityComponents($numerators, $denominators)) {
        //         return new $className($numerators, $denominators);
        //     }
        // }

        // No derived class was found with these units, use the catchall type
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
     * @return array[] A tuple of the form (AbstractPhysicalQuantity[], AbstractPhysicalQuantity[]) representing numerators and denominators
     */
    private static function recursiveDecomposeFactors(array $numerators, array $denominators)
    {
        // This function breaks a quantity down into its non-decomposable base quantities.
        // The return is an 2-item array, where the first term is an array of the numerator factors,
        // and the second term is an array of the denominator factors.
        $decomposeFactors = function (PhysicalQuantityInterface $factor) {
            if (!($factor instanceof AbstractDerivedPhysicalQuantity)) {
                return [[$factor], []];
            }

            list($factorNumerators, $factorDenominators) = $factor->getComponentFactors(); // @TODO ORLY?
            return AbstractDerivedPhysicalQuantity::recursiveDecomposeFactors(
                $factorNumerators,
                $factorDenominators
            );
        };

        // Build the set of numerators and denominators that are non-decomposable factors
        $resultNumerators   = [];
        $resultDenominators = [];

        foreach ($numerators as $numerator) {
            list($decomposedNumerators, $decomposedDenominators) = $decomposeFactors($numerator);
            $resultNumerators   += $decomposedNumerators;
            $resultDenominators += $decomposedDenominators;
        }

        foreach ($denominators as $denominator) {
            list($decomposedNumerators, $decomposedDenominators) = $decomposeFactors($denominator);
            $resultNumerators   += $decomposedDenominators;
            $resultDenominators += $decomposedNumerators;
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
     * Where the dimensionless coefficient value captures the relative proportions between
     * the cancelled quantities.
     *
     * @param AbstractPhysicalQuantity[] $numerators
     * @param AbstractPhysicalQuantity[] $denominators
     *
     * @return array[] A tuple of the form (AbstractPhysicalQuantity[], AbstractPhysicalQuantity[]) representing numerators and denominators
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
                if ($numerator->isEquivalentQuantity($denominator)) {
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
}
