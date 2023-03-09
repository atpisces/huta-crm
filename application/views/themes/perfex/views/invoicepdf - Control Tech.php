<?php

defined('BASEPATH') or exit('No direct script access allowed');

$dimensions = $pdf->getPageDimensions();

$company_detail_header_left = '<div style="float:left; width:100%;">
   <div style="float:left; width:40%;">
         <img src="'.site_url("assets/images/logo.jpg").'" style="float:left; width:60%; margin-bottom:20px;"  >
   </div>
   <div style="float:left; width:60%;">
         <p>
            C.R Number: 4030423068
         </p>
         <p>
            VAT Number: 310208394300003
         </p>
         <p>
            Chamber No: 201000411034
         </p>
   </div>
</div>';

$company_detail_header_right = '<div style="float:left; width:100%;">
      <div style="float:left; width:10%;">
         <div style="float:left; width:3px; height:100px; background:#000"></div>
      </div>
      <div style="float:left; width:90%;">
         <p>
            Office 501, Rawan Plaza 
            6587 Fayd Al Samaa St
            Ruwais District, Jeddah                             
         </p>                     
      </div>
</div>';

pdf_multi_row($company_detail_header_left, $company_detail_header_right, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

$pdf->ln(5);

$invoice_detail = '<table class="table table-bordered" border="1" cellpadding="0" cellspacing="0" width="100%">
      <tr>
         <td colspan="6" align="center">
            <b>
            Advance Payment VAT Invoice
            <br>
            فاتورة دفعه مقدمة ضريبية
            </b>
         </td>
      </tr>
      <tr>
         <td colspan="2">
            <b>Addressed to (Buyer)</b>
         </td>
         <td colspan="2"><b>Order No</b></td>
         <td colspan="2"><b>Date</b></td>
      </tr>
      <tr>
         <td colspan="2" rowspan="3">Address goes here...</td>
         <td colspan="2">INV21-174</td>
         <td colspan="2">5th Oct 2021</td>
      </tr>
      <tr>
         <td colspan="4"><b>Buyer VAT Number</b></td>
      </tr>
      <tr>
         <td colspan="4">353453</td>
      </tr>
      <tr>
         <td colspan="2"><b>Seller:</b></td>
         <td colspan="4"><b>Seller VAT Number:</b></td>
      </tr>
      <tr>
         <td colspan="2" rowspan="5">Address goes here...</td>
         <td colspan="4">342342342</td>
      </tr>
      <tr>
         <td colspan="4"><b>Client P.O</b></td>
      </tr>
      <tr>
         <td colspan="4">P.O Address......</td>
      </tr>
      <tr>
         <td colspan="2"><b>Dispatch from</b></td>
         <td colspan="2"><b>Delivery to</b></td>
      </tr>
      <tr>
         <td colspan="2">Jeddah</td>
         <td colspan="2">Dammam Industrial City</td>
      </tr>
      <tr>
         <th><b>No</b></th>
         <th colspan="2"><b>Description of Goods</b></th>
         <th><b>Qty</b></th>
         <th><b>Unit Rate</b></th>
         <th><b>Amount (SAR)</b></th>
      </tr>
      <tr>
         <td align="center">1</td>
         <td colspan="2">abc</td>
         <td align="center">00</td>
         <td align="center">00</td>
         <td align="center">00</td>
      </tr>
      <tr>
         <th colspan="5" style="text-align:center;"><b>Discount</b></th>
         <th style="text-align:center;">00</th>
      </tr>
      <tr>
         <th colspan="5" style="text-align:center;"><b>Total Net Value in SAR</b></th>
         <th style="text-align:center;"><b>00</b></th>
      </tr>
      <tr>
         <th colspan="5" style="text-align:center;"><b>Due Value: Advance Payment 60% of Total Value</b></th>
         <th style="text-align:center;"><b>00</b></th>
      </tr>
      <tr>
         <th colspan="5" style="text-align:center;"><b>VAT 15% of Invoice value</b></th>
         <th style="text-align:center;"><b>00</b></th>
      </tr>
      <tr>
         <th colspan="5" style="text-align:center;"><b>Total Invoice Payable Amount in SAR</b></th>
         <th style="text-align:center;"><b>00</b></th>
      </tr>
      <tr>
         <th colspan="6" style="text-align:center;"><b>For Control Technologies Es.</b></th>
      </tr>
      <tr>
         <td colspan="3" style="text-align:center;"><b>Stamp</b></td>
         <td colspan="3" style="text-align:center;"><b>Confirmed By</b></td>
      </tr>
      <tr>
         <td colspan="3"></td>
         <td colspan="3"></td>
      </tr>
      <tr>
         <td colspan="6"></td>
      </tr>
   </table>';

   pdf_multi_row($invoice_detail, "", $pdf, ($dimensions['wk'] ) - $dimensions['lm']);

   $pdf->ln(2);

$company_detail_footer_left = '<div style="float:left; width:100%;">
         <div style="float:left; width:50%;">
            <p>
            Control Technologies Est.                      
            </p>
            <p>
            Jeddah, Saudi Arabia 
            </p>
            <p>
            Tel: +966 126612420
            </p>
            <p>
            Fax: +966 126612420
            </p>
         </div>
         <div style="float:left; width:50%;">
            <p>
            Mail: info@controltech-sa.com
            </p>
            <p>
            Website: www.controltech-sa.com
            </p>
            <p>
            IBAN: SA39 6010 0005 1950 2173 2001 
            </p>
            <p>
            Bank Name: Bank AlJazira 
            </p>
         </div>
      </div>';

$company_detail_footer_right = '<div style="float:left; width:100%;">
     
      <div style="float:left; width:100%; text-align:right;">
            <img src="'.site_url("assets/images/qr-code.png").'" style="width:200px;"  >
      </div>
</div>';
pdf_multi_row($company_detail_footer_left, $company_detail_footer_right, $pdf, ($dimensions['wk']  / 2) - $dimensions['lm']);
