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
<h4 style="font-weight:bold; margin-left:50%;">فاتورة ضريبية<br> Tax Invoice</h4>
</div>';

$info_left_column = '<div style="float:left; width:60%;">
    <div style="float:left; width:75%; text-align:center;">
        <table class="table" border="1" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <th>Invoice Number:</th>
                <td></td>
                <td></td>
                <th style="text-align:right;">رقم الفاتورة</th>
            </tr>
        </table>
        <table class="table" border="1" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <th>Invoice Issue Date:</th>
                <td></td>
                <td></td>
                <th style="text-align:right;">تاريخ إصدار الفاتورة</th>
            </tr>
            <tr>
                <th>Due Date:</th>
                <td></td>
                <td></td>
                <th style="text-align:right;">تاريخ الاستحقاق</th>
            </tr>
        </table>
    </div>
</div>';

$info_right_column = '<div style="float:left; width:60%;">
                        <img src="'.site_url("assets/images/qr-code.png").'" style="width: 200px;">
                     </div>';

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
pdf_multi_row($info_left_column,$info_right_column, $pdf, ($dimensions['wk'] / 1.5) - $dimensions['lm']);

$pdf->ln(5);

//$organization_info = '<div style="color:#424242;">';

$organization_info = '<table class="table table-bordered" border="1" cellpadding="0" cellspacing="0" width="100%">
                 
<tr>
   <th>Name</th>
   <td></td>
   <td></td>
   <th style="text-align:right;">اسم</th>
</tr>
<tr>
   <th>Building No.</th>
   <td></td>
   <td></td>
   <th style="text-align:right;">رقم المبنى</th>
</tr>
<tr>
   <th>Street Name</th>
   <td></td>
   <td></td>
   <th style="text-align:right;">اسم الشارع</th>
</tr>
<tr>
   <th>District</th>
   <td></td>
   <td></td>
   <th style="text-align:right;">يصرف</th>
</tr>
<tr>
   <th>City</th>
   <td></td>
   <td></td>
   <th style="text-align:right;">مدينة</th>
</tr>
<tr>
   <th>Country</th>
   <td></td>
   <td></td>
   <th style="text-align:right;">دولة</th>
</tr>
<tr>
   <th>Postal Code</th>
   <td></td>
   <td></td>
   <th style="text-align:right;">رمز بريدي</th>
</tr>
<tr>
   <th>Additional Number</th>
   <td></td>
   <td></td>
   <th style="text-align:right;">رقم إضافي</th>
</tr>
<tr>
   <th>Vat Number</th>
   <td></td>
   <td></td>
   <th style="text-align:right;">ظريبه الشراء</th>
</tr>
<tr>
   <th>Other Sale ID</th>
   <td></td>
   <td></td>
   <th style="text-align:right;">معرف بيع آخر</th>
</tr>
</table>';

// $organization_info .= format_organization_info();

// $organization_info .= '</div>';

// Bill to
// $invoice_info = '<b>' . _l('invoice_bill_to') . ':</b>';
// $invoice_info .= '<div style="color:#424242;">';
//     $invoice_info .= format_customer_info($invoice, 'invoice', 'billing');
// $invoice_info .= '</div>';

$buyer_info = '<table class="table table-bordered" border="1" cellpadding="0" cellspacing="0" width="100%">
                     <tr>
                        <th style="text-align:left;">Name</th>
                        <td></td>
                        <td></td>
                        <th style="text-align:right;">اسم</th>
                     </tr>
                     <tr>
                        <th style="text-align:left;">Building No.</th>
                        <td></td>
                        <td></td>
                        <th style="text-align:right;">رقم المبنى</th>
                     </tr>
                     <tr>
                        <th style="text-align:left;">Street Name</th>
                        <td></td>
                        <td></td>
                        <th style="text-align:right;">اسم الشارع</th>
                     </tr>
                     <tr>
                        <th style="text-align:left;">District</th>
                        <td></td>
                        <td></td>
                        <th style="text-align:right;">يصرف</th>
                     </tr>
                     <tr>
                        <th style="text-align:left;">City</th>
                        <td></td>
                        <td></td>
                        <th style="text-align:right;">مدينة</th>
                     </tr>
                     <tr>
                        <th style="text-align:left;">Country</th>
                        <td></td>
                        <td></td>
                        <th style="text-align:right;">دولة</th>
                     </tr>
                     <tr>
                        <th style="text-align:left;">Postal Code</th>
                        <td></td>
                        <td></td>
                        <th style="text-align:right;">رمز بريدي</th>
                     </tr>
                     <tr>
                        <th style="text-align:left;">Additional Number</th>
                        <td></td>
                        <td></td>
                        <th style="text-align:right;">رقم إضافي</th>
                     </tr>
                     <tr>
                        <th style="text-align:left;">Vat Number</th>
                        <td></td>
                        <td></td>
                        <th style="text-align:right;">ظريبه الشراء</th>
                     </tr>
                     <tr>
                        <th style="text-align:left;">Other Buyer ID</th>
                        <td></td>
                        <td></td>
                        <th style="text-align:right;">معرف المشتري الآخر</th>
                     </tr>
                  </table>';
// ship to to
// if ($invoice->include_shipping == 1 && $invoice->show_shipping_on_invoice == 1) {
//     $invoice_info .= '<br /><b>' . _l('ship_to') . ':</b>';
//     $invoice_info .= '<div style="color:#424242;">';
//     $invoice_info .= format_customer_info($invoice, 'invoice', 'shipping');
//     $invoice_info .= '</div>';
// }

pdf_multi_row($organization_info, $buyer_info, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

$pdf->ln(5);
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
   <tr>
      <td>
         Item A
         البند أ
      </td>
      <td>200.00 SAR</td>
      <td>1</td>
      <td>200 SAR</td>
      <td>0</td>
      <td>15%</td>
      <td>30.00 SAR</td>
      <td>230.00 SAR</td>
   </tr>
   <tr>
      <td>
         Item B
         البند ب
      </td>
      <td>200.00 SAR</td>
      <td>1</td>
      <td>200 SAR</td>
      <td>0</td>
      <td>15%</td>
      <td>30.00 SAR</td>
      <td>230.00 SAR</td>
   </tr>
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
      $343.00
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
      $343.00
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
      $343.00
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
      $0.00
      </td>
   </tr>
</tbody>
</table>';
pdf_multi_row($invoice_total, "", $pdf, ($dimensions['wk'] ) - $dimensions['lm']);

$pdf->ln(5);

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
   <tr>
      <td>
      1
      </td>
      <td>
      Bank  
      </td>
      <td align="right">
      2021-10-25	
      </td>
      <td>
      $343.00
      </td>
   </tr>
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
      sdfsdfs
      </th>
   </tr>
   <tr>
      <th style="font-weight:bold;">
      <b>Terms & Conditions:</b>
      </th>
   </tr>
   <tr>
      <th>
      fsdfdf
      </th>
   </tr>
</tbody>
</table>';
pdf_multi_row($invoice_detail, "", $pdf, ($dimensions['wk'] ) - $dimensions['lm']);


$pdf->ln(2);
// $invoice_info .= '<br />' . _l('invoice_data_date') . ' ' . _d($invoice->date) . '<br />';

// $invoice_info = hooks()->apply_filters('invoice_pdf_header_after_date', $invoice_info, $invoice);

// if (!empty($invoice->duedate)) {
//     $invoice_info .= _l('invoice_data_duedate') . ' ' . _d($invoice->duedate) . '<br />';
//     $invoice_info = hooks()->apply_filters('invoice_pdf_header_after_due_date', $invoice_info, $invoice);
// }

// if ($invoice->sale_agent != 0 && get_option('show_sale_agent_on_invoices') == 1) {
//     $invoice_info .= _l('sale_agent_string') . ': ' . get_staff_full_name($invoice->sale_agent) . '<br />';
//     $invoice_info = hooks()->apply_filters('invoice_pdf_header_after_sale_agent', $invoice_info, $invoice);
// }

// if ($invoice->project_id != 0 && get_option('show_project_on_invoice') == 1) {
//     $invoice_info .= _l('project') . ': ' . get_project_name_by_id($invoice->project_id) . '<br />';
//     $invoice_info = hooks()->apply_filters('invoice_pdf_header_after_project_name', $invoice_info, $invoice);
// }

// $invoice_info = hooks()->apply_filters('invoice_pdf_header_before_custom_fields', $invoice_info, $invoice);

// foreach ($pdf_custom_fields as $field) {
//     $value = get_custom_field_value($invoice->id, $field['id'], 'invoice');
//     if ($value == '') {
//         continue;
//     }
//     $invoice_info .= $field['name'] . ': ' . $value . '<br />';
// }

// $invoice_info = hooks()->apply_filters('invoice_pdf_header_after_custom_fields', $invoice_info, $invoice);

// $left_info  = $swap == '1' ? $invoice_info : $organization_info;
// $right_info = $swap == '1' ? $organization_info : $invoice_info;


// $pdf->Ln(hooks()->apply_filters('pdf_info_and_table_separator', 6));

// $items = get_items_table_data($invoice, 'invoice', 'pdf');

// $tblhtml = $items->table();

//$pdf->writeHTML($tblhtml, true, false, false, false, '');

// $pdf->Ln(8);

// $tbltotal = '';
// $tbltotal .= '<table cellpadding="6" style="font-size:' . ($font_size + 4) . 'px">';
// $tbltotal .= '
// <tr>
//     <td align="right" width="85%"><strong>' . _l('invoice_subtotal') . '</strong></td>
//     <td align="right" width="15%">' . app_format_money($invoice->subtotal, $invoice->currency_name) . '</td>
// </tr>';

// if (is_sale_discount_applied($invoice)) {
//     $tbltotal .= '
//     <tr>
//         <td align="right" width="85%"><strong>' . _l('invoice_discount');
//     if (is_sale_discount($invoice, 'percent')) {
//         $tbltotal .= ' (' . app_format_number($invoice->discount_percent, true) . '%)';
//     }
//     $tbltotal .= '</strong>';
//     $tbltotal .= '</td>';
//     $tbltotal .= '<td align="right" width="15%">-' . app_format_money($invoice->discount_total, $invoice->currency_name) . '</td>
//     </tr>';
// }

// foreach ($items->taxes() as $tax) {
//     $tbltotal .= '<tr>
//     <td align="right" width="85%"><strong>' . $tax['taxname'] . ' (' . app_format_number($tax['taxrate']) . '%)' . '</strong></td>
//     <td align="right" width="15%">' . app_format_money($tax['total_tax'], $invoice->currency_name) . '</td>
// </tr>';
// }

// if ((int) $invoice->adjustment != 0) {
//     $tbltotal .= '<tr>
//     <td align="right" width="85%"><strong>' . _l('invoice_adjustment') . '</strong></td>
//     <td align="right" width="15%">' . app_format_money($invoice->adjustment, $invoice->currency_name) . '</td>
// </tr>';
// }

// $tbltotal .= '
// <tr style="background-color:#f0f0f0;">
//     <td align="right" width="85%"><strong>' . _l('invoice_total') . '</strong></td>
//     <td align="right" width="15%">' . app_format_money($invoice->total, $invoice->currency_name) . '</td>
// </tr>';

// if (count($invoice->payments) > 0 && get_option('show_total_paid_on_invoice') == 1) {
//     $tbltotal .= '
//     <tr>
//         <td align="right" width="85%"><strong>' . _l('invoice_total_paid') . '</strong></td>
//         <td align="right" width="15%">-' . app_format_money(sum_from_table(db_prefix().'invoicepaymentrecords', [
//         'field' => 'amount',
//         'where' => [
//             'invoiceid' => $invoice->id,
//         ],
//     ]), $invoice->currency_name) . '</td>
//     </tr>';
// }

// if (get_option('show_credits_applied_on_invoice') == 1 && $credits_applied = total_credits_applied_to_invoice($invoice->id)) {
//     $tbltotal .= '
//     <tr>
//         <td align="right" width="85%"><strong>' . _l('applied_credits') . '</strong></td>
//         <td align="right" width="15%">-' . app_format_money($credits_applied, $invoice->currency_name) . '</td>
//     </tr>';
// }

// if (get_option('show_amount_due_on_invoice') == 1 && $invoice->status != Invoices_model::STATUS_CANCELLED) {
//     $tbltotal .= '<tr style="background-color:#f0f0f0;">
//        <td align="right" width="85%"><strong>' . _l('invoice_amount_due') . '</strong></td>
//        <td align="right" width="15%">' . app_format_money($invoice->total_left_to_pay, $invoice->currency_name) . '</td>
//    </tr>';
// }

// $tbltotal .= '</table>';
//$pdf->writeHTML($tbltotal, true, false, false, false, '');

// if (get_option('total_to_words_enabled') == 1) {
//     // Set the font bold
//     $pdf->SetFont($font_name, 'B', $font_size);
//     $pdf->writeHTMLCell('', '', '', '', _l('num_word') . ': ' . $CI->numberword->convert($invoice->total, $invoice->currency_name), 0, 1, false, true, 'C', true);
//     // Set the font again to normal like the rest of the pdf
//     $pdf->SetFont($font_name, '', $font_size);
//     $pdf->Ln(4);
// }

// if (count($invoice->payments) > 0 && get_option('show_transactions_on_invoice_pdf') == 1) {
//     $pdf->Ln(4);
//     $border = 'border-bottom-color:#000000;border-bottom-width:1px;border-bottom-style:solid; 1px solid black;';
//     $pdf->SetFont($font_name, 'B', $font_size);
//     $pdf->Cell(0, 0, _l('invoice_received_payments') . ":", 0, 1, 'L', 0, '', 0);
//     $pdf->SetFont($font_name, '', $font_size);
//     $pdf->Ln(4);
//     $tblhtml = '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="0">
//         <tr height="20"  style="color:#000;border:1px solid #000;">
//         <th width="25%;" style="' . $border . '">' . _l('invoice_payments_table_number_heading') . '</th>
//         <th width="25%;" style="' . $border . '">' . _l('invoice_payments_table_mode_heading') . '</th>
//         <th width="25%;" style="' . $border . '">' . _l('invoice_payments_table_date_heading') . '</th>
//         <th width="25%;" style="' . $border . '">' . _l('invoice_payments_table_amount_heading') . '</th>
//     </tr>';
//     $tblhtml .= '<tbody>';
//     foreach ($invoice->payments as $payment) {
//         $payment_name = $payment['name'];
//         if (!empty($payment['paymentmethod'])) {
//             $payment_name .= ' - ' . $payment['paymentmethod'];
//         }
//         $tblhtml .= '
//             <tr>
//             <td>' . $payment['paymentid'] . '</td>
//             <td>' . $payment_name . '</td>
//             <td>' . _d($payment['date']) . '</td>
//             <td>' . app_format_money($payment['amount'], $invoice->currency_name) . '</td>
//             </tr>
//         ';
//     }
//     $tblhtml .= '</tbody>';
//     $tblhtml .= '</table>';
//     $pdf->writeHTML($tblhtml, true, false, false, false, '');
// }

// if (found_invoice_mode($payment_modes, $invoice->id, true, true)) {
//     $pdf->Ln(4);
//     $pdf->SetFont($font_name, 'B', $font_size);
//     $pdf->Cell(0, 0, _l('invoice_html_offline_payment') . ":", 0, 1, 'L', 0, '', 0);
//     $pdf->SetFont($font_name, '', $font_size);

//     foreach ($payment_modes as $mode) {
//         if (is_numeric($mode['id'])) {
//             if (!is_payment_mode_allowed_for_invoice($mode['id'], $invoice->id)) {
//                 continue;
//             }
//         }
//         if (isset($mode['show_on_pdf']) && $mode['show_on_pdf'] == 1) {
//             $pdf->Ln(1);
//             $pdf->Cell(0, 0, $mode['name'], 0, 1, 'L', 0, '', 0);
//             $pdf->Ln(2);
//             $pdf->writeHTMLCell('', '', '', '', $mode['description'], 0, 1, false, true, 'L', true);
//         }
//     }
// }

// if (!empty($invoice->clientnote)) {
//     $pdf->Ln(4);
//     $pdf->SetFont($font_name, 'B', $font_size);
//     $pdf->Cell(0, 0, _l('invoice_note'), 0, 1, 'L', 0, '', 0);
//     $pdf->SetFont($font_name, '', $font_size);
//     $pdf->Ln(2);
//     $pdf->writeHTMLCell('', '', '', '', $invoice->clientnote, 0, 1, false, true, 'L', true);
// }

// if (!empty($invoice->terms)) {
//     $pdf->Ln(4);
//     $pdf->SetFont($font_name, 'B', $font_size);
//     $pdf->Cell(0, 0, _l('terms_and_conditions') . ":", 0, 1, 'L', 0, '', 0);
//     $pdf->SetFont($font_name, '', $font_size);
//     $pdf->Ln(2);
//     $pdf->writeHTMLCell('', '', '', '', $invoice->terms, 0, 1, false, true, 'L', true);
// }
