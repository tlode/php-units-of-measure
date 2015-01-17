<?php

namespace PhpUnitsOfMeasureTest;

use PHPUnit_Framework_TestCase;

use PhpUnitsOfMeasure\UnitOfMeasure;
use PhpUnitsOfMeasure\AbstractDerivedPhysicalQuantity;
use PhpUnitsOfMeasure\DerivedPhysicalQuantityFactory;
use PhpUnitsOfMeasure\PhysicalQuantity\UnknownDerivedPhysicalQuantity;
use PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Plooposity;
use PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Pumpalumpiness;
use PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wigginess;
use PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wonkicity;
use PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Woogosity;

/**
 * These tests serve as a high-level verification that the basic
 * behavior of this library is performing as expected.
 *
 * In addition, these tests should be a clearer demonstration
 * of the intended use cases than the set of unit tests would be.
 *
 * To clarify things a bit, the example physical quantity classes
 * used in the tests below are defined the same way that real
 * quantities would be defined.  The test classes that begin with "W" are
 * test cases of base physical quantities, while the test cases
 * that begin with "P" are the derived physical quantities.  Please
 * review these test fixtures for examples of how real quantities would
 * be created.
 *
 * Because of the large amount of global state preserved in the static
 * properties of the various physical quantity classes, we'll run
 * each test in this file its own process.
 *
 * @runTestsInSeparateProcesses
 */
class DemonstrationTests extends PHPUnit_Framework_TestCase
{
    // ***************************************************************
    // *** BASE QUANTITIES                                         ***
    // ***                                                         ***
    // *** Base quantities are indivisible quantities like Length  ***
    // *** and Mass, which are not derived from other quantities.  ***
    // *** While the interfaces of base and derived quantities are ***
    // *** the same, the behavior is slightly different during     ***
    // *** instantiation.                                          ***
    // ***************************************************************

    public function testInstantiatingBaseQuantities()
    {
        // New quantities are created like this, with
        // the class name being the physical quantity that
        // is being measured, and the parameters being the
        // scalar value and unit of measure, respectively.
        //
        // Note that each unit can be referred to by several names
        // (for example sopee, sopees, s).
        $a = new Wigginess(1, 'sopee');
        $this->assertInstanceOf('PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wigginess', $a);

        $a = new Wigginess(2.123, 'sopees');
        $this->assertInstanceOf('PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wigginess', $a);

        $a = new Wigginess(2.6, 's');
        $this->assertInstanceOf('PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wigginess', $a);

        $a = new Wigginess(1, 'tumpet');
        $this->assertInstanceOf('PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wigginess', $a);

        $a = new Wigginess(2.123, 'tumpets');
        $this->assertInstanceOf('PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wigginess', $a);

        $a = new Wigginess(2.6, 't');
        $this->assertInstanceOf('PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wigginess', $a);
    }

    public function testStringConversionForBaseQuantities()
    {
        // Casting physical quantity objects to strings will
        // produce a reasonable string describing the quantity.
        $a = new Wigginess(21.123, 'sopees');
        $this->assertSame('21.123 s', (string) $a);

        $a = new Wigginess(21.123, 'tumpets');
        $this->assertSame('21.123 t', (string) $a);
    }

    public function testUnitConversionForBaseQuantities()
    {
        // Creating equivalent quantities from existing
        // quantities in different units of measure is done
        // with the toUnit() method.  Note that the method
        // returns a new object, and does not modify the
        // unit of the object on which it is called.
        //
        // (In imaginary Testworld, 2.5 sopees is 1 tumpet.
        // See the Wigginess.php example class for details
        // on how these units are defined.)
        $a = new Wigginess(21, 'sopees');
        $this->assertSame(21, $a->toUnit('sopee'));
        $this->assertSame(21/2.5, $a->toUnit('tumpets'));
        $this->assertSame(21/2.5, $a->toUnit('t'));
    }

    public function testCreatingNewUnitsForBaseQuantities()
    {
        // New units of measure can be defined and registered
        // with physical quantities on the fly.

        // Here, we create three new units, demonstrating the 3
        // different methods for instantiating them.  These units
        // will exist in addition to the units that come 'out of
        // the box' for the given quantity, once they're registered
        // with the static addUnit().

        // The linear unit factory is useful for defining new
        // units that are a simple scaling factor conversion
        // to the quantity's native unit of measure.
        // In this case there are 4.5 bbbb's in the native unit of measure.
        $unitA = UnitOfMeasure::linearUnitFactory('bbbb', 4.5);
        Wigginess::addUnit($unitA);

        // Using the native unit factory method is equivalent to a
        // linear unit with a factor of 1.  It's convenient for creating
        // a unit to represent the native unit of measure.
        $unitB = UnitOfMeasure::nativeUnitFactory('aaaa');
        Wigginess::addUnit($unitB);

        // The long form constructor is necessary for units
        // that don't have simple scaling factor functions for
        // their conversions.  For this unit we'll also add 2 more
        // aliases (dddd and eeee) that serve as equivalent names
        // for the unit's real name (cccc).
        $unitC = new UnitOfMeasure(
            'cccc',
            function ($valueInNativeUnit) {
                return $valueInNativeUnit - 100;
            },
            function ($valueInThisUnit) {
                return $valueInThisUnit + 100;
            }
        );
        $unitC->addAlias('dddd');
        $unitC->addAlias('eeee');
        Wigginess::addUnit($unitC);


        // Here we can see that the units defined above
        // convert as expected with the built-in units.
        $a = new Wigginess(21, 'sopees');
        $this->assertSame(21, $a->toUnit('aaaa'));
        $this->assertSame(21/4.5, $a->toUnit('bbbb'));
        $this->assertSame(21-100, $a->toUnit('cccc'));
        $this->assertSame(21-100, $a->toUnit('dddd'));
        $this->assertSame(21-100, $a->toUnit('eeee'));

        $b = new Wigginess(21, 'tumpets');
        $this->assertSame(21*2.5, $b->toUnit('aaaa'));
        $this->assertSame(21/4.5*2.5, $b->toUnit('bbbb'));
        $this->assertSame((21*2.5)-100, $b->toUnit('cccc'));
        $this->assertSame((21*2.5)-100, $b->toUnit('dddd'));
        $this->assertSame((21*2.5)-100, $b->toUnit('eeee'));
    }

    public function testAutomaticSIUnitsForBaseQuantities()
    {
        // SI units have a standard prefix naming convention to easily
        // provide powers-of-ten versions of a standard unit.  For instance,
        // for the physical quantity length, the meter is the standard SI
        // unit, and 1000 meters is a kilometer, 1/1000th of a meter is a
        // millimeter, and so on.
        //
        // The Woogosity class has the HasSIUnitsTrait trait, and can
        // automatically generate new units for a given unit, for all
        // the standard SI prefixes.  See the Woogosity.php class file
        // for an example of how this is done.
        $a = new Woogosity(21, 'plurp');

        $this->assertInstanceOf('PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Woogosity', $a);
        $this->assertSame(21*4.5 * 1e3, $a->toUnit('millilupees'));
        $this->assertSame(21*4.5 * 1e3, $a->toUnit('ml'));
        $this->assertSame(21*4.5 / 1e6, $a->toUnit('megalupees'));
        $this->assertSame(21*4.5 / 1e6, $a->toUnit('Ml'));
    }

    public function testAddBaseQuantities()

    {
        // Two quantities of equivalent value can be summed
        // by calling the add method.
        $a = new Wigginess(3, 'sopee');
        $b = new Wigginess(2, 'tumpet');
        $c = $a->add($b);

        $this->assertInstanceOf('PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wigginess', $c);
        $this->assertSame(3 + (2*2.5).' s', (string) $c);
    }

    public function testSubtractBaseQuantities()
    {
        // Similar to the add method, subtract called on the
        // left-hand-side operand object will subtract the
        // parameter quantity and produce a new value.
        $a = new Wigginess(3, 'sopee');
        $b = new Wigginess(2, 'tumpet');
        $c = $a->subtract($b);

        $this->assertInstanceOf('PhpUnitsOfMeasureTest\Fixtures\PhysicalQuantity\Wigginess', $c);
        $this->assertSame(3 - (2*2.5).' s', (string) $c);
    }

    public function testMultiplyBaseQuantities()
    {
        // Whereas adding and subtracting quantities will produce new
        // quantities of the same type, multiplication and division will
        // produce quantities of different types (think miles divided by hours
        // producing miles-per-hour).
        //
        // Because we haven't previously defined Wigginess * Wonkicity
        // as a known derived quantity, the resulting class of this
        // multiplication will be the catchall UnknownDerivedPhysicalQuantity.
        $a = new Wigginess(3, 'sopee');
        $b = new Wonkicity(2, 'uvee');
        $c = $a->multiplyBy($b);

        $this->assertInstanceOf(UnknownDerivedPhysicalQuantity::class, $c);
        $this->assertSame((3 * 2).' s * u', (string) $c);
    }

    public function testDivideBaseQuantities()
    {
        // Division works similarly to multiplication, and like subtraction,
        // the left-hand-side of the division is the object on which you call
        // the method, and the right hand side is the parameter passed into the
        // method.
        $a = new Wigginess(3, 'sopee');
        $b = new Wonkicity(2, 'uvee');
        $c = $a->divideBy($b);

        $this->assertInstanceOf(UnknownDerivedPhysicalQuantity::class, $c);
        $this->assertSame((3 / 2).' s / u', (string) $c);
    }

    public function testMoreMultiplicationAndDivisionOfBaseQuantities()
    {
        // Here we show some variations on the derived unit string descriptions.
        $a = new Wigginess(3, 'sopee');
        $b = new Wonkicity(2, 'uvee');

        $this->assertSame((3 * 2).' s * u', (string) $a->multiplyBy($b));

        $this->assertSame((3 / 2).' s / u', (string) $a->divideBy($b));

        $this->assertSame((3 * 3).' s^2', (string) $a->multiplyBy($a));

        $this->assertSame((3 * 3 * 3).' s^3', (string) $a->multiplyBy($a)->multiplyBy($a));

        $this->assertSame((3 * 3 / 2).' s^2 / u', (string) $a->multiplyBy($a)->divideBy($b));

        $this->assertSame((3 / 3).'', (string) $a->divideBy($a));
    }

    // ****************************************************************
    // *** DERIVED QUANTITIES                                       ***
    // ***                                                          ***
    // *** Derived quantities are composited out of base quantities ***
    // *** or other derived units.                                  ***
    // ****************************************************************


    // public function testInstantiatingDerivedQuantities()
    // {
    //     // Using derived quantities is very similar to using base
    //     // quantities.
    //     $a = new Plooposity(1, 'horp');
    //     $this->assertInstanceOf(Plooposity::class, $a);

    //     $a = new Plooposity(2, 'horps');
    //     $this->assertInstanceOf(Plooposity::class, $a);

    //     $a = new Plooposity(2, 'ho');
    //     $this->assertInstanceOf(Plooposity::class, $a);

    //     $a = new Plooposity(1, 'jerg');
    //     $this->assertInstanceOf(Plooposity::class, $a);

    //     $a = new Plooposity(2, 'jergs');
    //     $this->assertInstanceOf(Plooposity::class, $a);

    //     $a = new Plooposity(2, 'je');
    //     $this->assertInstanceOf(Plooposity::class, $a);
    // }

    // public function testStringConversionForDerivedQuantities()
    // {
    //     // String conversion for derived quantities works the same
    //     // as for base quantities.
    //     $a = new Plooposity(21.123, 'horps');
    //     $this->assertSame('21.123 ho', (string) $a);

    //     $a = new Plooposity(21.123, 'jergs');
    //     $this->assertSame('21.123 je', (string) $a);
    // }

    // public function testUnitConversionForDerivedQuantities()
    // {
    //     // in converting between units of measure, derived quantities
    //     // behave the same as base quantities.
    //     $a = new Plooposity(21, 'horps');
    //     $this->assertSame(21, $a->toUnit('horp'));
    //     $this->assertSame(21/2.5, $a->toUnit('jergs')); //@TODO check the numbers here
    //     $this->assertSame(21/2.5, $a->toUnit('j'));  //@TODO check the numbers here
    // }

    // public function testCreatingNewUnitsForDerivedQuantities()
    // {
    //     // Create three new units, with the 3 different methods
    //     //   for instantiating them.
    //     $unitA = UnitOfMeasure::nativeUnitFactory('aaaa');
    //     Plooposity::addUnit($unitA);

    //     $unitB = UnitOfMeasure::linearUnitFactory('bbbb', 4.5);
    //     Plooposity::addUnit($unitB);

    //     $unitC = new UnitOfMeasure(
    //         'cccc',
    //         function ($valueInNativeUnit) {
    //             return $valueInNativeUnit - 100;
    //         },
    //         function ($valueInThisUnit) {
    //             return $valueInThisUnit + 100;
    //         }
    //     );
    //     $unitC->addAlias('dddd');
    //     $unitC->addAlias('eeee');
    //     Plooposity::addUnit($unitC);

    //     // Demonstrate the conversions
    //     $a = new Plooposity(21, 'horps');

    //     $this->assertSame(21, $a->toUnit('aaaa'));
    //     $this->assertSame(21/4.5, $a->toUnit('bbbb'));
    //     $this->assertSame(21-100, $a->toUnit('cccc'));
    //     $this->assertSame(21-100, $a->toUnit('dddd'));
    //     $this->assertSame(21-100, $a->toUnit('eeee'));

    //     $b = new Plooposity(21, 'jergs');

    //     $this->assertSame(21*2.5, $b->toUnit('aaaa'));
    //     $this->assertSame(21/4.5*2.5, $b->toUnit('bbbb'));
    //     $this->assertSame((21*2.5)-100, $b->toUnit('cccc'));
    //     $this->assertSame((21*2.5)-100, $b->toUnit('dddd'));
    //     $this->assertSame((21*2.5)-100, $b->toUnit('eeee'));
    // }

    // public function testAutomaticMetricUnitsForDerivedClasses()
    // {
    //     // The automatic SI prefixes have the same effect as the
    //     // base quantities, but the implemention is slightly different.
    //     // See the Pumpalumpiness.php class file to see how this works.
    //     $a = new Pumpalumpiness(21, 'glergs');

    //     $this->assertInstanceOf(Pumpalumpiness::class, $a);
    //     $this->assertSame(21*3.5 * 1e3, $a->toUnit('millifloops'));
    //     $this->assertSame(21*3.5 * 1e3, $a->toUnit('mfl'));
    //     $this->assertSame(21*3.5 / 1e6, $a->toUnit('megafloops'));
    //     $this->assertSame(21*3.5 / 1e6, $a->toUnit('Mfl'));
    // }

    // public function testAddDerivedQuantities()
    // {
    //     // Derived quantities behave the same as base quantities for
    //     // addition.
    //     $a = new Plooposity(3, 'horps');
    //     $b = new Plooposity(2, 'jerg');
    //     $c = $a->add($b);

    //     $this->assertInstanceOf(Plooposity::class, $c);
    //     $this->assertSame(3 + (2*2.5), $c->toUnit('horps'));
    // }

    // public function testSubtractDerivedQuantities()
    // {
    //     // Derived quantities behave the same as base quantities for
    //     // subtraction.
    //     $a = new Plooposity(3, 'horps');
    //     $b = new Plooposity(2, 'jerg');
    //     $c = $a->subtract($b);

    //     $this->assertInstanceOf(Plooposity::class, $c);
    //     $this->assertSame(3 - (2*2.5), $c->toUnit('horps'));
    // }

    // public function testMultiplyDerivedUnits()
    // {
    //     // Derived quantities behave the same as base quantities for
    //     // multiplication.
    //     $a = new Plooposity(3, 'horps');
    //     $b = new Pumpalumpiness(2, 'floops');
    //     $c = $a->multiplyBy($b);

    //     $this->assertInstanceOf(UnknownDerivedPhysicalQuantity::class, $c);
    //     $this->assertSame(3 * 2, $c->toUnit('horp floops'));
    // }

    // public function testDivideDerivedUnits()
    // {
    //     // Derived quantities behave the same as base quantities for
    //     // division.
    //     $a = new Plooposity(3, 'horps');
    //     $b = new Pumpalumpiness(2, 'floops');
    //     $c = $a->divideBy($b);

    //     $this->assertInstanceOf(UnknownDerivedPhysicalQuantity::class, $c);
    //     $this->assertSame(3 / 2, $c->toUnit('horps/floop'));
    // }

    // public function testRegisteringNewDerivedQuantities()
    // {
    //     // If we want multiplication and division to produce objects of some
    //     // class other than UnknownDerivedPhysicalQuantity (for instance, we
    //     // want Length divided by Time to produce an object of type Velocity),
    //     // then we need to define these derived quantities as new classes and
    //     // register them.
    //     //
    //     // We register derived quantity clases by calling addDerivedQuantity()
    //     // on the DerivedPhysicalQuantityFactory class, so that it can properly
    //     // choose the type when composing quantities from other quantities.
    //     // If these classes are not registered before they're needed, the
    //     // resulting types of multiplyBy() and divideBy() will end up being
    //     // UnknownDerivedPhysicalQuantity.
    //     DerivedPhysicalQuantityFactory::addDerivedQuantity(Plooposity::class);
    // }

    // public function testDivideBaseQuantitiesForAKnownDerivedQuantity()
    // {
    //     // Here we're registering the new derived quantity, and showing
    //     // how division can arrive at the correct type. Note that this
    //     // is the same division procedure as above in testDivideBaseQuantities,
    //     // but first we're defining Wigginess divded by Wonkicity as a known
    //     // type: Plooposity.  See the Plooposity.php class file for details on
    //     // how this relationship is defined.
    //     DerivedPhysicalQuantityFactory::addDerivedQuantity(Plooposity::class);

    //     $a = new Wigginess(3, 'sopee');
    //     $b = new Wonkicity(2, 'uvee');
    //     $c = $a->divideBy($b);

    //     $this->assertInstanceOf(Plooposity::class, $c);
    //     $this->assertSame(3 / 2, $c->toUnit('sopees / uvee'));
    // }
}
