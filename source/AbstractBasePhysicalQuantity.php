<?php
namespace PhpUnitsOfMeasure;

/**
 * This class is the parent of all the physical quantity classes, and
 * provides the infrastructure necessary for storing quantities and converting
 * between different units of measure.
 */
abstract class AbstractBasePhysicalQuantity extends AbstractPhysicalQuantity implements PhysicalQuantityInterface
{
    /**
     * The collection of units of measure in which this quantity can
     * be represented.
     *
     * @var \PhpUnitsOfMeasure\UnitOfMeasureInterface[]
     */
    protected static $unitDefinitions = [];

    /**
     * The unit of measure to be considered as the native
     * unit of measure.
     *
     * @var \PhpUnitsOfMeasure\UnitOfMeasureInterface
     */
    protected static $nativeUnitOfMeasure;

    /**
     * Have the default units been configured yet for this quantity?
     *
     * @var boolean
     */
    protected static $hasBeenInitialized = false;

    /**
     * Register a new Unit of Measure with this quantity.
     *
     * The intended use is to register a new unit of measure to which measurements
     * of this physical quantity can be converted.
     *
     * @param \PhpUnitsOfMeasure\UnitOfMeasureInterface $unit The new unit of measure
     */
    public static function registerUnitOfMeasure(UnitOfMeasureInterface $unit)
    {
        // If this class hasn't had its default units set, set them now
        if (!static::$hasBeenInitialized) {
            static::$hasBeenInitialized = true;
            static::initializeUnitsOfMeasure();
        }

        // Test for pre-existing unit name or alias conflicts
        $currentUnits = static::getSupportedUnits($withAliases = true);

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
     * Establish a "native" unit of measure for this physical quantity.
     *
     * This unit is typically the SI standard for this physical quantity.
     *
     * This is typically called from static::initializeUnitsOfMeasure(),
     * during the static initalization of a physical quantity class.
     *
     * @param UnitOfMeasureInterface $nativeUnit The new native unit of measure.
     */
    protected static function registerNativeUnitOfMeasure(UnitOfMeasureInterface $nativeUnit)
    {
        // First, attempt to register the unit in the list of units of measure that
        // this quantity supports.  Ignore any duplication errors that occur, its ok
        // if this unit is already present.
        try {
            static::registerUnitOfMeasure($nativeUnit);
        } catch (Exception\DuplicateUnitNameOrAlias $e) {
        }

        static::$nativeUnitOfMeasure = $nativeUnit;
    }

    /**
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::getSupportedUnits
     */
    public static function getSupportedUnits($withAliases = false)
    {
        // If this class hasn't had its default units set, set them now
        if (!static::$hasBeenInitialized) {
            static::$hasBeenInitialized = true;
            static::initializeUnitsOfMeasure();
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
    private static function findUnitOfMeasureByNameOrAlias($unit)
    {
        // If this class hasn't had its default units set, set them now
        if (!static::$hasBeenInitialized) {
            static::$hasBeenInitialized = true;
            static::initializeUnitsOfMeasure();
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
     *
     * This should include any generally used units of measure through
     * static::registerUnitOfMeasure, and also the native unit of measure through
     * static::registerNativeUnitOfMeasure().
     */
    abstract protected static function initializeUnitsOfMeasure();

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
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::isSameQuantity
     */
    protected function isSameQuantity(PhysicalQuantityInterface $firstQuantity, PhysicalQuantityInterface $secondQuantity)
    {
        return get_class($firstQuantity) === get_class($secondQuantity);
    }

    /**
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::toUnit
     */
    public function toUnit($unit)
    {
        $nativeUnitValue = $this->toNativeUnit();

        $toUnit      = static::findUnitOfMeasureByNameOrAlias($unit);
        $toUnitValue = $toUnit->convertValueFromNativeUnitOfMeasure($nativeUnitValue);

        return $toUnitValue;
    }

    /**
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::toUnit
     */
    public function toNativeUnit()
    {
        $originalUnit    = static::findUnitOfMeasureByNameOrAlias($this->originalUnit);
        $nativeUnitValue = $originalUnit->convertValueToNativeUnitOfMeasure($this->originalValue);

        return $nativeUnitValue;
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
}
