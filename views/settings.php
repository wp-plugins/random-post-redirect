<div class="wrap">
    <h2 class="wpcube"><?php echo $this->plugin->displayName; ?> &raquo; <?php _e('Settings'); ?></h2>
           
    <?php    
    if ( isset( $this->message ) ) {
        ?>
        <div class="updated fade"><p><?php echo $this->message; ?></p></div>  
        <?php
    }
    if ( isset( $this->errorMessage ) ) {
        ?>
        <div class="error fade"><p><?php echo $this->errorMessage; ?></p></div>  
        <?php
    }
    ?> 
    
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <!-- Content -->
            <div id="post-body-content">
            
                <!-- Form Start -->
                <form id="post" name="post" method="post" action="admin.php?page=<?php echo $this->plugin->name; ?>">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">                        
                        <div class="postbox">
                            <h3 class="hndle"><?php _e( 'Page', $this->plugin->name ); ?></h3>
                            
                            <div class="option">
                                <p>
                                    <?php echo home_url().'/'; ?>
                                    <input type="text" name="<?php echo $this->plugin->name; ?>[page]" value="<?php echo (isset($this->settings['page']) ? $this->settings['page'] : ''); ?>" />
                                </p>
                                <p class="description">
                                    <?php _e('Enter the end of the URL above. If this URL is then visited, it will redirect to a random Post.', $this->plugin->name); ?>
                                </p>
                            </div>
                            
                            <div class="option">
                                <p>
                                    <strong><?php _e( 'Categories to Exclude', $this->plugin->name ); ?></strong>
                                </p>
                                <p>     
                                    <select multiple="multiple" name="<?php echo $this->plugin->name; ?>[exclude][]" id="<?php echo $this->plugin->name; ?>[exclude]" class="widefat">
                                        <?php
                                        if ( is_array( $this->plugin->categories ) ) {
                                            foreach( $this->plugin->categories as $key=>$category ){
                                                echo '<option value="' . $category->term_id . '" ' . (in_array($category->term_id, $this->settings['exclude']) ? 'selected="selected"' : '') . '>' . $category->name . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </p>
                                <p class="description">
                                    <?php _e('Selected categories will not be included in the random Post lookup.', $this->plugin->name); ?>
                                </p>
                            </div>
                        </div>
                        <!-- /postbox -->
                       
                        <!-- Save -->
                        <div class="submit">
                            <?php wp_nonce_field( $this->plugin->name, $this->plugin->name.'_nonce' ); ?>
                            <input type="submit" name="submit" value="<?php _e( 'Save', $this->plugin->name ); ?>" class="button button-primary" /> 
                        </div>
                    </div>
                    <!-- /normal-sortables -->
                </form>
                <!-- /form end -->
                
            </div>
            <!-- /post-body-content -->
            
            <!-- Sidebar -->
            <div id="postbox-container-1" class="postbox-container">
                <?php require_once($this->plugin->folder.'/_modules/dashboard/views/sidebar-donate.php'); ?>        
            </div>
            <!-- /postbox-container -->
        </div>
    </div> 

    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-1">
            <div id="post-body-content">
                <?php require_once($this->plugin->folder.'/_modules/dashboard/views/footer-upgrade.php'); ?>
            </div>
        </div>
    </div>        
</div>