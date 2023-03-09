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
                <?php if(!isset($_SESSION["cattr_user"])){ ?>
                        <form name="CattrLoginForm" method="post" action="<?php echo base_url('admin/cattr/index'); ?>">
                            <div class="row">
                        <?php 
                            if(isset($invalid_user)){
                                echo '<h5 align="center" class="alert alert-danger">'.$invalid_user.'</h5>';
                            }
                        ?>
                                <h4 align="center">Link your account with Cattr</h4>
                                <p align="center">Email: <b><?php echo $staff_details->email; ?></b></p>
                                <div class="col-6" style="display: table; margin: 0 auto;">
                                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                                    <input type="password" name="cattr_account_password" placeholder="Cattr Account Password?" class="form-control" required /><br>
                                    <button type="submit" class="btn btn-primary only-save customer-form-submiter col-md-12">CONNECT</button>
                                </div>
                            </div>
                        </form>
                <?php } else {

                        if(isset($_SESSION["cattr_user"]) && $_SESSION["cattr_is_admin"] == "1"){
                ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <h4><b>All User's List</b></h4>
                                    <?php //print_r($all_users); ?>
                                    <div class="table-responsive">
                                        <table class="table dataTable no-footer" id="Data">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Full Name</th>
                                                    <th>Email</th>
                                                    <th>Today's Time</th>
                                                    <th>Online Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                    <?php
                                        $sr = 1;
                                        if($all_users){
                                            foreach($all_users as $user){
                                                $total_time = $controller->user_todays_total_time($user->id);
                                                // print_r($total_time);
                                                $init = $total_time->time;
                                                $hours = floor($init / 3600);
                                                $minutes = floor(($init / 60) % 60);
                                                $seconds = $init % 60;
                                                $time = $hours."h ".$minutes."m ".$seconds."s";
                                    ?>
                                                <tr>
                                                    <td><?php echo $sr; ?></td>
                                                    <td><?php echo $user->full_name; ?></td>
                                                    <td><?php echo $user->email; ?></td>
                                                    <td><?php echo $time; ?></td>
                                                    <td>
                                                    <?php 
                                                        if($user->online == "1"){
                                                            echo '<span class="badge bg-success">Online</span>';
                                                        } else {
                                                            echo '<span class="badge bg-danger">Offline</span>';
                                                        }
                                                    ?>   
                                                    </td>
                                                    <td>
                                                        <a href="<?php echo base_url('admin/cattr/view_individual_user_projects/'.$user->id); ?>" class="badge bg-success">View Projects</a>
                                                    </td>
                                                </tr>
                                    <?php
                                                $sr++;
                                            }
                                        }
                                    ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                    <?php } ?>
                            <div class="row">
                                <div class="col-md-12">
                                <?php
                                    // echo $_SESSION["cattr_is_admin"];
                                    // $cattr_response_decode = "";
                                    if(isset($cattr_response)){ 
                                        // print_r($cattr_response);

                                        // echo "<br><br>";
                                        // echo $cattr_response->access_token."<br>";
                                        // echo $cattr_response->token_type."<br>";
                                    }
                                    if($projects_list){
                                        // echo "session: ".$_SESSION["cattr_access_token"];
                                ?>
                                        <h4><b>My Projects List</b></h4>
                                <?php
                                        // echo "<br><br>Projects: ";
                                        // print_r($projects_list);
                                        foreach($projects_list as $project){
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
