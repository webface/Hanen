(function($) {

	$(function() {

        var userId = '<?php echo $settings->userId; ?>';
        var userToken = '<?php echo $settings->userToken; ?>';


         
         $(".instagram-<?php echo $id; ?>").instastream({
            instaToken: userToken,

            instaUser: userId,

            instaResults: <?php echo $settings->instaQty; ?>,

            instaMenu: 'no'
 
        });

	});

})(jQuery);