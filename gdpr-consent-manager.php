<?php
/*
Plugin Name: GDPR Consent Manager
Description: A simple GDPR consent manager plugin for WordPress.
Version: 1.0
Author: Your Name
*/

// Security: Prevent direct access to the file
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register the settings page.
 */
function gdpr_consent_manager_menu() {
    add_options_page(
        __( 'GDPR Consent Manager', 'gdpr-consent-manager' ),
        __( 'GDPR Consent', 'gdpr-consent-manager' ),
        'manage_options',
        'gdpr-consent-manager',
        'gdpr_consent_manager_settings_page'
    );
}
add_action( 'admin_menu', 'gdpr_consent_manager_menu' );

/**
 * Render the settings page.
 */
function gdpr_consent_manager_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'GDPR Consent Manager', 'gdpr-consent-manager' ); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'gdpr_consent_manager_options' );
            do_settings_sections( 'gdpr_consent_manager' );
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

/**
 * Register settings, sections, and fields.
 */
function gdpr_consent_manager_settings_init() {
    register_setting( 'gdpr_consent_manager_options', 'gdpr_consent_manager_options' );

    add_settings_section(
        'gdpr_consent_manager_section',
        __( 'Banner Settings', 'gdpr-consent-manager' ),
        '__return_null',
        'gdpr_consent_manager'
    );

    add_settings_field(
        'gdpr_consent_manager_banner_text',
        __( 'Banner Text', 'gdpr-consent-manager' ),
        'gdpr_consent_manager_banner_text_render',
        'gdpr_consent_manager',
        'gdpr_consent_manager_section'
    );

    add_settings_field(
        'gdpr_consent_manager_css',
        __( 'Custom CSS', 'gdpr-consent-manager' ),
        'gdpr_consent_manager_css_render',
        'gdpr_consent_manager',
        'gdpr_consent_manager_section'
    );
}
add_action( 'admin_init', 'gdpr_consent_manager_settings_init' );

/**
 * Render the banner text field.
 */
function gdpr_consent_manager_banner_text_render() {
    $options = get_option( 'gdpr_consent_manager_options' );
    ?>
    <textarea name="gdpr_consent_manager_options[banner_text]" rows="5" cols="50"><?php echo esc_textarea( $options['banner_text'] ?? '' ); ?></textarea>
    <?php
}

/**
 * Render the custom CSS field.
 */
function gdpr_consent_manager_css_render() {
    $options = get_option( 'gdpr_consent_manager_options' );
    ?>
    <textarea name="gdpr_consent_manager_options[css]" rows="5" cols="50"><?php echo esc_textarea( $options['css'] ?? '' ); ?></textarea>
    <?php
}

/**
 * Display the GDPR consent banner.
 */
function gdpr_consent_manager_display_banner() {
    $options = get_option( 'gdpr_consent_manager_options' );
    $banner_text = $options['banner_text'] ?? __( 'We use cookies to ensure that we give you the best experience on our website.', 'gdpr-consent-manager' );
    $custom_css = $options['css'] ?? '';

    if ( ! isset( $_COOKIE['gdpr_consent'] ) ) {
        ?>
        <div id="gdpr-consent-banner" style="<?php echo esc_attr( $custom_css ); ?>">
            <p><?php echo esc_html( $banner_text ); ?></p>
            <button id="gdpr-consent-accept"><?php esc_html_e( 'Accept', 'gdpr-consent-manager' ); ?></button>
        </div>
        <script>
            document.getElementById('gdpr-consent-accept').addEventListener('click', function() {
                document.getElementById('gdpr-consent-banner').style.display = 'none';
                document.cookie = "gdpr_consent=accepted; path=/; max-age=" + 60 * 60 * 24 * 365;
            });
        </script>
        <style>
            #gdpr-consent-banner {
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                background-color: #333;
                color: #fff;
                text-align: center;
                padding: 10px;
                z-index: 9999;
            }

            #gdpr-consent-banner button {
                background-color: #0073aa;
                color: #fff;
                border: none;
                padding: 5px 10px;
                cursor: pointer;
                margin-left: 10px;
            }

            #gdpr-consent-banner button:hover {
                background-color: #005177;
            }
        </style>
        <?php
    }
}
add_action( 'wp_footer', 'gdpr_consent_manager_display_banner' );

?>