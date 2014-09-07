<?php

namespace PhpUnitsOfMeasureTest\PhysicalQuantity;

abstract class AbstractPhysicalQuantityTestCase extends \PHPUnit_Framework_TestCase
{
    protected $supportedUnitsWithAliases = [];

    /**
     * This test is here to help make sure the tests are in sync with the code
     */
    public function testSupportedUnits()
    {
        $quantityClass = get_class($this->instantiateTestQuantity());

        $this->assertEquals(
            $this->supportedUnitsWithAliases,
            $quantityClass::getSupportedUnits($withAliases = true)
        );
    }

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
