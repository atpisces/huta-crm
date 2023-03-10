<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'invoice_number',
    'contract',
    db_prefix().'pur_invoices.pur_order',
    'invoice_date',
    'is_recurring_from',
    'subtotal',
    'tax', 
    'total',
    'payment_request_status',
    'payment_status',
    'transactionid',
    'vendor_note'
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'pur_invoices';
$join         = [ 'LEFT JOIN '.db_prefix().'pur_contracts ON '.db_prefix().'pur_contracts.id = '.db_prefix().'pur_invoices.contract' ];
$where = [];

$base_currency = get_base_currency_pur();

if ($this->ci->input->post('from_date')
    && $this->ci->input->post('from_date') != '') {
    array_push($where, 'AND invoice_date >= "'.$this->ci->input->post('from_date').'"');
}

if(isset($vendor)){
    array_push($where, ' AND '.db_prefix().'pur_invoices.vendor = '.$vendor);
}

if ($this->ci->input->post('to_date')
    && $this->ci->input->post('to_date') != '') {
    array_push($where, 'AND invoice_date <= "'.$this->ci->input->post('to_date').'"');
}

$contract = $this->ci->input->post('contract');
if (isset($contract)) {
    $where_contract = '';
    foreach ($contract as $t) {
        if ($t != '') {
            if ($where_contract == '') {
                $where_contract .= ' AND ('.db_prefix().'pur_invoices.contract = "' . $t . '"';
            } else {
                $where_contract .= ' or '.db_prefix().'pur_invoices.contract = "' . $t . '"';
            }
        }
    }
    if ($where_contract != '') {
        $where_contract .= ')';
        array_push($where, $where_contract);
    }
}

$pur_orders = $this->ci->input->post('pur_orders');
if (isset($pur_orders)) {
    $where_pur_orders = '';
    foreach ($pur_orders as $t) {
        if ($t != '') {
            if ($where_pur_orders == '') {
                $where_pur_orders .= ' AND ('.db_prefix().'pur_invoices.pur_order = "' . $t . '"';
            } else {
                $where_pur_orders .= ' or '.db_prefix().'pur_invoices.pur_order = "' . $t . '"';
            }
        }
    }
    if ($where_pur_orders != '') {
        $where_pur_orders .= ')';
        array_push($where, $where_pur_orders);
    }
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix().'pur_invoices.id as id','(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'taggables JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id WHERE rel_id = ' . db_prefix() . 'pur_invoices.id and rel_type="pur_invoice" ORDER by tag_order ASC) as tags', 'contract_number'
]);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

   for ($i = 0; $i < count($aColumns); $i++) {

        $_data = $aRow[$aColumns[$i]];
        if($aColumns[$i] == 'invoice_number'){
            $numberOutput = '';
    
            $numberOutput = '<a href="' . admin_url('purchase/purchase_invoice/' . $aRow['id']) . '"  >'.$aRow['invoice_number']. '</a>';
            
            $numberOutput .= '<div class="row-options">';

            if (has_permission('purchase_invoices', '', 'view')) {
                $numberOutput .= ' <a href="' . admin_url('purchase/purchase_invoice/' . $aRow['id']) . '" >' . _l('view') . '</a>';
            }
            if ((has_permission('purchase_invoices', '', 'edit') || is_admin()) ) {
                $numberOutput .= ' | <a href="' . admin_url('purchase/pur_invoice/' . $aRow['id']) . '">' . _l('edit') . '</a>';
            }
            if (has_permission('purchase_invoices', '', 'delete') || is_admin()) {
                $numberOutput .= ' | <a href="' . admin_url('purchase/delete_pur_invoice/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
            }
            $numberOutput .= '</div>';

            $_data = $numberOutput;
        }elseif($aColumns[$i] == 'vendor_note'){
            $_data = render_tags($aRow['tags']);
        }elseif($aColumns[$i] == 'invoice_date'){
            $_data = _d($aRow['invoice_date']);
        }elseif($aColumns[$i] == 'subtotal'){
            $_data = app_format_money($aRow['subtotal'],$base_currency->symbol);
        }elseif($aColumns[$i] == 'tax'){
            $_data = app_format_money($aRow['tax'],$base_currency->symbol);
        }elseif($aColumns[$i] == 'total'){
            $_data = app_format_money($aRow['total'],$base_currency->symbol);
        }elseif($aColumns[$i] == 'payment_status'){
            $class = '';
            if($aRow['payment_status'] == 'unpaid'){
                $class = 'danger';
            }elseif($aRow['payment_status'] == 'paid'){
                $class = 'success';
            }elseif ($aRow['payment_status'] == 'partially_paid') {
                $class = 'warning';
            }

            $_data = '<span class="label label-'.$class.' s-status invoice-status-3">'._l($aRow['payment_status']).'</span>';
        }elseif($aColumns[$i] == 'contract'){
            $_data = '<a href="'.admin_url('purchase/contract/'.$aRow['contract']).'">'.$aRow['contract_number'].'</a>';
        }elseif($aColumns[$i] == 'payment_request_status'){
            $_data = get_payment_request_status_by_inv($aRow['id']);
        }elseif($aColumns[$i] == db_prefix().'pur_invoices.pur_order'){
            $_data = '<a href="'.admin_url('purchase/purchase_order/'.$aRow[db_prefix().'pur_invoices.pur_order']).'">'.get_pur_order_subject($aRow[ db_prefix().'pur_invoices.pur_order']).'</a>';
        }elseif($aColumns[$i] == 'is_recurring_from'){
            $_data = ' <a href="' . admin_url('purchase/purchase_invoice/' . $aRow['is_recurring_from']) . '" >' . get_pur_invoice_number($aRow['is_recurring_from']) . '</a>'; 
        }

        $row[] = $_data;
    }
    $output['aaData'][] = $row;

}
