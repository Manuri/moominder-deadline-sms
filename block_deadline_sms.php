<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class block_deadline_sms extends block_base{
    
    public function init(){
        $this->title = get_string('deadline_sms', 'block_deadline_sms');
    }
    
    public function get_content() {
        
        if($this->content !== null){
            return $this->content;
        }
        
        $this->content         =  new stdClass;
              $this->content->text   = get_string('wantservice','block_deadline_sms');
              $this->content->text   .= '<form id="form1" name="form1" method="post" action="">';
              $this->content->text	.= '<table width="180" border="0"><tr>';
              $this->content->text	.= '<td width="60"><input type="submit" name="ok" id="button" value="'.get_string('yes' , 'block_deadline_sms').'" a align="left"/></td>';
              $this->content->text	.= '<td width="60"><input type="submit" name="no" id="button" value="'.get_string('no' , 'block_deadline_sms').'" a align="right"/></td>';
              $this->content->text	.= '</tr> </table>';
              $this->content->text	.= '</form>';

            return $this->content;
    }
    
    public function cron(){
        global $DB;
        
        $now = time();
        
        $instances = $DB->get_records_sql('select * from mdl_assign');
        
        foreach ($instances as $record){
            echo $assignment_id = $record->id;
            echo $assignment_name = $record->name;
            echo $due_date = $record->duedate;
        }
        
        
    }
    
    //to connect with the database
        function db_connect(){
            $con = mysql_connect("localhost", "root","");

            if(!$con){
                die("no connection!!!!!!!!11");
            }
            else {
                echo "connection established!!!!!!!1";
            }
            mysql_select_db("amaya_moodle",$con);
            
            return $con;
        }
        
       
        
        
    
    
}


?>
