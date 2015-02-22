<?php

namespace Saxulum\Tests\Crud\Util;

use Saxulum\Crud\Util\Helper;

class HelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $input
     * @param string $expect
     * @dataProvider camelCaseToUnderscoreProvider
     */
    public function testCamelCaseToUnderscore($input, $expect)
    {
        $this->assertEquals($expect, Helper::camelCaseToUnderscore($input));
    }

    /**
     * @return array
     */
    public function camelCaseToUnderscoreProvider()
    {
        return array(
            array(
                'dies_Ist_Ein_Test',
                'dies_ist_ein_test',
            ),
            array(
                'Dies_Ist_Ein_Test',
                'dies_ist_ein_test',
            ),
            array(
                'diesIstEinTest',
                'dies_ist_ein_test',
            ),
            array(
                'DiesIstEinTest',
                'dies_ist_ein_test',
            ),
        );
    }
}
