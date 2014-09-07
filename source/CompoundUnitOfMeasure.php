<?php
namespace PhpUnitsOfMeasure;

/**
 * This implementation of UnitOfMeasureInterface uses
 * a pair of anonymous functions to cast values to and from
 * the native unit of measure, respectively.
 */
class CompoundUnitOfMeasure implements UnitOfMeasureInterface
{
    /**
     * The canonical name for this unit of measure.
     *
     * Typically this is the official way the unit is abbreviated.
     *
     * @var string
     */
    protected $name;

    /**
     * A collection of alias names that map to this unit of measure
     *
     * @var string[]
     */
    protected $aliases = [];

    protected $componentUnits;

    /**
     * Configure this object's mandatory properties.
     *
     * @param string   $name           This unit of measure's canonical name
     * @param callable $fromNativeUnit The callable that can cast values into this unit of measure from the native unit of measure
     * @param callable $toNativeUnit   The callable that can cast values into the native unit from this unit of measure
     */
    public function __construct($name, array $componentUnits)
    {
        if (!is_string($name)) {
            throw new Exception\NonStringUnitName("Alias ($name) must be a string value.");
        }

        $this->name           = $name;
        $this->componentUnits = $componentUnits;
    }

    /**
     * @see \PhpUnitsOfMeasure\UnitOfMeasureInterface::getName
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @see \PhpUnitsOfMeasure\UnitOfMeasureInterface::addAlias
     */
    public function addAlias($alias)
    {
        if (!is_string($alias)) {
            throw new Exception\NonStringUnitName("Alias ($alias) must be a string value.");
        }

        $this->aliases[] = $alias;
    }

    /**
     * @see \PhpUnitsOfMeasure\UnitOfMeasureInterface::getAliases
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * @see \PhpUnitsOfMeasure\UnitOfMeasureInterface::isAliasOf
     */
    public function isAliasOf($unit)
    {
        if (!is_string($unit)) {
            throw new Exception\NonStringUnitName("Alias ($unit) must be a string value.");
        }

        return in_array($unit, $this->aliases);
    }

    /**
     * @see \PhpUnitsOfMeasure\UnitOfMeasureInterface::convertValueFromNativeUnitOfMeasure
     */
    public function convertValueFromNativeUnitOfMeasure($value)
    {
        if (!is_numeric($value)) {
            throw new Exception\NonNumericValue("Value ($value) must be numeric.");
        }

        // TODO
    }

    /**
     * @see \PhpUnitsOfMeasure\UnitOfMeasureInterface::convertValueToNativeUnitOfMeasure
     */
    public function convertValueToNativeUnitOfMeasure($value)
    {
        if (!is_numeric($value)) {
            throw new Exception\NonNumericValue("Value ($value) must be numeric.");
        }

        // TODO
    }
}
