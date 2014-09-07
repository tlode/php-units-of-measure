<?php

namespace PhpUnitsOfMeasureTest\PhysicalQuantity;

abstract class AbstractPhysicalQuantityTestCase extends \PHPUnit_Framework_TestCase
{
    protected $supportedUnitsWithAliases = [];

    /**
     * Verify that the object instantiates without error.
     */
    public function testConstructorSucceeds()
    {
        $this->instantiateTestQuantity();
    }

    /**
     * Build a test object of the target physical quantity.
     *
     * @return PhysicalQuantityInterface
     */
    abstract protected function instantiateTestQuantity();
}
