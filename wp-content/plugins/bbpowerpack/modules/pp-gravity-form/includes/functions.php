<?php
/**
* Functions file for Gravity Form module
*/

function gf_module_form_titles() {

    $options = array( '' => __('None', 'bb-powerpack') );

    if ( class_exists( 'GFForms' ) ) {
        $forms = RGFormsModel::get_forms( null, 'title' );
        if ( count( $forms ) ) {
            foreach ( $forms as $form )
            $options[$form->id] = $form->title;
        }
    }

    return $options;
}

function gf_hex2rgba($hex, $opacity) {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgba = array($r, $g, $b, $opacity);
   return 'rgba(' . implode(",", $rgba) . ')'; // returns the rgb values separated by commas
   //return $rgb; // returns an array with the rgb values
}
