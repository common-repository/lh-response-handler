<?php
/**
 * Plugin Name: LH Response Handler
 * Plugin URI: https://lhero.org/portfolio/lh-response-handler/
 * Description: Handle redirects and much more in a flexible wordpress way
 * Author: Peter Shaw
 * Author URI: https://shawfactor.com/
 * Version: 1.00
 * Text Domain: lh_response
 * Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (!class_exists('LH_Response_handler_plugin')) {
    

class LH_Response_handler_plugin {
    
    private static $instance;
 
    static function return_plugin_namespace(){
    
        return 'lh_response';
    
    }

    static function plugin_name(){
        
        return 'LH Response Handler';
        
    }

    static function return_file_name(){

        return plugin_basename( __FILE__ );

    }



    

static function return_posttype(){
    
    return 'lh_response-url';
    
    
} 

 static function return_taxonomy_name(){
    
    return 'lh_response-status_code';
    
    
} 

static function curpageurl() {
	$pageURL = 'http';

	if ((isset($_SERVER["HTTPS"])) && ($_SERVER["HTTPS"] == "on")){
		$pageURL .= "s";
}

	$pageURL .= "://";

	if (($_SERVER["SERVER_PORT"] != "80") and ($_SERVER["SERVER_PORT"] != "443")){
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];

	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

}

	return $pageURL;
}

static function isValidURL($url){
    
    if (empty($url)){
        
        return false;
        
    }  else {

        return (bool)parse_url($url);
        
        
    }
}


static function is_this_plugin_network_activated(){

if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
}

if ( is_plugin_active_for_network( self::return_file_name() ) ) {
    // Plugin is activated

return true;

} else  {


return false;


}

}

static function write_log($log) {
        if (true === WP_DEBUG) {
            if (is_array($log) || is_object($log)) {
                error_log(plugin_basename( __FILE__ ).' - '.print_r($log, true));
            } else {
                error_log(plugin_basename( __FILE__ ).' - '.$log);
            }
        }
    }

static function setup_crons(){
    
            wp_clear_scheduled_hook( self::return_plugin_namespace().'_initial_run' );
            wp_schedule_single_event(time(), self::return_plugin_namespace().'_initial_run' );

    
}


static function match_url($url){

global $wpdb;   

if (self::is_this_plugin_network_activated()){
    
$prefix = $wpdb->base_prefix;
    
} else {
    
 $prefix = $wpdb->prefix;   
    
}
    
$sql = "SELECT ".$prefix."posts.ID FROM ".$prefix."posts LEFT JOIN ".$prefix."postmeta ON ".$prefix."postmeta.post_id = ".$prefix."posts.ID WHERE 1=1 AND ".$prefix."posts.post_type = '".self::return_posttype()."' AND ".$prefix."postmeta.meta_key = '_".self::return_plugin_namespace()."-matching_url' and ".$prefix."postmeta.meta_value = '".$url."' ORDER BY ".$prefix."posts.post_date DESC";


$post_id = $wpdb->get_var($sql);

if (is_numeric($post_id)){
    
    if (headers_sent()){
        
        
        self::write_log('Response handling failed headers already sent for url: '.$url);
        
        
        
    } else {
    
    
if (self::is_this_plugin_network_activated()  && !is_main_site()){


    
// switch to main site
switch_to_blog( get_network()->site_id );


self::register_status_code_taxonomy();
    
    
}
    
$terms = wp_get_post_terms( $post_id, self::return_taxonomy_name()); 

if (isset($terms[0]->slug)){
    
if (substr($terms[0]->slug, 0, 1) == 3){

$redirect_url = get_post_meta($post_id, "_".self::return_plugin_namespace()."-redirect_url", true);

if (!empty($redirect_url) && self::isValidURL($redirect_url)){
    
wp_redirect($redirect_url ,  $terms[0]->slug, self::plugin_name());
exit;

}
    
} elseif (substr($terms[0]->slug, 0, 3) == 410){
    
header( "HTTP/1.1 410 Gone" );    
exit;
    
    
}
    
    
}
    
    
    
}

restore_current_blog();

}
    
    
}

static function register_status_code_taxonomy(){
    
    $types =  array( self::return_posttype() );
    
    
	$labels = array(
		'name'              => _x( 'Status Code', 'taxonomy general name', self::return_plugin_namespace() ),
		'singular_name'     => _x( 'Status Code', 'taxonomy singular name', self::return_plugin_namespace()),
		'menu_name'         => __( 'Status Code', self::return_plugin_namespace()),
	);

register_taxonomy(
    self::return_taxonomy_name(),
   $types,
    array(
        'public' => false,
        'hierarchical' => true,
        'show_in_nav_menus' => false,
		'labels'  => $labels,
        'show_ui' => false,
		'show_admin_column' => true
    )
);
    
    
    
}


private function register_url_post_type() {
    



    $capabilities  = array(
        'edit_post' => 'edit_others_posts',
        'edit_posts' => 'edit_others_posts',
        'edit_others_posts' => 'edit_others_posts',
        'publish_posts' => 'edit_others_posts',
        'read_post' => 'read_post',
        'read_private_posts' => 'read_private_posts',
        'delete_posts' => 'delete_others_posts',
        'delete_post' => 'delete_others_posts'
        
    );

$labels = array(
    'name' => 'Response',
      'singular_name' => 'Response',
      'menu_name'	=> 'Responses',
      'add_new' => 'Add New',
      'add_new_item' => 'Add New Response',
      'edit' => 'Edit response',
      'edit_item' => 'Edit Response',
      'new_item' => 'New Response',
      'view' => 'View Response',
      'view_item' => 'View Response',
      'search_items' => 'Search Responses',
      'not_found' => 'No responses Found',
      'not_found_in_trash' => 'No responses Found in Trash');


register_post_type(self::return_posttype(), array(
        'label' => 'Responses',
        'description' => '',
        'public' => false,
        'capabilities' => $capabilities,
        'show_ui' => true,
        'show_in_nav_menus'  => false,
        'show_in_rest' => true,
        'hierarchical' => false,
        'supports' =>  array( 'title','author','excerpt'),
        'labels' => $labels,
        'show_in_menu'  => 'tools.php',
        )
    );


}





public function setup_post_types() {
    
if (self::is_this_plugin_network_activated()  && is_main_site()){

$this->register_url_post_type();

} elseif (!self::is_this_plugin_network_activated()){
    
$this->register_url_post_type();    
    
}

}

public function create_taxonomies() {
    
if (self::is_this_plugin_network_activated()  && is_main_site()){
	    
	    self::register_status_code_taxonomy();
	    
} elseif (!self::is_this_plugin_network_activated()){

        self::register_status_code_taxonomy();
    
}
	    
	    
	}
	


public function render_redirect_rules_box_content( $post, $callback_args ){
    
$matching_url = get_post_meta($post->ID, "_".self::return_plugin_namespace()."-matching_url", true);

$redirect_url = get_post_meta($post->ID, "_".self::return_plugin_namespace()."-redirect_url", true);


$terms = wp_get_post_terms( $post->ID, self::return_taxonomy_name());

if (isset($terms[0]->slug)){
    
    $selected = $terms[0]->slug;
    
    
} else {
    
    $selected = apply_filters( 'lh_response_handler_default_option', '301', $post, $callback_args);
    
}

wp_nonce_field( self::return_plugin_namespace()."-post_edit-nonce", self::return_plugin_namespace()."-post_edit-nonce" );

$terms = get_terms( self::return_taxonomy_name(), array(
    'hide_empty' => false,
) );

?><table class="form-table">
<tr valign="top">
<th scope="row"><label for="<?php echo self::return_plugin_namespace(); ?>-matching_url"><?php _e("Matching URL", self::return_plugin_namespace() ); ?></label></th>
<td>
<input type="url" name="<?php echo self::return_plugin_namespace(); ?>-matching_url" id="<?php echo self::return_plugin_namespace(); ?>-matching_url" value="<?php echo $matching_url; ?>" required="required" />
</td>
</tr>
<tr valign="top">
<th scope="row"><label for="<?php echo self::return_plugin_namespace(); ?>-status_code"><?php _e("Status Code", self::return_plugin_namespace() ); ?></label></th>
<td><select name="<?php echo self::return_plugin_namespace(); ?>-status_code" id="<?php echo self::return_plugin_namespace(); ?>-status_code" autocomplete="off">
    
<?php foreach ( $terms as $term) { ?>
<option value="<?php echo $term->slug; ?>" <?php if ($selected == $term->slug) { echo 'selected="selected"';  } ?>><?php echo $term->slug.' - '.$term->name; ?></option>
<?php } ?>
</select>
</td>
</tr>


<tr valign="top">
<th scope="row"><label for="<?php echo self::return_plugin_namespace(); ?>-redirect_url"><?php _e("Redirect to", self::return_plugin_namespace() ); ?></label></th>
<td>
<input type="url" name="<?php echo self::return_plugin_namespace(); ?>-redirect_url" id="<?php echo self::return_plugin_namespace(); ?>-redirect_url" value="<?php echo $redirect_url; ?>" /> (this is required for all redirects to work, no otherwise required)
</td>
</tr>
</table>
<?php



}





public function add_meta_boxes($post_type, $post)  {
    
    
if ($post_type == self::return_posttype()) {
        


add_meta_box(self::return_plugin_namespace()."-redirect_rules-div", "Rules", array($this,"render_redirect_rules_box_content"), $post_type, "normal", "high", array());


}
    
    
}

public function run_initial_processes(){
    
$this->create_taxonomies();
flush_rewrite_rules();




if (empty(term_exists('301', self::return_taxonomy_name() ))){

wp_insert_term(
  'Moved Permanently', // the term 
  self::return_taxonomy_name(), // the taxonomy
  array(
    'description'=> 'The requested resource has been assigned a new permanent URI and any future references to this resource SHOULD use one of the returned URIs',
    'slug' => '301'
  )
);

}

if (empty(term_exists('307', self::return_taxonomy_name() ))){

wp_insert_term(
  'Temporary Redirect', // the term 
  self::return_taxonomy_name(), // the taxonomy
  array(
    'description'=> 'The requested resource resides temporarily under a different URI',
    'slug' => '307'
  )
);

}


if (empty(term_exists('410', self::return_taxonomy_name() ))){

wp_insert_term(
  'Gone', // the term 
  self::return_taxonomy_name(), // the taxonomy
  array(
    'description'=> 'The requested resource no longer available at the origin server and that this condition is likely to be permanent',
    'slug' => '410'
  )
);

}    
    
    
}
	
	
public function update_post_details( $post_id, $post, $update ) {
    

    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;
    
if (isset($_POST[self::return_plugin_namespace()."-post_edit-nonce"]) && wp_verify_nonce($_POST[self::return_plugin_namespace()."-post_edit-nonce"], self::return_plugin_namespace()."-post_edit-nonce") ){
    
 
    
if (self::isValidURL($_POST[self::return_plugin_namespace()."-matching_url"])){
    
       
    
update_post_meta($post_id, "_".self::return_plugin_namespace()."-matching_url",esc_url_raw($_POST[self::return_plugin_namespace()."-matching_url"]));
    
}

$exists = term_exists($_POST[self::return_plugin_namespace()."-status_code"], self::return_taxonomy_name() );

if (!empty($exists)){
    

$return = wp_set_post_terms( $post_id, $exists['term_id'], self::return_taxonomy_name(), false );    
    
}




if (self::isValidURL($_POST[self::return_plugin_namespace()."-redirect_url"])){
    
update_post_meta($post_id, "_".self::return_plugin_namespace()."-redirect_url", esc_url_raw($_POST[self::return_plugin_namespace()."-redirect_url"]));
    
}
    
    
    
    
    
}
    
    
    
}

public function remove_coauthors_support_for_responses($supported_post_types){
    
$supported_post_types = array_diff( $supported_post_types, array(self::return_posttype() ));
    

return $supported_post_types;
    
}

public function intercept_request(){
    
    if( is_404() ){
        
$url = self::curpageurl();
self::match_url($url);
   
    }
    
    
}

public function plugin_init(){
    
load_plugin_textdomain( self::return_plugin_namespace(), false, basename( dirname( __FILE__ ) ) . '/languages' ); 
    
$priority = PHP_INT_MAX - 1;
	    
    
//Register custom post type for urls to have their response handled
add_action('init', array($this,'setup_post_types'), 10 );

//create the taxonomies for status codes
add_action( 'init', array($this,'create_taxonomies'),9); 

//add the show related metabox
add_action('add_meta_boxes', array($this,'add_meta_boxes'),10,2);

//handle posted values from the metabox
add_action( 'save_post', array($this,'update_post_details'),10,3);

//responses only need one author
add_filter('coauthors_supported_post_types', array($this,'remove_coauthors_support_for_responses'), 100, 1);

//do the status code on 404s
add_action( 'template_redirect', array($this,'intercept_request'),$priority);

//add processing to the initial cron job
add_action(self::return_plugin_namespace().'_initial_run',  array($this, 'run_initial_processes' ));
    
    
}
	


            
    /**
     * Gets an instance of our plugin.
     *
     * using the singleton pattern
     */
    public static function get_instance(){
        if (null === self::$instance) {
            self::$instance = new self();
        }
 
        return self::$instance;
    }
    
    
static function on_activate($network_wide) {
	    
	    
if ( is_multisite() && $network_wide ) { 

            $args = array('number' => 500, 'fields' => 'ids');
        
            $sites = get_sites($args);
    foreach ($sites as $blog_id) {

            switch_to_blog($blog_id);
            self::setup_crons();
            restore_current_blog();
        } 

    } else {


           self::setup_crons();

}
	    
	    
	}

    
    
    /**
	* Constructor
	*/
	public function __construct() {
	    
	 
	    
	 //run our hooks on plugins loaded to as we may need checks       
    add_action( 'plugins_loaded', array($this,'plugin_init'));
	    


	    
	    
	}
    
    
}

$lh_response_handler_instance = LH_Response_handler_plugin::get_instance();
register_activation_hook(__FILE__, array('LH_Response_handler_plugin', 'on_activate'));



}

?>