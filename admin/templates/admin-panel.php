<?php
// eead096fd179621c96778d5cb0e507a6: This is https://aviationstack.com/quickstart API key
$afs_api_fetched_count = get_option('afs_api_fetched_count');
if(empty($afs_api_fetched_count)){
    $afs_api_fetched_count = 0;
}
?>
<div class="container afs-admin-settings">
    <div class="card">
        <div class="card-header">
            <h3><?php _e( 'Settings', 'airport-flight-status' )?></h3>
        </div>
        <div class="card-body">
            <form action="<?php the_permalink(); ?>" id="contactForm" method="post">
                <div class="form-group">
                    <label for="afs_API_KEY"><?php _e( 'API KEY', 'airport-flight-status' );?></label>
                    <input type="text" class="form-control" name="afs_API_KEY" id="afs_API_KEY" placeholder="Enter API KEY" value="<?php echo $afs_API_KEY?>">
                    
                    <small  class="form-text text-muted">Current API Count <b><?php echo $afs_api_fetched_count;?>/100</b></small><br />
                    <small  class="form-text text-muted">Use https://aviationstack.com/quickstart link to create a new API KEY.</small>
                </div>
                <div class="form-group">
                    <label for="afs_default_airport_iata"><?php _e( 'Default airport iata code', 'airport-flight-status' );?></label>
                    <input type="text" class="form-control" name="afs_default_airport_iata" id="afs_default_airport_iata" placeholder="Add default airport iata code. Eg: LHR" value="<?php echo $afs_default_airport_iata?>">
                    <small class="form-text text-muted">Use https://aviationstack.com/quickstart link get list of airport iata codes.</small>
                </div>
                <div class="form-group">
                    <label><?php _e( 'Choose airport page:', 'airport-flight-status' );?></label><br />
                    <?php wp_dropdown_pages( array( 
                            'name' => 'afs_airport_page', 
                            'show_option_none' => __( '— Select —' ), 
                            'option_none_value' => '0', 
                            'selected' => $afs_airport_page,
                            )); ?>
                </div>
                <input type="hidden" name="submitted" id="submitted" value="true" />
                <button type="submit" class="btn btn-primary"><?php _e( 'Submit', 'airport-flight-status' );?></button>
            </form>
        </div>
    </div>
</div>