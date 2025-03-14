<?php
if( ! defined( 'ABSPATH' ) ) die(); // Stop execution if accessed directly

// Initiate Admin menus and Frontend script on plugin load
function init_ga_dd() {
  	add_action( 'admin_menu', 'ga_dd_admin_menu' );
	add_action( 'admin_init', 'ga_dd_admin_init' );
	add_action('admin_enqueue_scripts', 'ga_dd_admin_scripts');

  	if(ga_dd_analytics_id()){
		$custom_code = ga_dd_custom_code();
		if(isset($custom_code['location']) && $custom_code['location'] == "footer") {
  			add_action( 'wp_footer', 'ga_dd_header_scirpt_render' );	// Add Google Analytics script in WP Footer
		}
		else {
  			add_action( 'wp_head', 'ga_dd_header_scirpt_render' );	// Add Google Analytics script in WP Head
		}
  	}
}

// Admin Menus
function ga_dd_admin_menu() {
	$iconurl = GOOGLE_ANALYTICS_DD_URL . "/assets/doubledome.png"; // Icon to show on menu
    add_menu_page(__('DoubleDome - Google Analytics Setup','doubledome-google-analytics'), __('DoubleDome GA4','doubledome-google-analytics'), 'manage_options', 'ga-dd', 'dd_ga_admin_page', $iconurl); // Menu item in WP Admin sidebar
}

// Admin Setting Page
function dd_ga_admin_page() {
	$ga_dd_options = ga_dd_custom_code();
	$checkedhead = "";
	$checkedfooter = "";
	$checkedbefore = "";
	$checkedafter = "";

	if(isset($ga_dd_options['location']) && $ga_dd_options['location'] == "footer") {
		$checkedfooter = ' checked="checked"';
	}
	else {
		$checkedhead = ' checked="checked"';
	}

	if(isset($ga_dd_options['custom_code_location']) && $ga_dd_options['custom_code_location'] == "after") {
		$checkedafter = ' checked="checked"';
	}
	else {
		$checkedbefore = ' checked="checked"';
	}
	?>
    <div class="wrap">
        <h1><?php echo esc_html__( 'DoubleDome - Google Analytics Setup','doubledome-google-analytics' ); ?></h1>
        
        <div class="metabox-holder">
        	<div class="meta-box-sortables ui-sortable">
                <div id="ga_dd_plugin_overview" class="postbox ga_dd_box">
					
					<h2 class="<?php if (isset($_GET['settings-updated'])) echo 'close'; else echo 'open'; ?>"><?php esc_html_e('OVERVIEW & HOW TO USE', 'doubledome-google-analytics'); ?></h2>
					
					<div class="toggle<?php if (isset($_GET['settings-updated'])) echo ' default-hidden'; ?>">
						
						<div class="ga_dd_overview">															
                            <table border="0">
								<tbody>
                                	<tr>
										<td class="icontd"><img src="<?php echo GOOGLE_ANALYTICS_DD_URL; ?>/assets/dd-ga4-overview-icon.gif" alt="<?php esc_html_e('OVERVIEW & HOW TO USE', 'doubledome-google-analytics'); ?>"></td>
                                        <td class="overviewtd">
											<h3><?php esc_html_e('Overview:', 'doubledome-google-analytics'); ?></h3>
                                            <p><?php esc_html_e('This very simple plugin allows you to quickly connect your website to your Google Analytics account for complete tracking. Within one setting view you\'ll be able to add your GA 4 ID, page location, and any desired custom GTag objects and custom code.', 'doubledome-google-analytics'); ?></p>
                                            <h3><?php esc_html_e('How To Use:', 'doubledome-google-analytics'); ?></h3>
                                            <ol>
                                                <li><?php esc_html_e('Create a GA 4 account:', 'doubledome-google-analytics'); ?> <a href="https://support.google.com/analytics/answer/9306384" target="_blank">https://support.google.com/analytics/answer/9306384</a></li>
                                                <li><?php esc_html_e('Take note of your tracking ID', 'doubledome-google-analytics'); ?></li>
                                                <li><?php esc_html_e('Add your tracking ID to the plugin settings', 'doubledome-google-analytics'); ?></li>
                                                <li><?php esc_html_e('Select location of the tracking code (not Google recommendation)', 'doubledome-google-analytics'); ?></li>
                                                <li><?php esc_html_e('Set any custom Gtag Object(s) if desired', 'doubledome-google-analytics'); ?></li>
                                                <li><?php esc_html_e('Set any Custom Code to be included with your tracking script', 'doubledome-google-analytics'); ?></li>
                                                <li><?php esc_html_e('Set location of your Custom Code if necessary', 'doubledome-google-analytics'); ?></li>
                                            </ol>
                                        </td>
                                    </tr>
                                </table>
							</div>
							
						</div>
						
					</div>                
                </div>
        		
                <div id="ga_dd_plugin_settings" class="postbox ga_dd_box">
                	<h2 class="<?php if (isset($_GET['settings-updated'])) echo 'open'; else echo 'close'; ?>"><?php esc_html_e('PLUGIN SETTINGS', 'doubledome-google-analytics'); ?></h2>
                    <div class="toggle<?php if (!isset($_GET['settings-updated'])) echo ' default-hidden'; ?>">
                    <form action='options.php' method='post'>
                        <?php
                            dd_validation_check();
                            settings_fields( 'ga-dd_settings-option' );
                            do_settings_sections( 'ga-dd' ); 
                        ?>
                        <div class="postbox ga_dd_box">
                        <table class="form-table" role="presentation">
                            <tbody>
                                <tr>
                                    <?php $ga_dd_id = get_option( 'ga_dd_id' ) ? get_option( 'ga_dd_id' ) : ''; ?>
                                    <th scope="row" style="width: 20%;"><?php echo esc_html__('Google Analytics 4 Measurement ID: ','doubledome-google-analytics'); ?></th>
                                    <td>
                                        <input type="text" id="ga_dd_id" placeholder="G-XXXXXXXXXX" name="ga_dd_id" value="<?php echo esc_attr( $ga_dd_id ); ?>"/>
                                        <p class="description"><?php echo esc_html__( 'Sample Measurement ID:  G-9AC00000B2','doubledome-google-analytics'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" style="width: 20%;"><?php echo esc_html__('Measurement ID Location: ','doubledome-google-analytics'); ?></th>
                                    <td>
                                        <div class="dd-option">
                                            <input type="radio" name="ga_dd_code[location]" value="header"<?php echo $checkedhead; ?> /> 
                                            <label><?php echo esc_html__('Include tracking code in page head (inside', 'doubledome-google-analytics'); ?> <code>&lt;head&gt;&lt;/head&gt;</code>) - <?php echo esc_html__('Google recommends this option', 'doubledome-google-analytics'); ?></label>
                                        </div>
                                        <div class="dd-option">
                                            <input type="radio" name="ga_dd_code[location]" value="footer"<?php echo $checkedfooter; ?> /> 
                                            <label><?php echo esc_html__('Include tracking code in page footer (near', 'doubledome-google-analytics'); ?> <code>&lt;/body&gt;</code>)</label>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" style="width: 20%;"><?php echo esc_html__('Custom GTag Objects: ','doubledome-google-analytics'); ?></th>
                                    <td>
                                        <textarea id="ga_dd_code[gtag_object]" name="ga_dd_code[gtag_object]" type="textarea" rows="4" cols="50"><?php if(isset($ga_dd_options['gtag_object'])) echo esc_textarea($ga_dd_options['gtag_object']); ?></textarea>
                                        <p class="description"><?php echo esc_html__( 'Any code entered here will be added to gtag(\'config\').','doubledome-google-analytics'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" style="width: 20%;"><?php echo esc_html__('Custom Code:','doubledome-google-analytics'); ?></th>
                                    <td>
                                        <textarea id="ga_dd_code[custom_code]" name="ga_dd_code[custom_code]" type="textarea" rows="4" cols="50"><?php if(isset($ga_dd_options['custom_code'])) echo esc_textarea($ga_dd_options['custom_code']); ?></textarea>
                                        <p class="description"><?php echo esc_html__( 'Any code entered here will be added to the head or footer based on your above selection of the Measurement ID Location. Please use <script>, <style> or proper tags to enclose your code.','doubledome-google-analytics'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" style="width: 20%;"><?php echo esc_html__('Custom Code Location: ','doubledome-google-analytics'); ?></th>
                                    <td>
                                        <div class="dd-option">
                                            <input type="radio" name="ga_dd_code[custom_code_location]" value="before"<?php echo $checkedbefore ?> /> 
                                            <label><?php echo esc_html__('Place Custom Code before the GTag code','doubledome-google-analytics'); ?></label>
                                        </div>
                                        <div class="dd-option">
                                            <input type="radio" name="ga_dd_code[custom_code_location]" value="after"<?php echo $checkedafter ?> /> 
                                            <label><?php echo esc_html__('Place Custom Code after the GTag code','doubledome-google-analytics'); ?></label>
                                        </div>
                                    </td>
                                </tr>
                                    
                            </tbody>
                        </table>
                        <div>
                            <?php submit_button(); ?>
                        </div>
                        </div>
                    </form>
                    </div>
                </div>
                
                <div id="ga_dd_plugin_support" class="postbox ga_dd_box">
                	<h2 class="close"><?php esc_html_e('SUPPORT', 'doubledome-google-analytics'); ?></h2>
                    <div class="toggle default-hidden">
                	<p><?php echo esc_html__('If you need any help, please send an email to','doubledome-google-analytics'); ?> <a href="mailto:pluginsupport@doubledome.com?subject=GA 4 Plugin Support: DoubleDome Digital Marketing">pluginsupport@doubledome.com</a>.</p>
                    </div>
                </div>                
    </div>
	<?php 
}

function dd_validation_check() {
	$ga_dd_id = get_option( 'ga_dd_id' ) ? get_option( 'ga_dd_id' ) : '';
	if( isset($_GET['settings-updated'])) { 
		if($ga_dd_id):
		?>
			<div class="updated notice is-dismissible"> 
				<p><strong><?php echo esc_html__('Google Analytics 4 settings saved successfully.','doubledome-google-analytics') ?></strong></p>
			</div>
		<?php else: ?>
			<div class="notice notice-error is-dismissible"> 
				<p><strong><?php echo esc_html__('No Measurement ID Found. Please enter a valid Google Analytics 4 Measurement ID. .','doubledome-google-analytics') ?></strong></p>
			</div>
		<?php
		endif;
	}
}

// Options table Setting field setup
function ga_dd_admin_init(){
	register_setting( 'ga-dd_settings-option', 'ga_dd_id' );
	register_setting( 'ga-dd_settings-option', 'ga_dd_code' );
}

// Print code in HTML Head
function ga_dd_header_scirpt_render(){
	$ga_dd_options = ga_dd_custom_code();
	
	if(isset($ga_dd_options['gtag_object']) && $ga_dd_options['gtag_object'] != "") {
		$gtagobject = $ga_dd_options['gtag_object'];
	}
	else {
		$gtagobject = "";
	}
	
	if(isset($ga_dd_options['custom_code']) && $ga_dd_options['custom_code'] != "") {
		$custom_code = $ga_dd_options['custom_code'];
	}
	else {
		$custom_code = "";
	}
	
	if(isset($ga_dd_options['custom_code_location']) && $ga_dd_options['custom_code_location'] == "after") {
		$custom_code_location = "after";
	}
	else {
		$custom_code_location = "before";
	}

	$allowed_html_for_custom_code = 
		array(
			'link'		=> array(
				'rel' 	=> array(),
				'href' 	=> array(),
				'as'	=> array(),
				'hreflang'	=> array(),
				'media'		=> array(),
				'sizes'	=> array(),
				'title'	=> array(),
				'type'	=> array(),
				'crossorigin'	=> array(),
				'referrerpolicy'	=> array()
			),
			'script'	=> array(
				'type'	=> array(),
				'src'	=> array(),
				'async'	=> array(),
				'defer'	=> array(),
				'integrity'	=> array(),
				'nomodule'	=> array(),
				'referrerpolicy'	=> array(),
				'crossorigin'	=> array()
			),
			'style'		=> array(
				'type'	=> array(),
				'media'	=> array()
			),
			'meta'		=> array(
				'charset'	=> array(),
				'content'	=> array(),
				'name'	=> array(),
				'http-equiv'	=> array()
			)
		);
	if($custom_code_location == "before") {
		echo wp_kses($custom_code, $allowed_html_for_custom_code);
		gtagjs(ga_dd_analytics_id(), $gtagobject);
	}
	else {
		gtagjs(ga_dd_analytics_id(), $gtagobject);
		echo wp_kses($custom_code, $allowed_html_for_custom_code);
	}
}

function gtagjs($analytics_id = '', $gtagobject = '') {
	?>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr($analytics_id); ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '<?php echo esc_attr($analytics_id); ?>'<?php if ($gtagobject) { ?>, '<?php echo esc_js(stripslashes(rtrim($gtagobject))); ?>'<?php } ?>);
</script>
    <?php
}

// Get Google Analytics ID saved into settings
function ga_dd_analytics_id() {
	$ga_dd_id = get_option('ga_dd_id') ? get_option('ga_dd_id') : '';
	return $ga_dd_id;
}

// Get Custom Options saved into settings
function ga_dd_custom_code() {
	$ga_dd_code = get_option('ga_dd_code') ? get_option('ga_dd_code') : array();
	return $ga_dd_code;
}

// Load the CSS file in Admin
function ga_dd_admin_scripts($hook) {
	if ($hook === 'toplevel_page_ga-dd') {			
		wp_enqueue_style('google-analytics-dd', GOOGLE_ANALYTICS_DD_URL .'/assets/admin_settings.css', array(), GA_DD_VERSION);
		wp_enqueue_script('google-analytics-dd', GOOGLE_ANALYTICS_DD_URL .'/assets/admin_settings.js', array(), GA_DD_VERSION);
	}
}
?>