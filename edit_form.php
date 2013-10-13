<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class block_deadline_sms_edit_form extends block_edit_form{
    protected function specific_definition($mform){
 

	$mform->addelement('text','config_title',get_string('blocktitle','block_deadline_sms'));
	$mform->setDefault('config_title','default value');
	$mform->setType('config_title',PARAM_MULTILANG);
        
        $mform->addelement('text','config_gateway',get_string('gateway','block_deadline_sms'));
	$mform->setDefault('config_gateway','default value');
	$mform->setType('config_gateway',PARAM_MULTILANG);
        
        $mform->addelement('text','config_pwd',get_string('pwd','block_deadline_sms'));
	$mform->setDefault('config_pwd','default value');
	$mform->setType('config_pwd',PARAM_MULTILANG);
        
        $mform->addelement('text','config_username',get_string('pwd','block_deadline_sms'));
	$mform->setDefault('config_username','default value');
	$mform->setType('config_username',PARAM_MULTILANG);
        
        $mform->addelement('text','config_baseurl',get_string('baseurl','block_deadline_sms'));
	$mform->setDefault('config_baseurl','default value');
	$mform->setType('config_baseurl',PARAM_MULTILANG);
    }
}
?>
