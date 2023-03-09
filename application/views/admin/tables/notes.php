<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'CONCAT('.db_prefix() . 'clients.userid,"/",' . db_prefix() . 'clients.company) as companydetails',
    'description',
    'CONCAT('. db_prefix() . 'staff.firstname," ", ' . db_prefix() . 'staff.lastname) as staffname',
    'dateadded',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'notes';

$join = [
    'INNER JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'notes.rel_id',
    'INNER JOIN ' . db_prefix() . 'staff ON ' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'notes.addedfrom'
];

$where  = [' AND ' . db_prefix() . 'notes.rel_type = "customer"'];
 // ORDER BY tblnotes.id DESC 

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tblnotes.dateadded']);
$output  = $result['output'];
$rResult = $result['rResult'];

// print_r($output);
// echo "<br><br><br>";
// print_r($rResult);
// rsort($rResult);

$sr = 1;
foreach ($rResult as $aRow) {
    $row = [];

    $company = explode("/",$aRow["companydetails"]);
    $row[] = $sr." / ".$aRow["id"];
    $row[] = '<a href="'.base_url("admin/clients/client/".$company[0]).'" target="_blank">'.$company[1].'</a>';
    $row[] = $aRow["description"];
    $row[] = $aRow["staffname"];
    $row[] = date("d-m-Y h:i:s",strtotime($aRow["dateadded"]));

    $output['aaData'][] = $row;
    $sr++;
}
