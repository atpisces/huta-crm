
<style type="text/css">
   .invoice-table-column{
      border-top: 1px solid #000000 !important;
      border-bottom: 1px solid #000000 !important;
      border-left: 1px solid #000000 !important;
      border-right: 1px solid #000000 !important;
      color: #000000 !important;
      font-size: 15px;
   }
   .invoice-detail-table>tbody>tr:first-child td{
      border-top: 1px solid #000000 !important;
      border-bottom: 1px solid #000000 !important;
      border-left: 1px solid #000000 !important;
      border-right: 1px solid #000000 !important;
   }
</style>
<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(APPPATH . 'libraries/phpqrcode/qrlib.php');

// print_r($invoice_result);
// echo $invoice_result->branchid;
// exit;
//$CI->load->model('branches_model');
$CI =& get_instance();
$CI->load->model('estimates_model');
$CI->load->model('projects_model');
//$branch_result= $CI->branches_model->get_brand_and_details($invoice_result->branchid);
// print_r($branch_result);
// echo $invoice_result->branchid;
// exit;
// echo "My official results here: ".$result;


$clients_data = $CI->clients_model->get($credit_note->clientid);
// $project_details = $CI->projects_model->get_project_dtails($invoice->project_id);
// $project_exp = explode("/",$project_details->name);
//print_r($project_details);
//echo "NO".$invoice->project_id;
//exit;

$dimensions = $pdf->getPageDimensions();


$info_center_column = '<div style="width:200px !important; margin-left: 20px; text-align:center; color: #000000">
<div style="font-weight:bold; font-size: 27px; width: 100px !important;">DELIVERY NOTE</div>
</div>';
$info_left_column = '';
$info_left_column = '<div style="float: left; width: 100%; padding: 30px;">
               <table style="padding: 3px;">
                  <tr>
                     <th width="49.5%" class="invoice-table-column"><b>Date:</b> &nbsp;&nbsp;'._d($credit_note->date).'</th>
                     <th width="49.5%" align="right" class="invoice-table-column"><b>No.:</b> &nbsp;&nbsp;'.$credit_note_number.'</th>
                  </tr>
                  <tr>
                    <td colspan="2"></td>
                  </tr>
               </table>
               <table style="padding: 3px;">
                  <tr>
                    <td width="49.5%"><b>SELLER:</b></td>
                    <td width="49.5%"><b>BUYER:</b></td>
                  </tr>
                  <tr>
                    <td width="49.5%"><b>Ilyas Arab Engineering Construction Co.,Ltd</b>
                      <br><b>ADD:</b> No.22 Gate 2-Delmon Building,Al Barq St Al Rawabi Dist Saudi Arabia , Riyadh,Saudi Arabia
                      <br><b>VAT Number:</b>311197151900003
                      <br><b>MOB:</b>+961121212121
                      <br><b>Email:</b>abc@gmail.com
                    </td>
                    <td width="49.5%"><b>Receiver:</b>
                      <br><b>ADD:</b> 
                      <br><b>VAT Number:</b>
                      <br><b>MOB:</b>
                      <br><b>Email:</b>
                    </td>
                  </tr>
               </table>
            </div>';

$info_left_column1 .= '
               <table class="table table-bordered invoice-detail-table" style="padding: 3px;">
                  <tr>
                     <th width="6%" align="center" ><b>NO</b></th>
                     <th width="34%" align="center" ><b>Name/Specification</b></th>
                     <th width="16%" align="center" ><b>Unit</b></th>
                     <th width="12%" align="center" ><b>Qty</b></th>
                     <th width="19%" align="center" ><b>Unit Rate</b></th>
                     <th width="12%" align="center" ><b>Amount</b></th>
                  </tr>';
               $sr = 1; $price_excl_vat = 0; $vat_amt = 0; $amount_inclusive_vat = 0;
               $totalvat = 0; $amt_exc_vat = 0; $totalsar = 0;
               $items = get_items_table_data($credit_note, 'credit_note');
               foreach ($credit_note->items as $row) {

                  $estimate_item_tax = $CI->estimates_model->get_item_tax_value($row["id"],$row["rel_id"],'credit_note');
         
                  $info_left_column1 .= '<tr>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #ffffff; border-right-color: #000000; border-left-color: #000000;">'.$sr.'</td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #ffffff; border-right-color: #000000; border-left-color: #000000;">'.$row["description"].'<br />'.$row["long_description"].'<br></td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #ffffff; border-right-color: #000000; border-left-color: #000000;">'.app_format_money($row["rate"], $invoice->currency_name).'</td>';
                     if($estimate_item_tax) { 
                      //$info_left_column1 .= $estimate_item_tax->taxrate; 
                      $tt = $estimate_item_tax->taxrate; } else { 
             //$info_left_column1 .= '0.00'; 
           }
                     $tax_calc = $row["rate"] * ($tt/100);
                     $adding_tax = $row["rate"] + $tax_calc;
                     $totalvat = $totalvat + $tax_calc;
                     $totalsar = $totalsar + $adding_tax;
                  $info_left_column1 .= '<td align="center" class="invoice-table-column" style="border: 1px solid #ffffff; border-right-color: #000000; border-left-color: #000000;">'.app_format_money($tax_calc, $invoice->currency_name).'</td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #ffffff; border-right-color: #000000; border-left-color: #000000;">'.app_format_money($adding_tax, $invoice->currency_name).'<br /></td>
                  </tr>';
            
                     $sr++;
                  }

$info_left_column1 .= '';

                  $info_left_column1 .= '';

$info_left_column1 .= '';

$info_left_column1 .= '
                  <tr>
                    <td colspan="6"></td>
                  </tr>
                  <tr>
                     <th colspan="6">&nbsp;&nbsp;<b>Remarks:</b>';
                        if (!empty($credit_note->clientnote)) {
                              $info_left_column1 .= '<br><b style="font-weight: bold;">Note:</b><br>'.$credit_note->clientnote;
                        }
                        if (!empty($credit_note->terms)) {
                              $info_left_column1 .= '<hr />
                              <b>Terms and Conditions:</b><br>'.$credit_note->terms;
                        }
   $info_left_column1 .= '</th>
                     <!--<th colspan="1" class="invoice-table-column" align="center" style="border: 1px solid #000000;"></th>
                     <th colspan="2" class="invoice-table-column" align="center" style="border: 1px solid #000000;"><img src="'.site_url($qr_code_img).'" width="100px"></th>-->
                  </tr>
                  <tr>
                    <td colspan="6"></td>
                  </tr>
                  <tr>
                    <td colspan="6"></td>
                  </tr>
                  <tr>
                    <td colspan="6"></td>
                  </tr>
                  <tr>
                     <th colspan="2" ><b>&nbsp;&nbsp;Warehouse:</b></th>
                     <th colspan="2" ><b>Sales:</b></th>
                     <th colspan="2" ><b>Receiver:</b></th>
                  </tr>';

$info_left_column1 .= '</table></div>';
// Add logo
//$info_left_column .= pdf_logo_url();

/*$info_left_column2 = '
<div style="float:left; width:60%;">
    <div style="float:left; width:75%; text-align:center;">
        <table class="table" border="1" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <th>Invoice Number:</th>
                <td>'.format_invoice_number($credit_note->id).'</td>
                <td>'.format_invoice_number($credit_note->id).'</td>
                <th style="text-align:right;">رقم الفاتورة</th>
            </tr>
        </table>
        <table class="table" border="1" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <th>Invoice Issue Date:</th>
                <td>'.$credit_note->date.'</td>
                <td>'.$credit_note->date.'</td>
                <th style="text-align:right;">تاريخ إصدار الفاتورة</th>
            </tr>
            <tr>
                <th>Due Date:</th>
                <td>'.$credit_note->duedate.'</td>
                <td>'.$credit_note->duedate.'</td>
                <th style="text-align:right;">تاريخ الاستحقاق</th>
            </tr>
        </table>
    </div>
</div>';*/


// Write top left logo and right column info/text
pdf_multi_row($info_center_column,"", $pdf, ($dimensions['wk'] / 1.05) - $dimensions['lm']);
pdf_multi_row($info_left_column,"", $pdf, ($dimensions['wk'] / 1.05) - $dimensions['lm']);
pdf_multi_row($info_left_column1,"", $pdf, '');
//pdf_multi_row($info_left_column2,"", $pdf, ($dimensions['wk'] / 1.05) - $dimensions['lm']);


//pdf_multi_row($invoice_detail, "", $pdf, ($dimensions['wk'] ) - $dimensions['lm']);

?>

