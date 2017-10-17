(function($) {

        $(".fl-node-<?php echo $id; ?> table.pp-table-content tbody tr:nth-child(odd)").addClass("odd");
        $(".fl-node-<?php echo $id; ?> table.pp-table-content tbody tr:nth-child(even)").addClass("even");

		$( document ).trigger( "enhance.tablesaw" );

})(jQuery);
