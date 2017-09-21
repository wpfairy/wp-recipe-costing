<?php  
/**
 * Plugin Name: WP Recipe Costing
 * 
 * Plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the
 * plugin admin area. This file also includes all of the dependencies used by
 * the plugin, registers the activation and deactivation functions, and defines
 * a function that starts the plugin.
 *
 * @link              https://github.com/wpfairy/wp-recipe-costing/wpf-recipe-costing/
 * @since             0.1.1
 * @package           WpfRecipeCosting
 *
 * @wordpress-plugin
 * Plugin Name:       WP Recipe Costing
 * Plugin URI:        https://github.com/wpfairy/wp-recipe-costing/
 * Description:       With this plugin you can create, manage, and scale your recipes, costing cards from within WordPress. Option to import recipes from WP Ultimate Recipes.
 * Version:           0.1.1
 * Author:            WPFairy LLC
 * Author URI:        https://wpfairy.com/
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package    Wpf_Recipe_Costing
*/

use Wpf_Recipe_Costing\Includes;
use Wpf_Recipe_Costing\Includes\Admin;

// If this file is accessed directory, then abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

$WpfRecipeCosting_minimalRequiredPhpVersion = '5.0';

/**
 * Check the PHP version and give a useful error message if the user's version is less than the required version
 * @return boolean true if version check passed. If false, triggers an error which WP will handle, by displaying
 * an error message on the Admin page
 */
function WpfRecipeCosting_noticePhpVersionWrong() {
    global $WpfRecipeCosting_minimalRequiredPhpVersion;
    echo '<div class="updated fade">' .
      __('Error: plugin "WP Recipe Costing" requires a newer version of PHP to be running.',  'wpf-recipe-costing').
            '<br/>' . __('Minimal version of PHP required: ', 'wpf-recipe-costing') . '<strong>' . $WpfRecipeCosting_minimalRequiredPhpVersion . '</strong>' .
            '<br/>' . __('Your server\'s PHP version: ', 'wpf-recipe-costing') . '<strong>' . phpversion() . '</strong>' .
         '</div>';
}


function WpfRecipeCosting_PhpVersionCheck() {
    global $WpfRecipeCosting_minimalRequiredPhpVersion;
    if (version_compare(phpversion(), $WpfRecipeCosting_minimalRequiredPhpVersion) < 0) {
        add_action('admin_notices', 'includes/WpfRecipeCosting_noticePhpVersionWrong');
        return false;
    }
    return true;
}


/**
 * Initialize internationalization (i18n) for this plugin.
 * References:
 *      http://codex.wordpress.org/I18n_for_WordPress_Developers
 *      http://www.wdmac.com/how-to-create-a-po-language-translation#more-631
 * @return void
 */
function WpfRecipeCosting_i18n_init() {
    $pluginDir = dirname(plugin_basename(__FILE__));
    load_plugin_textdomain('wpf-recipe-costing', false, $pluginDir . '/languages/');
}


//////////////////////////////////
// Run initialization
/////////////////////////////////

// Initialize i18n
add_action('plugins_loadedi', 'includes/WpfRecipeCosting_i18n_init');

// Run the version check.
// If it is successful, continue with initialization for this plugin
if (WpfRecipeCosting_PhpVersionCheck()) {
    // Only load and run the init function if we know PHP version can parse it
    include_once('includes/wpf-recipe-costing_init.php');
    WpfRecipeCosting_init(__FILE__);
    
    // Include the autoloader so we can dynamically include the rest of the classes.
    require_once( trailingslashit( dirname( __FILE__ ) ) . 'includes/autoload.php' );
}
