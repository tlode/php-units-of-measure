<?php
namespace PhpUnitsOfMeasure;

use PhpUnitsOfMeasure\PhysicalQuantity\DimensionlessCoefficient;

class DerivedPhysicalQuantity extends AbstractPhysicalQuantity //implements PhysicalQuantityInterface
{
    /**
     * Given a set of numerator and denominator quantities, instantiate and return
     * a derived quantity.
     *
     * @param  PhysicalQuantityInterface[] $numeratorFactors
     * @param  PhysicalQuantityInterface[] $denominatorFactors
     *
     * @return DerivedPhysicalQuantity
     */
    static public function factory(array $numeratorFactors, array $denominatorFactors)
    {
        // Break down all derived units until we're left with a collection of base quantities
        list($numeratorFactors, $denominatorFactors) = static::recursiveDecomposeFactors(
            $numeratorFactors,
            $denominatorFactors
        );

        // Cancel units to find the minimum factors necessary for this unit
        list($numeratorFactors, $denominatorFactors) = static::reduceFactors(
            $numeratorFactors,
            $denominatorFactors
        );

        // Attempt to find a compound class that represents the same collection of units
        // @TODO this should look for actual classes, not just instantiate one of these parents
        return new static($numeratorFactors, $denominatorFactors);
    }

    /**
     * Given a set of numerators and a set of denominators, attempt to decompose them
     * into a set of numerators and denominators made entirely of base quantities.
     *
     * For example:
     * (Kg*m/s^2) * (m/s) / (ft*lbs) / (s)
     * Should decompose into:
     * (Kg * m * m) / (s * s * s * ft * lbs * s)
     *
     * This provides an easy way to track down which derived physical quantity (if any)
     * represents this particular derived value.
     *
     * @param PhysicalQuantityInterface[] $numerators   The numerator values for a quantity
     * @param PhysicalQuantityInterface[] $denominators The denominator values for a quantity
     *
     * @return array[] A tuple of the form (BasePhysicalQuantity[], BasePhysicalQuantity[]) representing numerators and denominators
     */
    static private function recursiveDecomposeFactors(array $numerators, array $denominators)
    {
        $resultNumerators   = [];
        $resultDenominators = [];

        foreach ($numerators as $factor) {
            if ($factor instanceof DerivedPhysicalQuantity) {
                list($factorNumerators, $factorDenominators) = static::recursiveDecomposeFactors(
                    $factor->numeratorFactors,
                    $factor->denominatorFactors
                );
                $resultNumerators   = array_merge($resultNumerators, $factorNumerators);
                $resultDenominators = array_merge($resultDenominators, $factorDenominators);
            } else {
                $resultNumerators[] = $factor;
            }
        }

        foreach ($denominators as $factor) {
            if ($factor instanceof DerivedPhysicalQuantity) {
                list($factorNumerators, $factorDenominators) = static::recursiveDecomposeFactors(
                    $factor->numeratorFactors,
                    $factor->denominatorFactors
                );
                $resultDenominators = array_merge($resultDenominators, $factorNumerators);
                $resultNumerators   = array_merge($resultNumerators, $factorDenominators);
            } else {
                $resultDenominators[] = $factor;
            }
        }

        return [$resultNumerators, $resultDenominators];
    }

    /**
     * Given a set of numerators and a set of denominators that have been reduced
     * to their base unit form, attempt to reduce them by cancelling factors.
     *
     * @param BasePhysicalQuantity[] $numerators
     * @param BasePhysicalQuantity[] $denominators
     *
     * @return array[] A tuple of the form (BasePhysicalQuantity[], BasePhysicalQuantity[]) representing numerators and denominators
     */
    static private function reduceFactors(array $numerators, array $denominators)
    {
        // Tally up the pre-existing dimensionless coefficients into a single numerator value,
        //  and remove them
        $coefficient = new DimensionlessCoefficient(1);
        foreach ($numerators as $index => $numerator) {
            if ($numerator instanceof DimensionlessCoefficient) {
                $coefficient = new DimensionlessCoefficient(
                    $coefficient->toNativeUnit() * $numerator->toNativeUnit()
                );
                unset($numerators[$index]);
            }
        }
        foreach ($denominators as $index => $denominator) {
            if ($denominator instanceof DimensionlessCoefficient) {
                $coefficient = new DimensionlessCoefficient(
                    $coefficient->toNativeUnit() / $denominator->toNativeUnit()
                );
                unset($denominators[$index]);
            }
        }

        // Identify any cancellable base units, remove them, and move their ratio into the
        // coefficient
        foreach ($numerators as $numIndex => $numerator) {
            foreach ($denominators as $denomIndex => $denominator) {
                if (get_class($numerator) === get_class($denominator)) {
                    $coefficient = new DimensionlessCoefficient(
                        $coefficient->toNativeUnit() * $numerator->toNativeUnit() / $denominator->toNativeUnit()
                    );
                    unset($numerators[$numIndex]);
                    unset($denominators[$denomIndex]);
                    break;
                }
            }
        }

        // Once all the cancellable units are cancelled, append the coefficent to the
        //  numerator
        $numerators[] = $coefficient;

        // return the reduced set
        return [$numerators, $denominators];
    }







    // @TODO these should be protected, but i'm testing
    public $numeratorFactors = [];

    public $denominatorFactors = [];

    protected function __construct(array $numeratorFactors, array $denominatorFactors)
    {
        $this->numeratorFactors   = array_values($numeratorFactors);
        $this->denominatorFactors = array_values($denominatorFactors);
    }

    /**
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::isSameQuantity
     */
    protected function isSameQuantity(PhysicalQuantityInterface $firstQuantity, PhysicalQuantityInterface $secondQuantity)
    {
        // If the classes match and the type isn't the generic derived quantity type, they match
        if (get_class($firstQuantity) !== 'PhpUnitsOfMeasure\DerivedPhysicalQuantity' &&
            get_class($firstQuantity) === get_class($secondQuantity))
        {
            return true;
        }

        // otherwise, compare the units directly and make sure they match.
        $firstQuantityNumerators = asort(array_map('get_class', $firstQuantity->$numeratorFactors));
        $firstQuantityDenominators = asort(array_map('get_class', $firstQuantity->$denominatorFactors));

        $secondQuantityNumerators = asort(array_map('get_class', $secondQuantity->$numeratorFactors));
        $secondQuantityDenominators = asort(array_map('get_class', $secondQuantity->$denominatorFactors));

        return ($firstQuantityNumerators === $secondQuantityNumerators) &&
            ($firstQuantityDenominators === $secondQuantityDenominators);
    }
}
