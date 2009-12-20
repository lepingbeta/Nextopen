<div id="header">
  <h1>Header Header</h1>
    <a href="/"><?php echo $sys_lang['hd_home_btn']?></a>
    <?php if( $_SESSION[SYS_NAME]['user']['id'] > 0 ): ?>
      <a href="/user/settings"><?php echo $sys_lang['hd_settings_btn']?></a>
      <a href="/user/signout"><?php echo $sys_lang['hd_signout_btn']?></a>
    <?php else:?>
      <a href="/user/signin"><?php echo $sys_lang['hd_signin_btn']?></a>
      <a href="/user/signup"><?php echo $sys_lang['hd_signup_btn']?></a>
    <?php endif;?>
  <!-- end #header -->
</div>