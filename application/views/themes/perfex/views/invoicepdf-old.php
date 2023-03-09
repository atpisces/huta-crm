
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
$CI->load->model('invoices_model');
//$branch_result= $CI->branches_model->get_brand_and_details($invoice_result->branchid);
// print_r($branch_result);
// echo $invoice_result->branchid;
// exit;
// echo "My official results here: ".$result;


$extrafields_data = $CI->invoices_model->get_extra_fields_data($invoice->id);
$clients_data = $CI->clients_model->get($invoice->clientid);
// echo "Data: ";
// print_r($extrafields_data);
// exit;

$dimensions = $pdf->getPageDimensions();


$info_center_column = '<div style="width:200px !important; margin-left: 20px; text-align:center; border: 2px solid #000000; background-color: #bfbfbf; color: #000000">
<div style="font-weight:bold; width: 100px !important;">TAX INVOICE <br>فاتورة ضريبية </div>
</div>';

$info_left_column = '<div style="float: left; width: 100%; padding: 30px;">
               <table style="border: 1px solid #000000;">
                  <tr>
                     <th width="33%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Seller Name:</b></th>
                     <td width="33%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.get_option('invoice_company_name').'</td>
                     <th width="33%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>البائع اسم:</b></th>
                  </tr>
                  <tr>
                     <th width="33%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Invoice No:</b></th>
                     <td width="33%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.format_invoice_number($invoice->id).'</td>
                     <th width="33%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>رقم الفاتورة :</b></th>
                  </tr>
                  <tr>
                     <th width="33%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Date of Invoice:</b></th>
                     <td width="33%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.date('d/m/Y', strtotime($invoice->date)).'</td>
                     <th width="33%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>تاري خ الفاتورة :</b></th>
                  </tr>
                  <tr>
                     <th width="33%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Invoice for the period:</b></th>
                     <td width="33%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.date('01/m/Y').' - '.date('t/m/Y').'</td>
                     <th width="33%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>فاتورة الف رية:</b></th>
                  </tr>
                  <tr>
                     <th width="33%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Huta Marine Works Ltd. VAT No.</b></th>
                     <td width="33%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.get_option('company_vat').'</td>
                     <th width="33%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>رقم ال ر ضي ي ب ل ر شكة هوتا للاعمال البحريه المحدود:</b></th>
                  </tr>
               </table>
            </div>';

$info_left_column1 = '<div style="float: left; width: 100%; padding: 30px;">
               <table cellpadding="5px">
                  <tr>
                     <th width="99%" align="center" class="invoice-table-column" style="border: 2px solid #000000;"><b>Customer Details / تفاصيل العميل</b></th>
                  </tr>
                  <tr>
                     <th width="20%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Company Name:</b></th>
                     <td width="59%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.get_option('invoice_company_name').'</b></td>
                     <th width="20%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>راسم الزبون : </b></th>
                  </tr>
                  <tr>
                     <th width="20%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Client VAT #:</b></th>
                     <td width="59%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.$clients_data->vat.'</b></td>
                     <th width="20%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>ترقم ال ر ضي ي ب ::</b></th>
                  </tr>
                  <tr>
                     <th width="20%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Client Address:</b></th>
                     <td width="59%" align="center" class="invoice-table-column">'.$clients_data->address.'</td>
                     <th width="20%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>عنوان العميل :</b></th>
                  </tr>';
$pdf_custom_fields = get_custom_fields('invoice', array('show_on_pdf' => 1, 'show_on_client_portal' => 1));
                 foreach ($pdf_custom_fields as $field) {
                    $value = get_custom_field_value($invoice->id, $field['id'], 'invoice');
                    if ($value == '') {
                      continue;
                    }
                    $exp = explode("/",$field['name']);
$info_left_column1 .= '<tr>
                     <th width="20%" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.$exp[0].':</b></th>
                     <td width="59%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.$value.'</td>
                     <th width="20%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.$exp[1].':</b></th>
                  </tr>';
                  }
$info_left_column1 .= '</table>';

$info_left_column1 .= '
               <table class="table table-bordered invoice-detail-table" cellpadding="3px">
                  <tr>
                    <td colspan="6"></td>
                  </tr>
                  <tr style="background-color: #bfbfbf; color: #000000">
                     <th width="12%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>S. No. <br> الرقم التسلسلي</b></th>
                     <th width="31%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>Description <br> الصنف</b></th>
                     <th width="16%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>Amount Exclusive of Vat <br> السعر الاجمالي 
غير شامل ضريبة</b></th>
                     <th width="12%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>VAT Rate <br> الضريبة القيمة المضافة %</b></th>
                     <th width="12%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>VAT Amount (SAR) <br> الضريبة القيمة المضافة
 ريال سعودي</b></th>
                     <th width="16%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>Total (SAR) <br> الاجمالي ريال سعودي</b></th>
                  </tr>';
               $sr = 1; $price_excl_vat = 0; $vat_amt = 0; $amount_inclusive_vat = 0;
               $totalvat = 0; $amt_exc_vat = 0; $totalsar = 0;
               $items = get_items_table_data($invoice, 'invoice');
               foreach ($invoice->items as $row) {
                  $excvat = $row["qty"] * $row["rate"];
                  $amt_exc_vat = $amt_exc_vat + $row["rate"];
                  $vat_amt = $vat_amt + $invoice->total_tax;
                  $amount_inclusive_vat = $amount_inclusive_vat + $invoice->subtotal;

                  $estimate_item_tax = $CI->estimates_model->get_item_tax_value($row["id"],$row["rel_id"],'invoice');
         
                  $info_left_column1 .= '<tr>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.$sr.'</td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.$row["description"].'<br />'.$row["long_description"].'</td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.app_format_money($row["rate"], $invoice->currency_name).'</td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;">';
                     if($estimate_item_tax) { $info_left_column1 .= $estimate_item_tax->taxrate; $tt = $estimate_item_tax->taxrate; } else { $info_left_column1 .= '0.00'; }
                     $tax_calc = $row["rate"] * ($tt/100);
                     $adding_tax = $row["rate"] + $tax_calc;
                     $totalvat = $totalvat + $tax_calc;
                     $totalsar = $totalsar + $adding_tax;
                  $info_left_column1 .= '%</td><td width="12%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.app_format_money($invoice->total_tax, $invoice->currency_name).'</td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.app_format_money($adding_tax, $invoice->currency_name).'</td>
                  </tr>';
            
                     $sr++;
                  }

                  $five_percent_withheld = $amount_inclusive_vat * 0.05;
                  $final_amount = $amount_inclusive_vat - $five_percent_withheld;
                  $file =  "assets/images/".$invoice->hash.".png";

$info_left_column1 .= '<tr style="background: #f2f2f2; color: #000000">
                     <th colspan="2" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>Gross Invoice for the Period <br> الفاتورة الإجمالية للف رية</b></th>
                     <th align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.app_format_money($amt_exc_vat, $invoice->currency_name).'</b></th>
                     <th align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b></b></th>
                     <th align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.app_format_money($totalvat, $invoice->currency_name).'</b></th>
                     <th align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.app_format_money($totalsar, $invoice->currency_name).'</b></th>
                  </tr>';
            foreach($extrafields_data as $extra){
              if($extra["fieldtype"] == "Recovery"){
                  $info_left_column1 .= '<tr>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.$sr.'</td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;">Recovery of Advance Payment';
$info_left_column1 .= $invoice->recovery_payment.''.$invoice->recovery_label_field;
                  $recovery_c_vat = 0; $recovery_vat = 0; $recovery_with_vat = 0;

                  $recovery_c_vat = $extra["fieldamount"];
                  $recovery_vat = $recovery_c_vat * ($extra["fieldvat"]/100);
                  $recovery_with_vat = $recovery_c_vat + $recovery_vat;

$info_left_column1 .= '<br> استرداد الدفعة المقدمة</td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.app_format_money($extra["fieldamount"], $invoice->currency_name).'</td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.$extra["fieldvat"].'%</td>
                     <td width="12%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.app_format_money($recovery_vat, $invoice->currency_name).'</td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.app_format_money($recovery_with_vat, $invoice->currency_name).'</td>
                  </tr>';
                  $sr++;
                  $amt_exc_vat = $amt_exc_vat - $recovery_c_vat;
                  $totalvat = $totalvat - $recovery_vat;
                  $totalsar = $totalsar - $recovery_with_vat;
              }
            }

                  $rem_amt_exc = $amt_exc_vat;
                  $rem_amt_vat = $totalvat;
                  $rem_amt_total = $totalsar;

$info_left_column1 .= '<tr style="background: #f2f2f2; color: #000000">
                     <th colspan="2" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>Net Invoice for the Period <br> صاف الفاتورة للف رية</b></th>
                     <th align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.app_format_money($rem_amt_exc, $invoice->currency_name).'</b></th>
                     <th align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b></b></th>
                     <th align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.app_format_money($rem_amt_vat, $invoice->currency_name).'</b></th>
                     <th align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.app_format_money($rem_amt_total, $invoice->currency_name).'</b></th>
                  </tr>';

  function einv_generate_tlv_qr_code($array_tag=array()){
      $index=1;
      $tlv_string = '';
      foreach($array_tag as $tag_val){
          $tlv_string.=pack("H*", sprintf("%02X",(string) "$index")).
                       pack("H*", sprintf("%02X",strlen((string) "$tag_val"))).
                       (string) "$tag_val";
          $index++;                              
      }
      
      return base64_encode($tlv_string);
  }

  $Code=einv_generate_tlv_qr_code(array(get_option('invoice_company_name'),get_option('company_vat'),date("Y-m-d H:i:s a",strtotime($invoice->datecreated)),$rem_amt_total,$rem_amt_vat));
   if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') 
        $link = "https://"; 
    else
        $link = "http://"; 
    
   $link .= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; 
   
  //$text = "Seller:".get_option('invoice_company_name')."\nVAT Number: ".get_option('company_vat')."\nDate: ".date("Y-m-d H:i:sa",strtotime($invoice->date))."\nAmount: ".app_format_money($invoice->total, $invoice->currency_name)."\nTax : ".app_format_money($tax['total_tax'], $invoice->currency_name);
  //$Code = base64_encode($text); 
  $File_NAme = $invoice->hash;

  $file =  "assets/images/".$invoice->hash.".png";
  // unlink($file);
  $qr_code_img = $file;
  if(file_exists($file)){
    // $qr_code_img = $file;
  }
  // else{
  //     //set it to writable location, a place for temp generated PNG files
  //     //$PNG_TEMP_DIR = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets/images/' . DIRECTORY_SEPARATOR;
  //     $PNG_TEMP_DIR = 'assets/images/';
      
  //     //html PNG location prefix
  //     $PNG_WEB_DIR = 'assets/images/';
      
  //     //ofcourse we need rights to create temp dir
  //     if (!file_exists($PNG_TEMP_DIR))
  //           mkdir($PNG_TEMP_DIR);
      
  //     $filename = 'text';
      
  //     $errorCorrectionLevel = 'L';
  //        $matrixPointSize = 44;
      
  //     if (isset($Code)) { 
         
  //           $filename .= $File_NAme.'.png';
      
  //           if (trim($Code) == '')
  //              die('data cannot be empty!');
               
  //           // user data
  //           $filename = $PNG_TEMP_DIR.''.$File_NAme.'.png';
  //           QRcode::png($Code, $filename, $errorCorrectionLevel, $matrixPointSize, 2);    
         
  //     } else {    
      
  //           QRcode::png('PHP QR Code :)', $filename, $errorCorrectionLevel, $matrixPointSize, 2);        
  //     }
  //  }
                $withheld_amt = 0; $evat = 0; $extratotal= 0;
            foreach($extrafields_data as $extra){
              if($extra["fieldtype"] == "Retention"){
                $evat = $extra["fieldamount"] * ($extra["fieldvat"]/100);
                $extratotal = $extra["fieldamount"] + $evat;
                $withheld_amt = $withheld_amt + $extratotal;
$info_left_column1 .= '<tr>
                     <td colspan="2" class="invoice-table-column" style="border: 1px solid #000000;">Amount withheld as Retention <br> خصم الاستبقاء</td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.app_format_money($extra["fieldamount"], $invoice->currency_name).'</td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.$extra["fieldvat"].'%</td>
                     <td width="12%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.app_format_money($evat, $invoice->currency_name).'</td>
                     <th align="right" class="invoice-table-column" style="border: 1px solid #000000;">('.app_format_money($extratotal, $invoice->currency_name).')</th>
                  </tr>';
              }
            }
              $deductions = 0; $evat = 0; $extratotal = 0;
            foreach($extrafields_data as $extra){
              if($extra["fieldtype"] == "Deduction"){
                $evat = $extra["fieldamount"] * ($extra["fieldvat"]/100);
                $extratotal = $extra["fieldamount"] + $evat;
                $deductions = $deductions + $extratotal;
$info_left_column1 .= '<tr>
                     <td colspan="2" class="invoice-table-column" style="border: 1px solid #000000;">Other Deductions <br> استقطاعات أخرى</td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.app_format_money($extra["fieldamount"], $invoice->currency_name).'</td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.$extra["fieldvat"].'%</td>
                     <td width="12%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.app_format_money($evat, $invoice->currency_name).'</td>
                     <th align="right" class="invoice-table-column" style="border: 1px solid #000000;">('.app_format_money($extratotal, $invoice->currency_name).')</th>
                  </tr>';
              }
            }
              $net_amt_due = $rem_amt_total - $withheld_amt;
              $net_amt_due = $net_amt_due - $deductions;
$info_left_column1 .= '<tr>
                     <td colspan="5" class="invoice-table-column" style="border: 1px solid #000000;">Net Amount Due <br> المبلغ المتب رق المستحق</td>
                     <th align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.app_format_money($net_amt_due, $invoice->currency_name).'</b></th>
                  </tr>
                  <tr>
                     <td colspan="6" align="cetner" class="invoice-table-column" style="border: 1px solid #000000;">';
if (get_option('total_to_words_enabled') == 1) {
               $info_left_column1 .= 'With words : ' . ucfirst($CI->numberword->convert($net_amt_due, $invoice->currency_name));
            }
$info_left_column1 .= '</td>
                  </tr>
                  <tr>
                     <th colspan="2" class="invoice-table-column" style="border: 1px solid #000000;"><b>Huta Marine Works Ltd</b>';
                        if (!empty($invoice->clientnote)) {
                              $info_left_column1 .= '<br><b style="font-weight: bold;">Note:</b><br>'.$invoice->clientnote;
                        }
                        if (!empty($invoice->terms)) {
                              $info_left_column1 .= '<hr />
                              <b>Terms and Conditions:</b><br>'.$invoice->terms;
                        }
   $info_left_column1 .= '</th>
                     <th colspan="2" class="invoice-table-column" align="center"></th>
                     <th colspan="2" class="invoice-table-column" align="center" style="border: 1px solid #000000;"><img src="'.site_url($qr_code_img).'" width="100px"></th>
                  </tr>';

$info_left_column1 .= '</table></div>';
// Add logo
//$info_left_column .= pdf_logo_url();

$info_left_column2 = '
<div style="float:left; width:60%;">
    <div style="float:left; width:75%; text-align:center;">
        <table class="table" border="1" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <th>Invoice Number:</th>
                <td>'.format_invoice_number($invoice->id).'</td>
                <td>'.format_invoice_number($invoice->id).'</td>
                <th style="text-align:right;">رقم الفاتورة</th>
            </tr>
        </table>
        <table class="table" border="1" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <th>Invoice Issue Date:</th>
                <td>'.$invoice->date.'</td>
                <td>'.$invoice->date.'</td>
                <th style="text-align:right;">تاريخ إصدار الفاتورة</th>
            </tr>
            <tr>
                <th>Due Date:</th>
                <td>'.$invoice->duedate.'</td>
                <td>'.$invoice->duedate.'</td>
                <th style="text-align:right;">تاريخ الاستحقاق</th>
            </tr>
        </table>
    </div>
</div>';


// Write top left logo and right column info/text
pdf_multi_row($info_center_column,"", $pdf, ($dimensions['wk'] / 1.05) - $dimensions['lm']);
pdf_multi_row($info_left_column,"", $pdf, ($dimensions['wk'] / 1.05) - $dimensions['lm']);
pdf_multi_row($info_left_column1,"", $pdf, '');
//pdf_multi_row($info_left_column2,"", $pdf, ($dimensions['wk'] / 1.05) - $dimensions['lm']);


//pdf_multi_row($invoice_detail, "", $pdf, ($dimensions['wk'] ) - $dimensions['lm']);

?>

