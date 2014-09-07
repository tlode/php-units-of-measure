<?php
namespace PhpUnitsOfMeasure;

abstract class AbstractPhysicalQuantity
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
    public static function getSupportedUnits($withAliases = false)
    {
        $unitDefinitions = static::getUnitsOfMeasure();

        $units = [];
        foreach ($unitDefinitions as $unitOfMeasure) {
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
    protected static function findUnitOfMeasureByNameOrAlias($unit)
    {
        $unitDefinitions = static::getUnitsOfMeasure();
        foreach ($unitDefinitions as $unitOfMeasure) {
            if ($unit === $unitOfMeasure->getName() || $unitOfMeasure->isAliasOf($unit)) {
                return $unitOfMeasure;
            }
        }

        throw new Exception\UnknownUnitOfMeasure("Unknown unit of measure ($unit)");
    }

    /**
     * Fetch the set of unit definitions for this physical quantity.
     *
     * If the class hasn't yet been initalized, do that first.
     *
     * @return \PhpUnitsOfMeasure\UnitOfMeasureInterface[]
     */
    protected static function getUnitsOfMeasure()
    {
        // If this class hasn't had its default units set, set them now
        if (!static::$hasBeenInitialized) {
            static::$hasBeenInitialized = true;
            static::initializeUnitsOfMeasure();
        }

        return static::$unitDefinitions;
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
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::toUnit
     */
    public function toNativeUnit()
    {
        $originalUnit    = static::findUnitOfMeasureByNameOrAlias($this->getOriginalUnit());
        $nativeUnitValue = $originalUnit->convertValueToNativeUnitOfMeasure($this->getOriginalValue());

        return $nativeUnitValue;
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
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::__toString
     */
    public function __toString()
    {
        $originalUnit = static::findUnitOfMeasureByNameOrAlias($this->getOriginalUnit());
        $canonicalUnitName = $originalUnit->getName();

        return $this->getOriginalValue() . ' ' . $canonicalUnitName;
    }

    /**
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::add
     */
    public function add(PhysicalQuantityInterface $quantity)
    {
        if (!$this->isSameQuantity($quantity, $this)) {
            throw new Exception\PhysicalQuantityMismatch(
                'Cannot add type ('.get_class($quantity).') to type ('.get_class($this).') because the units do not match.'
            );
        }

        $newValue = $this->getOriginalValue() + $quantity->toUnit($this->getOriginalUnit());

        return new static($newValue, $this->getOriginalUnit());
    }

    /**
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::subtract
     */
    public function subtract(PhysicalQuantityInterface $quantity)
    {
        if (!$this->isSameQuantity($quantity, $this)) {
            throw new Exception\PhysicalQuantityMismatch(
                'Cannot subtract type ('.get_class($quantity).') from type ('.get_class($this).') because the units do not match.'
            );
        }

        $newValue = $this->getOriginalValue() - $quantity->toUnit($this->getOriginalUnit());

        return new static($newValue, $this->getOriginalUnit());
    }

    /**
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::multiplyBy
     */
    public function multiplyBy(PhysicalQuantityInterface $quantity)
    {
        return AbstractDerivedPhysicalQuantity::factory([$this, $quantity], []);
    }

    /**
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::divideBy
     */
    public function divideBy(PhysicalQuantityInterface $quantity)
    {
        return AbstractDerivedPhysicalQuantity::factory([$this], [$quantity]);
    }

    /**
     * Get this quantity's value, in the original units of measure.
     *
     * @return float
     */
    abstract protected function getOriginalValue();

    /**
     * Get this quantity's original units of measure.
     *
     * @return string
     */
    abstract protected function getOriginalUnit();

    /**
     * Determine whether two given PhysicalQuantityInterface objects represent the same
     * physical quantity.
     *
     * @param PhysicalQuantityInterface $firstQuantity
     * @param PhysicalQuantityInterface $secondQuantity
     *
     * @return boolean True if the quantities are the same, false if not.
     */
    abstract protected function isSameQuantity(PhysicalQuantityInterface $firstQuantity, PhysicalQuantityInterface $secondQuantity);
}
