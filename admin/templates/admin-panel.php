<?php
// eead096fd179621c96778d5cb0e507a6: This is https://aviationstack.com/quickstart API key

?>
<div class="container afs-admin-settings">
    <div class="card">
        <div class="card-header">
            <h3>Settings</h3>
        </div>
        <div class="card-body">
            <form action="<?php the_permalink(); ?>" id="contactForm" method="post">
                <div class="form-group">
                    <label for="afs_API_KEY">API KEY</label>
                    <input type="text" class="form-control" name="afs_API_KEY" id="afs_API_KEY" aria-describedby="emailHelp" placeholder="Enter API KEY" value="<?php echo $afs_API_KEY?>">
                    <!--<small id="emailHelp" class="form-text text-muted">Use https://aviationstack.com/quickstart link to create a new API KEY.</small>-->
                </div>
                <div class="form-group">
                    <label>Choose airport page:</label><br />
                    <?php wp_dropdown_pages( array( 
                            'name' => 'afs_airport_page', 
                            'show_option_none' => __( '— Select —' ), 
                            'option_none_value' => '0', 
                            'selected' => $afs_airport_page,
                            )); ?>
                </div>
                <input type="hidden" name="submitted" id="submitted" value="true" />
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>