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
          <a href="<?php echo admin_url('accounting/report'); ?>"><?php echo _l('back_to_report_list'); ?></a>
          <hr />
          <div class="row">
            <div class="col-md-10">
              <div class="row">
              <?php echo form_open(admin_url('accounting/view_report'),array('id'=>'filter-form')); ?>
                <div class="col-md-3">
                  <?php echo render_date_input('from_date','from_date', _d($from_date)); ?>
                </div>
                <div class="col-md-3">
                  <?php echo render_date_input('to_date','to_date', _d($to_date)); ?>
                </div>
                <div class="col-md-3">
                  <?php 
                  $method = [
                          1 => ['id' => 'cash', 'name' => _l('cash')],
                          2 => ['id' => 'accrual', 'name' => _l('accrual')],
                         ];
                  echo render_select('accounting_method', $method, array('id', 'name'),'accounting_method', $accounting_method, array(), array(), '', '', false);
                  ?>
                </div>
                <div class="col-md-3">
                  <?php echo form_hidden('type', 'balance_sheet_comparison'); ?>
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
                      <p class="text-center no-margin-top-20 no-margin-left-24">00-00-0000 - 00-00-0000</p>
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
                  <tr>
                    <td colspan="5">Staff Name: Xyz</td>
                  </tr>
                  <tr>
                    <th>#</th>
                    <th>Before Extend</th>
                    <th>Extend Duration</th>
                    <th>After Extend</th>
                    <th></th>
                  </tr>
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
