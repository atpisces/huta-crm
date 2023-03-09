<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(APPPATH . 'libraries/phpqrcode/qrlib.php');

$fvattotal = 0; $withoutvat = 0; $excvat = 0; $final_amount = 0;
$chk = 1;
$items = get_items_table_data($invoice, 'invoice');
foreach ($invoice->items as $row) {
	$excvat = $row["qty"] * $row["rate"];
	$withoutvat = $withoutvat + $excvat;
	foreach ($items->taxes() as $tax) { $tt = $tax['taxrate']; $price_excl_vat = $price_excl_vat + $tax['taxrate']; }
	if($invoice->discount_type == "before_tax"){
		if($invoice->discount_total > 0 && $chk == 1){
			$excvat = $excvat - $invoice->discount_total;
			$fif_percent_withheld = $excvat * ($tt/100);
			$chk = 2;
		} else {
			$fif_percent_withheld = $excvat * ($tt/100);
		}
	} else if($invoice->discount_type == "after_tax"){
		if($invoice->discount_total > 0 && $chk == 1){
			$excvat = $excvat * ($tt/100);
			$fif_percent_withheld = $excvat - $invoice->discount_total;
			$chk = 2;
		} else {
			$fif_percent_withheld = $excvat * ($tt/100);
		}
	} else {
		$fif_percent_withheld = $excvat * ($tt/100);
	}
	//$fif_percent_withheld = $excvat * ($tt/100);
	$fvattotal = $fvattotal + $fif_percent_withheld;
}
$withoutvat = $withoutvat - $invoice->discount_total;
$final_amount = $withoutvat + $fvattotal;
   
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

// print_r($invoice_result);
// echo $invoice_result->branchid;
// exit;
$CI =& get_instance();
$CI->load->model('invoices_model');
$invoice_result= $CI->invoices_model->get($invoice->id);
//$CI->load->model('branches_model');
$CI->load->model('clients_model');
$CI->load->model('projects_model');
$project_details = $CI->projects_model->get_project_details($invoice->project_id);
//$branch_result= $CI->branches_model->get_brand_and_details($invoice_result->branchid);
// print_r($branch_result);
// echo $invoice_result->branchid;
// exit;
// echo "My official results here: ".$result;


$clients_data = $CI->clients_model->get($invoice->clientid);
//print_r($invoice_result);
//echo "<br /><br />";
//echo $invoice_result->items[0]['id'];
//echo "<br /><br />";
// exit;

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
//foreach($payment_modes_details as $mode){
  // echo $mode["description"]."<br />";
//}
// exit;

//Invoice for the period
$inv_for_the_month = ""; $contract_field = "";

$pdf_custom_fields = get_custom_fields('invoice', array('show_on_pdf' => 1, 'show_on_client_portal' => 1));
foreach ($pdf_custom_fields as $field) {
	$value = get_custom_field_value($invoice->id, $field['id'], 'invoice');
	if ($value == '') {
		continue;
	}
	if($field['name'] == "Invoice for the month"){
		$inv_for_the_month = $value;
	}
	if($field['name'] == "Contract"){
		$contract_field = $value;
	}
}

$cr_number = "";
$pdf_custom_fields = get_custom_fields('customers', array('show_on_client_portal' => 1));
foreach ($pdf_custom_fields as $field) {
	$value = get_custom_field_value($invoice->clientid, $field['id'], 'customers');
	if ($value == '') {
		continue;
	}
	if($field['name'] == "CR Number"){
		$cr_number = $value;
	}
}

$dimensions = $pdf->getPageDimensions();
$company_logo = get_option('company_logo');
				   /*$pdf_custom_fields = get_custom_fields('items', array('show_on_pdf' => 1, 'show_on_client_portal' => 1));
				   foreach ($pdf_custom_fields as $field) {
					   //print_r($field);
					  $value = get_custom_field_value($invoice_result->items[0]['id'], $field['id'], 'items');
					  echo $invoice_result->items[0]['id']r." = ".$field["name"]." : ".$value."<br />";
					  if ($value == '') {
						 continue;
					  }
				   }*/
//exit;
                  //$file =  "assets/images/".$invoice->hash.".png";
$info_center_column = '<div>
               <table style="margin: 0px;">
                  <tr>
                     <td width="20%" style="padding-top: 10px;"><br><img src="'.base_url('uploads/company/'.$company_logo).'" class="img img-responsive" style="height: 100px;"></td>
                     <td width="66%" style=" background-color: #ffffff; padding: 0px; text-align: center; vertical-align: middle; font-size: 22px;"><br><h4 class="bold invoice-html-number">VAT INVOICE &nbsp;&nbsp;&nbsp;&nbsp;فاتورة ضريبة القيمة المضافة</h4></td>
                     <!--<td width="33%" style="padding: 0px; text-align: right; vertical-align: middle; font-size: 15px;" align="right">
                        <img src="'.site_url($qr_code_img).'" width="100px">
                     </td>-->
                  </tr>
               </table>
</div>';

$info_left_column = '<div style="float: left; width: 100%; padding: 30px;">
               <table cellpadding="3px" style="font-size: 12px;">
                  <tr>
                     <th width="17%" class="invoice-table-column" style="border: 1px solid #000000;"><b>SHIPPER/EXPORTER:</b></th>
                     <th width="38%" class="invoice-table-column" style="border: 1px solid #000000;">Al Fateh Co. Ltd <br>C.R. 4030366416 <br>2421 Quraish,Al Salama, 8115 Jeddah, K S A</th>
                     <td width="29%" align="left" class="invoice-table-column" style="border: 1px solid #000000;"><b>MATERIAL ORIGIN COUNTRY:</b></td>
                     <th width="15%" align="left" class="invoice-table-column" style="border: 1px solid #000000;">EGYPT</th>
                  </tr>
                  <tr>
                     <th width="17%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Tel:</b></th>
                     <th width="38%" class="invoice-table-column" style="border: 1px solid #000000;">(+) 966126124171</th>
                     <td width="29%" align="left" class="invoice-table-column" style="border: 1px solid #000000;"><b>MATERIAL PRODUCTION COUNTRY:</b></td>
                     <th width="15%" align="left" class="invoice-table-column" style="border: 1px solid #000000;">EGYPT</th>
                  </tr>
                  <tr>
                     <th width="17%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Fax:</b></th>
                     <th width="38%" class="invoice-table-column" style="border: 1px solid #000000;">(+) 966126124170</th>
                     <td width="29%" align="left" class="invoice-table-column" style="border: 1px solid #000000;"><b>MATERIAL AQUISITION COUNTRY:</b></td>
                     <th width="15%" align="left" class="invoice-table-column" style="border: 1px solid #000000;">EGYPT</th>
                  </tr>
                  <tr>
                     <th width="17%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Vat:</b></th>
                     <th width="38%" class="invoice-table-column" style="border: 1px solid #000000;">310452991500003</th>
                     <td width="29%" align="left" class="invoice-table-column" style="border: 1px solid #000000;"><b>MATERIAL PROVENANCE COUNTRY:</b></td>
                     <th width="15%" align="left" class="invoice-table-column" style="border: 1px solid #000000;">EGYPT</th>
                  </tr>
               </table>
               <table cellpadding="3px" style="font-size: 12px;">
                  <tr><th></th></tr>
                  <tr>
                     <th width="10%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Ship To:</b></th>
                     <td width="44.5%" align="left" class="invoice-table-column" style="border: 1px solid #000000;">Swissotel Al Maqam <br>Makkah Clock Royal Tower, A Fairmont Hotel - <br> King Abdul Aziz Endowment, <br> Abraj Al Bait Complex PO Box 1281 - <br>Makkah 21955 - KSA </td>
                     <th width="44.5%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>سويس المقام مكه 
<br>ص ب 8918 جدة 21492
<br>أبراج البيت - وقف الملك عبدالعزيز
<br>مجمع ام القري - مكة المكرمة - المملكة العربية السعودية
</b></th>
                  </tr>
               </table>
               <table cellpadding="3px" style="font-size: 12px;">
                  <tr colspan="9"><th></th></tr>
                  <tr>
                     <th width="10%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">PROJECT</th>
                     <th width="10%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">PO #</th>
                     <th width="10%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">INVOICE #</th>
                     <th width="10%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">Gross Weight</th>
                     <th width="10%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">Net Weight</th>
                     <th width="10%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">Carton Qty</th>
                     <th width="13%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">Port of Shipping</th>
                     <th width="13%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">Port of Destination</th>
                     <th width="13%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">Port of Destination</th>
                  </tr>
                  <tr>
                     <th align="center" class="invoice-table-column" style="border: 1px solid #000000;">Swissotel Maqam</th>
                     <th align="center" class="invoice-table-column" style="border: 1px solid #000000;">23205</th>
                     <th align="center" class="invoice-table-column" style="border: 1px solid #000000;">SWS010122-2</th>
                     <th align="center" class="invoice-table-column" style="border: 1px solid #000000;">3705</th>
                     <th align="center" class="invoice-table-column" style="border: 1px solid #000000;">3481</th>
                     <th align="center" class="invoice-table-column" style="border: 1px solid #000000;">196</th>
                     <th align="center" class="invoice-table-column" style="border: 1px solid #000000;">Damiatte </th>
                     <th align="center" class="invoice-table-column" style="border: 1px solid #000000;">Jeddah</th>
                     <th align="center" class="invoice-table-column" style="border: 1px solid #000000;">MAGU224706/2</th>
                  </tr>
               </table>
            </div>';

	$scomp = explode("/",get_option('invoice_company_name'));
	$ccomp = explode("/",$invoice->client->company);

$info_left_column1 .= '<div style="float: left; width: 100%; padding: 30px;">
               <table class="table table-bordered invoice-detail-table" cellpadding="1.5px" style="font-size: 12px;">
                  <tr style="background-color: #bfbfbf; color: #000000">
                     <th width="9.5%" align="left" class="invoice-table-column" style="border: 1px solid #000000;"><b>No.</b></th>
                     <th width="25%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>Item</b></th>
                     <th width="10%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>Fabric</b></th>
                     <th width="10%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>Size.cm</b></th>
                     <th width="10%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>Color</b></th>
					           <th width="10%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>Qty / Pc</b></th>
                     <th width="10%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>Unit Price</b></th>
                     <th width="14.5%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>Tot. Value</b></th>
                  </tr>';
                     $sr = 1; $price_excl_vat = 0; $vat_amt = 0; $sub_sub_total = 0; $tfif_percent_withheld = 0; $fvattotal = 0;
   				  $withoutvat = 0; $chk = 1;
                     $items = get_items_table_data($invoice, 'invoice');
                     foreach ($invoice->items as $row) {
                        $excvat = $row["qty"] * $row["rate"];
   					 $withoutvat = $withoutvat + $excvat;
   					 $tfif_percent_withheld = $fif_percent_withheld + $fif_percent_withheld;
                        $sub_sub_total = $sub_sub_total + $invoice->subtotal;
   					 $incvat = $excvat + $fif_percent_withheld;
         
                  $info_left_column1 .= '<tr>
                     <td align="left" class="invoice-table-column" style="border: 1px solid #000000;">'.$sr.'</td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.$row["description"].'<br />'.$row["long_description"].'</td>
					 <td align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.$row["qty"].'</td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.app_format_money($row["rate"], '').'</td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.app_format_money($excvat, '').'</td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;">';
                     foreach ($items->taxes() as $tax) { $info_left_column1 .= round($tax['taxrate'], 0) . '%'; $tt = $tax['taxrate']; $price_excl_vat = $price_excl_vat + $tax['taxrate']; }

						          $fif_percent_withheld = $excvat * ($tt/100);
					 
          					 $fvattotal = $fvattotal + $fif_percent_withheld;
          					 $tfif_percent_withheld = $excvat + $fif_percent_withheld;
                  $info_left_column1 .= '</td><td align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.app_format_money($fif_percent_withheld, '').'</td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.app_format_money($tfif_percent_withheld, '').'</td>
                  </tr>';
            
                     $sr++;
                  }

				  //$withoutvat = $withoutvat - $invoice->discount_total;
				  $fif_percent_withheld = $sub_sub_total * 0.15;
				  $final_amount = ($withoutvat + $fvattotal) - $invoice->discount_total;

$info_left_column1 .= '<!-- <tr>
                     <td width="100%" style="border: 0px solid #000000;"></td>
                  </tr> -->
                  <tr>
                     <td align="left" class="invoice-table-column" style="border: 1px solid #000000;">Total</td>
                     <td align="right" class="invoice-table-column" style="border: 1px solid #000000;">المجموع</td>
           <td align="center" class="invoice-table-column" style="border: 1px solid #000000;"></td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;"></td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;"></td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;">00.00</td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;"></td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.app_format_money($tfif_percent_withheld, '').'</td>
                  </tr>
                  <tr>
                     <td align="left" class="invoice-table-column" style="border: 1px solid #000000;">Vat 15%</td>
                     <td align="right" class="invoice-table-column" style="border: 1px solid #000000;">ضريبة القيمة المضافة 15%</td>
           <td align="center" class="invoice-table-column" style="border: 1px solid #000000;"></td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;"></td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;"></td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;">00.00</td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;"></td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.app_format_money($tfif_percent_withheld, '').'</td>
                  </tr>
                  <tr>
                     <td align="left" class="invoice-table-column" style="border: 1px solid #000000;">Grand Total</td>
                     <td align="right" class="invoice-table-column" style="border: 1px solid #000000;">المبلغ الإجمالي</td>
           <td align="center" class="invoice-table-column" style="border: 1px solid #000000;"></td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;"></td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;"></td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;">00.00</td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;"></td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.app_format_money($tfif_percent_withheld, '').'</td>
                  </tr>';
$info_left_column1 .= '
                  <tr>
                     <th colspan="8" class="invoice-table-column" style="border: 1px solid #000000;"><b style="font-weight: bold;"><u>Manufacture:</u></b><br>Name:  KAZAREEN TEXTILE CO. - 10th of Ramadan Branch <br>Address: 10th of Ramadan City - 3rd Industrial Zone <br>Tel.: 055/413198 <br>Fax: 055/413331 <br>Country of Origin: EGYPT';

                    $info_left_column1 .= '<br><br>';

                        if (!empty($invoice->clientnote)) {
                              $info_left_column1 .= '<b style="font-weight: bold;"><u>BANK INFORMATION:</u></b><br>'.$invoice->clientnote;
                        }
            foreach($payment_modes_details as $mode){
              $info_left_column1 .= $mode["name"]."<br>".$mode["description"]."<br />";
            }
                        if (!empty($invoice->terms)) {
                              $info_left_column1 .= '<hr />
                              <b>Terms and Conditions:</b><br>'.$invoice->terms;
                        }
                     $info_left_column1 .= '</th>
                  </tr>';

$info_left_column1 .= '</table></div>';
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

$tbl = <<<EOD
<table class="table table-striped" cellspacing="0" cellpadding="1" border="1">
    <tr>
        <td>COL 1 - ROW 1 kjkjk jk jk jkj  jk   jk jk jkjk j  jk jkCOLSPAN 3</td>
        <td>COL 2 - ROW 1</td>
        <td>COL 3 - ROW 1</td>
    </tr>

</table>
EOD;

// Write top left logo and right column info/text
pdf_multi_row($info_center_column,"", $pdf, ($dimensions['wk'] / 1.05) - $dimensions['lm']);
pdf_multi_row($info_left_column,"", $pdf, ($dimensions['wk'] / 1.05) - $dimensions['lm']);
pdf_multi_row($info_left_column1,"", $pdf, '');
//$pdf->writeHTML($tbl, true, false, false, false, '');

//pdf_multi_row($info_left_column2,"", $pdf, '');

?>

