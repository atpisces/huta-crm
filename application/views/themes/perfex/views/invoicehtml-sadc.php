<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
   include_once(APPPATH . 'libraries/phpqrcode/qrlib.php');
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

  $Code=einv_generate_tlv_qr_code(array(get_option('invoice_company_name'),get_option('company_vat'),date("Y-m-d H:i:sa",strtotime($invoice->date)),app_format_money($invoice->total, $invoice->currency_name),app_format_money($tax['total_tax'], $invoice->currency_name)));
   if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') 
        $link = "https://"; 
    else
        $link = "http://"; 
    
   $link .= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; 
   
  //$text = "Seller:".get_option('invoice_company_name')."\nVAT Number: ".get_option('company_vat')."\nDate: ".date("Y-m-d H:i:sa",strtotime($invoice->date))."\nAmount: ".app_format_money($invoice->total, $invoice->currency_name)."\nTax : ".app_format_money($tax['total_tax'], $invoice->currency_name);
  //$Code = base64_encode($text); 
  $File_NAme = $invoice->hash;

  $file =  "assets/images/".$invoice->hash.".png";
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
?>


<?php //echo "what is happening"; ?>

<?php if(is_invoice_overdue($invoice)){ ?>
   <div class="row">
      <div class="col-md-12">
         <div class="text-center text-white danger-bg">
            <h5><?php echo _l('overdue_by_days', get_total_days_overdue($invoice->duedate)) ?></h5>
         </div>
      </div>
   </div>
<?php } ?> 
               
<?php
   $id = $this->uri->segment(2);
   // exit;
   $CI =& get_instance();
   $CI->load->model('invoices_model');
   $invoice_result= $CI->invoices_model->get($id);
   // print_r($invoice_result);
   // echo $invoice_result->branchid;
   // exit;
   // $CI->load->model('branches_model');
   $CI->load->model('clients_model');
   //$branch_result= $CI->branches_model->get_brand_and_details($invoice_result->branchid);
   // print_r($branch_result);
   // echo $invoice_result->branchid;
   // exit;
   // echo "My official results here: ".$result;


   $clients_data = $CI->clients_model->get($invoice->clientid);
   // print_r($clients_data);
   // exit;
?>
<?php //print_r($branch_detail); ?>
<div class="mtop15 preview-top-wrapper">
   <div class="row">
      <div class="col-md-3">
         <div class="mbot30">
            <div class="invoice-html-logo">
               <?php echo get_dark_company_logo(); ?>
            </div>
         </div>
      </div>
      <div class="clearfix"></div>
   </div>
   <div class="top" data-sticky data-sticky-class="preview-sticky-header">
      <div class="container preview-sticky-container">
         <div class="row">
            <div class="col-md-12">
               <div class="pull-left">
                  <h3 class="bold no-mtop invoice-html-number no-mbot">
                     <span class="sticky-visible hide">
                        <?php echo format_invoice_number($invoice->id); ?>
                     </span>
                  </h3>
                  <h4 class="invoice-html-status mtop7">
                     <?php echo format_invoice_status($invoice->status, '', true); ?>
                  </h4>
               </div>
               <div class="visible-xs">
                  <div class="clearfix"></div>
               </div>
               <a href="#" class="btn btn-success pull-right mleft5 mtop5 action-button invoice-html-pay-now-top hide sticky-hidden
                  <?php if (($invoice->status != Invoices_model::STATUS_PAID && $invoice->status != Invoices_model::STATUS_CANCELLED
                     && $invoice->total > 0) && found_invoice_mode($payment_modes, $invoice->id, false)) {
                     echo ' pay-now-top';
                  } ?>">
                  <?php echo _l('invoice_html_online_payment_button_text'); ?>
               </a>
                
               <div class="col-md-3" style="display:none;">
                    <select class="form-control" name="Type" onchange="invoice_type(this.value)">
                        <option value="">Select Invoice Type</option>
                        <option value="Simplified">Simplified Invoice</option>
                        <option value="Detail">Detail Invoice</option>
                    </select>
               </div>
               <?php echo form_open($this->uri->uri_string()); ?>
               <button type="submit" name="invoicepdf" id="invoicepdf" value="invoicepdf" class="btn btn-default pull-right action-button mtop5">
                  <i class='fa fa-file-pdf-o'></i>
                  <?php echo _l('clients_invoice_html_btn_download'); ?>
               </button>
               <button type="submit" name="invoicepdf_simplified" id="invoicepdf_simplified" style="display:none;" value="invoicepdf_simplified" class="btn btn-default pull-right action-button mtop5">
                  <i class='fa fa-file-pdf-o'></i>
                  <?php echo _l('clients_invoice_html_btn_download'); ?>
               </button>
               <?php echo form_close(); ?>
               <?php if (is_client_logged_in() && has_contact_permission('invoices')) { ?>
                  <a href="<?php echo site_url('clients/invoices/'); ?>" class="btn btn-default pull-right mtop5 mright5 action-button go-to-portal">
                     <?php echo _l('client_go_to_dashboard'); ?>
                  </a>
               <?php } ?>
               <div class="clearfix"></div>
            </div>
         </div>
      </div>
   </div>
</div>
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
   $comp_name = get_option('invoice_company_name');
	$comp_add = get_option('invoice_company_address');

	$company_name = explode("/",$comp_name);
	$company_address = explode("/",$comp_add);

   $client_name = $estimate->client->company;
	$client_add = $estimate->client->address;
	$customer_name = explode("/",$client_name);
?>
<div class="clearfix"></div> 
<div class="panel_s mtop20" id="detail_invoice">
   <div class="panel-body">
      <div class="col-md-10 col-md-offset-1">
         <div class="row mtop20">
            <div class="col-md-12 col-sm-12 text-center" style="border: 0px solid #000000; background: #ffffff; color: #000000;">
               
               <table class="table" style="margin: 0px;">
                  <tr>
                     <td width="33%" style="padding: 0px;"><?php echo get_dark_company_logo(); ?></td>
                     <td width="33%" style="padding: 0px; text-align: center; vertical-align: middle;"><h4 class="bold invoice-html-number">TAX INVOICE <br>فاتورة ضريبية </h4></td>
                     <td width="33%" style="padding: 0px; text-align: right; vertical-align: middle; font-size: 15px;" align="right">
                        <p><b>Invoice No:</b> <?php echo format_invoice_number($invoice->id); ?></p>
                        <p><b>Invoice Date:</b> <?php echo date('d/m/Y', strtotime($invoice->date)); ?></p>
                     </td>
                  </tr>
               </table>
               
            </div>
            <div class="col-md-12 col-sm-12 text-center" style="padding: 0px; padding-top: 15px;">
               <table class="table table-bordered invoice-detail-table">
                  <tr>
                     <th width="33%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Seller Name:</b></th>
                     <td width="33%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><?php echo $company_name[0].'<br>'.$company_name[1]; ?></td>
                     <th width="33%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>:البائع اسم</b></th>
                  </tr>
                  
                  <tr>
                     <th width="33%" class="invoice-table-column"><b>Seller Address:</b></th>
                     <td width="33%" class="invoice-table-column"><?php echo $company_address[0].'<br>'.$company_address[1]; ?></td>
                     <th width="33%" align="right" class="invoice-table-column"><b>:عنوان البائع</b></th>
                  </tr>
                  <tr>
                     <th width="33%" class="invoice-table-column"><b>Seller VAT No.</b></th>
                     <td width="33%" class="invoice-table-column"><?php echo get_option('company_vat'); ?></td>
                     <th width="33%" align="right" class="invoice-table-column"><b>:رقم ضريبة</b></th>
                  </tr>
               </table>
            </div>
            <div class="col-md-12 col-sm-12 text-center" style="border: 2px solid #000000; background: #ffffff; color: #000000">
               <h4 class="bold invoice-html-number" style="margin: 5px;">Customer Details / بيانات العميل</h4>
            </div>
            <div class="col-md-12 col-sm-12 text-center" style="padding: 0px;">
               <table class="table table-bordered invoice-detail-table">
                  <tr>
                     <th width="20%" class="invoice-table-column"><b>Customer Name:</b></th>
                     <td width="60%" class="invoice-table-column"><b><?php echo $invoice->client->company; ?></b></td>
                     <th width="20%" align="right" class="invoice-table-column"><b>: اسم العميل </b></th>
                  </tr>
                  <tr>
                     <th width="20%" class="invoice-table-column"><b>Client VAT #:</b></th>
                     <td width="60%" class="invoice-table-column"><b><?php echo $clients_data->vat; ?></b></td>
                     <th width="20%" align="right" class="invoice-table-column"><b>:رقم ضريبة :</b></th>
                  </tr>
                  <tr>
                     <th width="20%" class="invoice-table-column"><b>Client Address:</b></th>
                     <td width="60%" class="invoice-table-column"><?php echo $clients_data->address; ?></td>
                     <th width="20%" align="right" class="invoice-table-column"><b>: عنوان العميل </b></th>
                  </tr>
                  
               </table>
            </div>
            <div class="col-md-12 col-sm-12 text-center" style="padding: 0px;">
               <table class="table table-bordered invoice-detail-table">
                  <tr style="background: #bfbfbf; color: #000000">
                     <th align="center" class="invoice-table-column"><b>SL. No. <br> مسلسل</b></th>
                     <th align="center" class="invoice-table-column"><b>Service Description <br> وصف الخدمة</b></th>
                     <th align="center" align="right" class="invoice-table-column"><b>QTY <br> كمية</b></th>
                     <th align="center" class="invoice-table-column"><b>Unit Rate <br> سعر الوحدة</b><br><?=$invoice->currency_name;?></th>
                     <th align="center" class="invoice-table-column"><b>Price Excl. Vat <br> السعر غير شامل ضريبة القيمة المضافة</b><br><?=$invoice->currency_name;?></th>
                     <th align="center" class="invoice-table-column"><b>VAT <br> ضريبة القيمة المضافة</b><br><?=$invoice->currency_name;?></th>
                     <th align="center" class="invoice-table-column"><b>VAT AMT <br> قيمة الضريبة</b><br><?=$invoice->currency_name;?></th>
                     <th align="center" class="invoice-table-column"><b>Amount Inclusive Vat <br> المبلغ شاملاً ضريبة القيمة المضافة</b><br><?=$invoice->currency_name;?></th>
                  </tr>
               <?php
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
               ?>
                  <tr>
                     <td align="center" class="invoice-table-column"><?php echo $sr; ?></td>
                     <td align="center" class="invoice-table-column"><?php echo $row["description"]?><br /><?php echo $row["long_description"]?></td>
                     <td align="center" class="invoice-table-column"><?php echo $row["qty"]."<br>".$row["unit"]; ?></td>
                     <td align="center" class="invoice-table-column"><?php echo app_format_number($row["rate"]); ?></td>
                     <td align="center" class="invoice-table-column"><?php echo app_format_number($excvat); ?></td>
                     <td align="center" class="invoice-table-column"><?php foreach ($items->taxes() as $tax) { echo app_format_number($tax['taxrate']) . '%'; $tt = $tax['taxrate']; $price_excl_vat = $price_excl_vat + $tax['taxrate']; } ?></td>
				<?php
					  
					 $fif_percent_withheld = $excvat * ($tt/100);
					 $fvattotal = $fvattotal + $fif_percent_withheld;
					 $tfif_percent_withheld = $excvat + $fif_percent_withheld;
				?>
                     <td align="center" class="invoice-table-column"><?php echo app_format_number($fif_percent_withheld); ?></td>
                     <td align="center" class="invoice-table-column"><?php echo app_format_number($tfif_percent_withheld); ?></td>
                  </tr>
               <?php
                     $sr++;
                  }
                  $fif_percent_withheld = $sub_sub_total * 0.15;
                  $final_amount = $withoutvat + $fvattotal;
               ?>
                  <tr>
                     <td colspan="8" style="border: 1px solid #ffffff;"></td>
                  </tr>
                  <tr>
                     <td colspan="8" style="border: 1px solid #ffffff;"></td>
                  </tr>
                  <tr style="background: #bfbfbf; color: #000000">
                     <th colspan="4" align="left" class="invoice-table-column" style="border: 1px solid #000000;"><b>Total Amounts</b></th>
                     <th colspan="4" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>المبالغ الإجمالية</b></th>
                  </tr>
                  <tr>
                     <th colspan="3" style="border: 1px solid #000000;"></th>
                     <th colspan="2" align="left" class="invoice-table-column" style="border: 1px solid #000000;"><b>Sub Total</b></th>
                     <th colspan="2" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><?=$invoice->currency_name;?> <b>المجموع الفرعي</b></th>
                     <th colspan="1" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b><?php echo app_format_number($withoutvat); ?></b></th>
                  </tr>
                  <tr>
                     <th colspan="3" style="border: 1px solid #000000;"></th>
                     <th colspan="2" align="left" class="invoice-table-column" style="border: 1px solid #000000;" style="border: 1px solid #000000;"><b>VAT @ 15% </b></th>
                     <th colspan="2" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><?=$invoice->currency_name;?> <b>ضريبة القيمة المضافة @ %15</b></th>
                     <th colspan="1" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b><?php echo app_format_number($fvattotal); ?></b></th>
                  </tr>
                  <tr>
                     <th colspan="3" style="border: 1px solid #000000;"></th>
                     <th colspan="2" align="left" class="invoice-table-column" style="border: 1px solid #000000;"><b>Total</b></th>
                     <th colspan="2" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><?=$invoice->currency_name;?> <b>المجموع</b></th>
                     <th colspan="2" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b><?php echo app_format_number($final_amount); ?></b></th>
                  </tr>
				   
            <?php $paidamount = 0; foreach ($invoice->payments as $payment) {
				   $paidamount = $paidamount + $payment['amount'];
					}
				  ?>
                  <tr>
                     <th colspan="3" style="border: 1px solid #000000;"></th>
                     <th colspan="2" align="left" class="invoice-table-column" style="border: 1px solid #000000;"><b>Paid Amount</b></th>
                     <th colspan="2" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><?=$invoice->currency_name;?> <b>المبلغ المدفوع</b></th>
                     <th colspan="2" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b><?php echo app_format_number($paidamount); ?></b></th>
                  </tr>
			<?php $remamount = $final_amount - $paidamount; ?>
                  <tr>
                     <th colspan="3" style="border: 1px solid #000000;"></th>
                     <th colspan="2" align="left" class="invoice-table-column" style="border: 1px solid #000000; color: #ff0000;"><b>Amount Due</b></th>
                     <th colspan="2" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><?=$invoice->currency_name;?> <b>المبلغ المستحق</b></th>
                     <th colspan="1" align="right" class="invoice-table-column" style="border: 1px solid #000000; color: #ff0000;"><b><?php echo app_format_number($remamount); ?></b></th>
                  </tr>
				   <?php $pdf_custom_fields = get_custom_fields('invoice', array('show_on_pdf' => 1, 'show_on_client_portal' => 1));
				   foreach ($pdf_custom_fields as $field) {
					  $value = get_custom_field_value($invoice->id, $field['id'], 'invoice');
					  if ($value == '') {
						 continue;
					  } 
				   		if($field['name'] == "Converted to SAR")
						{
				   ?>
				   <tr>
					   <th colspan="8" align="right"  style="border: 1px solid #000000;"><b>بيانات الضريبة المضافة بالريال</b></th>
				   </tr>
				   <tr>
                     <th colspan="3" style="border: 1px solid #000000;"></th>
                     <th colspan="2" align="right" class="invoice-table-column" style="border: 1px solid #000000; color: #ff0000;"><b>القيمة الخاضعة للضريبة بالريال</b></th>
                     <th colspan="2" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>ريال</b></th>
                     <th colspan="1" align="right" class="invoice-table-column" style="border: 1px solid #000000; color: #ff0000;"><b><?php echo app_format_number($value); ?></b></th>
                  </tr>
					  
				   <?php 
						}
					   if($field['name'] == "VAT Converted to SAR")
						{
				   ?>
				   <tr>
                     <th colspan="3" style="border: 1px solid #000000;"></th>
                     <th colspan="2" align="right" class="invoice-table-column" style="border: 1px solid #000000; color: #ff0000;"><b>قيمة الضريبة المضافة بالريال</b></th>
                     <th colspan="2" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><b>ريال</b></th>
                     <th colspan="1" align="right" class="invoice-table-column" style="border: 1px solid #000000; color: #ff0000;"><b><?php echo app_format_number($value); ?></b></th>
                  </tr>
					  
				   <?php 
						}
				   } 
				   ?>
				   
                  <tr>
                     <th colspan="5" class="invoice-table-column" style="border-right: 0px !important;">
                        
                        <?php if (!empty($invoice->clientnote)) { ?>
                              <br /><br /><b>Note:</b><br /><?php echo $invoice->clientnote; ?>
                        <?php } ?>
                        <?php if (!empty($invoice->terms)) { ?>
                              <hr />
                              <b><?php echo _l('terms_and_conditions'); ?>:</b><br /><?php echo $invoice->terms; ?>
                        <?php } ?>
                     </th>
                     <th colspan="3" class="invoice-table-column" align="right" style="border-left: 0px !important;"><img src="<?=site_url($qr_code_img)?>" width="200px"></th>
                  </tr>
               </table>
            </div>
            <?php if (get_option('total_to_words_enabled') == 1) { ?>
               <div class="col-md-12 text-center invoice-html-total-to-words">
                  <p class="bold no-margin">
                     <?php echo  _l('num_word') . ': ' . $this->numberword->convert($invoice->total, $invoice->currency_name); ?>
                  </p>
               </div>
            <?php } ?>
            <?php if (count($invoice->attachments) > 0 && $invoice->visible_attachments_to_customer_found == true) { ?>
               <div class="clearfix"></div>
               <div class="invoice-html-files">
                  <div class="col-md-12">
                     <hr />
                     <p class="bold mbot15 font-medium"><?php echo _l('invoice_files'); ?></p>
                  </div>
                  <?php foreach ($invoice->attachments as $attachment) {
                     // Do not show hidden attachments to customer
                     if ($attachment['visible_to_customer'] == 0) {
                        continue;
                     }
                     $attachment_url = site_url('download/file/sales_attachment/' . $attachment['attachment_key']);
                     if (!empty($attachment['external'])) {
                        $attachment_url = $attachment['external_link'];
                     }
                  ?>
                     <div class="col-md-12 mbot10">
                        <div class="pull-left"><i class="<?php echo get_mime_class($attachment['filetype']); ?>"></i></div>
                        <a href="<?php echo $attachment_url; ?>"><?php echo $attachment['file_name']; ?></a>
                     </div>
                  <?php } ?>
               </div>
            <?php } ?>
            <div class="col-md-12">
               <hr />
            </div>
            <div class="col-md-12">
               <hr />
            </div>
            <div class="col-md-12 invoice-html-payments" style="display: none;">
               <?php
               $total_payments = count($invoice->payments);
               if ($total_payments > 0) { ?>
                  <p class="bold mbot15 font-medium"><?php echo _l('invoice_received_payments'); ?>:</p>
                  <table class="table table-hover invoice-payments-table">
                     <thead>
                        <tr>
                           <th><?php echo _l('invoice_payments_table_number_heading'); ?></th>
                           <th><?php echo _l('invoice_payments_table_mode_heading'); ?></th>
                           <th><?php echo _l('invoice_payments_table_date_heading'); ?></th>
                           <th><?php echo _l('invoice_payments_table_amount_heading'); ?></th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php foreach ($invoice->payments as $payment) { ?>
                           <tr>
                              <td>
                                 <span class="pull-left"><?php echo $payment['paymentid']; ?></span>
                                 <?php echo form_open($this->uri->uri_string()); ?>
                                 <button type="submit" value="<?php echo $payment['paymentid']; ?>" class="btn btn-icon btn-default pull-right" name="paymentpdf"><i class="fa fa-file-pdf-o"></i></button>
                                 <?php echo form_close(); ?>
                              </td>
                              <td><?php echo $payment['name']; ?> <?php if (!empty($payment['paymentmethod'])) {
                                                                     echo ' - ' . $payment['paymentmethod'];
                                                                  } ?></td>
                              <td><?php echo _d($payment['date']); ?></td>
                              <td><?php echo app_format_money($payment['amount'], $invoice->currency_name); ?></td>
                           </tr>
                        <?php } ?>
                     </tbody>
                  </table>
                  <hr />
               <?php } else { ?>
                  <h5 class="bold pull-left"><?php echo _l('invoice_no_payments_found'); ?></h5>
                  <div class="clearfix"></div>
                  <hr />
               <?php } ?>
            </div>
            <?php
            // No payments for paid and cancelled
            if (($invoice->status != Invoices_model::STATUS_PAID
               && $invoice->status != Invoices_model::STATUS_CANCELLED
               && $invoice->total > 0)) { ?>
               <div class="col-md-12">
                  <div class="row">
                     <?php
                     $found_online_mode = false;
                     if (found_invoice_mode($payment_modes, $invoice->id, false)) {
                        $found_online_mode = true;
                     ?>
                        <div class="col-md-6 text-left">
                           <p class="bold mbot15 font-medium"><?php echo _l('invoice_html_online_payment'); ?></p>
                           <?php echo form_open($this->uri->uri_string(), array('id' => 'online_payment_form', 'novalidate' => true)); ?>
                           <?php foreach ($payment_modes as $mode) {
                              if (!is_numeric($mode['id']) && !empty($mode['id'])) {
                                 if (!is_payment_mode_allowed_for_invoice($mode['id'], $invoice->id)) {
                                    continue;
                                 }
                           ?>
                                 <div class="radio radio-success online-payment-radio">
                                    <input type="radio" value="<?php echo $mode['id']; ?>" id="pm_<?php echo $mode['id']; ?>" name="paymentmode">
                                    <label for="pm_<?php echo $mode['id']; ?>"><?php echo $mode['name']; ?></label>
                                 </div>
                                 <?php if (!empty($mode['description'])) { ?>
                                    <div class="mbot15">
                                       <?php echo $mode['description']; ?>
                                    </div>
                           <?php }
                              }
                           } ?>
                           <div class="form-group mtop25">
                              <?php if (get_option('allow_payment_amount_to_be_modified') == 1) { ?>
                                 <label for="amount" class="control-label"><?php echo _l('invoice_html_amount'); ?></label>
                                 <div class="input-group">
                                    <input type="number" required max="<?php echo $invoice->total_left_to_pay; ?>" data-total="<?php echo $invoice->total_left_to_pay; ?>" name="amount" class="form-control" value="<?php echo $invoice->total_left_to_pay; ?>">
                                    <span class="input-group-addon">
                                       <?php echo $invoice->symbol; ?>
                                    </span>
                                 </div>
                              <?php } else {
                                 echo '<h4 class="bold mbot25">' . _l('invoice_html_total_pay', app_format_money($invoice->total_left_to_pay, $invoice->currency_name)) . '</h4>';
                              }
                              ?>
                           </div>
                           <div id="pay_button">
                              <input id="pay_now" type="submit" name="make_payment" class="btn btn-success" value="<?php echo _l('invoice_html_online_payment_button_text'); ?>">
                           </div>
                           <input type="hidden" name="hash" value="<?php echo $hash; ?>">
                           <?php echo form_close(); ?>
                        </div>
                     <?php } ?>
                  </div>
               </div>
            <?php } ?>
         </div>
      </div>
   </div>
</div>

<div class="panel_s mtop20" id="simplified_invoice" style="display:none;">
   <div class="panel-body">
      <div class="col-md-10 col-md-offset-1">
         <div class="row mtop20">
         <div class="col-md-12 col-sm-12 text-center">
               <h4 class="bold invoice-html-number">فاتورة ضريبية مبسطة <br>Simplified Tax Invoice</h4>
            </div>
            <div class="col-md-12 col-sm-12 transaction-html-info-col-left" style="margin-top:50px;">
               <div class="col-md-3 col-sm-3" >
                  <b>Invoice Number:</b> 
               </div>
               <div class="col-md-3 col-sm-3" >
                  <b><?php echo format_invoice_number($invoice->id); ?></b> 
               </div>
               <div class="col-md-3 col-sm-3 text-right" >
               <b><?php echo format_invoice_number($invoice->id); ?> </b>
               </div>
               <div class="col-md-3 col-sm-3 text-right" >
               <b>رقم الفاتورة </b>
               </div>
            </div>
            
            <div class="col-md-12 col-sm-12 transaction-html-info-col-left" style="margin-top:20px;">
               <div class="col-md-3 col-sm-3" >
                  <b>Invoice Issue Date:</b> 
               </div>
               <div class="col-md-3 col-sm-3" >
                  <b><?php echo _d($invoice->date); ?></b> 
               </div>
               <div class="col-md-3 col-sm-3 text-right" >
               <b><?php echo _d($invoice->date); ?> </b>
               </div>
               <div class="col-md-3 col-sm-3 text-right" >
               <b>تاريخ إصدار الفاتورة</b>
               </div>
            </div>
            <div class="col-md-12 col-sm-12 transaction-html-info-col-left" style="margin-top:20px;">
               <div class="col-md-3 col-sm-3">
                  <b>VAT Number:</b> 
               </div>
               <div class="col-md-3 col-sm-3">
                  <b><?php echo $invoice->client->vat; ?></b> 
               </div>
               <div class="col-md-3 col-sm-3 text-right" >
                    <b><?php echo $invoice->client->vat; ?></b>
               </div>
               <div class="col-md-3 col-sm-3 text-right" >
               <b>ظريبه الشراء</b>
               </div>
            </div>
            <div class="row">
            <div class="col-md-12">
               <div class="table-responsive">
                  <?php
                     $items = get_items_table_data($invoice, 'invoice');
                  //   echo $items->table();
                  ?>
                  <table class="table table-bordered items items-preview invoice-items-preview">
                     <thead>
                        <tr>
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
                        <?php
                           $items = get_items_table_data($invoice, 'invoice');
                           foreach ($invoice->items as $row) {
                        ?>
                           <tr>
                              <td><?php echo $row["description"]?></td>
                              <td align="center"><?php echo app_format_money($row["rate"], $invoice->currency_name); ?></td>
                              <td align="center"><?php echo app_format_money($row["rate"], $invoice->currency_name); ?></td>
                              <td align="center"><?php echo $row["qty"]?></td>
                              <td align="center"><?php echo app_format_money($row["rate"], $invoice->currency_name); ?></td>
                              <td align="center"><?php echo app_format_money($row["rate"], $invoice->currency_name); ?></td>
                              <td align="center"><?php echo app_format_money($row["rate"], $invoice->currency_name); ?></td>
                              <td align="center"><?php echo app_format_money($row["rate"], $invoice->currency_name); ?></td>
                           </tr>
                        <?php
                              $sr++;
                           }
                        ?>
                     </tbody>
                  </table>
                   </div>
            </div>
            <div class="col-md-12">
               <table class="table table-bordered items items-preview invoice-items-preview">
                  <thead style="background:#415164; color:#FFF;">
                        <tr>
                           <th colspan="2">Total Amounts:</th>
                           <th colspan="2" style="text-align:right;">المبالغ الإجمالية</th>
                        </tr>
                  </thead>
                  <tbody>
                     <tr id="subtotal">
                        <td></td>
                        <td><span class="bold"><?php echo _l('invoice_subtotal'); ?></span>
                        </td>
                        <td style="text-align:right;">المجموع الفرعي</td>
                        <td class="subtotal">
                           <?php echo app_format_money($invoice->subtotal, $invoice->currency_name); ?>
                        </td>
                     </tr>
                     <?php if (is_sale_discount_applied($invoice)) { ?>
                        <tr>
                           <td></td>
                           <td>
                              <span class="bold"><?php echo _l('invoice_discount'); ?>
                                 <?php if (is_sale_discount($invoice, 'percent')) { ?>
                                    (<?php echo app_format_number($invoice->discount_percent, true); ?>%)
                                 <?php } ?></span>
                           </td>
                           <td style="text-align:right;"></td>
                           <td class="discount">
                              <?php echo '-' . app_format_money($invoice->discount_total, $invoice->currency_name); ?>
                           </td>
                        </tr>
                     <?php } ?>
                     <?php
                     foreach ($items->taxes() as $tax) {
                        echo '<tr class="tax-area"><td class="bold">' . $tax['taxname'] . ' (' . app_format_number($tax['taxrate']) . '%)</td><td>' . app_format_money($tax['total_tax'], $invoice->currency_name) . '</td></tr>';
                     }
                     ?>
                     <?php if ((int)$invoice->adjustment != 0) { ?>
                        <tr>
                           <td></td>
                           <td>
                              <span class="bold"><?php echo _l('invoice_adjustment'); ?></span>
                           </td>
                           <td style="text-align:right;"></td>
                           <td class="adjustment">
                              <?php echo app_format_money($invoice->adjustment, $invoice->currency_name); ?>
                           </td>
                        </tr>
                     <?php } ?>
                     <tr>
                        <td></td>
                        <td><span class="bold"><?php echo _l('invoice_total'); ?></span>
                        </td>
                        <td style="text-align:right;">المجموع</td>
                        <td class="total">
                           <?php echo app_format_money($invoice->total, $invoice->currency_name); ?>
                        </td>
                     </tr>
                     <?php if (count($invoice->payments) > 0 && get_option('show_total_paid_on_invoice') == 1) { ?>
                        <tr>
                           <td></td>
                           <td><span class="bold"><?php echo _l('invoice_total_paid'); ?></span></td>
                           <td style="text-align:right;">مجموع المبالغ المدفوعة</td>
                           <td>
                              <?php echo '-' . app_format_money(sum_from_table(db_prefix() . 'invoicepaymentrecords', array('field' => 'amount', 'where' => array('invoiceid' => $invoice->id))), $invoice->currency_name); ?>
                           </td>
                        </tr>
                     <?php } ?>
                     <?php if (get_option('show_credits_applied_on_invoice') == 1 && $credits_applied = total_credits_applied_to_invoice($invoice->id)) { ?>
                        <tr>
                           <td></td>
                           <td><span class="bold"><?php echo _l('applied_credits'); ?></span></td>
                           <td style="text-align:right;"></td>
                           <td>
                              <?php echo '-' . app_format_money($credits_applied, $invoice->currency_name); ?>
                           </td>
                        </tr>
                     <?php } ?>
                     <?php if (get_option('show_amount_due_on_invoice') == 1 && $invoice->status != Invoices_model::STATUS_CANCELLED) { ?>
                        <tr> 
                           <td style="width:40%;"></td>
                           <td><span class="<?php if ($invoice->total_left_to_pay > 0) {
                                                echo 'text-danger ';
                                             } ?>bold"><?php echo _l('invoice_amount_due'); ?></span></td>
                           <td style="text-align:right;">المبلغ المستحق</td>
                           <td>
                              <span class="<?php if ($invoice->total_left_to_pay > 0) {
                                                echo 'text-danger';
                                             } ?>">
                                 <?php echo app_format_money($invoice->total_left_to_pay, $invoice->currency_name); ?>
                              </span>
                           </td>
                        </tr>
                     <?php } ?>
                  </tbody>
               </table>
            </div>
                
            <div class="col-md-4 col-sm-4 col-md-offset-4 col-sm-offset-4 transaction-html-info-col-right text-center" style="margin-top:50px;">
               <img src="<?=site_url($qr_code_img)?>" width="200px">
            </div>
            <?php if (get_option('total_to_words_enabled') == 1) { ?>
               <div class="col-md-12 text-center invoice-html-total-to-words">
                  <p class="bold no-margin">
                     <?php echo  _l('num_word') . ': ' . $this->numberword->convert($invoice->total, $invoice->currency_name); ?>
                  </p>
               </div>
            <?php } ?>
            <?php if (count($invoice->attachments) > 0 && $invoice->visible_attachments_to_customer_found == true) { ?>
               <div class="clearfix"></div>
               <div class="invoice-html-files">
                  <div class="col-md-12">
                     <hr />
                     <p class="bold mbot15 font-medium"><?php echo _l('invoice_files'); ?></p>
                  </div>
                  <?php foreach ($invoice->attachments as $attachment) {
                     // Do not show hidden attachments to customer
                     if ($attachment['visible_to_customer'] == 0) {
                        continue;
                     }
                     $attachment_url = site_url('download/file/sales_attachment/' . $attachment['attachment_key']);
                     if (!empty($attachment['external'])) {
                        $attachment_url = $attachment['external_link'];
                     }
                  ?>
                     <div class="col-md-12 mbot10">
                        <div class="pull-left"><i class="<?php echo get_mime_class($attachment['filetype']); ?>"></i></div>
                        <a href="<?php echo $attachment_url; ?>"><?php echo $attachment['file_name']; ?></a>
                     </div>
                  <?php } ?>
               </div>
            <?php } ?>
            <?php if (!empty($invoice->clientnote)) { ?>
               <div class="col-md-12 invoice-html-note">
                  <b><?php echo _l('invoice_note'); ?></b><br /><br /><?php echo $invoice->clientnote; ?>
               </div>
            <?php } ?>
            <?php if (!empty($invoice->terms)) { ?>
               <div class="col-md-12 invoice-html-terms-and-conditions">
                  <hr />
                  <b><?php echo _l('terms_and_conditions'); ?>:</b><br /><br /><?php echo $invoice->terms; ?>
               </div>
            <?php } ?>
            <div class="col-md-12">
               <hr />
            </div>
            <?php
            // No payments for paid and cancelled
            if (($invoice->status != Invoices_model::STATUS_PAID
               && $invoice->status != Invoices_model::STATUS_CANCELLED
               && $invoice->total > 0)) { ?>
            <?php } ?>
         </div>
      </div>
   </div>
</div>


<script>
   $(function() {
      new Sticky('[data-sticky]');
      var $payNowTop = $('.pay-now-top');
      if ($payNowTop.length && !$('#pay_now').isInViewport()) {
         $payNowTop.removeClass('hide');
         $('.pay-now-top').on('click', function(e) {
            e.preventDefault();
            $('html,body').animate({
                  scrollTop: $("#online_payment_form").offset().top
               },
               'slow');
         });
      }

      $('#online_payment_form').appFormValidator();

      var online_payments = $('.online-payment-radio');
      if (online_payments.length == 1) {
         online_payments.find('input').prop('checked', true);
      }
   });
    
//    id="simplified_invoice"
// id="detail_invoice"
//    
    
    
                 // Detail
                
    function invoice_type(val)
    {
        if(val == "Simplified"){
            $("#simplified_invoice").show();
            $("#detail_invoice").hide();
            $("#invoicepdf_simplified").show();
            $("#invoicepdf").hide();
        }
        else{
            $("#detail_invoice").show();
            $("#simplified_invoice").hide();
            $("#invoicepdf").show();
            $("#invoicepdf_simplified").hide();
        }
    }
</script>
