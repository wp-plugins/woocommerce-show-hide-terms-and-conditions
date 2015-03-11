<?php
/**
 * Plugin Name: Woocommerce show terms and conditions
 * Description: Show "terms and conditions" if a specific woocommerce product is on cart
 * Version: 1.1
 * Author: Skomfare2
 * Author URI: https://profiles.wordpress.org/skomfare2/#content-plugins
 */
 
 
/*
*  ADD METABOX TO PRODUCT CPT
*/
if (is_admin()){	
	require_once('meta-box-class/my-meta-box-class.php');

	$skomfare2_wc_show_terms_config = array(
		'id'             => 'show_terms_meta_box',         
		'title'          => 'Show "Terms and Conditions" on Checkout',          
		'pages'          => array('product'),    
		'context'        => 'normal',           
		'priority'       => 'high',            
		'fields'         => array(),          
		'local_images'   => false,         
		'use_with_theme' => false         
	);


	/*
	* Initiate your meta box
	*/
	$skomfare2_show_terms_meta =  new AT_Meta_Box($skomfare2_wc_show_terms_config);

	$skomfare2_show_terms_meta->addSelect('_skomfare2_wc_show_terms_product_select',array('not_set'=>'Not set' , 'no'=>'No','yes'=>'Yes'),array('name'=> 'Show "Terms and Condition" on "Checkout" if this product is on cart', 'std'=> array('not_set')));	

	$skomfare2_show_terms_meta->Finish();
		
	
}			
 
add_filter( 'woocommerce_checkout_show_terms', 'skomfare2_show_terms' );

function skomfare2_show_terms() {
	
	global $woocommerce;
		
	foreach($woocommerce->cart->get_cart() as $cart_item_key => $values ) {
		
		$_product = $values['data'];
		
		
		//check IF WE HAVE PRODUCT WITH SHOW_TERMS Advanced-custom-fields set
		$has_terms = get_post_meta($_product->id ,'_skomfare2_wc_show_terms_product_select',true);
		
		//var_dump($has_terms);
		
		if($has_terms == 'yes' || !$has_terms || $has_terms=='' || $has_terms=='not_set'){
			return true;
		}
	}		
	
	return false;
}

add_filter( 'woocommerce_get_terms_page_id','skomfare2_get_page_id',1);
function skomfare2_get_page_id($page_id){
	
	global $woocommerce;
	
	foreach($woocommerce->cart->get_cart() as $cart_item_key => $values ) {
			$_product = $values['data'];

			//check IF WE HAVE PRODUCT WITH SHOW_TERMS Advanced-custom-fields set
			$has_terms = get_post_meta($_product->id ,'_skomfare2_wc_show_terms_product_select',true);

			if($has_terms == 'yes' || !$has_terms || $has_terms=='' || $has_terms == 'not_set'){
				
				return $page_id;
			}
	}		
	
	if(isset($_POST['woocommerce_update_order_review'])){
		return 0;
	}
	
}