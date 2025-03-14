<?php
/**
 * Plugin Name: Easy Google Analytics Integration - DoubleDome
 * Description: Seamlessly incorporate Google Analytics integration into the website using this easy-to-use Google Analytics integration plugin. Whether you're working on a Google Analytics install on your website for the first time or need help with Google Analytics integration, this tool simplifies the process. You can do Google Analytics 4 Setup effortlessly by following the straightforward steps provided. Simply add your GA4 ID, specify page locations, and customize any GTag objects or code as needed—all from a single settings page.
 * Tags: GA, GA4, Google Analytics, Google Analytics 4, Google Analytics Setup, Google Analytics integration in website, Google Analytics install, Google analytics integration, google analytics integrations, GA4 integration, Google Analytics 4 setup, Google Analytics 4 WordPress, ga4 installation, Google Analytics setup, Google Analytics help
 * Author: 		DoubleDome Digital Marketing
 * Author URI: 	https://www.doubledome.com/google-analytics-4-wordpress-plugin
 * Version: 	1.4
 * License:  	GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: doubledome-google-analytics
*/

if( ! defined( 'ABSPATH' ) ) die(); // Stop execution if accessed directly

define( 'GOOGLE_ANALYTICS_DD_ROOT', __DIR__ ); // Setup plugin directory Root path
define( 'GOOGLE_ANALYTICS_DD_URL', plugins_url('',__FILE__) ); // Setup plugin directory Root path
define( 'GA_DD_VERSION', "1.4");

require_once(GOOGLE_ANALYTICS_DD_ROOT . '/includes/gafunctions.php');

add_action( "plugins_loaded", "init_ga_dd" );  // Load initial Configuration