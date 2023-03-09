<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Biller extends AdminController
{
    /* List all staff members */
    public function index()
    {
       
        $data['biller'] = $this->biller_model->get('', ['active' => 1]);
        $data['title']         = _l('staff_members');
        $this->load->view('admin/billers/manage', $data);
    }
}
