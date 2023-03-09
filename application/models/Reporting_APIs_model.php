<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Reporting_APIs_model extends App_Model
{

    public function __construct()
    {

        parent::__construct();

    }

    public function get_CRM_Clients(){

        return $this->db->query('SELECT * FROM ' . db_prefix() . 'clients')->result_array();

    }

    public function get_CRM_Invoices(){

        return $this->db->query('SELECT * FROM ' . db_prefix() . 'invoices I INNER JOIN ' . db_prefix() . 'clients C ON C.userid = I.clientid')->result_array();

    }

}