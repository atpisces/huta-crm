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
                     <td width="33%" style=" background-color: #bfbfbf; padding: 0px; text-align: center; vertical-align: middle;"><br><h4 class="bold invoice-html-number">TAX INVOICE <br>فاتورة ضريبية </h4></td>
                     <td width="33%" style="padding: 0px; text-align: right; vertical-align: middle; font-size: 15px;" align="right">
                        <p><b>Invoice No:</b> '.format_invoice_number($invoice->id).'</p>
                        <p><b>Invoice Date:</b>'.date('d/m/Y', strtotime($invoice->date)).'</p>
                     </td>
                  </tr>
               </table>
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
                     <th width="33%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Seller Address:</b></th>
                     <td width="33%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.get_option('invoice_company_address').'</td>
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
                     <th width="99%" align="center" class="invoice-table-column" style="border: 2px solid #000000;"><b>Customer Details / تفاصيل العميل</b></th>
                  </tr>
                  <tr>
                     <th width="20%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Customer Name:</b></th>
                     <td width="59%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.$invoice->client->company.'</b></td>
                     <th width="20%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>اسم الزبون : </b></th>
                  </tr>
                  <tr>
                     <th width="20%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Client VAT #:</b></th>
                     <td width="59%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.$clients_data->vat.'</b></td>
                     <th width="20%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>رقم ضريبة:</b></th>
                  </tr>
                  <tr>
                     <th width="20%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Client Address:</b></th>
                     <td width="59%" align="center" class="invoice-table-column">'.$clients_data->address.'</td>
                     <th width="20%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>عنوان العميل :</b></th>
                  </tr>
                  <tr>
                     <th width="20%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Attn:</b></th>
                     <td width="59%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">-----</td>
                     <th width="20%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b> ملاحظة :</b></th>
                  </tr>
               </table>
            </div>';

$info_left_column1 .= '<div style="float: left; width: 100%; padding: 30px;">
               <table class="table table-bordered invoice-detail-table" cellpadding="5px">
                  <tr style="background-color: #bfbfbf; color: #000000">
                     <th width="6%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>SL. No. <br> مسلسل</b></th>
                     <th width="21%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>Service Description <br> وصف الخدمة</b></th>
                     <th width="12%" align="center" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>QTY <br> كمية</b></th>
                     <th width="12%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>Unit Rate <br> سعر الوحدة</b></th>
                     <th width="12%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>Price Excl. Vat <br> السعر غير شامل ضريبة القيمة المضافة</b></th>
                     <th width="12%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>VAT <br> ضريبة القيمة المضافة</b></th>
                     <th width="12%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>VAT AMT <br> قيمة الضريبة</b></th>
                     <th width="12%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>Amount Inclusive Vat <br> المبلغ شاملاً ضريبة القيمة المضافة</b></th>
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
                     <td width="12%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.$row["qty"].'</td>
                     <td width="12%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.app_format_money($row["rate"], $invoice->currency_name).'</td>
                     <td width="12%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.app_format_money($excvat, $invoice->currency_name).'</td>
                     <td width="12%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">';
                     foreach ($items->taxes() as $tax) { $info_left_column1 .= app_format_number($tax['taxrate']) . '%'; $tt = $tax['taxrate']; $price_excl_vat = $price_excl_vat + $tax['taxrate']; }
					 $fif_percent_withheld = $excvat * ($tt/100);
					 $fvattotal = $fvattotal + $fif_percent_withheld;
					 $tfif_percent_withheld = $excvat + $fif_percent_withheld;
                  $info_left_column1 .= '</td><td width="12%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.app_format_money($fif_percent_withheld, $invoice->currency_name).'</td>
                     <td width="12%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">'.app_format_money($tfif_percent_withheld, $invoice->currency_name).'</td>
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
                     <th colspan="3" style="border: 1px solid #000000;"></th>
                     <th colspan="2" align="left" class="invoice-table-column" style="border: 1px solid #000000;"><b>Sub Total</b></th>
                     <th colspan="2" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>المجموع الفرعي</b></th>
                     <th colspan="1" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.app_format_money($withoutvat, $invoice->currency_name).'</b></th>
                  </tr>
                  <tr>
                     <th colspan="3" style="border: 1px solid #000000;"></th>
                     <th colspan="2" align="left" class="invoice-table-column" style="border: 1px solid #000000;" style="border: 1px solid #000000;"><b>VAT @ 15% (15.00%)</b></th>
                     <th colspan="2" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>ضريبة القيمة المضافة @ 15٪ (15.00٪)</b></th>
                     <th colspan="1" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.app_format_money($fvattotal, $invoice->currency_name).'</b></th>
                  </tr>
                  <tr>
                     <th colspan="3" style="border: 1px solid #000000;"></th>
                     <th colspan="2" align="left" class="invoice-table-column" style="border: 1px solid #000000;"><b>Total</b></th>
                     <th colspan="2" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>المجموع</b></th>
                     <th colspan="2" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>'.app_format_money($final_amount, $invoice->currency_name).'</b></th>
                  </tr>
                  <tr>
                     <th colspan="3" style="border: 1px solid #000000;"></th>
                     <th colspan="2" align="left" class="invoice-table-column" style="border: 1px solid #000000; color: #ff0000;"><b>Amount Due</b></th>
                     <th colspan="2" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>المبلغ المستحق</b></th>
                     <th colspan="1" align="right" class="invoice-table-column" style="border: 1px solid #000000; color: #ff0000;"><b>'.app_format_money($final_amount, $invoice->currency_name).'</b></th>
                  </tr>
                  <tr>
                     <th colspan="5" class="invoice-table-column" style="border: 1px solid #000000; border-right: 0px !important;"><b>WE ENERGY Co. Ltd</b>';

                        if (!empty($invoice->clientnote)) {
                              $info_left_column1 .= '<br><br><b style="font-weight: bold;">Note:</b><br>'.$invoice->clientnote;
                        }
                        if (!empty($invoice->terms)) {
                              $info_left_column1 .= '<hr />
                              <b>Terms and Conditions:</b><br>'.$invoice->terms;
                        }
                     $info_left_column1 .= '</th>
                     <th colspan="3" class="invoice-table-column" align="right" style="border: 1px solid #000000; border-left: 0px !important;"><img src="'.site_url($file).'" width="200px"></th>
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

?>

