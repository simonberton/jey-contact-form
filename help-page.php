<div class="wrap">
	<div id="icon-plugins" class="icon32"></div>

	<div class="dx-help-page">
		<div class="content alignleft">
			<h2 class='page-welcome'><?php __('Welcome to <span><strong>Jey</strong>Contact Form Plugin!')?></span></h2>
			<div id="dx-help-content">						
				<form id="dx-plugin-base-form" action="options.php" method="POST">
					<?php settings_fields( 'dx_setting' ) ?>
					<?php do_settings_sections( 'dx-plugin-base' ) ?>
					<input type="submit" value="<?php __( "Save", 'dxbase' ); ?>" />
				</form> <!-- end of #dxtemplate-form -->
			</div>
		</div>
		<div class="sidebar alignright">
			<h2><?php __('About the plugin')?></h2>
			<p><?php __('This plugin is built by')?> <a href="http://simonberton.com" target="_blank">Berton</a>!</p>
			<p><?php __('Find us on')?> <a href="" target="_blank">Facebook</a> and <a href="https://www.facebook.com/bertonweb" target="_blank">Facebook</a></p>
			<p><?php __('If you enjoy using the plugin, go ahead and buy me a beer')?></p>
			<p><?php __('Plugin with flags of countries in telephone field and validation for each one comming on the paid version. Also will let you choose which fields are required and each message for validation')?><p>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="LDJV4R2YQ9KB4">
				<input type="image" src="https://www.paypalobjects.com/es_XC/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
				<img alt="" border="0" src="https://www.paypalobjects.com/es_XC/i/scr/pixel.gif" width="1" height="1">
			</form>
		</div>
	</div>
	
</div>