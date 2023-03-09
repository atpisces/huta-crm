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
                <?php if(isset($_SESSION["cattr_user"])){ ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <a href="<?php echo base_url('admin/cattr/index'); ?>" class="btn btn-info pull-left new mright5 btn-sm">< Cattr Dashboard</a>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-12">
                                <?php
                                    if($projects_list){
                                        // echo "session: ".$_SESSION["cattr_access_token"];
                                        // print_r($user_details);
                                ?>
                                        <h4><b><?php echo $user_details->full_name."'s"; ?> Projects List</b></h4>
                                <?php
                                        // echo "<br><br>Projects: ";
                                        // print_r($projects_list);
                                        foreach($projects_list as $project){
                                            $details = $controller->project_details($project->id);
                                            // print_r($details);
                                            $chk = "";
                                            foreach($details->workers as $worker){
                                                if($worker->user_id == $user_details->id){
                                                    $chk = "1";
                                                }
                                            }
                                            if($chk == "1"){
                                ?>
                                                <div class="col-lg-6 col-md-6 border-right panel_s" style="height: auto;">
                                                    <div class="panel-heading-bg" style="background:#2d2d2d;border-color:#2d2d2d;color:#fff;">
                                                      <h4 class="heading" style="margin: 0px;"><b><?php echo $project->name; ?></b></h4>
                                                      <p><big><?php echo $project->description; ?></big></p>
                                                        <a href="<?php echo base_url('admin/cattr/project_tasks/'.$project->id); ?>" class="btn btn-primary" style="float: right;">View Tasks</a>
                                                    </div>
                                                </div>
                                <?php
                                            }
                                        }
                                    }
                                ?>
                                </div>
                            </div>
                <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <?php init_tail(); ?>
</body>
</html>
