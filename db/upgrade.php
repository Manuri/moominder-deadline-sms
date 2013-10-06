<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function xmldb_block_deadline_sms_upgrade($oldversion) {
   // global $CFG;
    
    global $DB;
    
    $dbman = $DB->get_manager(); 
 
    $result = TRUE;
 
// Insert PHP code from XMLDB Editor here
    
     if ($oldversion < 2013092503) {

        // Define field id to be added to deadlinesms_subscriptions
        $table = new xmldb_table('deadlinesms_subscriptions');
        $field = new xmldb_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);

        // Conditionally launch add field id
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // deadline_sms savepoint reached
        upgrade_block_savepoint(true, 2013092503, 'deadline_sms');
    }

    
    
 
    return $result;
}

?>
