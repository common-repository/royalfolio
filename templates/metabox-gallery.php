<?php
// Grab current values from database
$royalfolio_gallery = @get_post_meta($post->ID, 'royalfolio_gallery', true);

// CSRF-security: Add nonce field
wp_nonce_field('royalfolio_metabox', 'royalfolio_metabox_nonce');
?>

<div class="royalfolio-meta-wrapper">
    
    <ul id="royalfolio-sortable">
        
        <?php if(is_array($royalfolio_gallery) AND !empty($royalfolio_gallery)) : ?>
        
            <?php foreach($royalfolio_gallery as $key => $value) : ?>
        
                <li id="<?php echo $key; ?>" data-imgurl="<?php echo $value; ?>"><img src="<?php echo $value; ?>" alt=""></li>
        
            <?php endforeach; ?>
        
        <?php else : ?>
        
            <?php echo __("No images has been added yet", "royalfolio"); ?>
        
        <?php endif; ?>
        
    </ul>

    <input type="hidden" name="royalfolio_image_urls" id="royalfolio_image_urls" value="<?php
    if(is_array($royalfolio_gallery) AND !empty($royalfolio_gallery)) {
        $array_of_img_urls = array();
        foreach($royalfolio_gallery as $key => $value) {
            $array_of_img_urls[] = $value;
        }
        echo implode(",", $array_of_img_urls);
    }
    ?>" />
    
    <input type="button" class="button" name="royalfolio_add_image_button" id="royalfolio_add_image_button" value="<?php echo __("Add image(s)", "royalfolio"); ?>" />
    
</div>