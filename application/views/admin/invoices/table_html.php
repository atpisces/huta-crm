<?php defined('BASEPATH') or exit('No direct script access allowed');

$table_data = array();
array_push($table_data,_l('invoice_dt_table_heading_number'));
// $custom_fields = get_custom_fields('invoice',array('show_on_table'=>1));
// foreach($custom_fields as $field){
//   array_push($table_data,$field['name']);
// }
array_push($table_data,_l('Invoice Date'));
array_push($table_data,_l('Sub Total'));
array_push($table_data,_l('invoice_total_tax'));
array_push($table_data,_l('invoice_dt_table_heading_amount'));
array_push($table_data,array(
    'name'=>_l('invoice_estimate_year'),
    'th_attrs'=>array('class'=>'not_visible')
  ));
array_push($table_data,array(
    'name'=>_l('invoice_dt_table_heading_client'),
    'th_attrs'=>array('class'=>(isset($client) ? 'not_visible' : ''))
  ));
array_push($table_data,_l('project'));
array_push($table_data,_l('tags'));
array_push($table_data,_l('invoice_dt_table_heading_duedate'));
array_push($table_data,_l('invoice_dt_table_heading_status'));
$table_data = hooks()->apply_filters('invoices_table_columns', $table_data);
render_datatable($table_data, (isset($class) ? $class : 'invoices'));
?>
