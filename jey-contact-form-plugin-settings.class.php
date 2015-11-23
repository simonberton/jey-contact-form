<?php

class JEY_ContactForm_Settings {
	
	private $dx_setting;
	/**
	 * Construct me
	 */
	public function __construct() {
		$this->dx_setting = get_option( 'dx_setting', '' );
		
		// register the checkbox
		add_action('admin_init', array( $this, 'register_settings' ) );
	}
		
	/**
	 * Setup the settings
	 * 
	 * Add a single checkbox setting for Active/Inactive and a text field 
	 * just for the sake of our demo
	 * 
	 */
	public function register_settings() {
		register_setting( 'dx_setting', 'dx_setting', array( $this, 'dx_validate_settings' ) );
		
		add_settings_section(
			'dx_settings_section',         // ID used to identify this section and with which to register options
			__( "Configure your settings", 'dxbase' ),                  // Title to be displayed on the administration page
			array($this, 'dx_settings_callback'), // Callback used to render the description of the section
			'dx-plugin-base'                           // Page on which to add this section of options
		);		
	
		add_settings_field(
			'dx_activate_google_captcha_in',                      // ID used to identify the field throughout the theme
			__( "Active Google Captcha: ", 'dxbase' ),                           // The label to the left of the option interface element
			array( $this, 'dx_activate_google_captcha_callback' ),   // The name of the function responsible for rendering the option interface
			'dx-plugin-base',                          // The page on which this option will be displayed
			'dx_settings_section'         // The name of the section to which this field belongs
		);

		add_settings_field(
			'dx_google_api_key',                      // ID used to identify the field throughout the theme
			__( "Google API key: ", 'dxbase' ),       // The label to the left of the option interface element
			array( $this, 'dx_google_api_key_callback' ),   // The name of the function responsible for rendering the option interface
			'dx-plugin-base',                          // The page on which this option will be displayed
			'dx_settings_section'         // The name of the section to which this field belongs
		);
		
		add_settings_field(
			'dx_google_secret',                      // ID used to identify the field throughout the theme
			__( "Google Secret: ", 'dxbase' ),                           // The label to the left of the option interface element
			array( $this, 'dx_google_secret_callback' ),   // The name of the function responsible for rendering the option interface
			'dx-plugin-base',                          // The page on which this option will be displayed
			'dx_settings_section'         // The name of the section to which this field belongs
		);

		add_settings_field(
			'dx_mail_to',                      // ID used to identify the field throughout the theme
			__( "Mail To: ", 'dxbase' ),                           // The label to the left of the option interface element
			array( $this, 'dx_mail_to_callback' ),   // The name of the function responsible for rendering the option interface
			'dx-plugin-base',                          // The page on which this option will be displayed
			'dx_settings_section'         // The name of the section to which this field belongs
		);

		add_settings_field(
			'dx_mail_subject',                      // ID used to identify the field throughout the theme
			__( "Mail Subject: ", 'dxbase' ),                           // The label to the left of the option interface element
			array( $this, 'dx_mail_subject_callback' ),   // The name of the function responsible for rendering the option interface
			'dx-plugin-base',                          // The page on which this option will be displayed
			'dx_settings_section'         // The name of the section to which this field belongs
		);

		add_settings_field(
			'dx_mail_from_headers',                      // ID used to identify the field throughout the theme
			__( "Mail From Headers: ", 'dxbase' ),                           // The label to the left of the option interface element
			array( $this, 'dx_mail_from_headers_callback' ),   // The name of the function responsible for rendering the option interface
			'dx-plugin-base',                          // The page on which this option will be displayed
			'dx_settings_section'         // The name of the section to which this field belongs
		);
	}
	
	public function dx_settings_callback() {
		echo _e('<br><strong>[jeycontactform]</strong> Paste this shortcode whenever you want Jey Contact Form <br><br>If you want to use google ReCaptcha, configure your account, enable it below and add your credentials<br><a href = "https://www.google.com/recaptcha/admin">https://www.google.com/recaptcha/admin</a>', 'dxbase' );
	}

	public function dx_mail_from_headers_callback() {
		$out = '';
		$val = '';
		// check if checkbox is checked
		if(! empty( $this->dx_setting ) && isset ( $this->dx_setting['dx_mail_from_headers_callback'] ) ) {
			$val = $this->dx_setting['dx_mail_from_headers_callback'];
		}

		$out = '<input placeholder="My Name <myname@example.com>" type="text" id="dx_mail_from_headers_callback" name="dx_setting[dx_mail_from_headers_callback]" value="' . $val . '"  />';
		
		echo $out;
	}

	
	public function dx_activate_google_captcha_callback() {
		$enabled = false;
		$out = ''; 
		$val = false;
		
		// check if checkbox is checked
		if(! empty( $this->dx_setting ) && isset ( $this->dx_setting['dx_activate_google_captcha_in'] ) ) {
			$val = true;
		}
		
		if($val) {
			$out = '<input type="checkbox" id="dx_activate_google_captcha_in" name="dx_setting[dx_activate_google_captcha_in]" CHECKED  />';
		} else {
			$out = '<input type="checkbox" id="dx_activate_google_captcha_in" name="dx_setting[dx_activate_google_captcha_in]" />';
		}
		
		echo $out;
	}
	
	public function dx_google_secret_callback() {
		$out = '';
		$val = '';
		
		// check if checkbox is checked
		if(! empty( $this->dx_setting ) && isset ( $this->dx_setting['dx_google_secret'] ) ) {
			$val = $this->dx_setting['dx_google_secret'];
		}

		$out = '<input type="text" id="dx_google_secret" name="dx_setting[dx_google_secret]" value="' . $val . '"  />';
		
		echo $out;
	}

	public function dx_google_api_key_callback() {
		$out = '';
		$val = '';
		
		// check if checkbox is checked
		if(! empty( $this->dx_setting ) && isset ( $this->dx_setting['dx_google_api_key'] ) ) {
			$val = $this->dx_setting['dx_google_api_key'];
		}

		$out = '<input type="text" id="dx_google_api_key" name="dx_setting[dx_google_api_key]" value="' . $val . '"  />';
		
		echo $out;
	}

	public function dx_mail_subject_callback() {
		$out = '';
		$val = '';
		
		// check if checkbox is checked
		if(! empty( $this->dx_setting ) && isset ( $this->dx_setting['dx_mail_subject'] ) ) {
			$val = $this->dx_setting['dx_mail_subject'];
		}

		$out = '<input type="text" id="dx_mail_subject" name="dx_setting[dx_mail_subject]" value="' . $val . '"  />';
		
		echo $out;
	}

	public function dx_mail_to_callback() {
		$out = '';
		$val = '';
		
		// check if checkbox is checked
		if(! empty( $this->dx_setting ) && isset ( $this->dx_setting['dx_mail_to'] ) ) {
			$val = $this->dx_setting['dx_mail_to'];
		}

		$out = '<input type="text" id="dx_mail_to" name="dx_setting[dx_mail_to]" value="' . $val . '"  />';
		
		echo $out;
	}
	
	/**
	 * Helper Settings function if you need a setting from the outside.
	 * 
	 * Keep in mind that in our demo the Settings class is initialized in a specific environment and if you
	 * want to make use of this function, you should initialize it earlier (before the base class)
	 * 
	 * @return boolean is enabled
	 */
	public function is_enabled() {
		if(! empty( $this->dx_setting ) && isset ( $this->dx_setting['dx_opt_in'] ) ) {
			return true;
		}
		return false;
	}
	
	/**
	 * Validate Settings
	 * 
	 * Filter the submitted data as per your request and return the array
	 * 
	 * @param array $input
	 */
	public function dx_validate_settings( $input ) {
		
		return $input;
	}
}
