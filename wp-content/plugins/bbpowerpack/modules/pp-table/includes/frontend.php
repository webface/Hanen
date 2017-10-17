<?php
    $table = $settings->header;
    $tableheaders = $settings->header;
    $tablerows = $settings->rows;
    $tablelabel = $settings->rows;

if (!empty($table[0])) {
    do_action( 'pp_before_table_module', $settings );
?>
<table class="pp-table-<?php echo $id; ?> pp-table-content tablesaw" <?php echo $settings->sortable; ?> data-tablesaw-mode="<?php echo $settings->scrollable; ?>" data-tablesaw-minimap>
    <thead>
        <tr>
            <?php $i = 1; foreach ( $tableheaders as $tableheader ) {
                echo '<th id="pp-table-col-'.$i++.'" class="pp-table-col" scope="col" data-tablesaw-sortable-col>';
                    echo $tableheader;
                echo '</th>';
            } $i = 0; ?>
        </tr>
    </thead>
    <tbody>
        <?php
            if (!empty($tablerows[0])) {
                foreach ( $tablerows as $tablerow ) {
                    echo '<tr class="pp-table-row">';
                        foreach ( $tablerow->cell as $tablecell ) {
                            echo '<td>' . $tablecell . '</td>';
                        }
                   echo '</tr>';
                }
            }
        ?>
    </tbody>
</table>
<?php if ( $settings->scrollable == 'swipe' && $settings->custom_breakpoint > 0 ) : ?>
    <script>
    if ( jQuery(window).width() >= <?php echo $settings->custom_breakpoint; ?> ) {
        jQuery(".fl-node-<?php echo $id; ?> table.pp-table-content").removeAttr('data-tablesaw-mode');
    }
    </script>
<?php endif;

do_action( 'pp_after_table_module', $settings );

}
