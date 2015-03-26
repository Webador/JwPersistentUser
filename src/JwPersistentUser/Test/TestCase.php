<?php

namespace JwPersistentUser\Test;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Assert that two dates do not differ more than $treshold seconds.
     *
     * This is a convenient method to test if dates are propably equal to each other.
     *
     * @param $expected
     * @param $actual
     * @param int $threshold
     */
    protected function assertDateTimeEquals($expected, $actual, $threshold = 10)
    {
        $this->assertInstanceOf('\DateTime', $expected);
        $this->assertInstanceOf('\DateTime', $actual);

        $diff = abs($expected->getTimestamp() - $actual->getTimestamp());

        $this->assertLessThan(
            $threshold,
            $diff,
            'Date objects differ too much (' . $diff . ' seconds, treshold is ' . $threshold . ')'
        );
    }
}
