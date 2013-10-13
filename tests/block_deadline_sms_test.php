<?php

/** Unit tests for block_deadline_sms
 * @package blocks
 * @subpackage deadline_sms
 * @author Amaya Perra 
 * 
 */
require_once '/var/www/moodle/blocks/moodleblock.class.php';
require_once '/var/www/moodle/blocks/deadline_sms/block_deadline_sms.php';

class block_deadline_sms_test extends advanced_testcase {

    var $testBlock;

    function setUp() {
        $this->testBlock = new block_deadline_sms();
    }

    /**
     * To test SMS sending 
     */
    public function test_sendSMS() {
        $this->testBlock->send_sms('+94718010490', 'unit%20testing%201', '+94711114843');
        //this->testBlock->send_sms('+94718010490','unit%20testing%201','+94720728002');
    }

    /**
     * To test writing to a file
     */
    public function test_writeToFile() {
        $this->testBlock->write_to_file("unit testing");
    }

    /**
     * To test connection to databse
     */
    public function test_db_connect() {
        $this->assertNotNull($this->testBlock->db_connect());
    }

    /**
     * To test subscription to the service
     * @global type $DB
     */
    public function test_deadlinesms_service_subscribe() {
        global $DB;
        $this->resetAfterTest(true);
        $this->testBlock->deadlinesms_service_subscribe(10, '9999999999');
        $this->assertEquals(1, $DB->count_records('deadlinesms_subscriptions', array('userid' => 10, 'telno' => '9999999999')));
    }

    /**
     * To test un-subscription from the service
     * @global type $DB
     */
    public function test_deadlinesms_service_unsubscribe() {
        global $DB;

        $this->resetAfterTest(true);
        $this->testBlock->deadlinesms_service_subscribe(10, '9999999999');
        $this->assertTrue($this->testBlock->deadlinesms_service_unsubscribe(10), 'Un-subscription is not working');
        $this->assertEmpty($DB->get_records_select('deadlinesms_subscriptions', "id = 10"), 'Not empty');
    }

}

?>
