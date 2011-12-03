<?php

namespace MartyIX\Tests;

class TestCase extends \PHPUnit_Framework_TestCase {

        protected $backupGlobals = FALSE;
        static private $messages = array();

        protected function log($variable)
        {
                self::$messages[] = var_export($variable, true);
        }

        static public function tearDownAfterClass()
        {
                echo "<br /><br /><br /><b>Test output:</b><br /><br />";
                echo "<pre>" . implode("\n<br />", self::$messages) . '</pre>';
        }
}