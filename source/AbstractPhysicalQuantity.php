<?php
namespace PhpUnitsOfMeasure;

abstract class AbstractPhysicalQuantity
{
    /**
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::add
     */
    public function add(PhysicalQuantityInterface $quantity)
    {
        if (!$this->isSameQuantity($quantity, $this)) {
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
        if (!$this->isSameQuantity($quantity, $this)) {
            throw new Exception\PhysicalQuantityMismatch('Cannot subtract type ('.get_class($quantity).') from type ('.get_class($this).').');
        }

        $newValue = $this->originalValue - $quantity->toUnit($this->originalUnit);

        return new static($newValue, $this->originalUnit);
    }

    /**
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::multiplyBy
     */
    public function multiplyBy(PhysicalQuantityInterface $quantity)
    {
        return DerivedPhysicalQuantity::factory([$this, $quantity], []);
    }

    /**
     * @see \PhpUnitsOfMeasure\PhysicalQuantityInterface::divideBy
     */
    public function divideBy(PhysicalQuantityInterface $quantity)
    {
        return DerivedPhysicalQuantity::factory([$this], [$quantity]);
    }

    /**
     * Given two quantities, determine whether they represent the same
     * quantity.
     *
     * For base units, this is simply comparing types.  For derived units,
     * it's a more complicated case of comparing units.
     *
     * @param  PhysicalQuantityInterface $firstQuantity
     * @param  PhysicalQuantityInterface $secondQuantity
     *
     * @return boolean True if both quantities represent the same dimensional values.
     */
    abstract protected function isSameQuantity(PhysicalQuantityInterface $firstQuantity, PhysicalQuantityInterface $secondQuantity);
}
