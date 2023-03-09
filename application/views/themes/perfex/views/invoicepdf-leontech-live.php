
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

$CI =& get_instance();
$CI->load->model('estimates_model');

$sr = 1; $exc_vat_total = 0;
$totalvat = 0; $amt_exc_vat = 0; $totalsar = 0;
$items = get_items_table_data($invoice, 'invoice');
foreach ($invoice->items as $row) {
    $rate = $row["qty"] * $row["rate"];
    $exc_vat_total = $exc_vat_total + $rate;

    $estimate_item_tax = $CI->estimates_model->get_item_tax_value($row["id"],$row["rel_id"],'invoice');
    if($estimate_item_tax) { 
        //$info_left_column1 .= $estimate_item_tax->taxrate; 
        $tt = $estimate_item_tax->taxrate; 
  } else {
    //$info_left_column1 .= '0.00'; 
    $tt = 0;
  }
  $tax_calc = $rate * ($tt/100);
  $adding_tax = $rate + $tax_calc;
  $totalvat = $totalvat + $tax_calc;
  $totalsar = $totalsar + $adding_tax;
}

   function einv_generate_tlv_qr_code($array_tag=array()){
      $index=1;
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
  else{
      //set it to writable location, a place for temp generated PNG files
      //$PNG_TEMP_DIR = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets/images/' . DIRECTORY_SEPARATOR;
      $PNG_TEMP_DIR = 'assets/images/';
      
      //html PNG location prefix
      $PNG_WEB_DIR = 'assets/images/';
      
      //ofcourse we need rights to create temp dir
      if (!file_exists($PNG_TEMP_DIR))
            mkdir($PNG_TEMP_DIR);
      
      $filename = 'text';
      
      $errorCorrectionLevel = 'L';
         $matrixPointSize = 44;
      
      if (isset($Code)) { 
         
            $filename .= $File_NAme.'.png';
      
            if (trim($Code) == '')
               die('data cannot be empty!');
               
            // user data
            $filename = $PNG_TEMP_DIR.''.$File_NAme.'.png';
            QRcode::png($Code, $filename, $errorCorrectionLevel, $matrixPointSize, 2);    
         
      } else {    
      
            QRcode::png('PHP QR Code :)', $filename, $errorCorrectionLevel, $matrixPointSize, 2);        
      }
   }
// print_r($invoice);
//$CI->load->model('branches_model');
$CI->load->model('invoices_model');
//$branch_result= $CI->branches_model->get_brand_and_details($invoice_result->branchid);
// print_r($branch_result);
// echo $invoice_result->branchid;
// exit;
// echo "My official results here: ".$result;
$clients_data = $CI->clients_model->get($invoice->clientid);
$project_details = $CI->projects_model->get_project_details($invoice->project_id);
$project_exp = explode("/",$project_details->name);
//print_r($clients_data);
//exit;
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
//print_r($payment_modes_details);
//exit;
foreach($payment_modes_details as $mode){
  // echo $mode["description"]."<br />";
}
// exit;



$dimensions = $pdf->getPageDimensions();
$company_logo = get_option('company_logo');


$info_center_column = '<div>
                <table style="margin: 0px;">
                  <tr>
                     <td width="33%" style="padding-top: 10px;"><img src="'.base_url('uploads/company/'.$company_logo).'" style="height: 80px;"></td>
                     <td width="33%" align="center"><span style="font-weight:bold; font-size: 25px; width: 100px !important;">TAX INVOICE <br> فاتورة ضريبية</span></td>
                     <td width="33%" align="center"><span style="font-weight:bold; font-size: 25px; width: 100px !important;"><img src="'.site_url($qr_code_img).'" width="100px"></span></td>
                  </tr>
                </table>
              </div>';

$western_arabic = array('0','1','2','3','4','5','6','7','8','9');
$eastern_arabic = array('٠','١','٢','٣','٤','٥','٦','٧','٨','٩');

$company_exp = explode("/", get_option('invoice_company_name'));
$address_exp = explode("/", get_option('invoice_company_address'));
$company_vat = get_option('company_vat');
$company_vat_arabic = str_replace($western_arabic, $eastern_arabic, get_option('company_vat'));
$company_cr = get_option('invoice_company_phonenumber');
$company_cr_arabic = str_replace($western_arabic, $eastern_arabic, get_option('invoice_company_phonenumber'));

$client_company_exp = explode("/", $clients_data->company);

$client_address_exp1 = ""; $client_address_exp2 = ""; $wahid = 1;
//$caddress = preg_replace( "/\r|\n/", "", strpos($clients_data->address) );
$caddress = str_replace(array("\r","\n"),"",strpos($clients_data->address));
if(strpos($caddress, ' / ') !== false){
	//echo("Uttay");
	$client_address_exp = explode("/", $clients_data->address); 
	//print_r($client_address_exp);
	$client_address_exp1 = $client_address_exp[0]; 
	$client_address_exp2 = $client_address_exp[1]; 
} else { 
	//echo("Thalay");
	$claddress = explode(" / ", $clients_data->address);
	$client_address_exp1 = $claddress[0]; 
	$client_address_exp2 = $claddress[1]; 
}
$client_vat = $clients_data->vat;
$client_vat_arabic = str_replace($western_arabic, $eastern_arabic, $clients_data->vat);
$client_cr = $clients_data->phonenumber;
$client_cr_arabic = str_replace($western_arabic, $eastern_arabic, $clients_data->phonenumber);

$pocontract = ""; $pono = ""; $podate = ""; $poamount = ""; $inenglish = ""; $inarabic = "";
$previous_payment = "";
$pdf_custom_fields = get_custom_fields('invoice', array('show_on_pdf' => 1, 'show_on_client_portal' => 1));
                 foreach ($pdf_custom_fields as $field) {
                    $value = get_custom_field_value($invoice->id, $field['id'], 'invoice');
                    if ($value == '') {
                      continue;
                    }
                    if($field['name'] == "PO Contract Ref#"){
            $pocontract = $value;
          }
                    if($field['name'] == "PO Number"){
            $pono = $value;
          }
                    if($field['name'] == "PO Date"){
            $podate = $value;
          }
                    if($field['name'] == "PO / Contract Amount"){
            $poamount = $value;
          }
                    if($field['name'] == "Amount in English"){
            $inenglish = $value;
          }
                    if($field['name'] == "Amount in Arabic"){
            $inarabic = $value;
          }
                    if($field['name'] == "Previous Payment"){
            $previous_payment = $value;
          }
         }
$info_left_column = '<div style="float: left; width: 100%; padding: 30px;">
               <table style="border: 1px solid #000000; font-size: 12px;">
                  <tr>
                     <th width="15%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Invoice Date:</b></th>
                     <th width="19.5%" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.date('F d, Y', strtotime($invoice->date)).'</b></th>
                     <th width="15%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>تاريخ الفاتورة:</b></th>
                     <th width="15%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Invoice No:</b></th>
                     <td width="19.5%" class="invoice-table-column" style="border: 1px solid #000000;">'.format_invoice_number($invoice->id).'</td>
                     <th width="15%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>رقم الفاتورة:</b></th>
                  </tr>
                  <tr>
                     <th width="15%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Project Name:</b></th>
                     <th width="19.5%" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.$project_details->name.'</b></th>
                     <th width="15%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>اسم المشروع:</b></th>
                     <th width="15%" class="invoice-table-column" style="border: 1px solid #000000;"><b>P.O/Contract Ref:</b></th>
                     <td width="19.5%" class="invoice-table-column" style="border: 1px solid #000000;">'.$pocontract.'</td>
                     <th width="15%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>مرجع أمر الشراء/ العقد:</b></th>
                  </tr>
                  <tr>
                     <th width="15%" class="invoice-table-column" style="border: 1px solid #000000;"><b>P.O. No /   الشراء أمر رقم :</b></th>
                     <th width="12.5%" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.$pono.'</b></th>
                     <th width="10%" class="invoice-table-column" style="border: 1px solid #000000;"><b>P.O Date / تاريخ أمر الشراء</b></th>
                     <th width="12%" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.date('d/M/Y', strtotime($podate)).'</b></th>
                     <th width="15%" class="invoice-table-column" style="border: 1px solid #000000;"><b>P.O / Contract Amount:</b></th>
                     <td width="19.5%" align="right" class="invoice-table-column" style="border: 1px solid #000000;">'.$poamount.'</td>
                     <th width="15%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>قيمة أمر الشراء / العقد:</b></th>
                  </tr>
                </table>
                <table style="border-bottom-color: #ffffff;">
                  <tr>
                    <td width="49.5%" colspan="2" style="border-right-color: #ffffff;"></td>
                    <td width="49.5%" colspan="2" style="border-left-color: #ffffff;"></td>
                  </tr>
                  <tr>
                     <th width="13%" class="invoice-table-column" style="border: 1px solid #000000;"><b>From:</b></th>
                     <td width="36.5%" align="left" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.$company_exp[0].'</b></td>
                     <th width="36.5%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.$company_exp[1].'</b></th>
                     <th width="13%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>من :</b></th>
                  </tr>
                  <tr>
                     <th width="13%" class="invoice-table-column" style="border: 1px solid #000000;">Address:</th>
                     <td width="36.5%" align="left" class="invoice-table-column" style="border: 1px solid #000000;">'.$address_exp[0].'</td>
                     <th width="36.5%" align="right" class="invoice-table-column" style="border: 1px solid #000000;">'.$address_exp[1].'</th>
                     <th width="13%" align="right" class="invoice-table-column" style="border: 1px solid #000000;">العنوان :</th>
                  </tr>
                  <tr>
                     <th width="13%" class="invoice-table-column" style="border: 1px solid #000000;">CR#:</th>
                     <td width="36.5%" align="left" class="invoice-table-column" style="border: 1px solid #000000;">'.$company_cr.'</td>
                     <th width="36.5%" align="right" class="invoice-table-column" style="border: 1px solid #000000;">'.$company_cr_arabic.'</th>
                     <th width="13%" align="right" class="invoice-table-column" style="border: 1px solid #000000;">رقم السجل التجاري:</th>
                  </tr>
                  <tr>
                     <th width="13%" class="invoice-table-column" style="border: 1px solid #000000;">VAT Number:</th>
                     <td width="36.5%" align="left" class="invoice-table-column" style="border: 1px solid #000000;">'.$company_vat.'</td>
                     <th width="36.5%" align="right" class="invoice-table-column" style="border: 1px solid #000000;">'.$company_vat_arabic.'</th>
                     <th width="13%" align="right" class="invoice-table-column" style="border: 1px solid #000000;">الرقم الضريبي  :</th>
                  </tr>
                  <tr>
                    <td width="49.5%" colspan="2" style="border-right-color: #ffffff;"></td>
                    <td width="49.5%" colspan="2" style="border-left-color: #ffffff;"></td>
                  </tr>
                  <tr>
                     <th width="13%" class="invoice-table-column" style="border: 1px solid #000000;"><b>To:</b></th>
                     <td width="36.5%" align="left" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.$client_company_exp[0].'</b></td>
                     <th width="36.5%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.$client_company_exp[1].'</b></th>
                     <th width="13%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>إلى :</b></th>
                  </tr>
                  <tr>
                     <th width="13%" class="invoice-table-column" style="border: 1px solid #000000;">Address:</th>
                     <td width="36.5%" align="left" class="invoice-table-column" style="border: 1px solid #000000;">'.$client_address_exp1.'</td>
                     <th width="36.5%" align="right" class="invoice-table-column" style="border: 1px solid #000000;">'.$client_address_exp2.'</th>
                     <th width="13%" align="right" class="invoice-table-column" style="border: 1px solid #000000;">العنوان :</th>
                  </tr>
                  <tr>
                     <th width="13%" class="invoice-table-column" style="border: 1px solid #000000;">CR#:</th>
                     <td width="36.5%" align="left" class="invoice-table-column" style="border: 1px solid #000000;">'.$client_cr.'</td>
                     <th width="36.5%" align="right" class="invoice-table-column" style="border: 1px solid #000000;">'.$client_cr_arabic.'</th>
                     <th width="13%" align="right" class="invoice-table-column" style="border: 1px solid #000000;">رقم السجل التجاري:</th>
                  </tr>
                  <tr>
                     <th width="13%" class="invoice-table-column" style="border: 1px solid #000000;">VAT Number:</th>
                     <td width="36.5%" align="left" class="invoice-table-column" style="border: 1px solid #000000;">'.$client_vat.'</td>
                     <th width="36.5%" align="right" class="invoice-table-column" style="border: 1px solid #000000;">'.$client_vat_arabic.'</th>
                     <th width="13%" align="right" class="invoice-table-column" style="border: 1px solid #000000;">الرقم الضريبي  :</th>
                  </tr>
               </table>
            </div>';

$info_left_column1 .= '<div style="float: left; width: 100%; padding: 30px;">
                <table width="99%" class="table table-bordered invoice-detail-table" cellpadding="1px" style="font-size: 12px;">
                  <tr style="background-color: #bfbfbf; color: #000000">
                    <td width="5%" align="center" style="border: 1px solid #000000;"><b>S/N</b></td>
                    <td width="29%" align="center" style="border: 1px solid #000000;"><b>Description</b></td>
                    <td width="6%" align="center" style="border: 1px solid #000000;"><b>Qty.</b></td>
                    <td width="7%" align="center" style="border: 1px solid #000000;"><b>Unit</b></td>
                    <td width="8%" align="center" style="border: 1px solid #000000;"><b>Unit Price</b></td>
                    <td width="15%" align="center" style="border: 1px solid #000000;"><b>Amount Excluded VAT</b></td>
                    <td width="15%" align="center" style="border: 1px solid #000000;"><b>Percentage of Item</b></td>
                    <td width="15%" align="center" style="border: 1px solid #000000;"><b>Total Amount after Percentage Applied (SAR)</b></td>
                  </tr>
                  <tr style="background-color: #bfbfbf; color: #000000">
                    <td align="center" style="border: 1px solid #000000;"><b>م </b></td>
                    <td align="center" style="border: 1px solid #000000;"><b>وصف</b></td>
                    <td align="center" style="border: 1px solid #000000;"><b>الكمية</b></td>
                    <td align="center" style="border: 1px solid #000000;"><b>وحدة</b></td>
                    <td align="center" style="border: 1px solid #000000;"><b>سعر الصنف</b></td>
                    <td align="center" style="border: 1px solid #000000;"><b>السعر غير شامل ضريبة القيمة المضافة</b></td>
                    <td align="center" style="border: 1px solid #000000;"><b>النسبة المئوية للمادة</b></td>
                    <td align="center" style="border: 1px solid #000000;"><b>المبلغ الإجمالي بعد تطبيق النسبة المئوية (ريال)</b></td>
                  </tr>';
               $sr = 1; $exc_vat_total = 0;
               $totalvat = 0; $amt_exc_vat = 0; $totalsar = 0;
               $items = get_items_table_data($invoice, 'invoice');
               foreach ($invoice->items as $row) {
                  $rate = $row["qty"] * $row["rate"];
                  //$exc_vat_total = $exc_vat_total + $rate;

                  $estimate_item_tax = $CI->estimates_model->get_item_tax_value($row["id"],$row["rel_id"],'invoice');
         
				   $ipercentage = "";
				   $pdf_custom_fields = get_custom_fields('items', array('show_on_pdf' => 1));
				   foreach ($pdf_custom_fields as $field) {
					   $value = get_custom_field_value($row["id"], $field['id'], 'items');
					   if ($value == '') {
						   continue;
					   }
					   if($field['name'] == "Percentage")
					   {
						   $ipercentage = $value;
					   }
				   }
				   
                  $info_left_column1 .= '<tr>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000; border-right-color: #000000; border-left-color: #000000;">'.$sr.'</td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000; border-right-color: #000000; border-left-color: #000000;">'.$row["description"].'<br />'.$row["long_description"].'<br></td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000; border-right-color: #000000; border-left-color: #000000;">'.$row["qty"].'</td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000; border-right-color: #000000; border-left-color: #000000;">'.$row["unit"].'</td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000; border-right-color: #000000; border-left-color: #000000;">'.app_format_money($row["rate"], $invoice->currency_name).'</td>';
                     if($estimate_item_tax) { 
                      //$info_left_column1 .= $estimate_item_tax->taxrate; 
                      $tt = $estimate_item_tax->taxrate; } else { 
             //$info_left_column1 .= '0.00'; 
           }
				     $per_rate = $rate * ($ipercentage/100);
                     /*$tax_calc = $rate * ($tt/100);
                     $adding_tax = $rate + $tax_calc;
                     $totalvat = $totalvat + $tax_calc;
                     $totalsar = $totalsar + $adding_tax;*/
				     $exc_vat_total = $exc_vat_total + $per_rate;
                     $tax_calc = $per_rate * ($tt/100);
                     $adding_tax = $per_rate + $tax_calc;
                     $totalvat = $totalvat + $tax_calc;
                     $totalsar = $totalsar + $adding_tax;
                  $info_left_column1 .= '<td align="center" class="invoice-table-column" style="border: 1px solid #000000; border-right-color: #000000; border-left-color: #000000;">'.app_format_money($rate, $invoice->currency_name).'</td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000; border-right-color: #000000; border-left-color: #000000;">'.$ipercentage.'%<br /></td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000; border-right-color: #000000; border-left-color: #000000;">'.app_format_money($per_rate, $invoice->currency_name).'<br /></td>
                  </tr>';
            
                     $sr++;
                  }

$info_left_column1 .= '';

                  $info_left_column1 .= '';

$info_left_column1 .= '';

$prev_paid_amount = 0;
$remaining_amount = $totalsar - $prev_paid_amount;
$fifty_perc_payable = $exc_vat_total / 2;

$info_left_column1 .= '
                  <tr>
                     <td colspan="7" align="right" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;"><b>Total Exclusive VAT / المضافة الضريبة بدون المجموع</b></td>
                     <th align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.app_format_money($exc_vat_total, $invoice->currency_name).'</b></th>
                  </tr>';

if($invoice->extra_deduction_amount_type == "before_tax"){
	$exc_vat_total = $exc_vat_total - $invoice->extra_deduction_amount;
	$totalvat = $exc_vat_total * (15/100);
	$totalsar = $exc_vat_total + $totalvat;
	$remaining_amount = $totalsar - $prev_paid_amount;
	
$info_left_column1 .= '<tr>
                     <td colspan="7" align="right" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;"><b>Deduction of Performance Security</b></td>
                     <th align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.app_format_money($invoice->extra_deduction_amount, $invoice->currency_name).'</b></th>
                  </tr>';
}

if($invoice->extra_deduction_amount_type == "after_tax"){
	$totalsar = $totalsar - $invoice->extra_deduction_amount;
	$remaining_amount = $totalsar - $prev_paid_amount;
	
$info_left_column1 .= '<tr>
                     <td colspan="7" align="right" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;"><b>Deduction of Performance Security</b></td>
                     <th align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.app_format_money($invoice->extra_deduction_amount, $invoice->currency_name).'</b></th>
                  </tr>';
}

$info_left_column1 .= '
                  <tr>
                     <td colspan="7" align="right" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;"><b>Total After Deduction Exclusive VAT / الإجمالي بعد الخصم ضريبة القيمة المضافة</b></td>
                     <th align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.app_format_money($exc_vat_total, $invoice->currency_name).'</b></th>
                  </tr>';

$info_left_column1 .= '<tr>
                     <td colspan="7" align="right" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;"><b>VAT 15% / لمضافة الضريبة قيمة</b></td>
                     <th align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.app_format_money($totalvat, $invoice->currency_name).'</b></th>
                  </tr>';
$info_left_column1 .= '<tr>
                     <td colspan="7" align="right" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;"><b>Total Including VAT 15% / المضافة الضريبة شامل المجموع</b></td>
                     <th align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.app_format_money($totalsar, $invoice->currency_name).'</b></th>
                  </tr>';
	
$info_left_column1 .= '<tr>
                     <td colspan="7" align="right" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;"><b>Previous Paid Amount / السابقة المدفوعات قيمة</b></td>
                     <th align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.$previous_payment.'</b></th>
                  </tr>
                  <tr>
                     <td colspan="7" align="right" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;"><b>Remaining Amount / المتبقية القيمة</b></td>
                     <th align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.app_format_money($remaining_amount, $invoice->currency_name).'</b></th>
                  </tr>
                  <tr>
                     <td colspan="7" align="right" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;"><b>Payable Amount / الدفع المستحقة القيمة</b></td>
                     <th align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.app_format_money($remaining_amount, $invoice->currency_name).'</b></th>
                  </tr>
                  <tr>
                    <td colspan="8" style="border-right-color: #ffffff; border-left-color: #ffffff;"></td>
                  </tr>
                  <tr style="background-color: #E8E8E8;">
                     <td colspan="2" align="center" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;"><b>Amount In Words</b></td>
                     <th colspan="6" align="left" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.$inenglish.'</b></th>
                  </tr>
                  <tr style="background-color: #E8E8E8;">
                     <td colspan="2" align="center" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;"><b>القيمة كتابة</b></td>
                     <th colspan="6" align="left" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.$inarabic.'</b></th>
                  </tr>
                  <tr>
                    <td colspan="8" style="border-right-color: #ffffff; border-left-color: #ffffff;"></td>
                  </tr>
				  <tr>
                     <td colspan="8" style="border: 1px solid #000000;">';
                        if (!empty($payment_modes_details)) {
							foreach($payment_modes_details as $mode){
                              $info_left_column1 .= '<b>Bank Details:</b><br>'.$mode["name"].'<br>'.$mode["description"];
							}
                        }
   $info_left_column1 .= '</td>
                  </tr>
              </table>

                <table width="99%" class="table table-bordered invoice-detail-table" cellpadding="5px" style="font-size: 12px; display: none;">
                  <tr>
                     <td width="33.33%" align="center" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;"><b><u>Submitted by</u></b></td>
                     <td width="33.33%" align="center" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;"><b><u>Checked by</u></b></td>
                     <td width="33.33%" align="center" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;"><b><u>Approved by</u></b></td>
                  </tr>
                  <tr>
                     <td width="10%" align="left" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;">Name:</td>
                     <td width="23.33%" align="left" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;"></td>
                     <td width="10%" align="left" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;">Name:</td>
                     <td width="23.33%" align="left" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;"></td>
                     <td width="10%" align="left" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;">Name:</td>
                     <td width="23.33%" align="left" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;"></td>
                  </tr>
                  <tr>
                     <td width="10%" align="left" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;">Co Name:</td>
                     <td width="23.33%" align="left" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;"></td>
                     <td width="10%" align="left" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;">Co Name:</td>
                     <td width="23.33%" align="left" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;"></td>
                     <td width="10%" align="left" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;">Co Name:</td>
                     <td width="23.33%" align="left" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;"></td>
                  </tr>
                </table>
                <table width="99%" class="table table-bordered invoice-detail-table" style="font-size: 12px; padding: 5px; padding-top: 25px; padding-bottom: 25px; display: none;">
                  <tr>
                     <td width="10%" align="left" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;">Signature:</td>
                     <td width="23.33%" align="left" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;"></td>
                     <td width="10%" align="left" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;">Signature:</td>
                     <td width="23.33%" align="left" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;"></td>
                     <td width="10%" align="left" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;">Signature:</td>
                     <td width="23.33%" align="left" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;"></td>
                  </tr>
                  <!-- <tr>
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
                  </tr> -->';

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

