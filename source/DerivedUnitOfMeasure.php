<?php
namespace PhpUnitsOfMeasure;

/**
 * This implementation of UnitOfMeasureInterface uses
 * a pair of anonymous functions to cast values to and from
 * the native unit of measure, respectively.
 */
class DerivedUnitOfMeasure implements UnitOfMeasureInterface
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

    /**
     * The units of measure which make up this unit.
     * @var array
     */
    protected $componentUnits;

    /**
     * Configure this object's mandatory properties.
     *
     * @param string   $name           This unit of measure's canonical name
     * @param callable $fromNativeUnit The callable that can cast values into this unit of measure from the native unit of measure
     * @param callable $toNativeUnit   The callable that can cast values into the native unit from this unit of measure
     */
    public function __construct($name, array $numerators, array $denominators)
    {
        if (!is_string($name)) {
            throw new Exception\NonStringUnitName([':name' => $name]);
        }

        $this->name           = $name;
        $this->componentUnits = [$numerators, $denominators];
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
            throw new Exception\NonStringUnitName([':name' => $alias]);
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
            throw new Exception\NonStringUnitName([':name' => $unit]);
        }

        return in_array($unit, $this->aliases);
    }

    /**
     * @see \PhpUnitsOfMeasure\UnitOfMeasureInterface::convertValueFromNativeUnitOfMeasure
     */
    public function convertValueFromNativeUnitOfMeasure($value)
    {
        if (!is_numeric($value)) {
            throw new Exception\NonNumericValue([':value' => $value]);
        }

        return $value;
        // TODO
    }

    /**
     * @see \PhpUnitsOfMeasure\UnitOfMeasureInterface::convertValueToNativeUnitOfMeasure
     */
    public function convertValueToNativeUnitOfMeasure($value)
    {
        if (!is_numeric($value)) {
            throw new Exception\NonNumericValue([':value' => $value]);
        }

        return $value;
        // TODO
    }
}
