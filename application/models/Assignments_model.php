<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Assignments_model extends App_Model
{
    private $pdf_fields = ['estimate', 'invoice', 'credit_note', 'items'];

    private $client_portal_fields = ['customers', 'estimate', 'invoice', 'proposal', 'contracts', 'tasks', 'projects', 'contacts', 'tickets', 'company', 'credit_note'];

    private $client_editable_fields = ['customers', 'contacts', 'tasks'];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param  integer (optional)
     * @return object
     * Get single custom field
     */
    public function get($id = false)
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix().'assignments')->row();
        }

        return $this->db->get(db_prefix().'assignments')->result_array();
    }

    /**
     * Add new custom field
     * @param mixed $data All $_POST data
     * @return  boolean
     */
    public function add($data)
    {

        $data['staffid'] = '1';

        $this->db->insert(db_prefix().'assignments', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Assignment Added [' . $data['name'] . ']');

            return $insert_id;
        }

        return false;
    }

    /**
     * Update custom field
     * @param mixed $data All $_POST data
     * @return  boolean
     */
    public function update($data, $id)
    {
        $original_field = $this->get($id);

        $this->db->where('id', $id);
        $this->db->update(db_prefix().'assignments', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Assignment Updated [' . $data['name'] . ']');

            return true;
        }

        return false;
    }

    /**
     * @param  integer
     * @return boolean
     * Delete Custom fields
     * All values for this custom field will be deleted from database
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix().'customfields');
        if ($this->db->affected_rows() > 0) {
            // Delete the values
            $this->db->where('fieldid', $id);
            $this->db->delete(db_prefix().'customfieldsvalues');
            log_activity('Custom Field Deleted [' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * Change custom field status  / active / inactive
     * @param  mixed $id     customfield id
     * @param  integer $status active or inactive
     */
    public function change_custom_field_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix().'customfields', [
            'active' => $status,
        ]);
        log_activity('Custom Field Status Changed [FieldID: ' . $id . ' - Active: ' . $status . ']');
    }

    /**
     * Return field where Shown on PDF is allowed
     * @return array
     */
    public function get_pdf_allowed_fields()
    {
        return $this->pdf_fields;
    }

    /**
     * Return fields where Show on customer portal is allowed
     * @return array
     */
    public function get_client_portal_allowed_fields()
    {
        return $this->client_portal_fields;
    }

    /**
     * Return fields where are editable in customers area
     * @return array
     */
    public function get_client_editable_fields()
    {
        return $this->client_editable_fields;
    }
}
