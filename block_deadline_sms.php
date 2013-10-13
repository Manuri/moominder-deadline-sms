<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class block_deadline_sms extends block_base {

    public function init() {
        //$this->title = get_string('deadline_sms', 'block_deadline_sms');
        $this->specialization();
    }

    public function get_content() {

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = get_string('wantservice', 'block_deadline_sms');
        $this->content->text .= '<form id="form1" name="form1" method="post" action="">';
        $this->content->text .= '<table width="180" border="0"><tr>';
        $this->content->text .= '<td width="60"><input type="submit" name="ok" id="button" value="' . get_string('yes', 'block_deadline_sms') . '" a align="left"/></td>';
        $this->content->text .= '<td width="60"><input type="submit" name="no" id="button" value="' . get_string('no', 'block_deadline_sms') . '" a align="right"/></td>';
        $this->content->text .= '</tr> </table>';
        $this->content->text .= '</form>';

        $this->get_input_from_interace();

        return $this->content;
    }

    public function specialization() {
        if (!empty($this->config->title)) {
            $this->title = $this->config->title;
        } else {
            //$this->config->title = 'Quiz SMS';
            $this->title = 'Deadline SMS';
        }
        if (empty($this->config->gateway)) {
            $this->config->gateway = '+94711114843';
        }

        if (empty($this->config->pwd)) {
            $this->config->pwd = '123';
        }

        if (empty($this->config->username)) {
            $this->config->username = 'kannelUser';
        }

        if (empty($this->config->baseurl)) {
            $this->config->baseurl = 'http://localhost:13013';
        }
    }

    //to get input from interface and subdcribe or unsubscribe
    function get_input_from_interace() {

        global $DB, $USER;

        if (isset($_POST['ok'])) {  //if someone wants to subscribe for the SMS Forums Service
            $userid = $USER->id;
            $telno = $USER->phone2;
            $this->write_to_file($userid . " " . $telno);

            if ($DB->record_exists('deadlinesms_subscriptions', array('userid' => $userid))) { //Check whether the user has already subscribed
                $this->content->text .= get_string('have_subscribed', 'block_deadline_sms');
            } else {
                if (strlen($telno) != 0) { //User should have enter his/her mobile phone no
                    $prefix_telno = get_string('prefix_telno', 'block_deadline_sms');
                    if (strpos($telno, $prefix_telno) !== false) { //The mobile phone no should be in the international format
                        $this->deadlinesms_service_subscribe($userid, $telno);
                        $this->content->text .= get_string('enabled', 'block_deadline_sms');
                    } else {
                        $this->content->text .= get_string('error_wrong_format', 'block_deadline_sms');
                    }
                } else {
                    $this->content->text .= get_string('error_no_telno', 'block_deadline_sms');
                }
            }
        }
        if (isset($_POST['no'])) {  //if someone doesn't want subcribe for the SMS Forums Service
            $userid = $USER->id;
            $this->deadlinesms_service_unsubscribe($userid);
            $this->content->text .= get_string('disabled', 'block_deadline_sms');
        }
    }

    function deadlinesms_service_subscribe($userid, $telno) {

        global $DB;
        if ($DB->record_exists('deadlinesms_subscriptions', array("userid" => $userid))) {
            return true;
        }

        if ($userid != null && $telno != null) {

            $sub = new stdClass();
            $sub->userid = $userid;
            $sub->telno = $telno;
            return $DB->insert_record('deadlinesms_subscriptions', $sub);
        } else {
            mtrace("Userid or Telephone no is Null");
        }
    }

    function deadlinesms_service_unsubscribe($userid) {
        global $DB;
        if ($DB->record_exists('deadlinesms_subscriptions', array("userid" => $userid))) {
            $DB->delete_records('deadlinesms_subscriptions', array("userid" => $userid));
            return true;
        }
        return true;
    }

    public function cron() {
        $this->check_assignments_and_notify_subscribers();
    }

    //to connect with the database
    function db_connect() {
        $con = mysql_connect("localhost", "root", "");

        if (!$con) {
            die("no connection!!!!!!!!11");
        } else {
            echo "connection established!!!!!!!1";
        }
        mysql_select_db("amaya_moodle", $con);

        return $con;
    }

    function check_courses_and_subscribed_users($difference, $cid, $assignmentname, $deadline) {
        global $DB;

        $context = $context = context_course::instance($cid);
        $enrolled_users = get_enrolled_users($context);

        $subscribed_users = $DB->get_records('deadlinesms_subscriptions');

        if ($difference >= 3540 && $difference <= 3600) {

            $this->write_to_file('difference>=3600');

            $result = $DB->get_records_select('course', "id = $cid");

            if (!$result) { // add this check.
                die('Invalid query: ' . mysql_error());
            }

            $rows = mysql_fetch_array($result);

            foreach ($enrolled_users as $enuser) {
                foreach ($subscribed_users as $subuser) {
                    if ($enuser->id == $subuser->userid) {

                        $this->send_deadline_sms($cid, $rows['shortname'], $assignmentname, date("Y-m-d H:i:s", $deadline), $subuser->telno);
                    }
                }
            }
        } else {
            $this->write_to_file('inside else****');
        }
    }

    function check_assignments_and_notify_subscribers() {
        global $DB;

        $now = time();
        echo 'time';
        echo $now;

        $instances = $DB->get_records_sql('select * from mdl_assign');

        foreach ($instances as $record) {
            echo $cid = $record->id;
            echo $assignmentame = $record->name;
            echo $deadline = $record->duedate;
            echo $difference = $record->difference = $deadline - $now;
            $this->check_courses_and_subscribed_users($difference, $cid, $assignmentame, $deadline);
        }
        return true;
    }

    function send_deadline_sms($courseid, $coursename, $assignmentname, $deadline, $to) {

        $message = $this->create_sms($courseid, $coursename, $assignmentname, $deadline);


        $this->write_to_file($to);
        // $this->send_sms($to, $message,'+94711114843');
        $from = $this->config->gateway;
        $this->send_sms($to, $message, $from);
    }

    function write_to_file($message) {
        $fp = fopen("/home/amaya/Desktop/myTextdeadline.txt", "a");


        if ($fp == false) {
            echo 'oh fp is false';
        } else {
            fwrite($fp, $message);
            fclose($fp);
        }
    }

    function create_sms($courseid, $coursename, $assignmentname, $deadline) {
        $sms = 'Assignment:' . $assignmentname . ' of ' . $courseid . ' ' . $coursename . ' will due at ' . $deadline;
        return $sms;
    }

    function send_sms($in_number, $in_msg, $from) {

        /* $url = "/cgi-bin/sendsms?username=kannelUser&password=123&from={$from}&to={$in_number}&text={$in_msg}";
          $url = str_replace(" ", "%20", $url);

          $results = file('http://localhost:13013' . $url); */
        $password = $this->config->pwd;
        $username = $this->config->username;
        $baseurl = $this->config->baseurl;

        $url = "/cgi-bin/sendsms?username={$username}&password={$password}&from={$from}&to={$in_number}&text={$in_msg}";
        $url = str_replace(" ", "%20", $url);

        // $results = file('http://localhost:13013' . $url);
        $results = file($baseurl . $url);
    }

}

?>
