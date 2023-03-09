<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(APPPATH . 'libraries/phpqrcode/qrlib.php');

// print_r($invoice_result);
// echo $invoice_result->branchid;
// exit;
$CI =& get_instance();
$CI->load->model('invoices_model');
$invoice_result= $CI->invoices_model->get($invoice->id);
//$CI->load->model('branches_model');
$CI->load->model('clients_model');
//$branch_result= $CI->branches_model->get_brand_and_details($invoice_result->branchid);
// print_r($branch_result);
// echo $invoice_result->branchid;
// exit;
// echo "My official results here: ".$result;


$clients_data = $CI->clients_model->get($invoice->clientid);
// print_r($clients_data);
// exit;
$dimensions = $pdf->getPageDimensions();
$company_logo = get_option('company_logo');

$info_center_column = '<div>
               <table style="margin: 0px;">
                  <tr>
                     <td width="33%" style="padding-top: 10px;"><br><img src="'.base_url('uploads/company/'.$company_logo).'" class="img img-responsive" style="height: 100px;"></td>
                     <td width="33%" style="padding: 0px; text-align: center; vertical-align: middle;"><br><h4 class="bold invoice-html-number">TAX INVOICE <br>فاتورة ضريبية </h4></td>
                     <td width="33%" style="padding: 0px; text-align: right; vertical-align: middle; font-size: 15px;" align="right">
                        <p><b>Invoice No:</b> '.format_invoice_number($invoice->id);
	
$pdf_custom_fields = get_custom_fields('invoice', array('show_on_pdf' => 1, 'show_on_client_portal' => 1));
               	 foreach ($pdf_custom_fields as $field) {
                  	$value = get_custom_field_value($invoice->id, $field['id'], 'invoice');
                  	if ($value == '') {
                     	continue;
                  	}
					 if($field['name'] == 'SADC Ref#')
					 {
$info_center_column .= '<br><b>SADC REF No: </b>'.$value;
					 }
				 }
$info_center_column .= '<br><b>Invoice Date:</b>'.date('d/m/Y', strtotime($invoice->date)).'</p>
                     </td>
                  </tr>
               </table>
</div>';
$comp_name = get_option('invoice_company_name');
	$comp_add = get_option('invoice_company_address');

	$company_name = explode("/",$comp_name);
	$company_address = explode("/",$comp_add);

   $client_name = $estimate->client->company;
	$client_add = $estimate->client->address;
	$customer_name = explode("/",$client_name);
$info_left_column = '<div style="float: left; width: 100%; padding: 30px;">
               <table style="border: 1px solid #000000;">
                  <tr>
                     <th width="33%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Seller Name:</b></th>
                     <td width="33%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.$company_name[0].'<br>'.$company_name[1].'</td>
                     <th width="33%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>البائع اسم:</b></th>
                  </tr>
                  
                  <tr>
                     <th width="33%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Seller Address:</b></th>
                     <td width="33%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.$company_address[0].'<br>'.$company_address[1].'</td>
                     <th width="33%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>عنوان البائع:</b></th>
                  </tr>
                  <tr>
                     <th width="33%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Seller VAT No.</b></th>
                     <td width="33%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.get_option('company_vat').'</td>
                     <th width="33%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>رقم ضريبة</b></th>
                  </tr>
               </table>
            </div>';

$info_left_column1 = '<div style="float: left; width: 100%; padding: 30px;">
               <table cellpadding="5px">
                  <tr>
                     <th width="99%" align="center" class="invoice-table-column" style="border: 2px solid #000000;"><b>Customer Details /  العميل بيانات </b></th>
                  </tr>
                  <tr>
                     <th width="20%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Client Name:</b></th>
                     <td width="59%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.$invoice->client->company.'</b></td>
                     <th width="20%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>اسم العميل : </b></th>
                  </tr>
                  <tr>
                     <th width="20%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Client VAT #:</b></th>
                     <td width="59%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.$clients_data->vat.'</b></td>
                     <th width="20%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>رقم ضريبة:</b></th>
                  </tr>
                  <tr>
                     <th width="20%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Client Address:</b></th>
                     <td width="59%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.$clients_data->address.'</td>
                     <th width="20%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>عنوان العميل :</b></th>
                  </tr>';

$pdf_custom_fields = get_custom_fields('invoice', array('show_on_pdf' => 1, 'show_on_client_portal' => 1));
               	 foreach ($pdf_custom_fields as $field) {
                  	$value = get_custom_field_value($invoice->id, $field['id'], 'invoice');
                  	if ($value == '') {
                     	continue;
                  	}
					 if($field['name'] == 'Client Ref#')
					 {
						 
$info_left_column1 .= '<tr>
                     <th width="20%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Client Ref#:</b></th>
                     <td width="59%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.$value.'</td>
                     <th width="20%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>معرف مرجع العميل :</b></th>
                  </tr>';
					 }
					 if($field['name'] == 'Delivery Date')
					 {
						 
$info_left_column1 .= '<tr>
                     <th width="20%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Delivery Date:</b></th>
                     <td width="59%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.date('d/m/Y', strtotime($value)).'</td>
                     <th width="20%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>تاريخ التوريد :</b></th>
                  </tr>';
					 }
				 }

$info_left_column1 .= '</table>';

$info_left_column1 .= '
               <table class="table table-bordered invoice-detail-table" cellpadding="5px">
                  <tr>
                     <td width="99%" style="border-bottom: 1px solid #000000;"></td>
                  </tr>
                  <tr style="background-color: #bfbfbf; color: #000000">
                     <th width="6%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>SL. No. <br> مسلسل</b></th>
                     <th width="21%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>Service Description <br> وصف الخدمة</b></th>
                     <th width="12%" align="center" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>QTY <br> كمية</b></th>
                     <th width="12%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>Unit Rate <br> سعر الوحدة</b><br>'.$invoice->currency_name.'</th>
                     <th width="12%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>Price Excl. Vat <br> السعر غير شامل ضريبة القيمة المضافة</b><br>'.$invoice->currency_name.'</th>
                     <th width="12%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>VAT <br> ضريبة القيمة المضافة</b><br>%</th>
                     <th width="12%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>VAT AMT <br> قيمة الضريبة</b><br>'.$invoice->currency_name.'</th>
                     <th width="12%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>Amount Inclusive Vat <br> المبلغ شاملاً ضريبة القيمة المضافة</b><br>'.$invoice->currency_name.'</th>
                  </tr>';
                     $sr = 1; $price_excl_vat = 0; $vat_amt = 0; $sub_sub_total = 0; $tfif_percent_withheld = 0; $fvattotal = 0;
   				  $withoutvat = 0;
                     $items = get_items_table_data($invoice, 'invoice');
                     foreach ($invoice->items as $row) {
                        $excvat = $row["qty"] * $row["rate"];
   					 $withoutvat = $withoutvat + $excvat;
   					 $fif_percent_withheld = $excvat * 0.15;
   					 $tfif_percent_withheld = $fif_percent_withheld + $fif_percent_withheld;
                        $sub_sub_total = $sub_sub_total + $invoice->subtotal;
   					 $incvat = $excvat + $fif_percent_withheld;
         
                  $info_left_column1 .= '<tr>
                     <td width="6%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.$sr.'</td>
                     <td width="21%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.$row["description"].'<br />'.$row["long_description"].'</td>
                     <td width="12%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.$row["qty"].'<br>'.$row["unit"].'</td>
                     <td width="12%" align="right" class="invoice-table-column" style="border: 1px solid #000000;">'.app_format_number($row["rate"]).'</td>
                     <td width="12%" align="right" class="invoice-table-column" style="border: 1px solid #000000;">'.app_format_number($excvat).'</td>
                     <td width="12%" align="right" class="invoice-table-column" style="border: 1px solid #000000;">';
                     foreach ($items->taxes() as $tax) { $info_left_column1 .= app_format_number($tax['taxrate']) . '%'; $tt = $tax['taxrate']; $price_excl_vat = $price_excl_vat + $tax['taxrate']; }
					 $fif_percent_withheld = $excvat * ($tt/100);
					 $fvattotal = $fvattotal + $fif_percent_withheld;
					 $tfif_percent_withheld = $excvat + $fif_percent_withheld;
                  $info_left_column1 .= '</td><td width="12%" align="right" class="invoice-table-column" style="border: 1px solid #000000;">'.app_format_number($fif_percent_withheld).'</td>
                     <td width="12%" align="right" class="invoice-table-column" style="border: 1px solid #000000;">'.app_format_number($tfif_percent_withheld).'</td>
                  </tr>';
            
                     $sr++;
                  }

                  $fif_percent_withheld = $sub_sub_total * 0.15;
                  $final_amount = $withoutvat + $fvattotal;
                  //$file =  "assets/images/".$invoice->hash.".png";
			   
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

  $Code=einv_generate_tlv_qr_code(array(get_option('invoice_company_name'),get_option('company_vat'),date("Y-m-d H:i:s a",strtotime($invoice->datecreated)),$final_amount,$fvattotal));
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

$info_left_column1 .= '
                  <tr>
                     <td colspan="8" style="border-bottom: 1px solid #000000;"></td>
                  </tr>
                  <tr style="background-color: #bfbfbf; color: #000000; border: 1px solid #000000;">
                     <th colspan="4" align="left" class="invoice-table-column" style="border: 1px solid #000000; border-top: 1px solid #000000;"><b>Total Amounts</b></th>
                     <th colspan="4" align="right" class="invoice-table-column" style="border: 1px solid #000000; border-top: 1px solid #000000;"><b>المبالغ الإجمالية</b></th>
                  </tr>
                  <tr>
                     <th colspan="3" style="border: 1px solid #000000;"></th>
                     <th colspan="2" align="left" class="invoice-table-column" style="border: 1px solid #000000;"><b>Sub Total</b></th>
                     <th colspan="2" align="right" class="invoice-table-column" style="border: 1px solid #000000;">'.$invoice->currency_name.' <b>المجموع الفرعي</b></th>
                     <th colspan="1" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.app_format_number($withoutvat).'</b></th>
                  </tr>
                  <tr>
                     <th colspan="3" style="border: 1px solid #000000;">';

$pdf_custom_fields = get_custom_fields('invoice', array('show_on_pdf' => 1, 'show_on_client_portal' => 1));
               	 foreach ($pdf_custom_fields as $field) {
                  	$value = get_custom_field_value($invoice->id, $field['id'], 'invoice');
                  	if ($value == '') {
                     	continue;
                  	}
					 if($field['name'] == 'Remarks for Zero VAT')
					 {
						 
$info_left_column1 .= 'Remarks: '.$value;
					 }
				 }
$info_left_column1 .= '</th>
                     <th colspan="2" align="left" class="invoice-table-column" style="border: 1px solid #000000;" style="border: 1px solid #000000;"><b>VAT @ ';
	if(count($items->taxes()) > 0) { foreach ($items->taxes() as $tax) { $info_left_column1 .= app_format_number($tax['taxrate']); } } else { $info_left_column1 .= '0'; }
$info_left_column1 .= '%</b></th>
                     <th colspan="2" align="right" class="invoice-table-column" style="border: 1px solid #000000;">'.$invoice->currency_name.' <b>ضريبة القيمة المضافة @ ';
	if(count($items->taxes()) > 0) { foreach ($items->taxes() as $tax) { $info_left_column1 .= app_format_number($tax['taxrate']); } } else { $info_left_column1 .= '0'; }
$info_left_column1 .= '٪ </b></th>
                     <th colspan="1" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.app_format_number($fvattotal).'</b></th>
                  </tr>
                  <tr>
                     <th colspan="3" style="border: 1px solid #000000;"></th>
                     <th colspan="2" align="left" class="invoice-table-column" style="border: 1px solid #000000;"><b>Total</b></th>
                     <th colspan="2" align="right" class="invoice-table-column" style="border: 1px solid #000000;">'.$invoice->currency_name.' <b>المجموع</b></th>
                     <th colspan="2" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.app_format_number($final_amount).'</b></th>
                  </tr>';
$paidamount = 0; foreach ($invoice->payments as $payment) {
	$paidamount = $paidamount + $payment['amount'];
}
$info_left_column1 .= '<tr>
                     <th colspan="3" style="border: 1px solid #000000;"></th>
                     <th colspan="2" align="left" class="invoice-table-column" style="border: 1px solid #000000; color: #000000;"><b>Paid Amount</b></th>
                     <th colspan="2" align="right" class="invoice-table-column" style="border: 1px solid #000000;">'.$invoice->currency_name.' <b>المبلغ المدفوع</b></th>
                     <th colspan="1" align="right" class="invoice-table-column" style="border: 1px solid #000000; color: #000000;"><b>'.app_format_number($paidamount).'</b></th>
                  </tr>';
$remamount = $final_amount - $paidamount;
$info_left_column1 .= '<tr>
                     <th colspan="3" style="border: 1px solid #000000;"></th>
                     <th colspan="2" align="left" class="invoice-table-column" style="border: 1px solid #000000; color: #000000;"><b>Amount Due</b></th>
                     <th colspan="2" align="right" class="invoice-table-column" style="border: 1px solid #000000;">'.$invoice->currency_name.' <b>المبلغ المستحق</b></th>
                     <th colspan="1" align="right" class="invoice-table-column" style="border: 1px solid #000000; color: #000000;"><b>'.app_format_number($remamount).'</b></th>
                  </tr>';
$pdf_custom_fields = get_custom_fields('invoice', array('show_on_pdf' => 1, 'show_on_client_portal' => 1));
               	 foreach ($pdf_custom_fields as $field) {
                  	$value = get_custom_field_value($invoice->id, $field['id'], 'invoice');
                  	if ($value == '') {
                     	continue;
                  	}
					 if($field['name'] == 'Converted to SAR')
					 {
                  $info_left_column1 .= '
				  <tr>
				  	<th colspan="8" style="text-align:right;border: 1px solid #000000;">بيانات الضريبة المضافة بالريال</th>
				  </tr>
				  <tr>
                     <th colspan="3" style="border: 1px solid #000000;"></th>
                     <th colspan="2" align="right" class="invoice-table-column" style="border: 1px solid #000000; color: #000000;"><b>القيمة الخاضعة للضريبة بالريال</b></th>
                     <th colspan="2" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>ريال</b></th>
                     <th colspan="1" align="right" class="invoice-table-column" style="border: 1px solid #000000; color: #000000;"><b>'.app_format_number($value).'</b></th>
                     
                  </tr>';
					 }
					 if($field['name'] == 'VAT Converted to SAR')
					 {
                  $info_left_column1 .= '
				  <tr>
                     <th colspan="3" style="border: 1px solid #000000;"></th>
                     <th colspan="2" align="right" class="invoice-table-column" style="border: 1px solid #000000; color: #000000;"><b>القيمة قيمة الضريبة المضافة بالريال15 %</b></th>
                     <th colspan="2" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>ريال</b></th>
                     <th colspan="1" align="right" class="invoice-table-column" style="border: 1px solid #000000; color: #000000;"><b>'.app_format_number($value).'</b></th>
                     
                  </tr>';
					 }
               	}
$info_left_column1 .= '
                  <tr>
                     <th colspan="5" class="invoice-table-column" style="border: 1px solid #000000;">';

                        if (!empty($invoice->clientnote)) {
                              $info_left_column1 .= '<br><br><b style="font-weight: bold;">Note:</b><br>'.$invoice->clientnote;
                        }
                        if (!empty($invoice->terms)) {
                              $info_left_column1 .= '<hr />
                              <b>Terms and Conditions:</b><br>'.$invoice->terms;
                        }
                     $info_left_column1 .= '</th>
                     <th colspan="3" align="center" style="border: 1px solid #000000;"><img src="'.site_url($file).'" width="100px"></th>
                  </tr>';

$info_left_column1 .= '</table></div>';
if (get_option('total_to_words_enabled') == 1) {
               $info_left_column1 .= '<div class="col-md-12 text-center invoice-html-total-to-words">
                  <p class="bold no-margin">With words : ' . $CI->numberword->convert($invoice->total, $invoice->currency_name).'</p>
               </div>';
            }			
// Add logo
//$info_left_column .= pdf_logo_url();

$info_left_column2 = '
            <div class="col-md-12 invoice-html-payments">';
               $total_payments = count($invoice->payments);
               if ($total_payments > 0) {
$info_left_column2 .= '<p class="bold mbot15 font-medium">'._l('invoice_received_payments').':</p>
                  <table class="table table-hover invoice-payments-table">
                     <thead>
                        <tr style="background-color: #bfbfbf; color: #000000; border: 1px solid #000000;">
                           <th class="invoice-table-column" style="border: 1px solid #000000;">'._l('invoice_payments_table_number_heading').'</th>
                           <th class="invoice-table-column" style="border: 1px solid #000000;">'._l('invoice_payments_table_mode_heading').'</th>
                           <th class="invoice-table-column" style="border: 1px solid #000000;">'._l('invoice_payments_table_date_heading').'</th>
                           <th class="invoice-table-column" style="border: 1px solid #000000;">'._l('invoice_payments_table_amount_heading').'</th>
                        </tr>
                     </thead>
                     <tbody>';
                        foreach ($invoice->payments as $payment) {
$info_left_column2 .= '<tr>
                              <td class="invoice-table-column">'.$payment['paymentid'].'</td>
                              <td class="invoice-table-column">'.$payment['name'].''; if (!empty($payment['paymentmethod'])) {
                                                                     $info_left_column2 .= ' - ' . $payment['paymentmethod'];
                                                                  }
							$info_left_column2 .= '</td>
                              <td class="invoice-table-column">'._d($payment['date']).'</td>
                              <td class="invoice-table-column">'.app_format_money($payment['amount'], $invoice->currency_name).'</td>
                           </tr>';
                        }
                     $info_left_column2 .= '</tbody>
                  </table>
                  <hr />';
               } else {
                  $info_left_column2 .= '<h5 class="bold pull-left">'. _l('invoice_no_payments_found').'</h5>
                  <div class="clearfix"></div>
                  <hr />';
               }
            $info_left_column2 .= '</div>';


// Write top left logo and right column info/text
pdf_multi_row($info_center_column,"", $pdf, ($dimensions['wk'] / 1.05) - $dimensions['lm']);
pdf_multi_row($info_left_column,"", $pdf, ($dimensions['wk'] / 1.05) - $dimensions['lm']);
pdf_multi_row($info_left_column1,"", $pdf, '');
//pdf_multi_row($info_left_column2,"", $pdf, '');

?>

