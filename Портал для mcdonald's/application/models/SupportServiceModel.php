<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SupportServiceModel extends CI_Model {

    // function __construct()
    // {
        
    // }

    public function getNewChangeformExtensionBase( $options = [] ):array
    {
        if ( empty( $options ) && $options['restaurant_id'] ) return [];

        return $this->db->query("
            SELECT 
                [new_name]
                ,[new_changetype]
                ,[new_schedulechangetype]
                ,[new_new_instoreworktime]
                ,[new_driveworktime]
                ,[new_expressworktime]
                ,[new_glovoworktime]
                ,[new_rocketworktime]
                ,[new_schedulechangereason]
                ,[new_resumptionwork]
                ,[new_comment]
                ,[new_employeedata]
                ,[new_directorfullname]
                ,[new_directoremail]
                ,[new_directorephone]
            FROM [mcd_MSCRM].[dbo].[new_changeformExtensionBase]
            WHERE [new_restaurant] = '".$options['restaurant_id']."'
        ")->result_array();
    }
}