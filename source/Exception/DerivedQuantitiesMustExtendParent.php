<?php
namespace PhpUnitsOfMeasure\Exception;

class DerivedQuantitiesMustExtendParent extends AbstractPhysicalQuantityException
{
    protected $error = 'Derived physical quantity classes must extend \PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity.';
}
