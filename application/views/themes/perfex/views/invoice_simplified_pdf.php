<?php

defined('BASEPATH') or exit('No direct script access allowed');

$dimensions = $pdf->getPageDimensions();

// $info_right_column = '';
// $info_left_column  = '';

// $info_right_column .= '<span style="font-weight:bold;font-size:27px;">' . _l('invoice_pdf_heading') . '</span><br />';
// $info_right_column .= '<b style="color:#ff0;"># ' . $invoice_number . '</b>';

// if (get_option('show_status_on_pdf_ei') == 1) {
//     $info_right_column .= '<br /><span style="color:rgb(' . invoice_status_color_pdf($status) . ');text-transform:uppercase;">' . format_invoice_status($status, '', false) . '</span>';
// }

// if ($status != Invoices_model::STATUS_PAID && $status != Invoices_model::STATUS_CANCELLED && get_option('show_pay_link_to_invoice_pdf') == 1
//     && found_invoice_mode($payment_modes, $invoice->id, false)) {
//     $info_right_column .= ' - <a style="color:#84c529;text-decoration:none;text-transform:uppercase;" href="' . site_url('invoice/' . $invoice->id . '/' . $invoice->hash) . '"><1b>' . _l('view_invoice_pdf_link_pay') . '</1b></a>';
// }

$info_center_column = '<div style="width:100%; float:left; text-align:center;">
<h4 style="font-weight:bold; margin-left:50%;">فاتورة ضريبية مبسطة
<br> Simplified Tax Invoice
</h4>
</div>';

$info_left_column = '<div style="float:left; width:95%;">
        <table class="table" border="0" cellpadding="5" cellspacing="0" width="100%">
            <tr>
                <th>Invoice Number:</th>
                <td></td>
                <td></td>
                <th style="text-align:right;">رقم الفاتورة</th>
            </tr>
            <tr>
                <th>Invoice Issue Date:</th>
                <td></td>
                <td></td>
                <th style="text-align:right;">تاريخ إصدار الفاتورة</th>
            </tr>
            <tr>
                <th>VAT Number:</th>
                <td></td>
                <td></td>
                <th style="text-align:right;">ظريبه الشراء</th>
            </tr>
        </table>
</div>';

// $info_right_column = '<div style="float:left; width:60%;">
//                         <img src="'.site_url("assets/images/qr-code.png").'" style="width: 200px;">
//                      </div>';

// <div style="float:left; width:24%;">
// <img src="'.site_url("assets/images/qr-code.png").'" style="width: 200px;">
// </div>

// <div class="col-sm-6 text-left transaction-html-info-col-left" style="margin-top:30px;">
// <div class="col-sm-6 text-left transaction-html-info-col-left" style="color:#fff; font-weight:bold; background:#314e73; padding:5px;">
//    Seller : 
// </div>
// <div class="col-sm-6 text-right transaction-html-info-col-right" style="color:#fff; font-weight:bold; background:#314e73;padding:5px;">
// تاجر         
// </div>
// </div>
// <div class="col-sm-6 text-right transaction-html-info-col-right" style="margin-top:30px;">
// <div class="col-sm-6 text-left transaction-html-info-col-left" style="color:#fff; font-weight:bold; background:#314e73;padding:5px;">
//    Buyer :    
// </div>
// <div class="col-sm-6 text-right transaction-html-info-col-right" style="color:#fff; font-weight:bold; background:#314e73;padding:5px;">
// مشتر         
// </div>
// </div>

// Add logo
$info_left_column .= pdf_logo_url();

// Write top left logo and right column info/text
pdf_multi_row($info_center_column,"", $pdf, ($dimensions['wk']) - $dimensions['lm']);
pdf_multi_row($info_left_column,"", $pdf, ($dimensions['wk']) - $dimensions['lm']);

$pdf->ln(5);


$items = get_items_table_data($invoice, 'invoice');

foreach ($invoice->items as $row) {
   $item = '<tr>
               <td>'.$row["description"].'</td>
               <td align="center">'.app_format_money($row["rate"], $invoice->currency_name).'</td>
               <td align="center">'.app_format_money($row["rate"], $invoice->currency_name).'</td>
               <td align="center">'.$row["qty"].'</td>
               <td align="center">'.app_format_money($row["rate"], $invoice->currency_name).'</td>
               <td align="center">'.app_format_money($row["rate"], $invoice->currency_name).'</td>
               <td align="center">'.app_format_money($row["rate"], $invoice->currency_name).'</td>
               <td align="center">'.app_format_money($row["rate"], $invoice->currency_name).'</td>
            </tr>';
}



$invoice_info = ' <table class="table table-bordered" border="1" cellpadding="0" cellspacing="0" width="95%">
                 
<thead>
   <tr style="background-color:#415164; color:#fff; border:1px solid #fff;">
      <th colspan="4">
         Line Items
      </th>
      <th colspan="4" style="text-align:right;">
      البنود
      </th>
   </tr>
   <tr style="background-color:#415164; color:#fff; border:1px solid #fff;">
      <th>
         Nature of Goods or <br>Services<br>
         طبيعة السلع أو الخدمات
      </th>
      <th>
         Unit Price
         سعر الوحدة
      </th>
      <th>
         Quantity
         كمية
      </th>
      <th>
         Taxable Amount
         المبلغ الخاضع للضريبة
      </th>
      <th>
         Discount
         خصم
      </th>
      <th>
         Tax Rate
         معدل الضريبة
      </th>
      <th>
         Tax Amount
         قيمة الضريبة
      </th>
      <th>
         Item Subtotal<br>(Including VAT)<br>
         المجموع الفرعي للبند <br> (متضمنًا ضريبة القيمة المضافة)
      </th>
   </tr>
</thead>
<tbody>
'.$item.'
</tbody>
</table>';

pdf_multi_row($invoice_info, "", $pdf, ($dimensions['wk'] ) - $dimensions['lm']);

$pdf->ln(5);
$invoice_total = ' <table class="table table-bordered" border="1" cellpadding="0" cellspacing="0" width="95%">                 
   <thead>
      <tr style="background-color:#415164; color:#fff; border:1px solid #fff;">
         <th colspan="2">
            Total Amounts:
         </th>
         <th colspan="2" style="text-align:right;">
         المبالغ الإجمالية
         </th>
      </tr>
   </thead>
   <tbody>
      <tr>
         <th>
         </th>
         <th>
            Sub Total
         </th>
         <td align="right">
         المجموع الفرعي	
         </td>
         <td>
         '.app_format_money($invoice->subtotal, $invoice->currency_name).'
         </td>
      </tr>
      <tr>
         <th>
         </th>
         <th>
         Total
         </th>
         <td align="right">
         المجموع
         </td>
         <td>
         '.app_format_money($invoice->total, $invoice->currency_name).'
         </td>
      </tr>
      <tr>
         <th>
         </th>
         <th>
         Total Paid	
         </th>
         <td align="right">
         مجموع المبالغ المدفوعة	
         </td>
         <td>
         '. "-" . app_format_money(sum_from_table(db_prefix() . 'invoicepaymentrecords', array('field' => 'amount', 'where' => array('invoiceid' => $invoice->id))), $invoice->currency_name).'
         </td>
      </tr>
      <tr>
         <th>
         </th>
         <th>
         Amount Due	
         </th>
         <td align="right">
         المبلغ المستحق	
         </td>
         <td>
         '.app_format_money($invoice->total_left_to_pay, $invoice->currency_name).'
         </td>
      </tr>
   </tbody>
</table>';
pdf_multi_row($invoice_total, "", $pdf, ($dimensions['wk'] ) - $dimensions['lm']);

$pdf->ln(5);

$invoice_qrcode = '<div style="float:left; width:95%; text-align:center;">
                        <img src="'.site_url("assets/images/qr-code.png").'" style="width: 200px;">
                     </div>';
pdf_multi_row($invoice_qrcode, "", $pdf, ($dimensions['wk'] ) - $dimensions['lm']);

$pdf->ln(5);

foreach ($invoice->payments as $payment) {
   
   if (!empty($payment['paymentmethod'])) {
      $method = ' - ' . $payment['paymentmethod'];
   }

   $item_detail ='<tr>
      <td>'.$payment["paymentid"].'
      </td>
      <td>'.$payment["name"]. $method.' </td>
      <td>'._d($payment["date"]).'</td>
      <td>'.app_format_money($payment['amount'], $invoice->currency_name).'</td>
   </tr>';
 }

$transaction = ' <table class="table table-bordered" border="0" cellpadding="5" cellspacing="0" width="95%">                 
<thead>
   <tr>
      <th colspan="4">
         Transactions:
      </th>
   </tr>
   <tr>
      <th>
      Payment #   
      </th>
      <th>
      Payment Mode
      </th>
      <th>
      Date	
      </th>
      <th>
      Amount
      </th>
   </tr>
</thead>
<hr>
<tbody>
   '.$item_detail.'
</tbody>
</table>';

pdf_multi_row($transaction, "", $pdf, ($dimensions['wk'] ) - $dimensions['lm']);

$pdf->ln(5);
$invoice_detail = ' <table class="table table-bordered" border="0" cellpadding="5" cellspacing="0" width="95%">                 
<tbody>
   <tr>
      <th style="font-weight:bold;">
      <b>Note:</b>
      </th>
   </tr>
   <tr>
      <th>
      '.$invoice->clientnote.'
      </th>
   </tr>
   <tr>
      <th style="font-weight:bold;">
      <b>Terms & Conditions:</b>
      </th>
   </tr>
   <tr>
      <th>
      '.$invoice->terms.'
      </th>
   </tr>
</tbody>
</table>';
pdf_multi_row($invoice_detail, "", $pdf, ($dimensions['wk'] ) - $dimensions['lm']);

$pdf->ln(2);