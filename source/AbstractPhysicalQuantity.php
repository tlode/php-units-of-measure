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
     * @throws \PhpUnitsOfMeasure\Exception\DuplicateUnitNameOrAlias If the name or alias already exists
     *
     * @param \PhpUnitsOfMeasure\UnitOfMeasureInterface $unit The new unit of measure
     */
    public static function registerUnitOfMeasure(UnitOfMeasureInterface $unit)
    {
        if (static::unitNameOrAliasesAlreadyExist($unit)) {
            $labels = implode(', ', array_merge([$unit->getName()], $unit->getAliases()));
            throw new Exception\DuplicateUnitNameOrAlias("The unit has a name or alias ($labels) which is already a registered unit for this quantity");
        }

        static::$unitDefinitions[] = $unit;
    }

    /**
     * Given a unit of measure, determine if its name or any of its aliases conflict
     * with the set of already-known unit names and aliases.
     *
     * @param  UnitOfMeasureInterface $unit The unit in question
     *
     * @return boolean true if there is a conflict, false if there is not
     */
    protected static function unitNameOrAliasesAlreadyExist(UnitOfMeasureInterface $unit)
    {
        // If this class hasn't had its default units set, set them now
        if (!static::$hasBeenInitialized) {
            static::$hasBeenInitialized = true;
            static::initializeUnitsOfMeasure();
        }
        $unitDefinitions = static::$unitDefinitions;

        $currentUnits = [];
        foreach ($unitDefinitions as $unitOfMeasure) {
            $currentUnits[] = $unitOfMeasure->getName();
            foreach ($unitOfMeasure->getAliases() as $alias) {
                $currentUnits[] = $alias;
            }
        }

        $newUnitName = $unit->getName();
        if (in_array($newUnitName, $currentUnits)) {
            return true;
        }

        $newAliases = $unit->getAliases();
        foreach ($newAliases as $newUnitAlias) {
            if (in_array($newUnitAlias, $currentUnits)) {
                return true;
            }
        }
        return false;
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
        // If this class hasn't had its default units set, set them now
        if (!static::$hasBeenInitialized) {
            static::$hasBeenInitialized = true;
            static::initializeUnitsOfMeasure();
        }
        $unitDefinitions = static::$unitDefinitions;

        foreach ($unitDefinitions as $unitOfMeasure) {
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
     * static::registerUnitOfMeasure().
     */
    abstract protected static function initializeUnitsOfMeasure();


    /**
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::toUnit
     */
    public function toUnit($toUnit)
    {
        return $this->toUnitOfMeasure(static::findUnitOfMeasureByNameOrAlias($toUnit));
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
     * Fetch the measurement in the quantity's native unit of measure
     *
     * @return float the measurement cast to the native unit of measurement
     */
    protected function toNativeUnit()
    {
        return $this
            ->getOriginalUnit()
            ->convertValueToNativeUnitOfMeasure($this->getOriginalValue());
    }

    /**
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::__toString
     */
    public function __toString()
    {
        return $this->getOriginalValue() . ' ' . $this->getOriginalUnit()->getName();
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

        $newValue = $this->getOriginalValue() + $quantity->toUnitOfMeasure($this->getOriginalUnit());

        // TODO not sure this is how derived quantities are going to instantiate
        return new static($newValue, $this->getOriginalUnit()->getName());
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

        $newValue = $this->getOriginalValue() - $quantity->toUnitOfMeasure($this->getOriginalUnit());

        // TODO not sure this is how derived quantities are going to instantiate
        return new static($newValue, $this->getOriginalUnit()->getName());
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
     * @return \PhpUnitsOfMeasure\UnitOfMeasureInterface
     */
    abstract protected function getOriginalUnit();

    /**
     * Determine whether two given PhysicalQuantityInterface objects represent the same
     * physical quantity.
     *
     * Note this is not considering magnitude.
     *
     * @param PhysicalQuantityInterface $firstQuantity
     * @param PhysicalQuantityInterface $secondQuantity
     *
     * @return boolean True if the quantities are the same, false if not.
     */
    abstract protected function isSameQuantity(PhysicalQuantityInterface $firstQuantity, PhysicalQuantityInterface $secondQuantity);
}
