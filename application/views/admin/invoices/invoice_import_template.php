<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
   <div class="panel_s invoice accounting-template">
      <div class="additional"></div>
      <div class="panel-body">
         <h4 class="customer-profile-group-heading">Import Individual Invoice</h4>
      
         <div class="row">
            <div class="col-md-4">
               <div class="form-group" app-field-wrapper="date">
                  <label for="date" class="control-label"> 
                     <small class="req text-danger">* </small>
                     <?php echo _l('import_invoice_date'); ?>
                  </label>
                  <div class="input-group date">
                     <input type="text" id="date" name="date" class="form-control datepicker" value="2021-11-12" autocomplete="off" aria-invalid="false">
                     <div class="input-group-addon">
                        <i class="fa fa-calendar calendar-icon"></i>
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="date" class="control-label"> 
                     <?php echo _l('import_invoice_reference'); ?>
                  </label>
                  <input type="text" name="date" class="form-control" autocomplete="off" aria-invalid="false">
               </div>
            </div>
            <div class="col-md-4">
               <div class="f_client_id">
                  <div class="form-group select-placeholder">
                     <label for="clientid" class="control-label"><?php echo _l('Biller'); ?></label>
                     <select id="clientid" name="clientid" data-live-search="true" data-width="100%" class="ajax-search<?php if(isset($invoice) && empty($invoice->clientid)){echo ' customer-removed';} ?>" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                     <?php $selected = (isset($invoice) ? $invoice->clientid : '');
                     if($selected == ''){
                        $selected = (isset($customer_id) ? $customer_id: '');
                     }
                     if($selected != ''){
                        $rel_data = get_relation_data('customer',$selected);
                        $rel_val = get_relation_values($rel_data,'customer');
                        echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
                     } ?>
                     </select>
                  </div>
               </div>
            </div>
            
            <div class="col-md-4">
               <div class="f_client_id">
                  <div class="form-group select-placeholder">
                     <label for="clientid" class="control-label"><?php echo _l('invoice_location'); ?></label>
                     <select id="clientid" name="clientid" data-live-search="true" data-width="100%" class="ajax-search<?php if(isset($invoice) && empty($invoice->clientid)){echo ' customer-removed';} ?>" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                     <?php $selected = (isset($invoice) ? $invoice->clientid : '');
                     if($selected == ''){
                        $selected = (isset($customer_id) ? $customer_id: '');
                     }
                     if($selected != ''){
                        $rel_data = get_relation_data('customer',$selected);
                        $rel_val = get_relation_values($rel_data,'customer');
                        echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
                     } ?>
                     </select>
                  </div>
               </div>
            </div>

            <div class="col-md-4">
               <div class="f_client_id">
                  <div class="form-group select-placeholder">
                     <label for="clientid" class="control-label"><?php echo _l('invoice_customer'); ?></label>
                     <select id="clientid" name="clientid" data-live-search="true" data-width="100%" class="ajax-search<?php if(isset($invoice) && empty($invoice->clientid)){echo ' customer-removed';} ?>" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                     <?php $selected = (isset($invoice) ? $invoice->clientid : '');
                     if($selected == ''){
                        $selected = (isset($customer_id) ? $customer_id: '');
                     }
                     if($selected != ''){
                        $rel_data = get_relation_data('customer',$selected);
                        $rel_val = get_relation_values($rel_data,'customer');
                        echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
                     } ?>
                     </select>
                  </div>
               </div>
            </div>

         </div>
         
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
            <div class="col-md-6">
               <div class="form-group">
                     <label for="date" class="control-label"> 
                        <small class="req text-danger">* </small>
                        <?php echo _l('import_invoice_document'); ?>
                     </label>
                     <input type="file" name="date" class="form-control" autocomplete="off" aria-invalid="false">
               </div>
            </div>            
         </div>

         <div class="row">

            <div class="col-md-4">
               <div class="form-group select-placeholder">
                     <label for="date" class="control-label"> 
                        <?php echo _l('import_invoice_order_tax'); ?>
                     </label>
                     <select id="clientid" name="clientid" data-live-search="true" data-width="100%" class="ajax-search<?php if(isset($invoice) && empty($invoice->clientid)){echo ' customer-removed';} ?>" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <option ></option>
                     </select>
               </div>
            </div>    
            
            <div class="col-md-4">
               <div class="form-group">
                     <label for="date" class="control-label"> 
                        <?php echo _l('import_invoice_order_discount'); ?>
                     </label>
                     <input type="text" name="date" class="form-control" autocomplete="off" aria-invalid="false">
               </div>
            </div>    

            <div class="col-md-4">
               <div class="form-group">
                     <label for="date" class="control-label"> 
                        <?php echo _l('import_invoice_shipping'); ?>
                     </label>
                     <input type="text" name="date" class="form-control" autocomplete="off" aria-invalid="false">
               </div>
            </div>    

            <div class="col-md-4">
               <div class="form-group select-placeholder">
                     <label for="date" class="control-label"> 
                        <small class="req text-danger">* </small>
                        <?php echo _l('import_invoice_sale_status'); ?>
                     </label>
                    <select id="clientid" name="clientid" data-live-search="true" data-width="100%" class="ajax-search<?php if(isset($invoice) && empty($invoice->clientid)){echo ' customer-removed';} ?>" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <option ></option>
                     </select>
               </div>
            </div>    

            <div class="col-md-4">
               <div class="form-group">
                     <label for="date" class="control-label"> 
                        <small class="req text-danger">* </small>
                        <?php echo _l('import_invoice_payment_term'); ?>
                     </label>
                     <input type="text" name="date" class="form-control" autocomplete="off" aria-invalid="false">
               </div>
            </div>    

            <div class="col-md-4">
               <div class="form-group select-placeholder">
                     <label for="date" class="control-label"> 
                        <small class="req text-danger">* </small>
                        <?php echo _l('import_invoice_payment_status'); ?>
                     </label>
                     <select id="clientid" name="clientid" data-live-search="true" data-width="100%" class="ajax-search<?php if(isset($invoice) && empty($invoice->clientid)){echo ' customer-removed';} ?>" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <option ></option>
                     </select>
               </div>
            </div>
            <div class="col-md-6">
                  <div class="form-group">
                     <label for="date" class="control-label"> 
                        <small class="req text-danger">* </small>
                        <?php echo _l('import_invoice_sale_note'); ?>
                     </label>
                     <textarea class="form-control" rows="10"></textarea>
               </div>
            </div>
            <div class="col-md-6">
               <div class="form-group">
                     <label for="date" class="control-label"> 
                        <small class="req text-danger">* </small>
                        <?php echo _l('import_invoice_staff_note'); ?>
                     </label>
                     <textarea class="form-control" rows="10"></textarea>
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
