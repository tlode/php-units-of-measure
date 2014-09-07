<?php
namespace PhpUnitsOfMeasure;

abstract class AbstractBasePhysicalQuantity extends AbstractPhysicalQuantity implements PhysicalQuantityInterface
{
    /**
     * The scalar value, in the original unit of measure.
     *
     * @var float
     */
    protected $originalValue;

    /**
     * The original unit of measure's string representation.
     *
     * @var string
     */
    protected $originalUnit;

    /**
     * Store the value and its original unit.
     *
     * @param float  $value The scalar value of the measurement
     * @param string $unit  The unit of measure in which this value is provided
     *
     * @throws \PhpUnitsOfMeasure\Exception\NonNumericValue If the value is not numeric
     * @throws \PhpUnitsOfMeasure\Exception\NonStringUnitName If the unit is not a string
     */
    public function __construct($value, $unit)
    {
        if (!is_numeric($value)) {
            throw new Exception\NonNumericValue("Value ($value) must be numeric.");
        }

        if (!is_string($unit)) {
            throw new Exception\NonStringUnitName("Alias ($unit) must be a string value.");
        }

        $this->originalValue = $value;
        $this->originalUnit  = $unit;
    }

    /**
     * @see \PhpUnitsOfMeasure\AbstractPhysicalQuantity::getOriginalValue
     */
    protected function getOriginalValue()
    {
        return $this->originalValue;
    }

    /**
     * @see \PhpUnitsOfMeasure\AbstractPhysicalQuantity::getOriginalUnit
     */
    protected function getOriginalUnit()
    {
        return $this->originalUnit;
    }

    /**
     * For these base quantities, we can assume the quantites are the same if their classes
     * are the same.
     *
     * @see \PhpUnitsOfMeasure\AbstractPhysicalQuantity::isSameQuantity
     */
    protected function isSameQuantity(PhysicalQuantityInterface $firstQuantity, PhysicalQuantityInterface $secondQuantity)
    {
        return get_class($firstQuantity) === get_class($secondQuantity);
    }
}
