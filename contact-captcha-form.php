<?php
/*
 * Plugin Name: Contact Captcha Form
 * Plugin URI: http://wellrootedmedia.com/plugins/contact-captcha-form/
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
     * CFFContact Settings
     */
    register_setting( 'cff-setting-group', 'cff-contact-name' );
    register_setting( 'cff-setting-group', 'cff-contact-email' );
    register_setting( 'cff-setting-group', 'cff-contact-phone' );
    register_setting( 'cff-setting-group', 'cff-contact-website' );
    register_setting( 'cff-setting-group', 'cff-contact-message' );
    register_setting( 'cff-setting-group', 'cff-contact-verify' );

    /*
     * CFFReservation Settings
     */
    register_setting( 'cff-setting-group', 'cff-register-select' );
    register_setting( 'cff-setting-group', 'cff-register-name' );
    register_setting( 'cff-setting-group', 'cff-register-email' );
    register_setting( 'cff-setting-group', 'cff-register-phone' );
    register_setting( 'cff-setting-group', 'cff-register-date' );
    register_setting( 'cff-setting-group', 'cff-register-note' );
    register_setting( 'cff-setting-group', 'cff-register-verify' );
}



function ccf_settings_page() {
?>
<div class="wrap">
    <h2><?php _e('CFF Plugin Options', 'CFF-plugin'); ?></h2>

    <form method="post" accept="options.php">
        <?php settings_fields( 'cff-setting-group' ); ?>
        <table class="form-table">

            <tr valign="top">
                <th scope="row"><?php _e('Name', 'CFF-plugin' ); ?></th>
                <td><input type="text" name="cff-contact-name" value="<?php echo get_option( 'cff-contact-name' ); ?>" /></td>
            </tr>

            <tr valign="top">
                <th scope="row"><?php _e('Email', 'CFF-plugin' ); ?></th>
                <td><input type="text" name="cff-contact-email" value="<?php echo get_option( 'cff-contact-email' ); ?>" /></td>
            </tr>

            <tr valign="top">
                <th scope="row"><?php _e('URL', 'CFF-plugin' ); ?></th>
                <td><input type="text" name="cff-contact-website" value="<?php echo get_option( 'cff-contact-website' ); ?>" /></td>
            </tr>

        </table>
        <p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes', 'CFF-plugin' );?>"/></p>
    </form>
</div>
<?php
}

/*
 * Register css for forms
 */
wp_register_style( 'contactCss', plugins_url( 'css/contact.css' , __FILE__ ) );
wp_register_style( 'registerCss', plugins_url( 'css/register.css' , __FILE__ ) );

function cff_contact_form() {
    wp_enqueue_style( 'contactCss' );
    include( plugin_dir_path( __FILE__ ) . 'contact-form.php');
}

function cff_register_form() {
    wp_enqueue_style( 'registerCss' );
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