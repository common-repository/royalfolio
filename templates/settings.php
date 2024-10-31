<div class="wrap">

    <h2><?php echo __("RoyalFolio Settings", "royalfolio"); ?></h2>
    
    <form method="post" action="options.php"> 
    
        <?php @settings_fields('royalfolio-frontend'); ?>
        
        <?php @do_settings_fields('royalfolio-frontend'); ?>

        <?php do_settings_sections('royalfolio'); ?>

        <?php @submit_button(); ?>
        
    </form>
    
</div>