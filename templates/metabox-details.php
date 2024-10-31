<?php
// Grab current values from database
$royalfolio_client = @get_post_meta($post->ID, 'royalfolio_client', true);
$royalfolio_year = @get_post_meta($post->ID, 'royalfolio_year', true);
$royalfolio_role = @get_post_meta($post->ID, 'royalfolio_role', true);

// Grab related options
$rf_show_client = get_option('rf_show_client');
$rf_show_year = get_option('rf_show_year');
$rf_show_role = get_option('rf_show_role');

// CSRF-security: Add nonce field
wp_nonce_field('royalfolio_metabox', 'royalfolio_metabox_nonce');
?>

<div class="royalfolio-meta-wrapper">

    <div class="royalfolio-form-group" <?php if(empty($rf_show_client) OR $rf_show_client != "1") { echo 'style="display: none;"'; } ?>>

        <label for="royalfolio_client" class="royalfolio-label"><?php _e('Client', 'royalfolio'); ?></label>
        <input type="text" id="royalfolio_client" name="royalfolio_client" class="widefat" value="<?php echo esc_attr($royalfolio_client); ?>" />

    </div>

    <div class="royalfolio-form-group" <?php if(empty($rf_show_client) OR $rf_show_client != "1") { echo 'style="display: none;"'; } ?>

        <label for="royalfolio_year" class="royalfolio-label"><?php _e('Year', 'royalfolio'); ?></label>
        <input type="text" id="royalfolio_year" name="royalfolio_year" class="widefat" value="<?php echo esc_attr($royalfolio_year); ?>" />

    </div>

    <div class="royalfolio-form-group" <?php if(empty($rf_show_client) OR $rf_show_client != "1") { echo 'style="display: none;"'; } ?>

        <label for="royalfolio_role" class="royalfolio-label"><?php _e('Your role', 'royalfolio'); ?></label>
        <textarea id="royalfolio_role" name="royalfolio_role" class="widefat" rows="5"><?php echo esc_textarea($royalfolio_role); ?></textarea>

    </div>

</div>