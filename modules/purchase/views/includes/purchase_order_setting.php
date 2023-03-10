<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>


<?php echo form_open_multipart(admin_url('purchase/pur_order_setting'),array('id'=>'pur_order_setting-form')); ?>
<div class="col-md-6">
	<?php echo render_input('pur_order_prefix','pur_order_prefix',get_purchase_option('pur_order_prefix')); ?>
</div>

<div class="col-md-6">
  <?php echo render_input('pur_request_prefix','pur_request_prefix',get_purchase_option('pur_request_prefix')); ?>
</div>

<div class="col-md-6">
  <?php echo render_input('pur_inv_prefix','pur_inv_prefix',get_purchase_option('pur_inv_prefix')); ?>
</div>

<div class="col-md-6 form-group">
  <label for="create_invoice_by"><?php echo _l('create_invoice_by'); ?></label>
  <select name="create_invoice_by" id="create_invoice_by" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
      <option value="contract" <?php if(get_purchase_option('create_invoice_by') == 'contract'){ echo 'selected'; } ?> ><?php echo _l('contract'); ?></option>
      <option value="pur_order" <?php if(get_purchase_option('create_invoice_by') == 'pur_order'){ echo 'selected'; } ?> ><?php echo _l('pur_order'); ?></option>
  </select>
</div>

<div class="col-md-6">
  <?php echo render_input('pur_invoice_auto_operations_hour','pur_invoice_auto_operations_hour',get_option('pur_invoice_auto_operations_hour'),'number', array('data-toggle'=>'tooltip','data-title'=>_l('hour_of_day_perform_auto_operations_format'),'max'=>23)); ?>
</div>

<div class="col-md-12">
  <hr>
</div>
	
<div class="col-md-6">
  <?php echo render_textarea('terms_and_conditions', 'terms_and_conditions', get_purchase_option('terms_and_conditions')); ?>
</div>

<div class="col-md-6">
  <?php echo render_textarea('vendor_note', 'vendor_note', get_purchase_option('vendor_note')); ?>
</div>

<?php if(get_po_logo() == ''){ ?>
  <div class="col-md-6 form-group">
    <label for="po_logo"><?php echo _l('po_logo'); ?></label>
    <input type="file" class="form-control" name="po_logo" accept="image/*" data-toggle="tooltip" title="<?php echo _l('settings_general_company_logo_tooltip'); ?>" />
  </div>
<?php } else { ?>
<div class="col-md-5">
  <?php echo get_po_logo(500, "img img-responsive", 'setting'); ?>
</div>
<?php if( is_admin()){ ?>
          <div class="col-md-6 text-left">
            <a href="<?php echo admin_url('purchase/remove_po_logo'); ?>" data-toggle="tooltip" title="<?php echo _l('remove_po_logo'); ?>" class="_delete text-danger"><i class="fa fa-remove"></i></a>
          </div>
        <?php } ?>
<?php } ?>

<div class="col-md-12">
  <hr>
</div>

	<button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
	<?php echo form_close(); ?>

<div class="clearfix"></div>


