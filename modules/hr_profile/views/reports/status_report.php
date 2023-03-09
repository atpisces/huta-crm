<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();?>
<link href="<?= module_dir_url('accounting', 'assets/css/report.css'); ?>" rel="stylesheet" type="text/css">
<link href="<?= module_dir_url('accounting', 'assets/plugins/treegrid/css/jquery.treegrid.css?v=1091'); ?>" rel="stylesheet" type="text/css">
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="panel_s">
        <div class="panel-body">
          <h4 class="no-margin font-bold"><?php echo _l($title); ?></h4>
          <hr />
          <div class="row">
            <div class="col-md-10">
              <div class="row">
              <?php echo form_open(admin_url('hr_profile/user_status_report'),array('id'=>'filter-form')); ?>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="staff_id" class="control-label"><?php echo _l('Staff List'); ?></label>
                      <select name="staff_id" class="selectpicker" id="staff_id" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" <?php if($edit_approval == 'true'){ echo 'disabled';} ; ?>> 
                        <option value=""></option> 
                        <?php foreach($staff as $s){ ?>
                      <option value="<?php echo html_entity_decode($s['staffid']); ?>" <?php if(isset($goods_delivery) && $goods_delivery->staff_id == $s['staffid']){ echo 'selected' ;} ?>> <?php echo html_entity_decode($s['firstname']).''.html_entity_decode($s['lastname']); ?></option>                  
                      <?php }?>
                      </select>

                    </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="staff_id" class="control-label"><?php echo _l('Select Type'); ?></label>
                      <select name="status_type" class="selectpicker" id="staff_id" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" <?php if($edit_approval == 'true'){ echo 'disabled';} ; ?>> 
                        <option value=""></option> 
                        <option value="iqama">Iqama</option>
                        <option value="passport">Passport</option>
                        <option value="medical">Medical Insurance</option>
                      </select>

                    </div>
                </div>

                <div class="col-md-3">
                  <?php echo render_date_input('from_date','Start Date', _d($from_date)); ?>
                </div>
                <div class="col-md-3">
                  <?php echo render_date_input('to_date','End Date', _d($to_date)); ?>
                </div>
                <div class="col-md-3">
                  <?php echo form_hidden('type', 'user_status_report'); ?>
                  <button type="submit" class="btn btn-info btn-submit mtop25"><?php echo _l('filter'); ?></button>
                </div>
              <?php echo form_close(); ?>
              </div>
            </div>
            <div class="col-md-2">
              <div class="btn-group pull-right mtop25">
                 <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-print"></i><?php if(is_mobile()){echo ' PDF';} ?> <span class="caret"></span></a>
                 <ul class="dropdown-menu dropdown-menu-right">
                    <li>
                       <a href="#" onclick="printDiv(); return false;">
                       <?php echo _l('export_to_pdf'); ?>
                       </a>
                    </li>
                    <li>
                       <a href="#" onclick="printExcel(); return false;">
                       <?php echo _l('export_to_excel'); ?>
                       </a>
                    </li>
                 </ul>
              </div>
            </div>
          </div>
          <div class="row"> 
            <div class="col-md-12"> 
              <hr>
            </div>
          </div>
          <!-- <div class="page" id="DivIdToPrint">
            <h1>Testing</h1>
        </div> -->
        <div class="page" id="DivIdToPrint" style="padding: 1cm;
    border: 1px #D3D3D3 solid;
    border-radius: 5px;
    background: white;
    box-shadow: 0 0 5px rgb(0 0 0 / 10%);
    display: flex;
    width: 21cm;
    margin: 1cm auto;">
          <div id="accordion">
            <div class="card">
              <table class="tree">
                <tbody>
                  <tr>
                    <td colspan="3"><div class="treegrid-container" style="margin-left: 24px;"><span class="treegrid-expander"></span>
                        <h3 class="text-center no-margin-top-20 no-margin-left-24">Status Report</h3>
                    </div></td>
                    <td></td>
                    <td></td>
                  </tr>
                  <tr>
                    <td colspan="3"><div class="treegrid-container" style="margin-left: 24px;"><span class="treegrid-expander"></span>
                      <h4 class="text-center no-margin-top-20 no-margin-left-24">For Staff</h4>
                    </div></td>
                    <td></td>
                    <td></td>
                  </tr>
                  <tr>
                    <td colspan="3"><div class="treegrid-container" style="margin-left: 24px;"><span class="treegrid-expander"></span>
          <?php if($from_date != "" && $to_date != ""){ ?>
                      <p class="text-center no-margin-top-20 no-margin-left-24"><?php echo date("d-m-Y",strtotime($from_date))." - ".date("d-m-Y",strtotime($to_date)); ?></p>
          <?php } else { ?>
                      <p class="text-center no-margin-top-20 no-margin-left-24">00-00-0000 - 00-00-0000</p>
          <?php } ?>
                    </div></td>
                    <td></td>
                    <td></td>
                  </tr>
                  <tr>
                    <td><div class="treegrid-container" style="margin-left: 24px;"><span class="treegrid-expander"></span></div></td>
                    <td></td>
                    <td></td>
                  </tr>
                  <tr class="border-top">
                    <td><div class="treegrid-container" style="margin-left: 24px;"><span class="treegrid-expander"></span></div></td>
                    <td colspan="2"></td>
                    <td></td>
                  </tr>
                </tbody>
              </table>
              <table>
                <tbody>

          <?php
              if(isset($staff_list)){
                foreach($staff_list as $staff){
          ?>
                  <tr>
                    <td colspan="5">Staff Name: <?php echo $staff["firstname"]." ".$staff["lastname"]; ?></td>
                  </tr>
              <?php
                  if($status_type == "" || $status_type == "iqama"){
              ?>
                  <tr>
                    <td colspan="5" align="center" bgcolor="#000000" style="color: #ffffff;">Iqama Record</td>
                  </tr>
                  <tr>
                    <th>#</th>
                    <th>Before Extend</th>
                    <th>Extend Duration</th>
                    <th>After Extend</th>
                    <th></th>
                  </tr>
              <?php
                    $n = 1;
                    foreach($statuses_records as $record){
                      if($record["staffid"] == $staff["staffid"] && $record["statustype"] == "iqama"){
                        $extend_duration = explode(",",$record["extend_duration"]);
              ?>
                        <tr>
                          <td><?php echo $n; ?></td>
                          <td style="font-weight: 100;"><?php echo date("d-m-Y",strtotime($record["old_date"])); ?></td>
                          <td style="font-weight: 100;"><?php echo "<b>".$extend_duration[0]."</b>year(s) <b>".$extend_duration[1]."</b>month(s) <b>".$extend_duration[2]."</b>day(s)"; ?></td>
                          <td style="font-weight: 100;"><?php echo date("d-m-Y",strtotime($record["new_date"])); ?></td>
                        </tr>
              <?php  
                        $n++;
                      }
                    }
                  }
                  if($status_type == "" || $status_type == "passport"){
              ?>
                  <tr>
                    <td colspan="5" align="center" bgcolor="#000000" style="color: #ffffff;">Passport Record</td>
                  </tr>
                  <tr>
                    <th>#</th>
                    <th>Before Extend</th>
                    <th>Extend Duration</th>
                    <th>After Extend</th>
                    <th></th>
                  </tr>
              <?php
                    $n = 1;
                    foreach($statuses_records as $record){
                      if($record["staffid"] == $staff["staffid"] && $record["statustype"] == "passport"){
                        $extend_duration = explode(",",$record["extend_duration"]);
              ?>
                        <tr>
                          <td><?php echo $n; ?></td>
                          <td style="font-weight: 100;"><?php echo date("d-m-Y",strtotime($record["old_date"])); ?></td>
                          <td style="font-weight: 100;"><?php echo "<b>".$extend_duration[0]."</b>year(s) <b>".$extend_duration[1]."</b>month(s) <b>".$extend_duration[2]."</b>day(s)"; ?></td>
                          <td style="font-weight: 100;"><?php echo date("d-m-Y",strtotime($record["new_date"])); ?></td>
                        </tr>
              <?php     
                        $n++;
                      }
                    }
                  }
                  if($status_type == "" || $status_type == "medical"){
              ?>
                  <tr>
                    <td colspan="5" align="center" bgcolor="#000000" style="color: #ffffff;">Medical Insurance Record</td>
                  </tr>
                  <tr>
                    <th>#</th>
                    <th>Before Extend</th>
                    <th>Extend Duration</th>
                    <th>After Extend</th>
                    <th></th>
                  </tr>
              <?php
                    $n = 1;
                    foreach($statuses_records as $record){
                      if($record["staffid"] == $staff["staffid"] && $record["statustype"] == "medical"){
                        $extend_duration = explode(",",$record["extend_duration"]);
              ?>
                        <tr>
                          <td><?php echo $n; ?></td>
                          <td style="font-weight: 100;"><?php echo date("d-m-Y",strtotime($record["old_date"])); ?></td>
                          <td style="font-weight: 100;"><?php echo "<b>".$extend_duration[0]."</b>year(s) <b>".$extend_duration[1]."</b>month(s) <b>".$extend_duration[2]."</b>day(s)"; ?></td>
                          <td style="font-weight: 100;"><?php echo date("d-m-Y",strtotime($record["new_date"])); ?></td>
                        </tr>
              <?php  
                        $n++;
                      }
                    }
                  }
              ?>
                  <tr>
                    <td colspan="5"><br><br></td>
                  </tr>
          <?php
                }
              }
          ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- box loading -->
<div id="box-loading"></div>
<?php init_tail(); ?>
</body>
</html>
