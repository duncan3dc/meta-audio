<?php

namespace duncan3dc\MetaAudioTests\Utils;

use duncan3dc\MetaAudio\Utils\Bit;

class BitTest extends \PHPUnit_Framework_TestCase
{

    public function bitProvider()
    {
        yield [0, 0, false];
        yield [0, 1, false];

        yield [1, 0, true];
        yield [1, 1, false];

        yield [2, 0, false];
        yield [2, 1, true];
        yield [2, 2, false];

        yield [3, 0, true];
        yield [3, 1, true];
        yield [3, 2, false];

        # Ensure we support all 32 bits used by the library
        $value = 4294967295;
        for ($bit = 0; $bit <= 31; ++$bit) {
            yield [$value, $bit, true];
        }

        # This is a 33 bit number, all bits should be off
        $value = 4294967296;
        for ($bit = 0; $bit <= 31; ++$bit) {
            yield [$value, $bit, false];
        }

        # One off the maximum 32bit number should mean 31 of the 32 bits are on
        $value = 4294967294;
        yield [$value, 0, false];
        for ($bit = 1; $bit <= 31; ++$bit) {
            yield [$value, $bit, true];
        }
    }
    /**
     * @dataProvider bitProvider
     */
    public function testIsOn($value, $bit, $expected)
    {
        $this->assertSame($expected, Bit::isOn($value, $bit));
    }
    /**
     * @dataProvider bitProvider
     */
    public function testIsOff($value, $bit, $expected)
    {
        $this->assertSame(!$expected, Bit::isOff($value, $bit));
    }


    public function turnOnProvider()
    {
        yield [0, 0, 1];
        yield [0, 1, 2];

        yield [1, 0, 1];
        yield [1, 1, 3];

        yield [2, 0, 3];
        yield [2, 1, 2];
        yield [2, 2, 6];

        yield [3, 0, 3];
        yield [3, 1, 3];
        yield [3, 2, 7];

        # Ensure we support all 32 bits used by the library
        yield [4294967295, 31, 4294967295];
        yield [2147483647, 31, 4294967295];
    }
    /**
     * @dataProvider turnOnProvider
     */
    public function testTurnOn($value, $bit, $expected)
    {
        $this->assertSame($expected, Bit::turnOn($value, $bit));
    }


    public function turnOffProvider()
    {
        yield [0, 0, 0];
        yield [0, 1, 0];

        yield [1, 0, 0];
        yield [1, 1, 1];

        yield [2, 0, 2];
        yield [2, 1, 0];
        yield [2, 2, 2];

        yield [3, 0, 2];
        yield [3, 1, 1];
        yield [3, 2, 3];

        # Ensure we support all 32 bits used by the library
        yield [4294967295, 31, 2147483647];
        yield [2147483647, 31, 2147483647];
        yield [4294967295, 0, 4294967294];
    }
    /**
     * @dataProvider turnOffProvider
     */
    public function testTurnOff($value, $bit, $expected)
    {
        $this->assertSame($expected, Bit::turnOff($value, $bit));
    }
}
