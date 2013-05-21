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

//    $table_name = $wpdb->prefix . "cff_data";
//
//    $cff_db_version = "1.0";
//
//    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
//        $sql = "CREATE TABLE " . $table_name . " (
//        id mediumint(9) NOT NULL AUTO_INCREMENT,
//        time bigint(11) DEFAULT '0' NOT NULL,
//        name tinyint NOT NULL,
//        text text NOT NULL,
//        url VARCHAR(55) NOT NULL,
//        UNIQUE KEY id (id)
//        );";
//
//        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
//        dbDelta($sql);
//
//        add_option('cff_db_version', $cff_db_version);
//    }
//
//    $installed_ver = get_option('cff_db_version');
//
//    if( $installed_ver != $cff_db_version) {
//        update_option('cff_db_version', $cff_db_version);
//    }

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

//    add_submenu_page(
//        __FILE__,
//        "CFF Register Settings",
//        "CFF Register",
//        "administrator",
//        __FILE__.'template_settings',
//        'ccf_settings_template'
//    );
//
//    // adds link to settings link
//    add_options_page( 'CFF Settings Page', 'CFF Settings', 'administrator', __FILE__, 'ccf_settings_page' );

    // call register settings function
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





// execute our settings section function
add_action('admin_init', 'ccf_settings_init' );

function ccf_settings_init() {
    // create the new setting section on the settings > reading page
    add_settings_section('ccf_setting_section', 'CFF Plugin Settins', 'ccf_setting_section', 'reading' );
    // register the individual settin options
    add_settings_field(
        'ccf_setting_enable_id', // unique id for the field
        'Enable CFF Plugin?', // passing the title of the field
        'ccf_setting_enabled', // callback function to display option field
        'reading', // setting page where it should be displayed
        'ccf_setting_section' // name of section we are adding field to
    );
    add_settings_field(
        'ccf_saved_setting_name_id', // unique id for the field
        'Your Name', // passing the title of the field
        'ccf_setting_name', // callback function to display option field
        'reading', // setting page where it should be displayed
        'ccf_setting_section' // name of section we are adding field to
    );
    // register our setting to store our array of values
    register_setting( 'reading', 'ccf_setting_values' );
}

// setting section
function ccf_setting_section() {
    echo '<p>Configure the CFF plugin options below</p>';
}

// create the enabled checkbox option to save the checkbox value
function ccf_setting_enabled() {
    // load our options array
    $ccf_options = get_option('ccf_setting_values');

    // if the option exists the checkbox needs to be checked
    if ( $ccf_options['enabled'] ) {
        $checked = ' checked="checked" ';
    }

    // dispay the checkbox form field
    echo '<input ' . $checked . ' name = "ccf_setting_values[enabled]" type = "checkbox" />Enabled';
}

// create the text field setting to save the name
function ccf_setting_name() {
    // load the option value
    $ccf_options = get_option( 'ccf_setting_values' );
    $name = $ccf_options['name'];

    // display the text form field
    echo '<input type="text" name="ccf_setting_values[name]" value="' . esc_attr($name) . '" />';
}


add_action( 'admin_init', 'ccf_meta_box_init' );

function ccf_meta_box_init() {
    // create out custom meta box
    add_meta_box('CFF-meta', __('Product Information', 'CFF-plugin'), 'ccf_meta_box', 'post', 'side', 'default' );

    // hook to save our meta box data when the post is saved
    add_action( 'save_post', 'ccf_save_meta_box' );
}

function ccf_meta_box( $post, $box ) {
    // retrieve our custom meta box values
    $featured = get_post_meta( $post->ID, '_ccf_type', true );
    $ccf_price = get_post_meta( $post->ID, '_ccf_price', true );

    // custom meta box form elements
    echo '<p>' . __('Price', 'CFF-plugin') . ': <input type="text" name="ccf_price" value="' . esc_attr( $ccf_price ) . '" size="5" /></p><p>' . __('Type', 'CFF-plugin') . ': <select name = "ccf_product_type" id = "ccf_product_type"><option value = "0" ' . (is_null($featured) || $featured == '0' ? ' selcted="selected" ' : '' ).'>Normal</option><option value = "1" ' . ($featured == '1' ? 'seleceted="selected" ' : '' ) . '>Special</option><option value = "2" ' . ($featured == '2' ? 'selected="selected" ' : '' ) . '>Featured</option><option value = "3" ' . ($featured == '3' ? 'selected="selected" ' : '' ) . '>Clearance</option></select></p>';
}


function ccf_save_meta_box( $post_id ) {
    // if postis a revision skip saving our meta box data
    //if ( $post->post_type == 'revision' ) { return; }

    // process form data if $_post is set
    if ( isset( $_POST['ccf_product_type'] ) ) {
        // save the meta box as post meta using the post ID as a unique prefix
        update_post_meta( $post_id, '_ccf_type', esc_attr( $_POST['ccf_product_type'] ) );
        update_post_meta( $post_id, '_ccf_price', esc_attr( $_POST['ccf_price'] ) );
    }
}

function ccf_get_the_data_on_page( $post ) {
    $ccf_type = get_post_meta( $post->ID, '_ccf_type', true );
    $ccf_price = get_post_meta( $post->ID, '_ccf_price', true );
    $ccf_type_array = array( "0" => "Normal", "1" => "Special", "2" => "Featured", "3" => "Clearance" );
        switch ( $ccf_type ) {
            /*
             * case 1:
               echo "Number 1";
               break;
             case 2:
               echo "Number 2";
               break;
             case 3:
               echo "Number 3";
               break;
             default:
               echo "No number between 1 and 3";
             */
            case 0:
                echo '<p>PRICE: ' . esc_html( $ccf_price ) . '</p>';
                echo '<p>TYPE:  Normal</p>';
                break;
            case 1:
                echo '<p>PRICE: ' . esc_html( $ccf_price ) . '</p>';
                echo '<p>TYPE:  Special</p>';
                break;
            case 2:
                echo '<p>PRICE: ' . esc_html( $ccf_price ) . '</p>';
                echo '<p>TYPE:  Featured</p>';
                break;
            case 3:
                echo '<p>PRICE: ' . esc_html( $ccf_price ) . '</p>';
                echo '<p>TYPE:  Clearance</p>';
                break;
            default:
                echo 'No price and type at this time';
        }
}

function cff_contact_form() {
    include( plugin_dir_path( __FILE__ ) . 'contact-form.php');
}

function cff_register_form() {
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