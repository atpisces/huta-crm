
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

// print_r($invoice);
//$CI->load->model('branches_model');
$CI =& get_instance();
$CI->load->model('estimates_model');
$CI->load->model('estimates_model');
$CI->load->model('invoices_model');
//$branch_result= $CI->branches_model->get_brand_and_details($invoice_result->branchid);
// print_r($branch_result);
// echo $invoice_result->branchid;
// exit;
// echo "My official results here: ".$result;
$clients_data = $CI->clients_model->get($invoice->clientid);
$project_details = $CI->projects_model->get_project_details($invoice->project_id);
$project_exp = explode("/",$project_details->name);
//print_r($project_details);
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
// print_r($payment_modes_details);
foreach($payment_modes_details as $mode){
  // echo $mode["description"]."<br />";
}
// exit;



$dimensions = $pdf->getPageDimensions();


$info_center_column = '<div style="width:200px !important; margin-left: 20px; text-align:center; border: 2px solid #ffffff; background-color: #ffffff; color: #000000">
<div><span style="font-weight:bold; font-size: 27px; width: 100px !important;">TAX INVOICE / </span><span style="font-size: 27px; width: 100px !important;">فاتورة ضريبة القيمة المضافة</span></div>
</div>';

$exp_company = explode("/",get_option('invoice_company_name'));
$exp_company = explode("/",get_option('invoice_company_name'));

$western_arabic = array('0','1','2','3','4','5','6','7','8','9');
$eastern_arabic = array('٠','١','٢','٣','٤','٥','٦','٧','٨','٩');

$client_vat = str_replace($western_arabic, $eastern_arabic, $clients_data->vat);

//Custom Field Values
$dammam = ""; $customer_po = ""; $ectt_vat = ""; $ectt_vat_arabic = ""; $ectt_sales_order_no = "";
$pdf_custom_fields = get_custom_fields('invoice', array('show_on_pdf' => 1, 'show_on_client_portal' => 1));
foreach ($pdf_custom_fields as $field) {
    $value = get_custom_field_value($invoice->id, $field['id'], 'invoice');
    if ($value == '') {
      continue;
    }
    if($field['name'] == 'Dammam / الدمام'){
      $dammam = $value;
    } else if($field['name'] == 'Customer PO'){
      $customer_po = $value;
    } else if($field['name'] == 'ECTT VAT Reg No'){
      $ectt_vat = $value;
      $ectt_vat_arabic = str_replace($western_arabic, $eastern_arabic, $value);
    } else if($field['name'] == 'ECTT Sales Order No'){
      $ectt_sales_order_no = $value;
    }
}
$info_left_column .= '<div style="float: left; width: 100%;">
               <table cellpadding="0px" style="font-size: 11px;">
                <tr>
                  <td width="99%" colspan="2"><b>Bill To / فاتورة الى</b></td>
                </tr>
               </table>
               <table cellpadding="0px" style="border: 1px solid #000000; font-size: 11px;">
                <tr>
                  <td width="49%">
                    <table cellpadding="3px">
                      <tr>
                        <td align="right" style="border-right-color: #000000;">'.$exp_company[1].'</td>
                        <td style="border-right-color: #000000;"><b>'.$exp_company[0].'</b></td>
                      </tr>
                      <tr>
                        <td align="right" style="border-right-color: #000000;">الدمام :'.$dammam.'</td>
                        <td style="border-right-color: #000000;">Dammam: '.$dammam.'</td>
                      </tr>
                      <tr>
                        <td align="right" style="border-right-color: #000000;"><br /><br />'.get_option('invoice_company_address').'</td>
                        <td style="border-right-color: #000000;"><br /><br />'.get_option('invoice_company_address').'</td>
                      </tr>
                      <tr>
                        <td align="right" style="border-right-color: #000000;"><br /><br />هاتف    : '.get_option('invoice_company_phonenumber').'</td>
                        <td style="border-right-color: #000000;"><br /><br />Tel: '.get_option('invoice_company_phonenumber').'</td>
                      </tr>
                      <tr>
                        <td align="right" style="border-right-color: #000000;"></td>
                        <td style="border-right-color: #000000;"><br /><br /><a href="mailto:'.get_option('smtp_email').'">'.get_option('smtp_email').'</a></td>
                      </tr>
                      <tr>
                        <td align="right" style="border-right-color: #000000;"><br /><br />رقم أمر الشراء الخاص بالعميل: '.$customer_po.'</td>
                        <td style="border-right-color: #000000;"><br /><br />Customer PO : '.$customer_po.'</td>
                      </tr>
                      <tr>
                        <td align="right" style="border-right-color: #000000;"><br /><br />رقم العميل: '.get_option('company_vat').'</td>
                        <td style="border-right-color: #000000;"><br /><br />Vendor Code: '.get_option('company_vat').'</td>
                      </tr>
                    </table>
                  </td>
                  <td>
                    <table cellpadding="2px">
                      <tr>
                        <td >Invoice No:</td>
                        <td style="border-right-color: #000000;">'.format_invoice_number($invoice->id).'</td>
                        <td align="left">'.format_invoice_number($invoice->id).'</td>
                        <td align="right">رقم الفاتورة:</td>
                      </tr>
                      <tr>
                        <td>Invoice Date:</td>
                        <td style="border-right-color: #000000;">'.date("F d, Y",strtotime($invoice->datecreated)).'</td>
                        <td align="left">'.date("F d, Y",strtotime($invoice->datecreated)).'</td>
                        <td align="right">تاريخ الفاتورة:</td>
                      </tr>
                      <tr>
                        <td>Due Date:</td>
                        <td style="border-right-color: #000000;">On Invoice</td>
                        <td align="left">على الفاتورة</td>
                        <td align="right">تاريخ الاستحقاق:</td>
                      </tr>
                      <tr>
                        <td>ECTT VAT Reg No:</td>
                        <td style="border-right-color: #000000;">'.$ectt_vat.'</td>
                        <td align="left">'.$ectt_vat_arabic.'</td>
                        <td align="right">الرقم التعريفي الخاص بضريبة
القيمة المضافة لشركة جي تي اس:</td>
                      </tr>
                      <tr>
                        <td>Project Code:</td>
                        <td style="border-right-color: #000000;">'.$project_details->name.'</td>
                        <td align="left"></td>
                        <td align="right"></td>
                      </tr>
                      <tr>
                        <td>ECTT Sales Order No:</td>
                        <td style="border-right-color: #000000;">'.$ectt_sales_order_no.'</td>
                        <td align="left">'.$ectt_sales_order_no.'</td>
                        <td align="right">رقم أمر البيع لشركة جي تي اس:</td>
                      </tr>
                      <tr>
                        <td><b>Ship To:</b></td>
                        <td style="border-right-color: #000000;">'.$clients_data->company.'</td>
                        <td align="left">'.$clients_data->company.'</td>
                        <td align="right"><b>سافر على متن سفينة ل</b></td>
                      </tr>
                      <tr>
                        <td><b>Customer VAT No:</b></td>
                        <td style="border-right-color: #000000;">'.$clients_data->vat.'</td>
                        <td align="left">'.$client_vat.'</td>
                        <td align="right"><b>القيمة المضافة لشركة : XXX </b></td>
                      </tr>
                    </table>
                  </td>
                </tr>
               </table>
               </div>';

$info_left_column1 = '<div style="float: left; width: 100%;">
               <table cellpadding="3px" style="font-size: 11px;">
                  <tr style="background-color: #bfbfbf; color: #000000">
                     <th width="5%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>التسلسل <br> SL#</b></th>
                     <th width="34%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>الوصف <br> Description</b></th>
                     <th width="20%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">
                      <table>
                        <tr>
                          <td><b>الكمية/شهر</b></td>
                          <td><b>Quantity/Month</b></td>
                        </tr>
                      </table>
                     </th>
                     <th width="19%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">
                      <table>
                        <tr>
                          <td><b>الوحدة</b></td>
                          <td><b>Rate/ Unit</b></td>
                        </tr>
                      </table>
                     </th>
                     <th width="21%" align="center" class="invoice-table-column" style="border: 1px solid #000000;">
                      <table>
                        <tr>
                          <td><b>اجمالي المبلغ
دولار امريكي</b></td>
                          <td><b>Total Amount-'.$invoice->currency_name.'</b></td>
                        </tr>
                      </table>
                     </th>
                  </tr>';
               $sr = 1; $price_excl_vat = 0; $vat_amt = 0; $amount_inclusive_vat = 0;
               $totalvat = 0; $amt_exc_vat = 0; $totalsar = 0;
               $total_amount_exc_vat = 0;
               $items = get_items_table_data($invoice, 'invoice');
               foreach ($invoice->items as $row) {

                  $rate_arabic = str_replace($western_arabic, $eastern_arabic, app_format_money($row["rate"], ''));
                  $project_percentage = $row["rate"] * ($row["unit"]/100);
                  $total_amount_exc_vat = $project_percentage;
                  $total_amount_exc_vat_arabic = str_replace($western_arabic, $eastern_arabic, app_format_money($project_percentage, ''));

                  $estimate_item_tax = $CI->estimates_model->get_item_tax_value($row["id"],$row["rel_id"],'invoice');
         
                  $info_left_column1 .= '<tr>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #ffffff; border-right-color: #000000; border-left-color: #000000;">'.$sr.'</td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #ffffff; border-right-color: #000000; border-left-color: #000000;">'.$row["description"].'<br />'.$row["long_description"].'<br></td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #ffffff; border-right-color: #000000; border-left-color: #000000;">
                      <table>
                        <tr>
                          <td>'.$row["unit"].'%</td>
                          <td>'.$row["unit"].'%</td>
                        </tr>
                      </table>
                      </td>
                      <td align="center" class="invoice-table-column" style="border: 1px solid #ffffff; border-right-color: #000000; border-left-color: #000000;">
                      <table>
                        <tr>
                          <td>'.$rate_arabic.'</td>
                          <td>'.app_format_money($row["rate"], '').'</td>
                        </tr>
                      </table>
                      </td>
                     <td align="center" class="invoice-table-column" style="border: 1px solid #ffffff; border-right-color: #000000; border-left-color: #000000;">
                      <table>
                        <tr>
                          <td>'.$total_amount_exc_vat_arabic.'</td>
                          <td>'.app_format_money($total_amount_exc_vat, '').'</td>
                        </tr>
                      </table>
                     </td>
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
  $vat_amount = $total_amount_exc_vat * (15/100);
  $vat_amount_arabic = str_replace($western_arabic, $eastern_arabic, app_format_money($vat_amount,''));
  $total_inc_vat = $total_amount_exc_vat + $vat_amount;
  $total_inc_vat_arabic = str_replace($western_arabic, $eastern_arabic, app_format_money($total_inc_vat,''));

  $adv_amount = 0;
  $adv_amount_arabic = str_replace($western_arabic, $eastern_arabic, app_format_money($adv_amount,''));

  $balance_amount = $total_inc_vat - $adv_amount;
  $balance_amount_arabic = str_replace($western_arabic, $eastern_arabic, app_format_money($balance_amount,''));

$info_left_column1 .= '
                  <tr>
                     <td colspan="4" align="right" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;">Total Amount Excluding VAT / اجمالي السعر بدون ضريبة القيمة المضافة</td>
                     <td style="background-color: #bfbfbf; color: #000000; border-top-color: #000000; border-bottom-color: #000000; border-right-color: #000000;">
                      <table cellpadding="0px">
                        <tr>
                          <td align="right" style="border-right-color: #000000;">'.$total_amount_exc_vat_arabic.' &nbsp;&nbsp;</td>
                          <td align="right">'.app_format_money($total_amount_exc_vat, '').'</td>
                        </tr>
                      </table>
                     </td>
                  </tr>
                  <tr>
                     <td colspan="3" align="right" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;">VAT/ ضريبة قيمة مضافة
                     </td>
                     <td align="center" style="border: 1px solid #000000; border-top-color: #000000;">15%</td>
                     <td style="background-color: #bfbfbf; color: #000000; border-top-color: #000000; border-bottom-color: #000000; border-right-color: #000000;">
                      <table cellpadding="0px">
                        <tr>
                          <td align="right" style="border-right-color: #000000;">'.$vat_amount_arabic.' &nbsp;&nbsp;</td>
                          <td align="right">'.app_format_money($vat_amount, '').'</td>
                        </tr>
                      </table>
                     </td>
                  </tr>
                  <tr>
                     <td colspan="4" align="right" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;">Total Amount Including VAT/ اجمالي السعر مع ضريبة القيمة المضافة</td>
                     <td style="background-color: #bfbfbf; color: #000000; border-top-color: #000000; border-bottom-color: #000000; border-right-color: #000000;">
                      <table cellpadding="0px">
                        <tr>
                          <td align="right" style="border-right-color: #000000;">'.$total_inc_vat_arabic.' &nbsp;&nbsp;</td>
                          <td align="right">'.app_format_money($total_inc_vat, '').'</td>
                        </tr>
                      </table>
                     </td>
                  </tr>
                  <tr>
                     <td colspan="4" align="right" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;">Advance Amount Received/ دفعات مقدمة مستلمة</td>
                     <td style="background-color: #bfbfbf; color: #000000; border-top-color: #000000; border-bottom-color: #000000; border-right-color: #000000;">
                      <table cellpadding="0px">
                        <tr>
                          <td align="right" style="border-right-color: #000000;">'.$adv_amount_arabic.' &nbsp;&nbsp;</td>
                          <td align="right">'.app_format_money($adv_amount, '').'</td>
                        </tr>
                      </table>
                     </td>
                  </tr>
                  <tr>
                     <td colspan="4" align="right" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000;">Balance Amount Receivable / اجمالي المبلغ المستح</td>
                     <td style="background-color: #bfbfbf; color: #000000; border-top-color: #000000; border-bottom-color: #000000; border-right-color: #000000;">
                      <table cellpadding="0px">
                        <tr>
                          <td align="right" style="border-right-color: #000000;">'.$balance_amount_arabic.' &nbsp;&nbsp;</td>
                          <td align="right">'.app_format_money($balance_amount, '').'</td>
                        </tr>
                      </table>
                     </td>
                  </tr>
                  <tr>
                     <td colspan="5" align="center" class="invoice-table-column" style="border: 1px solid #000000; border-top-color: #000000; background-color: #fff000;">'.$invoice->currency_name.' '.ucfirst($CI->numberword->convert($balance_amount, '')).'</td>
                  </tr>';

$info_left_column1 .= '</table>
                <br /><br />
                <table cellpadding="10px">
                  <tr>
                    <td width="2%"></td>
                    <td width="50%" style="border: 1px solid #000000; margin-left: 100px; border-radius: 50%;"><u>Our Bank Details</u>';
              if (!empty($invoice->clientnote)) {
                    $info_left_column1 .= '<br><b style="font-weight: bold;">Note:</b><br>'.$invoice->clientnote;
              }

              foreach($payment_modes_details as $mode){
                $info_left_column1 .= "<br><b>".$mode["name"]."</b><br>".$mode["description"];
              }

              if (!empty($invoice->terms)) {
                    $info_left_column1 .= '<hr />
                    <b>Terms and Conditions:</b><br>'.$invoice->terms;
              }
$info_left_column1 .= '</td>
                  </tr>
                </table>    
              </div>';
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

