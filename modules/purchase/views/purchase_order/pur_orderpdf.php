<?php defined('BASEPATH') or exit('No direct script access allowed');

$link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$link_explode = explode("/",$link);
$id_str = explode("?",$link_explode[7]);
$pur_order_id = $id_str[0];
// echo $pur_order_id;

$CI =& get_instance();
$CI->load->model('Purchase_model');
$CI->load->model('currencies_model');
$pur_order = $CI->Purchase_model->get_pur_order($pur_order_id);
$vendor = $CI->Purchase_model->get_vendor($pur_order->vendor);
$pur_order_detail = $CI->Purchase_model->get_pur_order_detail($pur_order_id);
$base_currency = $CI->currencies_model->get_base_currency();
// print_r($pur_order);

// echo $pur_order;
$pur_order_details = '';
$pur_order_details .= '<h2 align="center">PURCHASE ORDER</h2>';
$pur_order_details .= '<table>
      <tr>
      	<th width="65%" align="right"><b>Order No:</b></th>
        <th width="34%" align="right" class="invoice-table-column"><b>'.mb_strtoupper($pur_order->pur_order_number).'</b></th>
      </tr>
      <tr>
      	<th width="65%" align="right"><b>PO Date:</b></th>
        <th width="34%" align="right" class="invoice-table-column"><b>'.date("d-M-Y",strtotime($pur_order->order_date)).'</b></th>
      </tr>
    </table>
    <table>
      <tr><td colspan="2"></td></tr>
      <tr>
      	<td width="49.5%"><b>To:</b></td>
      	<td width="49.5%"><b>From:</b></td>
      </tr>
    </table>
    <table style="border: 1px solid #000000; font-size: 13px;">
      <tr>
      	<td width="49.5%" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;"><b>SUPPLIER NAME: '.$vendor->company.' </b>
      		<br>CR: 1011016534
      		<br>VAT: '.$vendor->vat.'
      	</td>
      	<td width="49.5%" style="border-bottom: 1px solid #000000;"><b>BUYER NAME: '.get_option('invoice_company_name').'</b>
      		<br>CR: 1010590450
      		<br>VAT: '.get_option('company_vat').'
      	</td>
      </tr>
      <tr>
      	<td width="49.5%" style="border-right: 1px solid #000000;">Attn: Mr. Munees
      		<br>Address: '.$vendor->address.'
      		<br>Ph: '.$vendor->phonenumber.' Email: testingvendor@gmail.com
      	</td>
      	<td width="49.5%">Attn: 
      		<br>Address: '.get_option('invoice_company_address').'
      		<br>Ph: '.get_option('invoice_company_phonenumber').' Email: '.get_option('smtp_email').'
      	</td>
      </tr>
    </table>
    <table style="padding: 2px; font-size: 13px;">
    	<tr><td colspan="6"></td></tr>
    	<tr>
    		<td width="5%" align="center" style="border: 1px solid #000000;"><b>ITEM</b></td>
    		<td width="40%" align="center" style="border: 1px solid #000000;"><b>DESCRIPTION</b></td>
    		<td width="8%" align="center" style="border: 1px solid #000000;"><b>UOM</b></td>
    		<td width="8%" align="center" style="border: 1px solid #000000;"><b>QTY</b></td>
    		<td width="23%" align="center" style="border: 1px solid #000000;"><b>UNIT PRICE (Without VAT)</b></td>
    		<td width="15%" align="center" style="border: 1px solid #000000;"><b>Total Amount (Without VAT)</b></td>
    	</tr>';
    $sr = 1;
    $exc_vat_total = 0;
    foreach($pur_order_detail as $row){
    	$items = $CI->Purchase_model->get_items_by_id($row['item_code']);
    	$units = $CI->Purchase_model->get_units_by_id($row['unit_id']);
    	if(isset($units->unit_name)) {
    		$unit_name = $units->unit_name;
    	} else {
    		$unit_name = "-";
    	}
    	$exc_vat_total = $exc_vat_total + $row['total'];
$pur_order_details .= '<tr>
    		<td align="center" style="border: 1px solid #000000;">'.$sr.'</td>
    		<td align="center" style="border: 1px solid #000000;">'.$row['description'].'</td>
    		<td align="center" style="border: 1px solid #000000;">'.$unit_name.'</td>
    		<td align="center" style="border: 1px solid #000000;">'.$row['quantity'].'</td>
    		<td align="center" style="border: 1px solid #000000;">'.app_format_money($row['unit_price'],$base_currency->symbol).'</td>
    		<td align="center" style="border: 1px solid #000000;">'.app_format_money($row['total'],$base_currency->symbol).'</td>
    	</tr>';
    	$sr++;
    }
    $inc_vat_total = 0;
    $vat = $exc_vat_total * (15/100);
    $inc_vat_total = $exc_vat_total + $vat;
$pur_order_details .= '
    	<tr>
    		<td align="center" style="border: 1px solid #000000;"></td>
    		<td align="center" style="border: 1px solid #000000;" colspan="3" rowspan="3"><b><br>Total</b></td>
    		<td align="center" style="border: 1px solid #000000;"><b>Total (Exclusive VAT) =</b></td>
    		<td align="center" style="border: 1px solid #000000;">'.app_format_money($exc_vat_total,$base_currency->symbol).'</td>
    	</tr>
    	<tr>
    		<td align="center" style="border: 1px solid #000000;"></td>
    		<td align="center" style="border: 1px solid #000000;"><b>VAT 15%</b></td>
    		<td align="center" style="border: 1px solid #000000;">'.app_format_money($vat,$base_currency->symbol).'</td>
    	</tr>
    	<tr>
    		<td align="center" style="border: 1px solid #000000;"></td>
    		<td align="center" style="border: 1px solid #000000;"><b>Total (Inclusive VAT) =</b></td>
    		<td align="center" style="border: 1px solid #000000;">'.app_format_money($inc_vat_total,$base_currency->symbol).'</td>
    	</tr>
    	<!--<tr>
    		<td colspan="6" style="border: 1px solid #000000;">Delivery terms: After PO. confirmed, material will be delivered within 10 days.</td>
    	</tr>
    	<tr>
    		<td colspan="6" style="border: 1px solid #000000;">Payment Terms: -100% Advance payment as per each P.O value.</td>
    	</tr>
    	<tr>
    		<td colspan="6" style="border: 1px solid #000000;">Delivery Location: Al Baha</td>
    	</tr>
    	<tr>
    		<td colspan="6" style="border: 1px solid #000000;">The Delivery Note: Invoice and certification shall be hand over with material same time.</td>
    	</tr>-->
    	<tr>
    		<td align="center" width="15%" style="border: 1px solid #000000;"><br><br>Terms<br>:</td>
    		<td align="left" width="84%" style="border: 1px solid #000000;"><br><br>'.$pur_order->terms.'<br></td>
    	</tr>
    	<tr>
    		<td align="center" width="15%" style="border: 1px solid #000000;"><b>Vendor Bank Account Details</b></td>
    		<td align="left" width="84%" style="border: 1px solid #000000;">'.nl2br($vendor->bank_detail).'</td>
    	</tr>
    	<tr>
    		<td align="center" width="15%" style="border: 1px solid #000000;"><br><br>Notes:<br></td>
    		<td align="left" width="84%" style="border: 1px solid #000000;"><br><br>'.$pur_order->vendornote.'<br></td>
    	</tr>
    	<tr>
    		<td align="center" width="15%" style="border: 1px solid #000000;"><br><br>Prepared By:<br></td>
    		<td align="center" width="25%" style="border: 1px solid #000000;"><br><br>Nazim Khan<br></td>
    		<td align="center" width="25%" style="border: 1px solid #000000;"><br><br>Precument Officer:<br></td>
    		<td align="center" width="34%" style="border: 1px solid #000000;"><br><br>Sign:____________________<br></td>
    	</tr>
    	<tr><td colspan="6"></td></tr>
    	<tr>
    		<td align="center" width="69%" colspan="4" style="border: 1px solid #000000; background-color: #bfbfbf;"><b>BUYER\'S AUTHORIZED REPRESENTATIVE</b></td>
    		<td colspan="2" width="30%" style="border: 1px solid #000000;">SUPPLIER\'S ACCEPTANCE</td>
    	</tr>
    	<tr>
    		<td align="center" width="69%" colspan="4" style="border: 1px solid #000000;"><b>'.get_option('invoice_company_name').'</b></td>
    		<td colspan="2" width="30%" style="border: 1px solid #000000;"></td>
    	</tr>
    	<tr>
    		<td align="center" width="24.75%" style="border: 1px solid #000000;"><b>Name: Eng. Zahid Asad</b></td>
    		<td align="center" width="24.75%" style="border: 1px solid #000000;"><b>Name: Mr Wudi</b></td>
    		<td align="center" width="24.75%" style="border: 1px solid #000000;"><b>Name:  Mr. Xu Pei</b></td>
    		<td align="center" width="24.75%" style="border: 1px solid #000000;"><b>Mohammad Mustafa</b></td>
    	</tr>
    	<tr>
    		<td align="center" width="24.75%" style="border: 1px solid #000000;"><b>Project Management Officer</b></td>
    		<td align="center" width="24.75%" style="border: 1px solid #000000;"><b>VP</b></td>
    		<td align="center" width="24.75%" style="border: 1px solid #000000;"><b>CEO</b></td>
    		<td align="center" width="24.75%" style="border: 1px solid #000000;"><b>Tashieed Elsharq</b></td>
    	</tr>
    	<tr>
    		<td align="center" width="24.75%" style="border: 1px solid #000000;">Signature</td>
    		<td align="center" width="24.75%" style="border: 1px solid #000000;">Signature</td>
    		<td align="center" width="24.75%" style="border: 1px solid #000000;">Signature</td>
    		<td align="center" width="24.75%" style="border: 1px solid #000000;">Signature</td>
    	</tr>
    	<tr>
    		<td align="center" width="24.75%" style="border: 1px solid #000000;"><br><br><br></td>
    		<td align="center" width="24.75%" style="border: 1px solid #000000;"><br><br><br></td>
    		<td align="center" width="24.75%" style="border: 1px solid #000000;"><br><br><br></td>
    		<td align="center" width="24.75%" style="border: 1px solid #000000;"><br><br><br></td>
    	</tr>
    </table>';


// These lines should aways at the end of the document left side. Dont indent these lines
$html = <<<EOF
<div class="div_pdf">
$pur_order_details
</div>
EOF;

$pdf->writeHTML($html, true, false, true, false, '');
