
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


$info_center_column = '<div style="width:200px !important; margin-left: 20px; text-align:center; border: 2px solid #000000; background-color: #bfbfbf; color: #000000">
<div style="font-weight:bold; font-size: 27px; width: 100px !important;">CREDIT NOTE</div>
</div>';

$info_left_column = '<div style="float: left; width: 100%; padding: 30px;">
               <table style="border: 1px solid #000000;">
                  <tr>
                     <th width="33%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Debit Note:</b></th>
                     <td width="33%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.$credit_note_number.'</td>
                     <th width="33%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>رقم الفاتورة :</b></th>
                  </tr>
                  <tr>
                     <th width="33%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Date:</b></th>
                     <td width="33%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'._d($credit_note->date).'</td>
                     <th width="33%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>تاريخ الفاتورة :</b></th>
                  </tr>
                  <tr>
                     <th width="33%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Huta Marine VAT No.</b></th>
                     <td width="33%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.get_option('company_vat').'</td>
                     <th width="33%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>رقم الضريبي لشركة هوتا للاعمال البحريه المحدودة</b></th>
                  </tr>
               </table>
            </div>';
$company_exp = explode("/", $clients_data->company);
$info_left_column1 = '<div style="float: left; width: 100%; padding: 30px;">
               <table cellpadding="5px">
                  <tr>
                     <th width="99%" align="center" class="invoice-table-column" style="border: 2px solid #000000;"><b>Customer Details / تفاصيل العميل</b></th>
                  </tr>
                  <tr>
                     <th width="20%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Company Name:</b></th>
                     <td width="59%" align="center" class="invoice-table-column" style="border: 1px solid #000000; font-size: 18px;"><b>'.$clients_data->company.'</b></td>
                     <th width="20%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>اسم الزبون : </b></th>
                  </tr>
                  <tr>
                     <th width="20%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Street Address:</b></th>
                     <td width="59%" align="center" class="invoice-table-column" style="border: 1px solid #ffffff; border-right-color: #000000;">'.$clients_data->address.'</td>
                     <th width="20%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>عنوان العميل :</b></th>
                  </tr>';
$pdf_custom_fields = get_custom_fields('credit_note', array('show_on_pdf' => 1, 'show_on_client_portal' => 1));
                 foreach ($pdf_custom_fields as $field) {
                    $value = get_custom_field_value($credit_note->id, $field['id'], 'credit_note');
                    if ($value == '') {
                      continue;
                    }
                    if($field['name'] == "ATT / ملاحظة"){
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
                     <th width="20%" class="invoice-table-column" style="border: 1px solid #000000;"><b>VAT Number:</b></th>
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
                     <th width="34%" align="left" class="invoice-table-column" style="border: 1px solid #ffffff; border-right-color: #000000; border-left-color: #000000;"><br />We debit your account for the Plant & Equipment and Public Liability Insurance Premium for 6nos Crawler Cranes for the period from 01-Jun-2021 to 31-May-2022. <br /><br /></th>
                     <th width="19%" align="center" class="invoice-table-column" style="border: 1px solid #ffffff; border-right-color: #000000; border-left-color: #000000;"></th>
                     <th width="15%" align="center" class="invoice-table-column" style="border: 1px solid #ffffff; border-right-color: #000000; border-left-color: #000000;"></th>
                     <th width="19%" align="center" class="invoice-table-column" style="border: 1px solid #ffffff; border-right-color: #000000; border-left-color: #000000;"></th>
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

  $Code=einv_generate_tlv_qr_code(array(get_option('invoice_company_name'),get_option('company_vat'),date("Y-m-d H:i:s a",strtotime($credit_note->datecreated)),$totalsar,$totalvat));
   if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') 
        $link = "https://"; 
    else
        $link = "http://"; 
    
   $link .= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; 
   
  //$text = "Seller:".get_option('invoice_company_name')."\nVAT Number: ".get_option('company_vat')."\nDate: ".date("Y-m-d H:i:sa",strtotime($invoice->date))."\nAmount: ".app_format_money($invoice->total, $invoice->currency_name)."\nTax : ".app_format_money($tax['total_tax'], $invoice->currency_name);
  //$Code = base64_encode($text); 
  $File_NAme = $credit_note_number;

  $file =  "assets/images/".$credit_note_number.".png";
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
$info_left_column1 .= '
                  <tr>
                     <td colspan="4" align="center" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;">';
if (get_option('total_to_words_enabled') == 1) {
               $info_left_column1 .= '(Saudi Riyals: : ' . ucfirst($CI->numberword->convert($totalsar, $credit_note->currency_name)) . ')';
            }
$info_left_column1 .= '</td>
                     <th align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.app_format_money($totalsar, $credit_note->currency_name).'</b></th>
                  </tr>
                  <tr>
                     <th colspan="2" class="invoice-table-column" style="border: 1px solid #000000;"><b>For Huta Marine Works Ltd</b>';
                        if (!empty($credit_note->clientnote)) {
                              $info_left_column1 .= '<br><b style="font-weight: bold;">Note:</b><br>'.$credit_note->clientnote;
                        }
                        if (!empty($credit_note->terms)) {
                              $info_left_column1 .= '<hr />
                              <b>Terms and Conditions:</b><br>'.$credit_note->terms;
                        }
   $info_left_column1 .= '<br><br><b>SALIM MOURAD <br>Chief Financial Officer, Huta Group</b></th>
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
</div>';


// Write top left logo and right column info/text
pdf_multi_row($info_center_column,"", $pdf, ($dimensions['wk'] / 1.05) - $dimensions['lm']);
pdf_multi_row($info_left_column,"", $pdf, ($dimensions['wk'] / 1.05) - $dimensions['lm']);
pdf_multi_row($info_left_column1,"", $pdf, '');
//pdf_multi_row($info_left_column2,"", $pdf, ($dimensions['wk'] / 1.05) - $dimensions['lm']);


//pdf_multi_row($invoice_detail, "", $pdf, ($dimensions['wk'] ) - $dimensions['lm']);

?>

