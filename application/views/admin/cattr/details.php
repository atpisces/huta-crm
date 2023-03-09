<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h3>Welcome to Cattr World</h3>
                <?php if(isset($_SESSION["cattr_user"])){ ?>
                        <div class="badge bg-success">
                            <h6 style="margin: 0px;"><u>Cattr</u> is connected</h6>
                        </div>
                        <a href="<?php echo base_url('admin/cattr/disconnect_cattr'); ?>"><div class="badge bg-danger">
                            <h6 style="margin: 0px;">Disconnect?</h6>
                        </div></a>
                <?php } ?>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-md-12">
                                <h1><b>User's Activity:</h1>
                                <?php print_r($users_info); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <?php init_tail(); ?>
</body>
</html>
