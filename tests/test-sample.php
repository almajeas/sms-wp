<?php
class SampleTest extends WP_UnitTestCase {

        function testSample() {
                global $wp_sms_plugin;
                $message = 'test message';
                $command = array( 'junk', 'get' , ' 1');
                $result = $wp_sms_plugin->execute_command($command);
                // replace this with some actual testing code
                $this->assertEquals($message, $result);
        }
}