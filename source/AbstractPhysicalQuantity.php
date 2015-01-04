<?php
namespace PhpUnitsOfMeasure;

abstract class AbstractPhysicalQuantity implements PhysicalQuantityInterface
{
    /**
     * The collection of units of measure in which this quantity can
     * be represented.
     *
     * Note that a null (not an empty array) value indicates that
     * this class hasn't been initialized yet.
     *
     * Finally, note that this property is commented out here to force
     * an error if a child class does not define it's own static instance
     * of this property.  This avoids accidental pollution of shared
     * state between AbstractPhysicalQuantity child classes if an extending class
     * forgets to define this property.
     *
     * TL;DR - child classes must define this property themselves!
     *
     * @var UnitOfMeasureInterface[]
     */
    // protected static $unitDefinitions;

    /**
     * Register a new unit of measure for this quantity.
     *
     * The meaning here is to register a new unit of measure to which measurements
     * of this physical quantity can be converted.  This method is used both when
     * initalizing a quantity class or later, to add custom units of measure.
     *
     * @throws Exception\DuplicateUnitNameOrAlias If the name or any alias already exists
     *
     * @param UnitOfMeasureInterface $unit The new unit of measure
     */
    public static function addUnit(UnitOfMeasureInterface $unit)
    {
        if (static::unitNameOrAliasesAlreadyExist($unit)) {
            throw new Exception\DuplicateUnitNameOrAlias([
                ':labels' => implode(', ', [$unit->getName()] + $unit->getAliases())
            ]);
        }

        static::$unitDefinitions[] = $unit;
    }

    /**
     * Get the unit of measure that matches the given name by either name or alias.
     *
     * @param string $unit A name or abbreviation by which the unit is known.
     *
     * @throws Exception\UnknownUnitOfMeasure when an unknown unit of measure is given
     *
     * @return UnitOfMeasureInterface
     */
    public static function getUnit($unit)
    {
        // If this class hasn't had its default units set, set them now
        if (!is_array(static::$unitDefinitions)) {
            static::$unitDefinitions = [];
            static::initialize();
        }

        foreach (static::$unitDefinitions as $unitOfMeasure) {
            if ($unit === $unitOfMeasure->getName() || $unitOfMeasure->isAliasOf($unit)) {
                return $unitOfMeasure;
            }
        }

        throw new Exception\UnknownUnitOfMeasure([':unit' => $unit]);
    }

    /**
     * Given a unit of measure, determine if its name or any of its aliases conflict
     * with the set of already-known unit names and aliases.
     *
     * @param UnitOfMeasureInterface $unit The unit in question
     *
     * @return boolean true if there is a conflict, false if there is not
     */
    protected static function unitNameOrAliasesAlreadyExist(UnitOfMeasureInterface $unit)
    {
        // If this class hasn't had its default units set, set them now
        if (!is_array(static::$unitDefinitions)) {
            static::$unitDefinitions = [];
            static::initialize();
        }

        $currentUnitNamesAndAliases = [];
        foreach (static::$unitDefinitions as $unitOfMeasure) {
            $currentUnitNamesAndAliases[] = $unitOfMeasure->getName();
            $currentUnitNamesAndAliases += $unitOfMeasure->getAliases();
        }

        $newUnitNamesAndAliases = [$unit->getName()] + $unit->getAliases();
        foreach ($newUnitNamesAndAliases as $newUnitName) {
            if (in_array($newUnitName, $currentUnitNamesAndAliases)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Initialize the static properties of this quantity class, such as the set of
     * default units of measure.
     */
    abstract protected static function initialize();


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
     * @throws Exception\NonNumericValue If the value is not numeric
     * @throws Exception\NonStringUnitName If the unit is not a string
     */
    public function __construct($value, $unit)
    {
        if (!is_numeric($value)) {
            throw new Exception\NonNumericValue([':value' => $value]);
        }

        if (!is_string($unit)) {
            throw new Exception\NonStringUnitName([':name' => $unit]);
        }

        $this->originalValue = $value;
        $this->originalUnit  = $unit;
    }

    /**
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::toUnit
     */
    public function toUnit($toUnit)
    {
        return $this->toUnitOfMeasure(static::getUnit($toUnit));
    }

    /**
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::toNativeUnit
     */
    public function toNativeUnit()
    {
        return $this->getOriginalUnit()
            ->convertValueToNativeUnitOfMeasure($this->originalValue);
    }

    /**
     * Convert this quantity to the given unit of measure.
     *
     * @param UnitOfMeasureInterface $unit The object representing the target unit of measure.
     *
     * @return float This quantity's value in the given unit of measure.
     */
    private function toUnitOfMeasure(UnitOfMeasureInterface $unit)
    {
        return $unit->convertValueFromNativeUnitOfMeasure($this->toNativeUnit());
    }

    /**
     * Get this quantity's original units of measure.
     *
     * @return UnitOfMeasureInterface
     */
    private function getOriginalUnit()
    {
        return static::getUnit($this->originalUnit);
    }

    /**
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::__toString
     */
    public function __toString()
    {
        return trim($this->originalValue . ' ' . $this->getOriginalUnit()->getName());
    }

    /**
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::add
     */
    public function add(PhysicalQuantityInterface $quantity)
    {
        if (!$this->isEquivalentQuantity($quantity)) {
            throw new Exception\PhysicalQuantityMismatch([
                ':lhs' => (string) $this,
                ':rhs' => (string) $quantity
            ]);
        }

        $newValue = $this->originalValue + $quantity->toUnitOfMeasure($this->getOriginalUnit());

        return new static($newValue, $this->getOriginalUnit()->getName());
    }

    /**
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::subtract
     */
    public function subtract(PhysicalQuantityInterface $quantity)
    {
        if (!$this->isEquivalentQuantity($quantity)) {
            throw new Exception\PhysicalQuantityMismatch([
                ':lhs' => (string) $this,
                ':rhs' => (string) $quantity
            ]);
        }

        $newValue = $this->originalValue - $quantity->toUnitOfMeasure($this->getOriginalUnit());

        return new static($newValue, $this->getOriginalUnit()->getName());
    }

    /**
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::isEquivalentQuantity
     */
    public function isEquivalentQuantity(PhysicalQuantityInterface $testQuantity)
    {
        return get_class($this) === get_class($testQuantity);
    }

    /**
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::multiplyBy
     */
    public function multiplyBy(PhysicalQuantityInterface $quantity)
    {
        return DerivedPhysicalQuantityFactory::factory([$this, $quantity], []);
    }

    /**
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::divideBy
     */
    public function divideBy(PhysicalQuantityInterface $quantity)
    {
        return DerivedPhysicalQuantityFactory::factory([$this], [$quantity]);
    }
}
