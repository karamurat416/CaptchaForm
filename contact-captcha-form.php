<?php
/*
 * Plugin Name: Contact Captcha Form
 * Plugin URI: http://wellrootedmedia.com/?p=1175
 * Description: This is a simple contact form with a captcha
 * Version: 1.1
 * Author: Well Rooted Media
 * Author URI: http://wellrootedmedia.com
 * License: GPL2
 */


session_start();

register_activation_hook(__FILE__, 'CFF_install');
function cff_install() {
    global $wp_version;
    global $wpdb;

    if ( version_compare( $wp_version, "2.9", "<" ) ) {
        deactivate_plugins(basename(__FILE__));
        wp_die("This plugin requres WordPress version 2.9 or higher.");
    }

}

register_deactivation_hook(__FILE__, 'CFF_uninstall');
function CFF_uninstall() {
    /*
     * TODO: make this drop anything to do with db's
     */
    echo "You have removed this plugin... Sorry to see you go.";
}

/*
 * Add upgrade option
 */
add_option('cff_db_version', '1.0');
register_activation_hook(__FILE__, 'cff_install');

/*
 * create custom plugin setting menu
 */
add_action('admin_menu', 'ccf_create_menu' );
function ccf_create_menu() {
    add_menu_page(
        'CFF Contact Settings', // page title
        'CFF Settings', // menu title
        'administrator', // set this so only admin see the menu
        __FILE__,
        'ccf_settings_page',
        plugins_url( 'images/wordpress.png', __FILE__ )
    );

    add_action( 'admin_init', 'ccf_register_settings' );
}

/*
 * register our settings
 */
function ccf_register_settings() {
    /*
     * TODO: make this an array option
     */
    register_setting( 'cff-settings-group', 'firstName' );
    register_setting( 'cff-settings-group', 'email' );

    register_setting( 'cff-settings-group', 'emailTemplate' );
}



function ccf_settings_page() {
    $firstName = get_option('firstName');
    $email = get_option('email');
    $emailTemplate = get_option('emailTemplate');
?>
    <h1>How to use the contact form plugin</h1>
    <p>At the moment, this is a generic form, and later will have an integration system to add/remove as many options for inputs, textarea's, etc...</p>
    <p>
        To use this plugin, all you have to do is create a new page and add the following code in the content area.
        <br />
        <input type="text" value="[contact_captcha form=contact]" size="30" />
    </p>

    <div class="wrap">
        <h2>Your Plugin Name</h2>

        <form method="post" action="options.php">
            <?php settings_fields( 'cff-settings-group' ); ?>
            <?php //do_settings( 'cff-settings-group' ); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><label for="firstName"><?php _e("First Name"); ?></label></th>
                    <td><input type="text" name="firstName" id="firstName" value="<?php echo $firstName; ?>" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row"><label for="email"><?php _e("Email"); ?></label></th>
                    <td><input type="text" name="email" id="email" value="<?php echo $email; ?>" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row"><label for="emailTemplate"><?php _e("Email Template"); ?></label></th>
                    <td><textarea name="emailTemplate" id="emailTemplate" cols="50" rows="10"><?php echo $emailTemplate; ?></textarea></td>
                </tr>
            </table>

            <?php submit_button(); ?>

        </form>
    </div>
<?php
}

/*
 * Register css for forms
 */
wp_register_style( 'stylesheet', plugins_url( 'css/stylesheet.css' , __FILE__ ) );

function cff_contact_form() {
    wp_enqueue_style( 'stylesheet' );
    include( plugin_dir_path( __FILE__ ) . 'contact-form.php');
}

function cff_register_form() {
    wp_enqueue_style( 'stylesheet' );
    include( plugin_dir_path( __FILE__ ) . 'register-form.php');
}

function cff_contact_shortcode($atts, $content = null) {
    extract( shortcode_atts( array(
        'form' => ''
    ), $atts ) );
    if ( $form == "contact" ) {
        return cff_contact_form();
    }
    elseif ( $form == "register" ) {
        return cff_register_form();
    }
}
add_shortcode( 'contact_captcha', 'cff_contact_shortcode' );

?>