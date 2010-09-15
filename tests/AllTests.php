<?php

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'HotDate_AllTests::main');
}

require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'HotDateTime_Diff.php';
require_once 'HotDateTime_TimeZone.php';

class HotDate_AllTests
{
    // {{{ main()

    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    // }}}
    // {{{ suite()

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('HotDate Tests');
        $suite->addTestSuite('HotDateTime_Diff');
        $suite->addTestSuite('HotDateTime_TimeZone');
        return $suite;
    }

    // }}}
}

if (PHPUnit_MAIN_METHOD == 'HotDate_AllTests::main') {
    HotDate_AllTests::main();
}

?>
