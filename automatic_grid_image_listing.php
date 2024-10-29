<?php
/**
 * Plugin Name: Automatic grid image listing
 * Plugin URI: http://www.vibgyorlogics.com/wordpress-plugins/
 * Description: Automatic grid image listing.
 * Version: 1.0
 * Author: Vibgyor Logics
 * Author URI: http://www.vibgyorlogics.com/wordpress-plugins/
 * License: GPL2
 */
add_action( 'admin_menu', 'register_automatic_grid_image_listing_dashboard' );

function register_automatic_grid_image_listing_dashboard(){
    add_menu_page( 'Grid Image Listing', 'Grid Image Listing', 'manage_options', 'automatic_grid_image_listing', 'automatic_grid_image_listing', '', 7 );
}
add_action( 'init', 'automatic_grid_image_listing_scripts' );
/**
 * Proper way to enqueue scripts and styles
 */
function automatic_grid_image_listing_scripts() {
	wp_enqueue_style( 'simple-que-ans_style', plugins_url().'/automatic-grid-image-listing/css/automatic_grid_image_listing.css' );
	wp_enqueue_script( 'simple-que-ans_js', plugins_url().'/automatic-grid-image-listing/js/automatic_grid_image_listing.js', array(), '1.0.0', true );
}

		

function automatic_grid_image_listing(){
	if(isset($_POST['automatic_grid_image_listing_submit'])){
		try{
			if (!file_exists(WP_CONTENT_DIR.'/'.$_POST['agil_base_folder_name'])) {
				mkdir(WP_CONTENT_DIR.'/'.$_POST['agil_base_folder_name'], 0777, true);
			}
		}
		catch(Exception $e){
		
		}
		update_option( 'agil_base_folder_name', $_POST['agil_base_folder_name'] );
		update_option( 'agil_border_color', $_POST['agil_border_color'] );
		update_option( 'agil_border_width', $_POST['agil_border_width'] );
		update_option( 'agil_grid_width', $_POST['agil_grid_width'] );
		update_option( 'agil_grid_height', $_POST['agil_grid_height'] );
		update_option( 'agil_error_msg', preg_replace('/\s+/', '_', $_POST['agil_error_msg']) );
		$message_setting = "All Changes saved successfully";
	}
	
	if(isset($_POST['automatic_grid_image_listing_form_upload_submit'])){
		if($_FILES["zip_file"]["name"]) {
			$filename = $_FILES["zip_file"]["name"];
			$source = $_FILES["zip_file"]["tmp_name"];
			$type = $_FILES["zip_file"]["type"];
			
			$name = explode(".", $filename);
			$accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
			foreach($accepted_types as $mime_type) {
				if($mime_type == $type) {
					$okay = true;
					break;
				} 
			}
	
			$continue = strtolower($name[1]) == 'zip' ? true : false;
			if(!$continue) {
				$message = "The file you are trying to upload is not a .zip file. Please try again.";
			}

			$target_path = WP_CONTENT_DIR."/".get_option('agil_base_folder_name')."/".$filename;  // change this to the correct site path
			if(move_uploaded_file($source, $target_path)) {
				$zip = new ZipArchive();
				$x = $zip->open($target_path);
				if ($x === true) {
					$zip->extractTo(WP_CONTENT_DIR."/".get_option('agil_base_folder_name')."/"); // change this to the correct site path
					$zip->close();			
					unlink($target_path);
				}
				$message = "Your .zip file was uploaded and unpacked.";
			} else {	
				$message = "There was a problem with the upload. Please try again.";
				}
		}
		
		
	}	
	
	echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
		echo '<h2>Automatic Grid Image Listing Dashbord</h2>';
	echo '</div>';
	
	?>
			
			<?php if($message_setting) echo "<p style='color:red'>$message_setting</p>"; ?>
            <h3>Automatic Grid Image Listing Settings</h3>
            <form method = 'post' action = '#' name = 'automatic_grid_image_listing_form' onsubmit = 'return agil_validate();'>
			<table width = '50%'><tr><td>
            <label class = 'text_label'>Base Folder Name*: </label></td>
            <td><input type = 'text' name = 'agil_base_folder_name'  value = <?php echo get_option('agil_base_folder_name')?> id = 'agil_base_folder_name' style = 'width:50%;'></td>
            <tr><td><label class = 'text_label'>Border Color: </label></td>
            <td><input type = 'text' name = 'agil_border_color' value = <?php echo get_option('agil_border_color')?>  id = 'agil_border_color' style = 'width:50%'></td>
            <tr><td><label class = 'text_label'>Border Width: </label></td>
            <td><input type = 'text' name = 'agil_border_width' value = <?php echo get_option('agil_border_width')?>  id = 'agil_border_width' style = 'width:50%'></td>            
            <tr><td><label class = 'text_label'>Grid Width: </label></td>
            <td><input type = 'text' name = 'agil_grid_width' value = <?php echo get_option('agil_grid_width')?>  id = 'agil_grid_width' style = 'width:50%'></td>           
            <tr><td><label class = 'text_label'>Grid height: </label></td>
            <td><input type = 'text' name = 'agil_grid_height' value = <?php echo get_option('agil_grid_height')?>  id = 'agil_grid_height' style = 'width:50%'></td>        
            <tr><td><label class = 'text_label'>Error Message: </label></td>
            <td><input type = 'text' name = 'agil_error_msg' value = "<?php echo str_replace('_', ' ', get_option('agil_error_msg'));?>"  id = 'agil_error_msg' style = 'width:50%'></td>
            <tr><td><input type = 'submit' name = 'automatic_grid_image_listing_submit' Value = 'Save'></tr>
            </table>
            </form>
            </br>
            </br>
            </br>
            </br>
            <hr>
            <?php if($message) echo "<p style='color:red'>$message</p>"; ?>
            <form enctype="multipart/form-data" method="post" action="#" name = 'automatic_grid_image_listing_form_upload'>
			<label>Choose a zip file to upload: <input type="file" name="zip_file" /></label>
			<br />
			<input type="submit" name="automatic_grid_image_listing_form_upload_submit" value="Upload" />
			</form>	
	<?php
	}

add_shortcode( 'agil_image_grid' , 'show_agil_image_grid' );
function show_agil_image_grid(){
	$base_path =  WP_CONTENT_DIR.'/'.get_option('agil_base_folder_name').'/';
	$base_real_path = content_url().'/'.get_option('agil_base_folder_name').'/';
	$dirs =  scandir($base_path);
	?>
           <?php if ( count($dirs) < 3 ) : ?>
			<div id="post-0" class="post not-found post-listing">
				<?php 
					$error_msg = 'Result not found';
					if(get_option('agil_error_msg')){
						$error_msg = str_replace('_', ' ', get_option('agil_error_msg'));
						}?>
				<h1 class="post-title"><?php _e( $error_msg, 'tie' ); ?></h1>
				<div class="entry">
					
					<p><?php _e( 'Not Found', 'tie' ); ?></p>
					<?php get_search_form(); ?>
				</div>
			</div>
		<?php endif; ?>
		
			<div class="post-inner">
				<div class="clear"></div>
				<div class="entry">
	<?php
    $brushes = array();
    $count = 0;
    foreach($dirs as $dir)
    {
      if($count > 1){
             
               $sub_dirs =  scandir($base_path.$dir);
              
                   $count_1 = 0;
                  foreach($sub_dirs as $sub_dir)
                    {
                
                         if($count_1 > 1){
                              
                              $tmp_file = explode('.', $sub_dir);
                              
                                if($tmp_file[1] == 'jpg' || $tmp_file[1] == 'png' || $tmp_file[1] == 'gif' || $tmp_file[1] == 'jpeg'){
                                     
                                     $brushes[$dir]['image'] = $sub_dir;

                                }else{
                                      $brushes[$dir]['brush'] = $sub_dir;
                                  }
                            }
                         $count_1++;
                    }
              
           }
        $count++;
    }
   
   if(get_option('agil_grid_width')){$grid_width = trim(get_option('agil_grid_width'));}else{$grid_width = 150;}
   if(get_option('agil_grid_height')){$grid_height = trim(get_option('agil_grid_height'));}else{$grid_height = 150;}
   if(get_option('agil_border_width')){$grid_border_width = trim(get_option('agil_border_width'));}else{$grid_border_width = 5;}
   if(get_option('agil_border_color')){$grid_border_color = trim(get_option('agil_border_color'));}else{$grid_border_color = 'black';}
   
                          foreach($brushes as $key => $brush){
                                  $img = $base_real_path.$key.'/'.$brush['image'];
                                  $brush_1 = $base_real_path.$key.'/'.$brush['brush'];
                                  echo "<a href = $brush_1 ><img src = $img style='width:".($grid_width)."px;height:".$grid_height."px;border:".$grid_border_width."px solid;border-color:".$grid_border_color.";float:left;margin:5px;' title = $key ></a>";
                            }

                                     ?>

				</div><!-- .entry /-->	
				
			</div><!-- .post-inner -->
                
                <?php
}
	
?>
