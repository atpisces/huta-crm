<?php

// defined('BASEPATH') or exit('No direct script access allowed');

class Reporting_APIs extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // $this->load->model('reporting_apis_model');
    }

    public function index($error = ''){


    }

    public function request_get_clients(){

        echo "testing";
        // $data['clients_list'] = $this->reporting_apis_model->get_CRM_Clients();
        // print_r(json_encode($data['clients_list']));


    }

    public function request_get_invoices(){

        // $data['invoices_list'] = $this->reporting_apis_model->get_CRM_Invoices();
        // print_r(json_encode($data['invoices_list']));

    }

}
?>