<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Assignments extends AdminController
{
    private $pdf_fields = [];

    private $client_portal_fields = [];

    private $client_editable_fields = [];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('custom_fields_model');
        $this->load->model('assignments_model');
        if (!is_admin()) {
            access_denied('Access Custom Fields');
        }
        // Add the pdf allowed fields
        $this->pdf_fields             = $this->custom_fields_model->get_pdf_allowed_fields();
        $this->client_portal_fields   = $this->custom_fields_model->get_client_portal_allowed_fields();
        $this->client_editable_fields = $this->custom_fields_model->get_client_editable_fields();
    }

    /* List all custom fields */
    public function index()
    {
        // $this->load->view('admin/tables/assignments', $data);
        // exit;
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('assignments');
        }
        $data['title'] = _l('Assignments');
        $this->load->view('admin/assignments/manage', $data);
    }

    public function field($id = '')
    {
        if ($this->input->post()) {
            // print_r($this->input->post());
            // exit;
            if ($id == '') {
                $id = $this->assignments_model->add($this->input->post());
                set_alert('success', _l('added_successfully', _l('assignment')));
                echo json_encode(['id' => $id]);
                die;
            }
            $success = $this->assignments_model->update($this->input->post(), $id);
            if (is_array($success) && isset($success['cant_change_option_custom_field'])) {
                set_alert('warning', _l('cf_option_in_use'));
            } elseif ($success === true) {
                set_alert('success', _l('updated_successfully', _l('custom_field')));
            }
            echo json_encode(['id' => $id]);
            die;
        }

        if ($id == '') {
            $title = _l('add_new', _l('custom_field_lowercase'));
        } else {
            $data['assignment_details'] = $this->assignments_model->get($id);
            $title                = _l('Edit Assignment');
        }

        $data['title']                  = $title;
        $this->load->view('admin/assignments/addnew', $data);
    }

    /* Delete announcement from database */
    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('assignments'));
        }
        $response = $this->custom_fields_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('custom_field')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('custom_field_lowercase')));
        }
        redirect(admin_url('assignments'));
    }

    /* Change custom field status active or inactive */
    public function change_custom_field_status($id, $status)
    {
        if ($this->input->is_ajax_request()) {
            $this->custom_fields_model->change_custom_field_status($id, $status);
        }
    }

    public function validate_default_date()
    {
        $date = strtotime($this->input->post('date'));
        $type = $this->input->post('type');

        echo json_encode([
            'valid'  => $date !== false,
            'sample' => $date ? $type == 'date_picker' ? _d(date('Y-m-d', $date)) : _dt(date('Y-m-d H:i', $date)) : null,
        ]);
    }
}
