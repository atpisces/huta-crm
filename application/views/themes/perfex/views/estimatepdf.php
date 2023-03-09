<?php

defined('BASEPATH') or exit('No direct script access allowed');


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
                     <td width="33%" style=" background: #bfbfbf; padding: 0px; text-align: center; vertical-align: middle;"><h4 class="bold invoice-html-number">PROFORMA INVOICE <br>فاتورة أولية </h4></td>
                     <td width="33%" style="padding: 0px; text-align: right; vertical-align: middle; font-size: 15px;" align="right">
                        <p><b>Proforma No:</b> '.format_estimate_number($estimate->id).'</p>
                        <p><b>Proforma Date:</b>'.date('d/m/Y', strtotime($estimate->date)).'</p>
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
                     <th width="99%" align="center" class="invoice-table-column" style="border: 2px solid #000000;"><b>Customer Details / العميل بيانات </b></th>
                  </tr>
                  <tr>
                     <th width="20%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Cleint Name:</b></th>
                     <td width="59%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.$customer_name[0].'<br>'.$customer_name[1].'</b></td>
                     <th width="20%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>اسم العميل : </b></th>
                  </tr>
                  <tr>
                     <th width="20%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Client VAT #:</b></th>
                     <td width="59%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.$estimate->client->vat.'</b></td>
                     <th width="20%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>رقم ضريبة:</b></th>
                  </tr>
                  <tr>
                     <th width="20%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Client Address:</b></th>
                     <td width="59%" align="center" class="invoice-table-column"  style="border: 1px solid #000000;">'.str_replace("/", "\n", $client_add).'</td>
                     <th width="20%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>عنوان العميل :</b></th>
                  </tr>
				  <tr>
                     <th width="20%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Ref. No.:</b></th>
                     <td width="59%" align="center" class="invoice-table-column"  style="border: 1px solid #000000;">'.$estimate->reference_no.'</td>
                     <th width="20%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>رقم المرجع :</b></th>
                  </tr>
                 
               </table>
            </div>';

$info_left_column1 .= '<div style="float: left; width: 100%; padding: 30px;">
               <table class="table table-bordered invoice-detail-table" cellpadding="5px">
                  <tr style="background-color: #bfbfbf; color: #000000">
                     <th width="6%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>SL. No. <br> مسلسل</b></th>
                     <th width="21%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>Service Description <br> وصف الخدمة</b></th>
                     <th width="12%" align="center" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>QTY <br> كمية</b></th>
                     <th width="12%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>Unit Rate <br> سعر الوحدة</b><br>'.$estimate->currency_name.'</th>
                     <th width="12%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>Price Excl. Vat <br> السعر غير شامل ضريبة القيمة المضافة</b><br>'.$estimate->currency_name.'</th>
                     <th width="12%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>VAT <br> ضريبة القيمة المضافة</b><br>%</th>
                     <th width="12%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>VAT AMT <br> قيمة الضريبة</b><br>'.$estimate->currency_name.'</th>
                     <th width="12%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>Amount Inclusive Vat <br> المبلغ شاملاً ضريبة القيمة المضافة</b><br>'.$estimate->currency_name.'</th>
                  </tr>';
					
                     $sr = 1; $price_excl_vat = 0; $vat_amt = 0; $sub_sub_total = 0; $tfif_percent_withheld = 0; $fvattotal = 0;
				      $withoutvat = 0;
                  $items = get_items_table_data($estimate, 'estimates');
                  
                  foreach ($estimate->items as $row) {
                     $excvat = $row["qty"] * $row["rate"];
					      $withoutvat = $withoutvat + $excvat;
					      $fif_percent_withheld = $excvat * 0.15;
					      $tfif_percent_withheld = $fif_percent_withheld + $fif_percent_withheld;
                     $sub_sub_total = $sub_sub_total + $invoice->subtotal;
					      $incvat = $excvat + $fif_percent_withheld;
					  $fvattotal = $fvattotal + $fif_percent_withheld;
         
                  $info_left_column1 .= '<tr>
                     <td width="6%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.$sr.'</td>
                     <td width="21%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.$row["description"].'<br />'.$row["long_description"].'</td>
                     <td width="12%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.$row["qty"].'<br>'.$row["unit"].'</td>
                     <td width="12%" align="right" class="invoice-table-column" style="border: 1px solid #000000;">'.app_format_money($row["rate"], $invoice->currency_name).'</td>
                     <td width="12%" align="right" class="invoice-table-column" style="border: 1px solid #000000;">'.app_format_money($excvat, $invoice->currency_name).'</td>
                     <td width="12%" align="right" class="invoice-table-column" style="border: 1px solid #000000;">15%';
                     foreach ($items->taxes() as $tax) { $tt = 15; $price_excl_vat = $price_excl_vat + $tax['taxrate']; }
					 $fif_percent_withheld = $excvat * ($tt/100);
					 
					 $tfif_percent_withheld = $excvat + $fif_percent_withheld;
                  $info_left_column1 .= '</td>
				  <td width="12%" align="right" class="invoice-table-column" style="border: 1px solid #000000;">'.app_format_money( $excvat * (15/100), $invoice->currency_name).'</td>
                     <td width="12%" align="right" class="invoice-table-column" style="border: 1px solid #000000;">'.app_format_money($tfif_percent_withheld+($excvat * (15/100)), $invoice->currency_name).'</td>
                  </tr>';
            
                     $sr++;
                  }

                  $fif_percent_withheld = $sub_sub_total * 0.15;
                  $final_amount = $withoutvat + $fvattotal;
                  $file =  "assets/images/".$invoice->hash.".png";

$info_left_column1 .= '<tr>
                     <td colspan="8" style="border: 1px solid #ffffff;"></td>
                  </tr>
                  <tr>
                     <td colspan="8" style="border-bottom: 1px solid #ffffff;"></td>
                  </tr>
                  <tr style="background-color: #bfbfbf; color: #000000; border: 1px solid #000000;">
                     <th colspan="4" align="left" class="invoice-table-column" style="border: 1px solid #000000; border-top: 1px solid #000000;"><b>Total Amounts</b></th>
                     <th colspan="4" align="right" class="invoice-table-column" style="border: 1px solid #000000; border-top: 1px solid #000000;"><b>المبالغ الإجمالية</b></th>
                  </tr>
                  <tr>
                     <th colspan="2" style="border: 1px solid #000000;"></th>
                     <th colspan="2" align="left" class="invoice-table-column" style="border: 1px solid #000000;"><b>Sub Total</b></th>
                     <th colspan="3" align="right" class="invoice-table-column" style="border: 1px solid #000000;">'.$estimate->currency_name.' <b>المجموع الفرعي</b></th>
                     <th colspan="1" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.app_format_money($withoutvat, $invoice->currency_name).'</b></th>
                  </tr>';
				if($fvattotal > 0)
				{
                  $info_left_column1 .= '<tr>
                     <th colspan="2" style="border: 1px solid #000000;"></th>
                     <th colspan="2" align="left" class="invoice-table-column" style="border: 1px solid #000000;" style="border: 1px solid #000000;"><b>VAT @ 15% </b></th>
                     <th colspan="3" align="right" class="invoice-table-column" style="border: 1px solid #000000;">'.$estimate->currency_name.' <b>ضريبة القيمة المضافة @ 15٪</b></th>
                     <th colspan="1" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.app_format_money($fvattotal, $invoice->currency_name).'</b></th>
                  </tr>';
				}
                  $info_left_column1 .= '<tr>
                     <th colspan="2" style="border: 1px solid #000000;"></th>
                     <th colspan="2" align="left" class="invoice-table-column" style="border: 1px solid #000000;"><b>Total</b></th>
                     <th colspan="3" align="right" class="invoice-table-column" style="border: 1px solid #000000;">'.$estimate->currency_name.'<b> المجموع</b></th>
                     <th colspan="2" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.app_format_money($final_amount, $invoice->currency_name).'</b></th>
                  </tr>';
$paidamount = 0; foreach ($invoice->payments as $payment) {
	$paidamount = $paidamount + $payment['amount'];
}
$pdf_custom_fields = get_custom_fields('estimate', array('show_on_pdf' => 1, 'show_on_client_portal' => 1));
               	 foreach ($pdf_custom_fields as $field) {
                  	$value = get_custom_field_value($estimate->id, $field['id'], 'estimate');
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
                     <th colspan="2" style="border: 1px solid #000000;"></th>
                     <th colspan="3" align="right" class="invoice-table-column" style="border: 1px solid #000000; color: #ff0000;"><b>القيمة الخاضعة للضريبة بالريال</b></th>
                     <th colspan="2" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>ريال</b></th>
                     <th colspan="1" align="right" class="invoice-table-column" style="border: 1px solid #000000; color: #ff0000;"><b>'.app_format_number($value).'</b></th>
                     
                  </tr>';
					 }
					 if($field['name'] == 'VAT Converted to SAR')
					 {
                  $info_left_column1 .= '
				  <tr>
                     <th colspan="2" style="border: 1px solid #000000;"></th>
                     <th colspan="3" align="right" class="invoice-table-column" style="border: 1px solid #000000; color: #ff0000;"><b>القيمة قيمة الضريبة المضافة بالريال15 %</b></th>
                     <th colspan="2" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>ريال</b></th>
                     <th colspan="1" align="right" class="invoice-table-column" style="border: 1px solid #000000; color: #ff0000;"><b>'.app_format_number($value).'</b></th>
                     
                  </tr>';
					 }
               	}
$info_left_column1 .= '
                  <tr>
                     <th colspan="8" class="invoice-table-column" style="border: 1px solid #000000; ">';

                        if (!empty($estimate->clientnote)) {
                              $info_left_column1 .= '<br><br><b style="font-weight: bold;">Note:</b><br>'.$estimate->clientnote;
                        }
                        if (!empty($estimate->terms)) {
                              $info_left_column1 .= '<hr />
                              <b>Terms and Conditions:</b><br>'.$estimate->terms;
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


// Write top left logo and right column info/text
pdf_multi_row($info_center_column,"", $pdf, ($dimensions['wk'] / 1.05) - $dimensions['lm']);
pdf_multi_row($info_left_column,"", $pdf, ($dimensions['wk'] / 1.05) - $dimensions['lm']);
pdf_multi_row($info_left_column1,"", $pdf, '');
//pdf_multi_row($info_left_column2,"", $pdf, '');

?>

