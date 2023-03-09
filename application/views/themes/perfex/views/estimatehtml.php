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
                  <h3 class="bold no-mtop estimate-html-number no-mbot">
                     <span class="sticky-visible hide">
                     <?php echo format_estimate_number($estimate->id); ?>
                     </span>
                  </h3>
                  <h4 class="estimate-html-status mtop7">
                     <?php echo format_estimate_status($estimate->status,'',true); ?>
                  </h4>
               </div>
               <div class="visible-xs">
                  <div class="clearfix"></div>
               </div>
               <?php
                  // Is not accepted, declined and expired
                  if ($estimate->status != 4 && $estimate->status != 3 && $estimate->status != 5) {
                    $can_be_accepted = true;
                    if($identity_confirmation_enabled == '0'){
                      echo form_open($this->uri->uri_string(), array('class'=>'pull-right mtop7 action-button'));
                      echo form_hidden('estimate_action', 4);
                      echo '<button type="submit" data-loading-text="'._l('wait_text').'" autocomplete="off" class="btn btn-success action-button accept"><i class="fa fa-check"></i> '._l('clients_accept_estimate').'</button>';
                      echo form_close();
                    } else {
                      echo '<button type="button" id="accept_action" class="btn btn-success mright5 mtop7 pull-right action-button accept"><i class="fa fa-check"></i> '._l('clients_accept_estimate').'</button>';
                    }
                  } else if($estimate->status == 3){
                    if (($estimate->expirydate >= date('Y-m-d') || !$estimate->expirydate) && $estimate->status != 5) {
                      $can_be_accepted = true;
                      if($identity_confirmation_enabled == '0'){
                        echo form_open($this->uri->uri_string(),array('class'=>'pull-right mtop7 action-button'));
                        echo form_hidden('estimate_action', 4);
                        echo '<button type="submit" data-loading-text="'._l('wait_text').'" autocomplete="off" class="btn btn-success action-button accept"><i class="fa fa-check"></i> '._l('clients_accept_estimate').'</button>';
                        echo form_close();
                      } else {
                        echo '<button type="button" id="accept_action" class="btn btn-success mright5 mtop7 pull-right action-button accept"><i class="fa fa-check"></i> '._l('clients_accept_estimate').'</button>';
                      }
                    }
                  }
                  // Is not accepted, declined and expired
                  if ($estimate->status != 4 && $estimate->status != 3 && $estimate->status != 5) {
                    echo form_open($this->uri->uri_string(), array('class'=>'pull-right action-button mright5 mtop7'));
                    echo form_hidden('estimate_action', 3);
                    echo '<button type="submit" data-loading-text="'._l('wait_text').'" autocomplete="off" class="btn btn-default action-button accept"><i class="fa fa-remove"></i> '._l('clients_decline_estimate').'</button>';
                    echo form_close();
                  }
                  ?>
               <?php echo form_open($this->uri->uri_string(), array('class'=>'pull-right action-button')); ?>
               <button type="submit" name="estimatepdf" class="btn btn-default action-button download mright5 mtop7" value="estimatepdf">
               <i class="fa fa-file-pdf-o"></i>
               <?php echo _l('clients_invoice_html_btn_download'); ?>
               </button>
               <?php echo form_close(); ?>
               <?php if(is_client_logged_in() && has_contact_permission('estimates')){ ?>
               <a href="<?php echo site_url('clients/estimates/'); ?>" class="btn btn-default pull-right mright5 mtop7 action-button go-to-portal">
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
                     <td width="33%" style=" background: #bfbfbf; padding: 0px; text-align: center; vertical-align: middle;"><h4 class="bold invoice-html-number">PROFORMA INVOICE <br>فاتورة بيرفورما </h4></td>
                     <td width="33%" style="padding: 0px; text-align: right; vertical-align: middle; font-size: 15px;" align="right">
                        <p><b>Proforma No:</b> <?php echo format_estimate_number($estimate->id); ?></p>
                        <p><b>Proforma Date:</b> <?php echo date('d/m/Y', strtotime($estimate->date)); ?></p>
                     </td>
                  </tr>
               </table>
               
            </div>
            <div class="col-md-12 col-sm-12 text-center" style="padding: 0px; padding-top: 15px;">
               <table class="table table-bordered invoice-detail-table">
                  <tr>
                     <th width="33%" class="invoice-table-column" style="border: 1px solid #000000;"><b>Seller Name:</b></th>
                     <td width="33%" align="center" class="invoice-table-column" style="border: 1px solid #000000;"><?php echo $company_name[0].'<br>'.$company_name[1]; ?></td>
                     <th width="33%" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b>: اسم البائع </b></th>
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
            <?php
         // print_r($estimate);
            //die;


            ?>
            <div class="col-md-12 col-sm-12 text-center" style="border: 2px solid #000000; background: #ffffff; color: #000000">
               <h4 class="bold invoice-html-number" style="margin: 5px;">Customer Details / بيانات العميل</h4>
            </div>
            <div class="col-md-12 col-sm-12 text-center" style="padding: 0px;">
               <table class="table table-bordered invoice-detail-table">
                  <tr>
                     <th width="20%" class="invoice-table-column"><b>Customer Name:</b></th>
                     <td width="60%" align="center" class="invoice-table-column"><b><?php echo $customer_name[0].'<br>'.$customer_name[1]; ?></b></td>
                     <th width="20%" align="right" class="invoice-table-column"><b>: اسم العميل </b></th>
                  </tr>
                  <tr>
                     <th width="20%" class="invoice-table-column"><b>Client VAT #:</b></th>
                     <td width="60%" class="invoice-table-column"><b><?php echo $estimate->client->vat; ?></b></td>
                     <th width="20%" align="right" class="invoice-table-column"><b>:رقم ضريبة </b></th>
                  </tr>
                  
                  <tr>
                     <th width="20%" class="invoice-table-column"><b>Client Address:</b></th>
                     <td width="60%" align="center" class="invoice-table-column"><?php echo str_replace("/", "\n", $client_add); ?></td>
                     <th width="20%" align="right" class="invoice-table-column"><b>: عنوان العميل </b></th>
                  </tr>
				   
                  <tr>
                     <th width="20%" class="invoice-table-column"><b>Ref. No.:</b></th>
                     <td width="60%" class="invoice-table-column"><b><?php echo $estimate->reference_no; ?></b></td>
                     <th width="20%" align="right" class="invoice-table-column"><b>: رقم المرجع </b></th>
                  </tr>
               </table>
            </div>
            <div class="col-md-12 col-sm-12 text-center" style="padding: 0px;">
               <table class="table table-bordered invoice-detail-table">
                  <tr style="background: #bfbfbf; color: #000000">
                     <th align="center" class="invoice-table-column"><b>SL. No. <br> مسلسل</b></th>
                     <th align="center" class="invoice-table-column"><b>Service Description <br> وصف الخدمة</b></th>
                     <th align="center" class="invoice-table-column"><b>QTY <br> كمية</b></th>
                     <th align="center" class="invoice-table-column"><b>Unit Rate <br> سعر الوحدة</b><br><?=$estimate->currency_name;?></th>
                     <th align="center" class="invoice-table-column"><b>Price Excl. Vat <br> السعر غير شامل ضريبة القيمة المضافة</b><br><?=$estimate->currency_name;?></th>
                     <th align="center" class="invoice-table-column"><b>VAT <br> ضريبة القيمة المضافة</b><br><?=$estimate->currency_name;?></th>
                     <th align="center" class="invoice-table-column"><b>VAT AMT <br> قيمة الضريبة</b><br><?=$estimate->currency_name;?></th>
                     <th align="center" class="invoice-table-column"><b>Amount Inclusive Vat <br> المبلغ شاملاً ضريبة القيمة المضافة</b><br><?=$estimate->currency_name;?></th>
                  </tr>
               <?php
                  
                  $sr = 1; $price_excl_vat = 0; $vat_amt = 0; $sub_sub_total = 0; $tfif_percent_withheld = 0; $fvattotal = 0;
				      $withoutvat = 0;
                  $items = get_items_table_data($estimate, 'estimates');
                  
                  foreach ($estimate->items as $row) {
                     $excvat = $row["qty"] * $row["rate"];
					      $withoutvat = $withoutvat + $excvat;
					      $fif_percent_withheld = $excvat * 0.15;
					      $tfif_percent_withheld = $fif_percent_withheld + $fif_percent_withheld;
					 // echo("imran: ".$fif_percent_withheld);
                     $sub_sub_total = $sub_sub_total + $withoutvat;
					      $incvat = $excvat + $fif_percent_withheld;
					  $fvattotal = $fvattotal + $fif_percent_withheld;
               ?>
                  <tr>
                     <td align="center" class="invoice-table-column"><?php echo $sr; ?></td>
                     <td align="center" class="invoice-table-column"><?php echo $row["description"]?><br /><?php echo $row["long_description"]?></td>
                     <td align="center" class="invoice-table-column"><?php echo $row["qty"]."<br>".$row["unit"]; ?></td>
                     <td align="right" class="invoice-table-column"><?php echo app_format_number($row["rate"]); ?></td>
                     <td align="right" class="invoice-table-column"><?php echo app_format_number($excvat); ?></td>
                     <td align="right" class="invoice-table-column">15%<?php foreach ($items->taxes() as $tax) {  $tt = $tax['taxrate']; $price_excl_vat = $price_excl_vat + $tax['taxrate']; } ?></td>
		   		<?php
                 $fif_percent_withheld = $excvat * ($tt/100);
                    $tfif_percent_withheld = $excvat + $fif_percent_withheld;
			   	?>
                     <td align="right" class="invoice-table-column"><?php echo app_format_number($excvat * (15/100)); ?></td>
                     <td align="right" class="invoice-table-column"><?php echo app_format_number($tfif_percent_withheld+($excvat * (15/100))); ?></td>
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
                     <th colspan="2" style="border: 1px solid #000000;"></th>
                     <th colspan="2" align="left" class="invoice-table-column" style="border: 1px solid #000000;"><b>Sub Total</b></th>
                     <th colspan="3" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><?=$estimate->currency_name;?> <b>المجموع الفرعي</b></th>
                     <th colspan="1" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b><?php echo app_format_number($withoutvat); ?></b></th>
                  </tr>
				   <?php
				   if($estimate->total_tax > 0)
				   {
				   ?>
                  <tr>
                     <th colspan="2" style="border: 1px solid #000000;"></th>
                     <th colspan="2" align="left" class="invoice-table-column" style="border: 1px solid #000000;" style="border: 1px solid #000000;"><b>VAT @ 15%</b></th>
                     <th colspan="3" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><?=$estimate->currency_name;?> <b>ضريبة القيمة المضافة @ 15٪ </b></th>
                     <th colspan="1" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b><?php echo app_format_number($estimate->total_tax); ?></b></th>
                  </tr>
				   <?php
				   }   
				   ?>
                  <tr>
                     <th colspan="2" style="border: 1px solid #000000;"></th>
                     <th colspan="2" align="left" class="invoice-table-column" style="border: 1px solid #000000;"><b>Total</b></th>
                     <th colspan="3" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><?=$estimate->currency_name;?> <b>المجموع</b> </th>
                     <th colspan="2" align="right" class="invoice-table-column" style="border: 1px solid #000000;"><b><?php echo app_format_number($final_amount); ?></b></th>
                  </tr>
				   <?php $pdf_custom_fields = get_custom_fields('estimate', array('show_on_pdf' => 1, 'show_on_client_portal' => 1));
				   foreach ($pdf_custom_fields as $field) {
					  $value = get_custom_field_value($estimate->id, $field['id'], 'estimate');
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
					   <td colspan="9" style="border: 1px solid #000000; text-align:left;">
						   <?php if (!empty($estimate->clientnote)) { ?>
                              <hr />
                              <b><?php echo _l('terms_and_conditions'); ?>:</b><br /><?php echo $estimate->clientnote; ?>
                        <?php } ?>
				   <?php if (!empty($estimate->terms)) { ?>
                              <hr />
                              <b><?php echo _l('terms_and_conditions'); ?>:</b><br /><?php echo $estimate->terms; ?>
                        <?php } ?>
            
					   </td>
				   </tr>
               </table>
            </div>
            <?php if (get_option('total_to_words_enabled') == 1) { ?>
               <div class="col-md-12 text-center invoice-html-total-to-words">
                  <p class="bold no-margin">
                     <?php echo  _l('num_word') . ': ' . $this->numberword->convert($estimate->total, $estimate->currency_name); ?>
                  </p>
               </div>
            <?php } ?>
            <?php
               if($identity_confirmation_enabled == '1' && $can_be_accepted){
                  get_template_part('identity_confirmation_form',array('formData'=>form_hidden('estimate_action',4)));
               }
            ?>

            <?php 
            /*
            if (count($estimate->attachments) > 0 && $estimate->visible_attachments_to_customer_found == true) { ?>
               <div class="clearfix"></div>
               <div class="invoice-html-files" >
                  <div class="col-md-12">
                     <hr />
                     <p class="bold mbot15 font-medium"><?php echo _l('invoice_files'); ?></p>
                  </div>
                  <?php foreach ($estimate->attachments as $attachment) {
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
            <?php } 
            */
            ?>
            <div class="col-md-12">
               <hr />
            </div>
            <div class="col-md-12">
               <hr />
            </div>
            <div class="col-md-12 invoice-html-payments" style="display: none;">
               <?php
               $total_payments = count($estimate->payments);
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
                        <?php foreach ($estimate->payments as $payment) { ?>
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
                              <td><?php echo app_format_money($payment['amount'], $estimate->currency_name); ?></td>
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
            if (($estimate->status != Invoices_model::STATUS_PAID
               && $estimate->status != Invoices_model::STATUS_CANCELLED
               && $estimate->total > 0)) { ?>
               <div class="col-md-12">
                  <div class="row">
                     <?php
                     $found_online_mode = false;
                     if (found_invoice_mode($payment_modes, $estimate->id, false)) {
                        $found_online_mode = true;
                     ?>
                        <div class="col-md-6 text-left">
                           <p class="bold mbot15 font-medium"><?php echo _l('invoice_html_online_payment'); ?></p>
                           <?php echo form_open($this->uri->uri_string(), array('id' => 'online_payment_form', 'novalidate' => true)); ?>
                           <?php foreach ($payment_modes as $mode) {
                              if (!is_numeric($mode['id']) && !empty($mode['id'])) {
                                 if (!is_payment_mode_allowed_for_invoice($mode['id'], $estimate->id)) {
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
                                    <input type="number" required max="<?php echo $invoice->total_left_to_pay; ?>" data-total="<?php echo $estimate->total_left_to_pay; ?>" name="amount" class="form-control" value="<?php echo $estimate->total_left_to_pay; ?>">
                                    <span class="input-group-addon">
                                       <?php echo $estimate->symbol; ?>
                                    </span>
                                 </div>
                              <?php } else {
                                 echo '<h4 class="bold mbot25">' . _l('invoice_html_total_pay', app_format_money($estimate->total_left_to_pay, $estimate->currency_name)) . '</h4>';
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
    

<script>
   $(function(){
     new Sticky('[data-sticky]');
   })
</script>
