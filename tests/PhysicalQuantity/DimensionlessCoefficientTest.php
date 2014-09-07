<?php

namespace PhpUnitsOfMeasureTest\PhysicalQuantity;

use PhpUnitsOfMeasure\PhysicalQuantity\DimensionlessCoefficient;

class DimensionlessCoefficientTest extends AbstractPhysicalQuantityTestCase
{
    protected $supportedUnitsWithAliases = [
        '',
    ];

    protected function instantiateTestQuantity()
    {
        return new DimensionlessCoefficient(1);
    }

    public function testConvertToNativeValue()
    {
        $value = new DimensionlessCoefficient(5);

        $this->assertSame(5, $value->toNativeUnit());
    }
}
