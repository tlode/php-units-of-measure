<?php
namespace PhpUnitsOfMeasure\Exception;

class ComponentQuantitiesAlreadySet extends AbstractPhysicalQuantityException
{
    protected $error = 'The component quantities have already been initialized for this derived physical quantity.';
}
