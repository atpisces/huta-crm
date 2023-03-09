<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
   include_once(APPPATH . 'libraries/phpqrcode/qrlib.php');
   if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') 
        $link = "https://"; 
    else
        $link = "http://"; 
    
   $link .= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; 
   
  $Code = "Business : ".get_option('invoice_company_name').", Address : ".get_option('invoice_company_address').", ".get_option('invoice_company_city').", VAT : ".get_option('company_vat').", Invoice No. : ".format_invoice_number($invoice->id).", Date: ".$invoice->date.", SubTotal : ".app_format_money($invoice->subtotal, $invoice->currency_name).", Total : ".app_format_money($invoice->total, $invoice->currency_name).", Tax : ".app_format_money($tax['total_tax'], $invoice->currency_name).", Customer : ".$link;
 
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
         $matrixPointSize = 4;
      
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
<?php if(is_invoice_overdue($invoice)){ ?>
   <div class="row">
      <div class="col-md-12">
         <div class="text-center text-white danger-bg">
            <h5><?php echo _l('overdue_by_days', get_total_days_overdue($invoice->duedate)) ?></h5>
         </div>
      </div>
   </div>
<?php } ?>
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
                
               <?php echo form_open($this->uri->uri_string()); ?>
               <button type="submit" name="invoicepdf" value="invoicepdf" class="btn btn-default pull-right action-button mtop5">
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
<div class="clearfix"></div> 
<div class="panel_s mtop20" id="detail_invoice">
   <div class="panel-body">
      <div class="col-md-10 col-md-offset-1">
         <div class="row mtop20">
            <div class="col-md-12 col-sm-12">
               <div class="col-md-6 col-sm-12 ">
                     <img src="<?=site_url('assets/images/logo.jpg');?>" style="float:left; width:60%; margin-bottom:20px;"  >
               </div>
               <div class="col-md-6 col-sm-12 ">
                  <div class="col-md-7 col-sm-12 ">
                     <p>
                        C.R Number: 4030423068
                     </p>
                     <p>
                        VAT Number: <?php echo get_option('company_vat'); ?>
                     </p>
                     <p>
                        Chamber No: 201000411034
                     </p>
                     
                  </div>
                  <div class="col-md-1 col-sm-12">
                     <div style="float:left; width:3px; height:100px; background:#000"></div>
                  </div>
                  <div class="col-md-4 col-sm-12">
                     <p>
                        <?php echo get_option('invoice_company_address').", ".get_option('invoice_company_city'); ?>                            

                     </p>                     
                  </div>
               </div>
            </div>
               <table class="table table-bordered" style="width:100%; cellspacing:0px; cellpadding:15px; ">
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
                     <td colspan="2" rowspan="3"><?php echo $invoice->client->city." - ".$invoice->client->address; ?></td>
                     <td colspan="2"><?php echo format_invoice_number($invoice->id); ?></td>
                     <td colspan="2"><?php echo $invoice->date; ?></td>
                  </tr>
                  <tr>
                     <td colspan="4"><b>Buyer VAT Number</b></td>
                  </tr>
                  <tr>
                     <td colspan="4"><?php echo $invoice->client->vat; ?></td>
                  </tr>
                  <tr>
                     <td colspan="2"><b>Seller:</b></td>
                     <td colspan="4"><b>Seller VAT Number:</b></td>
                  </tr>
                  <tr>
                     <td colspan="2" rowspan="5">
                        <?php echo get_option('invoice_company_city')." - ".get_option('invoice_company_address')?>
                     </td>
                     <td colspan="4">342342342</td>
                  </tr>
                  <tr>
                     <td colspan="4"><b>Client P.O</b></td>
                  </tr>
                  <tr>
                     <td colspan="4"></td>
                  </tr>
                  <tr>
                     <td colspan="2"><b>Dispatch from</b></td>
                     <td colspan="2"><b>Delivery to</b></td>
                  </tr>
                  <tr>
                     <td colspan="2"><?php echo get_option('invoice_company_city') ?></td>
                     <td colspan="2"><?php echo $invoice->client->city; ?></td>
                  </tr>
                  
                  <tr>
                     <td colspan="6">
                  
               <table class="table table-bordered" style="width:100%; cellspacing:0px; cellpadding:0px; ">
                 <tr>
                     <th><b>No</b></th>
                     <th colspan="2"><b>Description of Goods</b></th>
                     <th><b>Qty</b></th>
                     <th><b>Unit Rate</b></th>
                     <th><b>Amount (SAR)</b></th>
                   </tr>
                <?php
                    $items = get_items_table_data($invoice, 'invoice');
                    $sr = 1;
                    foreach ($invoice->items as $row) {
                ?>
                   <tr>
                     <td align="center">
                       <?php echo $sr; ?>
                     </td>
                     <td colspan="2"><?php echo $row["description"]?></td>
                     <td align="center"><?php echo $row["qty"]?></td>
                     <td align="center"><?php echo app_format_money($row["rate"], $invoice->currency_name); ?></td>
                     <td align="center"><?php echo app_format_money($row["rate"], $invoice->currency_name); ?></td>
                   </tr>
                <?php
                     $sr++;
                  }
                ?>
               </table>
            </td>
              </tr>
        
                  <tr>
                     <th colspan="5" style="text-align:center;"><b>Discount</b></th>
                     <th style="text-align:center;">    
                        <b><?php echo app_format_money($invoice->discount_total, $invoice->currency_name); ?></b>
                     </th>
                  </tr>
                  <tr>
                     <th colspan="5" style="text-align:center;"><b>Total Net Value in SAR</b></th>
                     <th style="text-align:center;">
                           <b> <?php echo app_format_money($invoice->total, $invoice->currency_name); ?></b>
                     </th>
                  </tr>
                  <tr>
                     <th colspan="5" style="text-align:center;"><b>Due Value: Advance Payment 60% of Total Value</b></th>
                     <th style="text-align:center;">
                           <b> <?php echo app_format_money($invoice->total, $invoice->currency_name); ?></b>
                     </th>
                  </tr>
                  <tr>
                     <th colspan="5" style="text-align:center;"><b>VAT 15% of Invoice value</b></th>
                     <th style="text-align:center;">
                        <b> <?php echo app_format_money($invoice->total, $invoice->currency_name); ?></b>
                     </th>
                  </tr>
                  <tr>
                     <th colspan="5" style="text-align:center;"><b>Total Invoice Payable Amount in SAR</b></th>
                     <th style="text-align:center;">
                        <b> <?php echo app_format_money($invoice->total, $invoice->currency_name); ?></b>
                     </th>
                  </tr>
                  <tr>
                     <th colspan="6" style="text-align:center;"><b>For Control Technologies Es.</b></th>
                  </tr>
                  <tr>
                     <td colspan="3" style="text-align:center;"><b>Stamp</b></td>
                     <td colspan="3" style="text-align:center;"><b>Confirmed By</b></td>
                  </tr>
                  <tr>
                     <td colspan="3" height="150px"></td>
                     <td colspan="3" height="150px"></td>
                  </tr>
                  <tr>
                     <td colspan="3" height="30px"></td>
                     <td colspan="3" height="30px"></td>
                  </tr>
               </table>
               <div class="col-md-12 col-sm-12">
               <div class="col-md-8 col-sm-12 ">
                  <div class="col-md-5 col-sm-12 ">
                     <p>
                     <?php echo get_option('invoice_company_name'); ?>                     
                     </p>
                     <p>
                     <?php echo get_option('invoice_company_city')." ".get_option('invoice_company_country_code'); ?> 
                     </p>
                     <p>
                     Tel: <?php echo get_option('invoice_company_phonenumber'); ?>
                     </p>
                     <p>
                     Fax: <?php echo get_option('invoice_company_phonenumber'); ?>
                     </p>
                     
                  </div>
                  <div class="col-md-1 col-sm-12">
                     <div style="float:left; width:3px; height:100px; background:#000"></div>
                  </div>
                  <div class="col-md-6 col-sm-12 ">
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
                  
               </div>
               <div class="col-md-4 col-sm-12 ">
                  <div class="col-md-12 col-sm-12 text-right">
                     <img src="<?=site_url($qr_code_img);?>" style="width:200px;"  >                   </div>
               </div>
            </div>
               
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
        }
        else{
            $("#detail_invoice").show();
            $("#simplified_invoice").hide();
        }
    }
</script>
