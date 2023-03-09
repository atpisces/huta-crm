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
                            <?php if(isset($_SESSION["staff_id"])){ ?>
                                <a href="<?php echo base_url('admin/cattr/view_individual_user_projects/'.$_SESSION["staff_id"]); ?>" class="btn btn-info pull-left new mright5 btn-sm">< Back to Projects</a>
                            <?php } else { ?>
                                <a href="<?php echo base_url('admin/cattr/index'); ?>" class="btn btn-info pull-left new new-invoice-list mright5 btn-sm">< Back to Projects</a>
                            <?php } ?>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <h3><b>Tasks List of Project: </b><span class="text-primary"><?php echo $tasks_list[0]->project->name; ?></span></h3>
                            <?php
                                $user_id = $_SESSION["cattr_user"];
                                $total_time = $controller->user_todays_total_time($user_id);
                                $init = $total_time->time;
                                $hours = floor($init / 3600);
                                $minutes = floor(($init / 60) % 60);
                                $seconds = $init % 60;

                            ?>
                                <br><br>
                                <h4 align="center">Today's Overall Working Time: <span class="badge bg-info"><big><?php echo $hours."hours ".$minutes."minutes and ".$seconds."seconds"; ?></big></span></h4>
                                <br><br>
                            <?php
                                // $cattr_response_decode = "";
                                if(isset($tasks_list)){ 
                                    // print_r($tasks_list);
                                    // echo $tasks_list[0]->project->name;

                                    echo "<br><br>";
                                    // echo $cattr_response->access_token."<br>";
                                    // echo $cattr_response->token_type."<br>";
                                    foreach($tasks_list as $task){
                                        $task_time = $controller->user_todays_total_time_by_task($task->id);
                                        // print_r($task_time);
                                        $init = $task_time->total->time;
                                        $hours = floor($init / 3600);
                                        $minutes = floor(($init / 60) % 60);
                                        $seconds = $init % 60;
                            ?>
                                        <div class="col-lg-6 col-md-6 border-right panel_s" style="height: auto;">
                                            <div class="row">
                                                <div class="col-lg-1">
                                                    <div class="kan-ban-step-indicator" style="position: inherit;">
                                                    </div>
                                                </div>
                                                <div class="col-lg-11">
                                                    <p style="margin-top: 5px;"><b style="font-size: 18px;"><?php echo $task->task_name; ?></b>
                                                        <br>
                                                        Today's Time: <span class="badge bg-info"><big><?php echo $hours."hours ".$minutes."minutes and ".$seconds."seconds"; ?></big></span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                            <?php
                                    }
                                }

                            ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <!-- <h1><b>User's Activity:</h1> -->
                                <?php //print_r($user_todays_total_time); ?>
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
