#gmap-<?php echo $id; ?> {
<?php if( $settings-> width == '') { ?>
    width: 100%;
<?php } else { ?>
    width:<?php echo $settings->width; ?>px;
<?php } 
if( $settings-> height == '') { ?>
    height: 300px;
<?php } else { ?>
    height:<?php echo $settings->height; ?>px;
<?php } ?>
}