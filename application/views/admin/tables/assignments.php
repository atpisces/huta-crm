<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'name',
    'description',
    'start_date',
    'end_date',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'assignments';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'name' || $aColumns[$i] == 'id') {
            $_data = '<a href="' . admin_url('assignments/field/' . $aRow['id']) . '">' . $_data . '</a>';
            if ($aColumns[$i] == 'name') {
                $_data .= '<div class="row-options">';
                $_data .= '<a href="' . admin_url('assignments/field/' . $aRow['id']) . '">' . _l('edit') . '</a>';
                $_data .= ' | <a href="' . admin_url('assignments/delete/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                $_data .= '</div>';
            }
        }

        $row[] = $_data;
    }


    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}
