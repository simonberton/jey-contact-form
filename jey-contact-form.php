<?php
/**
 * Plugin Name: Jey Contact Form
 * Description: A plugin that prints a contact form with google recaptcha, has a backend for you users contacts
 * Plugin URI: http://simonberton.com/contact-form-plugin
 * Author: simonberton
 * Author URI: http://simonberton.com/
 * Version: 1.0
 * Text Domain: jey-contact-form
 * License: GPL2
 */

/**
 * Get some constants ready for paths when your plugin grows 
 * 
 */

define( 'DXP_VERSION', '1.6' );
define( 'DXP_PATH', dirname( __FILE__ ) );
define( 'DXP_PATH_INCLUDES', dirname( __FILE__ ) . '/inc' );
define( 'DXP_FOLDER', basename( DXP_PATH ) );
define( 'DXP_URL', plugins_url() . '/' . DXP_FOLDER );
define( 'DXP_URL_INCLUDES', DXP_URL . '/inc' );

require_once __DIR__ . '/autoload.php';


/**
 * 
 * The plugin base class - the root of all WP goods!
 * 
 * @author nofearinc
 *
 */
class JEY_Contact_Form {
	
	/**
	 * 
	 * Assign everything as a call from within the constructor
	 */
	public function __construct() {
		// add script and style calls the WP way 
		// it's a bit confusing as styles are called with a scripts hook
		// @blamenacin - http://make.wordpress.org/core/2011/12/12/use-wp_enqueue_scripts-not-wp_print_styles-to-enqueue-scripts-and-styles-for-the-frontend/
		add_action( 'wp_enqueue_scripts', array( $this, 'dx_add_JS' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'dx_add_CSS' ) );
		
		// add scripts and styles only available in admin
		add_action( 'admin_enqueue_scripts', array( $this, 'dx_add_admin_JS' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'dx_add_admin_CSS' ) );
		
		// register admin pages for the plugin
		add_action( 'admin_menu', array( $this, 'dx_admin_pages_callback' ) );
		
		// register meta boxes for Pages (could be replicated for posts and custom post types)
		//add_action( 'add_meta_boxes', array( $this, 'dx_meta_boxes_callback' ) );
		
		// register save_post hooks for saving the custom fields
		add_action( 'save_post', array( $this, 'dx_save_sample_field' ) );
		
		// Register custom post types and taxonomies
		add_action( 'init', array( $this, 'dx_custom_post_types_callback' ), 5 );
		//add_action( 'init', array( $this, 'dx_custom_taxonomies_callback' ), 6 );
		
		// Register activation and deactivation hooks
		register_activation_hook( __FILE__, 'dx_on_activate_callback' );
		register_deactivation_hook( __FILE__, 'dx_on_deactivate_callback' );
		
		// Translation-ready
		add_action( 'plugins_loaded', array( $this, 'dx_add_textdomain' ) );
		
		// Add earlier execution as it needs to occur before admin page display
		add_action( 'admin_init', array( $this, 'dx_register_settings' ), 5 );

		
		add_action( 'admin_init', array( $this, 'add_contact_meta_boxes' ), 5 );
		add_action( 'save_post', array( $this, 'save_contacts_custom_fields' ) );

		// Add a sample shortcode
		add_action( 'init', array( $this, 'dx_sample_shortcode' ) );
		
		// Add a sample widget
		add_action( 'widgets_init', array( $this, 'dx_sample_widget' ) );
		
		// Add actions for storing value and fetching URL
		// use the wp_ajax_nopriv_ hook for non-logged users (handle guest actions)
 		add_action( 'wp_ajax_store_ajax_value', array( $this, 'store_ajax_value' ) );
 		add_action( 'wp_ajax_fetch_ajax_url_http', array( $this, 'fetch_ajax_url_http' ) );
		
	}	
	
	/**
	 * 
	 * Adding JavaScript scripts
	 * 
	 * Loading existing scripts from wp-includes or adding custom ones
	 * 
	 */
	public function dx_add_JS() {
		
		wp_register_script( 'jquery214min', plugins_url( '/js/jquery-2.1.4.min.js' , __FILE__ ));
		wp_enqueue_script( 'jquery214min' );

		wp_register_script( 'bootsrap', plugins_url( '/js/bootstrap.min.js' , __FILE__ ));
		wp_enqueue_script( 'bootsrap' );

		wp_register_script( 'validator', plugins_url( '/js/validator.js' , __FILE__ ));
		wp_enqueue_script( 'validator' );
	}
	
	
	/**
	 *
	 * Adding JavaScript scripts for the admin pages only
	 *
	 * Loading existing scripts from wp-includes or adding custom ones
	 *
	 */
	public function dx_add_admin_JS( $hook ) {
		wp_enqueue_script( 'jquery' );
	}
	
	/**
	 * 
	 * Add CSS styles
	 * 
	 */
	public function dx_add_CSS() {
		wp_register_style( 'samplestyle', plugins_url( '/css/samplestyle.css', __FILE__ ), array(), '1.0', 'screen' );
		wp_register_style( 'bootstrap', plugins_url( '/css/bootstrap.min.css', __FILE__ ));
		wp_enqueue_style( 'bootstrap' );
		wp_enqueue_style( 'samplestyle' );
	}
	
	/**
	 *
	 * Add admin CSS styles - available only on admin
	 *
	 */
	public function dx_add_admin_CSS( $hook ) {
		wp_register_style( 'samplestyle-admin', plugins_url( '/css/samplestyle-admin.css', __FILE__ ), array(), '1.0', 'screen' );
		wp_enqueue_style( 'samplestyle-admin' );
		
		if( 'toplevel_page_dx-plugin-base' === $hook ) {
			wp_register_style('dx_help_page',  plugins_url( '/help-page.css', __FILE__ ) );
			wp_enqueue_style('dx_help_page');
		}
	}
	
	/**
	 * 
	 * Callback for registering pages
	 * 
	 * This demo registers a custom page for the plugin and a subpage
	 *  
	 */
	public function dx_admin_pages_callback() {
		add_menu_page(__( "JEY Contact Form", 'dxbase' ), __( "JEY Contact Form", 'dxbase' ), 'edit_themes', 'dx-plugin-base', array( $this, 'dx_plugin_base' ) );		
	}
	
	/**
	 * 
	 * The content of the base page
	 * 
	 */
	public function dx_plugin_base() {
		include_once( DXP_PATH . '/help-page.php' );
	}
	
	/**
	 * Save the custom field from the side metabox
	 * @param $post_id the current post ID
	 * @return post_id the post ID from the input arguments
	 * 
	 */
	public function dx_save_sample_field( $post_id ) {
		// Avoid autosaves
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		$slug = 'contact';
		// If this isn't a 'book' post, don't update it.
		if ( ! isset( $_POST['post_type'] ) || $slug != $_POST['post_type'] ) {
			return;
		}
		
		// If the custom field is found, update the postmeta record
		// Also, filter the HTML just to be safe
		if ( isset( $_POST['dx_test_input']  ) ) {
			update_post_meta( $post_id, 'dx_test_input',  esc_html( $_POST['dx_test_input'] ) );
		}
	}
	

	
	/**
	 * Register custom post types
     *
	 */
	public function dx_custom_post_types_callback() {
		register_post_type( 'contact', array(
			'labels' => array(
				'name' => __("Contacts", 'dxbase'),
				'singular_name' => __("Contact", 'dxbase'),
				'add_new' => _x("Add New", 'contact', 'dxbase' ),
				'add_new_item' => __("Add New Contact", 'dxbase' ),
				'edit_item' => __("Edit Contact", 'dxbase' ),
				'new_item' => __("New Contact", 'dxbase' ),
				'view_item' => __("View Contact", 'dxbase' ),
				'search_items' => __("Search Contact", 'dxbase' ),
				'not_found' =>  __("No Contacts found", 'dxbase' ),
				'not_found_in_trash' => __("No Contacts found in Trash", 'dxbase' ),
			),
			'description' => __("Contacts from people", 'dxbase'),
			'public' => true,
			'publicly_queryable' => true,
			'query_var' => true,
			'rewrite' => true,
			'exclude_from_search' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'menu_position' => 40, // probably have to change, many plugins use this
			'supports' => array(
				'title',
				'thumbnail',
				'custom-fields',
				'page-attributes',
			)
		));	
	}

	//AHORA
	function add_contact_meta_boxes() {
		//add_meta_box("contact", "Contact Details", "add_contact_details_contacts_meta_box", "contact", "normal", "low");
	}

	function add_contact_details_contacts_meta_box()
	{
		global $post;
		$custom = get_post_custom( $post->ID );
	 
		?>
		<style>.width99 {width:99%;}</style>
		<p>
			<label> <?php __("First name:") ?></label><br />
			<textarea rows="5" name="first_name" class="width99"><?= @$custom["first_name"][0] ?></textarea>
		</p>
		<p>
			<label><?php __("Last name:") ?></label><br />
			<input type="text" name="last_name" value="<?= @$custom["last_name"][0] ?>" class="width99" />
		</p>
		<p>
			<label><?php __("Phone:") ?></label><br />
			<input type="text" name="phone" value="<?= @$custom["phone"][0] ?>" class="width99" />
		</p>
		<p>
			<label><?php __("Email:") ?></label><br />
			<input type="text" name="email" value="<?= @$custom["email"][0] ?>" class="width99" />
		</p>
		<p>
			<label><?php __("Message:") ?></label><br />
			<input type="text" name="message" value="<?= @$custom["message"][0] ?>" class="width99" />
		</p>
		<?php
	}
	/**
	 * Save custom field data when creating/updating posts
	 */
	function save_contacts_custom_fields(){
	  global $post;
	 
	  if ( $post )
	  {
	    update_post_meta($post->ID, "first_name", @$_POST["first_name"]);
	    update_post_meta($post->ID, "last_name", @$_POST["last_name"]);
	    update_post_meta($post->ID, "phone", @$_POST["phone"]);
	    update_post_meta($post->ID, "message", @$_POST["message"]);
	  }
	}
		//add_action( 'admin_init', 'add_contact_meta_boxes' );
	//FIN
	/**
	 * Initialize the Settings class
	 * 
	 * Register a settings section with a field for a secure WordPress admin option creation.
	 * 
	 */
	public function dx_register_settings() {
		require_once( DXP_PATH . '/jey-contact-form-plugin-settings.class.php' );
		new JEY_ContactForm_Settings();
	}
	
	/**
	 * Register a sample shortcode to be used
	 * 
	 * First parameter is the shortcode name, would be used like: [dxsampcode]
	 * 
	 */
	public function dx_sample_shortcode() {
		add_shortcode( 'jeycontactform', array( $this, 'dx_sample_shortcode_body' ) );
	}
	
	/**
	 * Returns the content of the sample shortcode, like [dxsamplcode]
	 * @param array $attr arguments passed to array, like [dxsamcode attr1="one" attr2="two"]
	 * @param string $content optional, could be used for a content to be wrapped, such as [dxsamcode]somecontnet[/dxsamcode]
	 */
	public function dx_sample_shortcode_body( $attr, $content = null ) {

		$options = get_option('dx_setting', array());
		$google_captcha = "";
		$save_contact = false;

		$csrf_token = md5(date("dmY", time() - 60 * 60 * 24));	

		if($csrf_token === $_POST["token"])
		{
			$save_contact = true;	
		}
		if($options["dx_activate_google_captcha_in"] == "on")
		{
			$save_contact = false;	

			$secret = $options["dx_google_secret"];
			$siteKey = $options["dx_google_api_key"];

			$recaptcha = new \ReCaptcha\ReCaptcha($secret);
			$resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

			$google_captcha = '<div class="g-recaptcha" data-sitekey="'. $siteKey .'"></div>
	        <script type="text/javascript"
	                src="https://www.google.com/recaptcha/api.js?hl='. $lang .'">
	        </script>';
	        
		    if ($csrf_token === $_POST["token"] && isset($_POST['g-recaptcha-response']) && $resp->isSuccess())
		    {
		    	$save_contact = true;	
		    }
		}
		
		if($save_contact)
		{
			$first_name = $_POST["first_name"];
			$last_name = $_POST["last_name"];
			$email = $_POST["email"];
			$message = $_POST["message"];
			$phone = $_POST["phone"];

			$post = array('post_name'   => 'Contact from '. $_POST["email"],
						  'post_title'  => 'Contact from '. $_POST["email"],
						  'post_status' => 'publish',
						  'post_type'   => 'contact');

			$post_id = wp_insert_post( $post ); 

		    update_post_meta($post_id, "first_name", $first_name);
		    update_post_meta($post_id, "last_name", $last_name);
		    update_post_meta($post_id, "phone", $phone);
		    update_post_meta($post_id, "email", $email);
		    update_post_meta($post_id, "message", $message);

		    //Enviar el mail de contacto
		    $to = $options["dx_mail_to"];
		    $subject = $options["dx_mail_subject"];
		    $headers = 'From: '. $options["dx_mail_from_headers_callback"] .'\r\n';

		    $message = "First name: ". $first_name. "\r\nLast name:  " . $last_name . "\r\nEmail: ". $email . "\r\nPhone: ". $phone. "\r\nMessage: ".$message; 

		    wp_mail( $to, $subject, $message, $headers );

			$retorno = 'Se guardo todo ok don niembraaaa';
		}

		$retorno = '<form role="form" id="contact_form" method="post">
						<input type="hidden" name="token" value="'. $csrf_token .'"/>
				        <div class="form-group">
				        	<label class="control-label" for="name">'. __("First name *.") .'</label>
				            <div class="input-group">
				               <input type="text" class="form-control" id="first_name" name="first_name" placeholder="'. __("Enter your first name") .'" />
				               <span class="input-group-addon"></span>
				            </div>
				            <span class="help-block" style="display: none;">'. __("Please enter your name.") .'</span>
				        </div>
			            <div class="form-group">
				        	<label class="control-label" for="name">'. __("Last name *") .'</label>
				            <div class="input-group">
				               <input type="text" class="form-control" id="last_name" name="last_name" placeholder="'. __("Enter your last name") .'" />
				               <span class="input-group-addon"></span>
				            </div>
				            <span class="help-block" style="display: none;">'. __("Please enter your last name.") .'</span>
				        </div>
				          <div class="form-group">
				         	<label class="control-label" for="phone">'. __("Phone *") .'</label>
				        	<div class="input-group">
				            	<input type="tel" class="form-control" id="phone" name="phone" placeholder="'. __("Enter your phone number") .'" />
				            	<span class="input-group-addon"></span>
				          	</div>
				          	<span class="help-block" style="display: none;">'. __("Please enter a valid phone number.") .'</span>
				        </div>
				        <div class="form-group">
				         	<label class="control-label" for="email">'. __("Email Address *") .'</label>
				        	<div class="input-group">
				        		<span class="input-group-addon">@</span>
				            	<input type="email" class="form-control" id="email" name="email" placeholder="'. __("Enter your email") .'"  />
				            	<span class="input-group-addon"></span>
				          	</div>
				          	<span class="help-block" style="display: none;">'. __("Please enter a valid e-mail address.") .'</span>
				        </div>
				        <div class="form-group">
				    	    <label class="control-label" for="message">'. __("Message *") .'</label>
				            <div class="input-group">
				    	        <textarea rows="5" cols="30" class="form-control" id="message" name="message" placeholder="'. __("Enter your message") .'" ></textarea>
				                <span class="input-group-addon"></span>
				            </div>
				            
				        </div>
				       '. $google_captcha .'
				        <div id="solicitud-error" class="alert alert-danger" role="alert" style="display:none">
						  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
						  <span class="inline-block" id="solicitud-error-message">Please enter a message.</span>
						</div>
			        	<button type="submit" id="feedbackSubmit" class="btn btn-primary btn-lg" data-loading-text="Sending..." style="display: block; margin-top: 10px;">'. __("Submit"). '</button>
			    	</form>';
		
		return __( $retorno, 'dxbase');
	}
	
	/**
	 * Hook for including a sample widget with options
	 */
	public function dx_sample_widget() {
		include_once DXP_PATH_INCLUDES . '/dx-sample-widget.class.php';
	}
	
	/**
	 * Add textdomain for plugin
	 */
	public function dx_add_textdomain() {
		load_plugin_textdomain( 'dxbase', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}
	
	/**
	 * Callback for saving a simple AJAX option with no page reload
	 */
	public function store_ajax_value() {
		if( isset( $_POST['data'] ) && isset( $_POST['data']['dx_option_from_ajax'] ) ) {
			update_option( 'dx_option_from_ajax' , $_POST['data']['dx_option_from_ajax'] );
		}	
		die();
	}
	
	/**
	 * Callback for getting a URL and fetching it's content in the admin page
	 */
	public function fetch_ajax_url_http() {
		if( isset( $_POST['data'] ) && isset( $_POST['data']['dx_url_for_ajax'] ) ) {
			$ajax_url = $_POST['data']['dx_url_for_ajax'];
			
			$response = wp_remote_get( $ajax_url );
			
			if( is_wp_error( $response ) ) {
				echo json_encode( __( 'Invalid HTTP resource', 'dxbase' ) );
				die();
			}
			
			if( isset( $response['body'] ) ) {
				if( preg_match( '/<title>(.*)<\/title>/', $response['body'], $matches ) ) {
					echo json_encode( $matches[1] );
					die();
				}
			}
		}
		echo json_encode( __( 'No title found or site was not fetched properly', 'dxbase' ) );
		die();
	}
	
}


/**
 * Register activation hook
 *
 */
function dx_on_activate_callback() {
	// do something on activation
}

/**
 * Register deactivation hook
 *
 */
function dx_on_deactivate_callback() {
	// do something when deactivated
}

// Initialize everything
$dx_plugin_base = new JEY_Contact_Form();
