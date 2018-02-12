<?php

namespace duncan3dc\MetaAudio\Utils;

/**
 * Class to handle bit check/updates.
 * $bit is the positiion, the least significant is 0 (eg in 1000 the 3rd bit is on, bits 0, 1, and 2 are off).
 */
class Bit
{

    /**
     * Check if a bit is on.
     *
     * @param int $value The decimal value to check
     * @param int $bit The position of the bit
     *
     * @return bool true if the bit is on
     */
    public static function isOn($value, $bit)
    {
        return (bool) ($value & (1 << $bit));
    }


    /**
     * Check if a bit is off.
     *
     * @param int $value The decimal value to check
     * @param int $bit The position of the bit
     *
     * @return bool true if the bit is off
     */
    public static function isOff($value, $bit)
    {
        return !static::isOn($value, $bit);
    }


    /**
     * Update the value to turn a bit on.
     *
     * @param int $value The decimal value to update
     * @param int $bit The position of the bit
     *
     * @return int
     */
    public static function turnOn($value, $bit)
    {
        return ($value | (1 << $bit));
    }


    /**
     * Update the value to turn a bit off.
     *
     * @param int $value The decimal value to update
     * @param int $bit The position of the bit
     *
     * @return int
     */
    public static function turnOff($value, $bit)
    {
        return ($value & ~(1 << $bit));
    }
}
