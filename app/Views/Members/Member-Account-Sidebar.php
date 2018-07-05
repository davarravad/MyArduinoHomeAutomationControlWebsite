<?php
/**
* Account Sidebar View
*
* UserApplePie
* @author David (DaVaR) Sargent <davar@userapplepie.com>
* @version 4.2.1
*/

use Libs\Language;
?>

<div class='col-lg-4 col-md-4 col-sm-12'>
  <div class='card border-primary mb-3'>
    <div class='card-header h4' style='font-weight: bold'>
      <?=Language::show('mem_act_settings_title', 'Members'); ?>
    </div>
    <ul class='list-group list-group-flush'>
      <li class='list-group-item'><a href='<?=DIR?>Edit-Profile' rel='nofollow'><?=Language::show('mem_act_edit_profile', 'Members'); ?></a></li>
      <li class='list-group-item'><a href='<?=DIR?>Change-Email' rel='nofollow'><?=Language::show('mem_act_change_email', 'Members'); ?></a></li>
      <li class='list-group-item'><a href='<?=DIR?>Change-Password' rel='nofollow'><?=Language::show('mem_act_change_pass', 'Members'); ?></a></li>
      <li class='list-group-item'><a href='<?=DIR?>Privacy-Settings' rel='nofollow'><?=Language::show('mem_act_privacy_settings', 'Members'); ?></a></li>
    </ul>
  </div>
</div>
