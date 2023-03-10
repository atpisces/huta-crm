<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
             <div class="panel_s">
                 <div class="panel-body">
                    <div class="_buttons">
                        <a href="<?php echo admin_url('assignments/field'); ?>" class="btn btn-info pull-left display-block"><?php echo _l('Add New Assignment'); ?></a>
                    </div>
                    <div class="clearfix"></div>
                    <hr class="hr-panel-heading" />
                    <div class="clearfix"></div>
                    <?php render_datatable(
                        array(
                            _l('id'),
                            _l('Name'),
                            _l('Description'),
                            _l('Start Date'),
                            _l('End Date'),
                            ),'assignments'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php init_tail(); ?>
    <script>
        $(function(){
            initDataTable('.table-assignments', window.location.href);
        });
    </script>
</body>
</html>
