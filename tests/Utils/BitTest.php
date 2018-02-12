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
}
