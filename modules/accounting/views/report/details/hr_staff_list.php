<div id="accordion">
  <div class="card">
    <table class="tree">
      <tbody>
        <tr>
            <td colspan="5">
                <h3 class="text-center no-margin-top-20 no-margin-left-24"><?php echo get_option('companyname'); ?></h3>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <td colspan="5">
              <h4 class="text-center no-margin-top-20 no-margin-left-24"><?php echo 'Staff Members List'; ?></h4>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <td colspan="5">
              <p class="text-center no-margin-top-20 no-margin-left-24"><?php echo _d($from_date) .' - '. _d($to_date); ?></p>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          <tr class="tr_header">
            <td class="text-bold"><?php echo _l('Full Name'); ?></td>
            <td class="text-bold"><?php echo _l('Email'); ?></td>
            <td class="text-bold"><?php echo _l('Role'); ?></td>
            <td class="text-bold"><?php echo _l('Joining Date'); ?></td>
          </tr>
        <?php
          $row_index = 1;
          $total = 0;
          ?>
          <?php 
        foreach ($data_report as $key => $value) {
          ?>

          <tr class="treegrid-0 parent-node expanded">
            <td class="parent"><?php echo $value['firstname']." ".$value['lastname']; ?></td>
            <td><?php echo $value['email']; ?></td>
            <td><?php if($value['name'] != NULL){ echo $value['name']; } else { echo "Admin"; } ?></td>
            <td><?php echo _d($value['datecreated']); ?></td>
          </tr>
        <?php } ?>
        </tbody>
    </table>
  </div>
</div>