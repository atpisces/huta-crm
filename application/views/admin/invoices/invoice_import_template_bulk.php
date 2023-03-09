<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
   <div class="panel_s invoice accounting-template">
      <div class="additional"></div>
      <div class="panel-body">
         <h4 class="customer-profile-group-heading">Import Bulk Invoices</h4>
      
         <div class="row">
            <div class="col-md-12">
               <div class="jumbotron">
                  <div class="container">
                     <div class="col-md-9">
                        <p style="font-size:14px;">The first line in downloaded csv file should remain as it is. Please do not change the order of columns. </p>
                     </div>
                     <div class="col-md-3">
                        <button type="button" class="btn btn-primary">Download Sample</button>
                     </div>
                  </div>
               </div>
            </div>            
         </div>

         <div class="row">
            <div class="col-md-6">
               <div class="form-group">
                     <label for="date" class="control-label"> 
                        <small class="req text-danger">* </small>
                        <?php echo _l('import_invoice_file'); ?>
                     </label>
                     <input type="file" name="date" class="form-control" autocomplete="off" aria-invalid="false">
               </div>
            </div>            
         </div>


       
      <div class="row">
         <div class="col-md-12 mtop15">
            <div class="bottom-transaction">
                  <div class="btn-bottom-toolbar text-right">
               
                  <button class="btn-tr btn btn-default mleft10 text-right invoice-form-submit save-as-draft transaction-submit">
                  <?php echo _l('import_invoice_save_preview'); ?>
                  </button>
                  
               <div class="btn-group dropup">
                  <button type="button" class="btn-tr btn btn-info invoice-form-submit transaction-submit"><?php echo _l('submit'); ?></button>
               </div>
               </div>
            </div>
         <div class="btn-bottom-pusher"></div>
         </div>
      </div>
      
      </div>

   </div>
   
   <?php hooks()->do_action('after_render_invoice_template', isset($invoice) ? $invoice : false); ?>
</div>
