
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

// print_r($invoice);
//$CI->load->model('branches_model');
$CI =& get_instance();
$CI->load->model('estimates_model');
$CI->load->model('estimates_model');
$CI->load->model('invoices_model');
//$branch_result= $CI->branches_model->get_brand_and_details($invoice_result->branchid);
// print_r($branch_result);
// echo $invoice_result->branchid;
// exit;
// echo "My official results here: ".$result;
$clients_data = $CI->clients_model->get($invoice->clientid);
$project_details = $CI->projects_model->get_project_details($invoice->project_id);
$project_exp = explode("/",$project_details->name);
//print_r($project_details);
//echo "NO".$invoice->project_id;
//exit;

// echo $invoice->allowed_payment_modes;
$modes_exp = explode("\"", $invoice->allowed_payment_modes);
// print_r($modes_exp);
$m = 1;
$modes = "";
foreach ($modes_exp as $key => $value) {
  if($m%2 == 0){
    $modes .= $value.",";
  }
  $m++;
}
// echo "<br>".$modes;

$payment_modes_details = $CI->invoices_model->get_invoice_payment_modes($modes);
// print_r($payment_modes_details);
foreach($payment_modes_details as $mode){
  // echo $mode["description"]."<br />";
}
// exit;



$dimensions = $pdf->getPageDimensions();


$info_center_column = '<div style="width:200px !important; margin-left: 20px; text-align:center; border: 2px solid #000000; background-color: #bfbfbf; color: #000000">
<div style="font-weight:bold; font-size: 27px; width: 100px !important;">TAX INVOICE</div>
</div>';

$info_left_column = '<div style="float: left; width: 100%; padding: 30px;">
               <table style="border: 1px solid #000000;">
                  <tr>
                     <th width="33%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Invoice No:</b></th>
                     <td width="33%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.format_invoice_number($invoice->id).'</td>
                     <th width="33%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>رقم الفاتورة :</b></th>
                  </tr>
                  <tr>
                     <th width="33%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Date of Invoice:</b></th>
                     <td width="33%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.date('d/m/Y', strtotime($invoice->date)).'</td>
                     <th width="33%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>تاريخ الفاتورة :</b></th>
                  </tr>
                  <tr>
                     <th width="33%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Huta Marine Works Ltd. VAT No.</b></th>
                     <td width="33%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.get_option('company_vat').'</td>
                     <th width="33%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>رقم الضريبي لشركة هوتا للاعمال البحريه المحدودة:</b></th>
                  </tr>
               </table>
            </div>';
$company_exp = explode("/", get_option('invoice_company_name'));
$info_left_column1 = '<div style="float: left; width: 100%; padding: 30px;">
               <table cellpadding="5px">
                  <tr>
                     <th width="99%" align="center" class="invoice-table-column" style="border: 2px solid #000000;"><b>Customer Details / تفاصيل العميل</b></th>
                  </tr>
                  <tr>
                     <th width="20%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Company Name:</b></th>
                     <td width="59%" align="center" class="invoice-table-column" style="border: 1px solid #000000; font-size: 20px;"><b>'.$company_exp[0].'<br>'.$company_exp[1].'</b></td>
                     <th width="20%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>اسم الزبون : </b></th>
                  </tr>
                  <tr>
                     <th width="20%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Street Address:</b></th>
                     <td width="59%" align="center" class="invoice-table-column" style="border: 1px solid #ffffff; border-right-color: #000000;">'.$clients_data->address.'</td>
                     <th width="20%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>عنوان العميل :</b></th>
                  </tr>';
$pdf_custom_fields = get_custom_fields('invoice', array('show_on_pdf' => 1, 'show_on_client_portal' => 1));
                 foreach ($pdf_custom_fields as $field) {
                    $value = get_custom_field_value($invoice->id, $field['id'], 'invoice');
                    if ($value == '') {
                      continue;
                    }
                    if($field['name'] == "PO / Contract Amount"){
                    $exp = explode("/",$field['name']);
$info_left_column1 .= '<tr>
                     <th width="20%" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.$exp[0].':</b></th>
                     <td width="59%" align="center" class="invoice-table-column" style="border: 1px solid #ffffff; border-right-color: #000000;">'.$value.'</td>
                     <th width="20%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.$exp[1].':</b></th>
                  </tr>';
                    }
                  }
$info_left_column1 .= '
                  <tr>
                     <th width="20%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Client VAT #:</b></th>
                     <td width="59%" align="center" class="invoice-table-column" style="border: 1px solid #ffffff; border-right-color: #000000; border-bottom-color: #000000;"><b>'.$clients_data->vat.'</b></td>
                     <th width="20%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>رقم الضريبي :</b></th>
                  </tr>
                  </table>';

$info_left_column1 .= '
               <table class="table table-bordered invoice-detail-table" cellpadding="3px">
                  <tr>
                    <td colspan="6"></td>
                  </tr>
                  <tr style="background-color: #bfbfbf; color: #000000">
                     <th width="12%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>S. No. <br> الرقم التسلسلي</b></th>
                     <th width="34%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>Description <br> الصنف</b></th>
                     <th width="19%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>Amount Exclusive of Vat <br> السعر الاجمالي 
غير شامل ضريبة</b></th>
                     <th width="15%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>VAT Amount (SAR) <br> الضريبة القيمة المضافة
 ريال سعودي</b></th>
                     <th width="19%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>Total (SAR) <br> الاجمالي ريال سعودي</b></th>
                  </tr>
                  <tr style="color: #000000">
                     <th width="12%" align="center" class="invoice-table-column" style="border: 1px solid #ffffff; border-right-color: #000000; border-left-color: #000000;"></th>
                     <th width="34%" align="left" class="invoice-table-column" style="border: 1px solid #ffffff; border-right-color: #000000; border-left-color: #000000;">We debit your account for the Plant & Equipment and Public Liability Insurance Premium for 6nos Crawler Cranes for the period from 01-Jun-2021 to 31-May-2022. <br /><br /></th>
                     <th width="19%" align="center" class="invoice-table-column" style="border: 1px solid #ffffff; border-right-color: #000000; border-left-color: #000000;"></th>
                     <th width="15%" align="center" class="invoice-table-column" style="border: 1px solid #ffffff; border-right-color: #000000; border-left-color: #000000;"></th>
                     <th width="19%" align="center" class="invoice-table-column" style="border: 1px solid #ffffff; border-right-color: #000000; border-left-color: #000000;"></th>
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
                     <td align="center" class="invoice-table-column" style="border: 1px solid #ffffff; border-right-color: #000000; border-left-color: #000000;">'.$sr.'</td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #ffffff; border-right-color: #000000; border-left-color: #000000;">'.$row["description"].'<br />'.$row["long_description"].'<br></td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #ffffff; border-right-color: #000000; border-left-color: #000000;">'.app_format_money($row["rate"], $invoice->currency_name).'</td>';
                     if($estimate_item_tax) { 
                      //$info_left_column1 .= $estimate_item_tax->taxrate; 
                      $tt = $estimate_item_tax->taxrate; } else { $info_left_column1 .= '0.00'; }
                     $tax_calc = $row["rate"] * ($tt/100);
                     $adding_tax = $row["rate"] + $tax_calc;
                     $totalvat = $totalvat + $tax_calc;
                     $totalsar = $totalsar + $adding_tax;
                  $info_left_column1 .= '<td align="center" class="invoice-table-column" style="border: 1px solid #ffffff; border-right-color: #000000; border-left-color: #000000;">'.app_format_money($invoice->total_tax, $invoice->currency_name).'</td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #ffffff; border-right-color: #000000; border-left-color: #000000;">'.app_format_money($adding_tax, $invoice->currency_name).'<br /></td>
                  </tr>';
            
                     $sr++;
                  }

$info_left_column1 .= '';

                  $info_left_column1 .= '';

$info_left_column1 .= '';

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

  $Code=einv_generate_tlv_qr_code(array(get_option('invoice_company_name'),get_option('company_vat'),date("Y-m-d H:i:s a",strtotime($invoice->datecreated)),$totalsar,$totalvat));
   if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') 
        $link = "https://"; 
    else
        $link = "http://"; 
    
   $link .= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; 
   
  //$text = "Seller:".get_option('invoice_company_name')."\nVAT Number: ".get_option('company_vat')."\nDate: ".date("Y-m-d H:i:sa",strtotime($invoice->date))."\nAmount: ".app_format_money($invoice->total, $invoice->currency_name)."\nTax : ".app_format_money($tax['total_tax'], $invoice->currency_name);
  //$Code = base64_encode($text); 
  $File_NAme = $invoice->hash;

  $file =  "assets/images/".$invoice->hash.".png";
  unlink($file);
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
$info_left_column1 .= '
                  <tr>
                     <td colspan="4" align="center" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;">';
if (get_option('total_to_words_enabled') == 1) {
               $info_left_column1 .= '(Saudi Riyals: : ' . ucfirst($CI->numberword->convert($totalsar, $invoice->currency_name)) . ')';
            }
$info_left_column1 .= '</td>
                     <th align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.app_format_money($totalsar, $invoice->currency_name).'</b></th>
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
                     <th colspan="1" class="invoice-table-column" align="center" style="border: 1px solid #000000;"></th>
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

