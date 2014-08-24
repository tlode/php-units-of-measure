<?php
namespace PhpUnitsOfMeasure;

/**
 * This class is the parent of all the physical quantity classes, and
 * provides the infrastructure necessary for storing quantities and converting
 * between different units of measure.
 */
abstract class PhysicalQuantity implements PhysicalQuantityInterface
{
    /**
     * The collection of units of measure in which this quantity can
     * be represented.
     *
     * @var \PhpUnitsOfMeasure\UnitOfMeasureInterface[]
     */
    static protected $unitDefinitions = [];

    /**
     * Have the default units been configured yet for this quantity?
     *
     * @var boolean
     */
    static protected $hasBeenInitialized = false;

    /**
     * Register a new Unit of Measure with this quantity.
     *
     * The intended use is to register a new unit of measure to which measurements
     * of this physical quantity can be converted.
     *
     * @param \PhpUnitsOfMeasure\UnitOfMeasureInterface $unit The new unit of measure
     */
    static public function registerUnitOfMeasure(UnitOfMeasureInterface $unit)
    {
        // If this class hasn't had its default units set, set them now
        if (!static::$hasBeenInitialized) {
            static::$hasBeenInitialized = true;
            static::registerDefaultUnitsOfMeasure();
        }

        // Test for pre-existing unit name or alias conflicts
        $currentUnits = static::getSupportedUnits(true);

        $newUnitName = $unit->getName();
        if (in_array($newUnitName, $currentUnits)) {
            throw new Exception\DuplicateUnitNameOrAlias('The unit name ('.$newUnitName.') is already a registered unit for this quantity');
        }

        $newAliases = $unit->getAliases();
        foreach ($newAliases as $newUnitAlias) {
            if (in_array($newUnitAlias, $currentUnits)) {
                throw new Exception\DuplicateUnitNameOrAlias('The unit alias ('.$newUnitAlias.') is already a registered unit for this quantity');
            }
        }

        // Store the new unit in the list of units for this quantity
        static::$unitDefinitions[] = $unit;
    }

    /**
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::getSupportedUnits
     */
    static public function getSupportedUnits($withAliases = false)
    {
        // If this class hasn't had its default units set, set them now
        if (!static::$hasBeenInitialized) {
            static::$hasBeenInitialized = true;
            static::registerDefaultUnitsOfMeasure();
        }

        $units = [];
        foreach (static::$unitDefinitions as $unitOfMeasure) {
            $units[] = $unitOfMeasure->getName();
            if ($withAliases) {
                foreach ($unitOfMeasure->getAliases() as $alias) {
                    $units[] = $alias;
                }
            }
        }

        return $units;
    }

    /**
     * Get the unit definition that matches the given unit of measure name.
     *
     * Note that this can match either the index or the aliases.
     *
     * @param  string $unit The starting unit of measure
     *
     * @throws \PhpUnitsOfMeasure\Exception\UnknownUnitOfMeasure when an unknown unit of measure is given
     *
     * @return \PhpUnitsOfMeasure\UnitOfMeasureInterface
     */
    static private function findUnitOfMeasureByNameOrAlias($unit)
    {
        // If this class hasn't had its default units set, set them now
        if (!static::$hasBeenInitialized) {
            static::$hasBeenInitialized = true;
            static::registerDefaultUnitsOfMeasure();
        }

        foreach (static::$unitDefinitions as $unitOfMeasure) {
            if ($unit === $unitOfMeasure->getName() || $unitOfMeasure->isAliasOf($unit)) {
                return $unitOfMeasure;
            }
        }

        throw new Exception\UnknownUnitOfMeasure("Unknown unit of measure ($unit)");
    }

    /**
     * Initialize the set of default units of measure for this quantity.
     */
    abstract static protected function registerDefaultUnitsOfMeasure();

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
        $this->originalUnit = $unit;
    }

    /**
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::toUnit
     */
    public function toUnit($unit)
    {
        $originalUnit    = static::findUnitOfMeasureByNameOrAlias($this->originalUnit);
        $nativeUnitValue = $originalUnit->convertValueToNativeUnitOfMeasure($this->originalValue);

        $toUnit      = static::findUnitOfMeasureByNameOrAlias($unit);
        $toUnitValue = $toUnit->convertValueFromNativeUnitOfMeasure($nativeUnitValue);

        return $toUnitValue;
    }

    /**
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::__toString
     */
    public function __toString()
    {
        $originalUnit = static::findUnitOfMeasureByNameOrAlias($this->originalUnit);
        $canonicalUnitName = $originalUnit->getName();

        return $this->originalValue . ' ' . $canonicalUnitName;
    }

    /**
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::add
     */
    public function add(PhysicalQuantityInterface $quantity)
    {
        if (get_class($quantity) !== get_class($this)) {
            throw new Exception\PhysicalQuantityMismatch('Cannot add type ('.get_class($quantity).') to type ('.get_class($this).').');
        }

        $newValue = $this->originalValue + $quantity->toUnit($this->originalUnit);

        return new static($newValue, $this->originalUnit);
    }

    /**
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::subtract
     */
    public function subtract(PhysicalQuantityInterface $quantity)
    {
        if (get_class($quantity) !== get_class($this)) {
            throw new Exception\PhysicalQuantityMismatch('Cannot subtract type ('.get_class($quantity).') from type ('.get_class($this).').');
        }

        $newValue = $this->originalValue - $quantity->toUnit($this->originalUnit);

        return new static($newValue, $this->originalUnit);
    }
}
