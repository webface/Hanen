(function($) {

	$(function() {


        if($(window).width() > <?php echo $settings->large; ?>) {

            $('.sw-columnizer-<?php echo $id; ?>').columnize({   
    
                columns: <?php echo $settings->desktop; ?>,

                columnFloat: '<?php echo $settings->reading; ?>',
    
            });  

        }

        else if($(window).width() > <?php echo $settings->medium; ?>) {

            $('.sw-columnizer-<?php echo $id; ?>').columnize({ 

                columns: <?php echo $settings->tablet; ?>,

                columnFloat: '<?php echo $settings->reading; ?>',

            });  

        }

        else {

            $('.sw-columnizer-<?php echo $id; ?>').columnize({    

                columns: <?php echo $settings->phone; ?>,

                columnFloat: '<?php echo $settings->reading; ?>',

            });  

        }

        $('.sw-columnizer-<?php echo $id; ?>').find('<?php echo $settings->dontsplit; ?>').addClass('dontsplit');
        $('.sw-columnizer-<?php echo $id; ?>').find('<?php echo $settings->dontend; ?>').addClass('dontend');
        $('.sw-columnizer-<?php echo $id; ?>').find('<?php echo $settings->iflast; ?>').addClass('removeiflast');
        $('.sw-columnizer-<?php echo $id; ?>').find('<?php echo $settings->iffirst; ?>').addClass('removeiffirst');
                
        <?php if($settings->heights == 'yes') { ?>
            $('.sw-columnizer-<?php echo $id; ?> .column').matchHeight();
        <?php } ?>

	});

})(jQuery);