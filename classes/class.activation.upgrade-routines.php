<?php

/* Public methods in this class will be run at least once during plugin activation script. */ 
/* Updater methods fired are stored in transient to prevent repeat processing */

if ( !class_exists('CTA_Activation_Update_Routines') ) {

	class CTA_Activation_Update_Routines {
		
		/* 
		* @introduced: 2.0.8
		* @migration-type: Meta pair migragtion
		* @mirgration: convert meta key cta_ab_variations to wp-cta-variations
		* @mirgration: convert meta key wp-cta-variation-notes to a sub key of wp-cta-variations object
		* @migration: convert meta key wp-cta-selected-template to wp-cta-selected-template-0
		*/
		public static function create_variation_object() {
			$ctas = get_posts( array(
				'post_type' => 'wp-call-to-action',
				'post_status' => 'publish'
			));

			/* loop through ctas and migrate data */
			foreach ($ctas as $cta) {
				$legacy_value = get_post_meta( $cta->ID , 'cta_ab_variations' , true );
				if ($legacy_value) {
					
					$variation_ids_array = explode(',' , $legacy_value );
					foreach ( $variation_ids_array as $vid ) {
						
						/* get variation status */
						$status = get_post_meta( $cta->ID , 'wp_cta_ab_variation_status' , true);
						
						/* Get variation notes & alter key for variations with vid=0 */
						if (!$vid) {
							$notes = get_post_meta( $cta->ID , 'wp-cta-variation-notes' , true );
							$template = get_post_meta( $cta->ID , 'wp-cta-selected-template' , true );
							update_post_meta( $cta->ID , 'wp-cta-selected-template-0' , $template );
						} else {
							$notes =  get_post_meta( $cta->ID , 'wp-cta-variation-notes-' . $vid , true);
						}
						
						if ($status || $status === null) {
							$status = 'active';
						} else {
							$status = 'paused';
						}
						
						$variations[ $vid ][ 'status' ] = $status;
						$variations[ $vid ][ 'notes' ] = $notes;
					}
					
					CTA_Variations::update_variations ( $cta->ID , $variations );
				}
				

			}
		}
		

	}

}

