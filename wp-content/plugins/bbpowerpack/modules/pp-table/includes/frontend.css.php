<?php
$headerfamily = $settings->header_font;
$rowfamily = $settings->row_font;
?>
.fl-node-<?php echo $id; ?> .tablesaw-bar .tablesaw-advance a.tablesaw-nav-btn {
	float: none !important;
}

.fl-node-<?php echo $id; ?> .pp-table-content thead,
.fl-node-<?php echo $id; ?> .pp-table-content.tablesaw thead {
    background: #<?php echo $settings->header_background; ?>;
	border: 0;
}

.fl-node-<?php echo $id; ?> .pp-table-content thead tr th,
.fl-node-<?php echo $id; ?> .pp-table-content.tablesaw-sortable th.tablesaw-sortable-head,
.fl-node-<?php echo $id; ?> .pp-table-content.tablesaw-sortable tr:first-child th.tablesaw-sortable-head {
	<?php if($settings->header_font_size != 'default') { ?>
	    font-size: <?php echo $settings->header_custom_font_size['desktop']; ?>px;
	<?php } ?>
    <?php if( $headerfamily['family'] != 'Default' ) { ?><?php FLBuilderFonts::font_css( $headerfamily ); ?><?php } ?>
    color: #<?php echo $settings->header_font_color; ?>;
	text-transform: <?php echo $settings->header_text_transform; ?>;
	text-align: <?php echo $settings->header_text_alignment; ?>;
	<?php if( $settings->header_padding['top'] >= 0 ) { ?>
	padding-top: <?php echo $settings->header_padding['top']; ?>px;
	<?php } ?>
	<?php if( $settings->header_padding['right'] >= 0 ) { ?>
	padding-right: <?php echo $settings->header_padding['right']; ?>px;
	<?php } ?>
	<?php if( $settings->header_padding['bottom'] >= 0 ) { ?>
	padding-bottom: <?php echo $settings->header_padding['bottom']; ?>px;
	<?php } ?>
	<?php if( $settings->header_padding['left'] >= 0 ) { ?>
	padding-left: <?php echo $settings->header_padding['left']; ?>px;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-table-content thead tr th {
	vertical-align: <?php echo $settings->header_vertical_alignment; ?>;
}

<?php if( $settings->sortable == 'data-tablesaw-sortable data-tablesaw-sortable-switch' ) { ?>
.fl-node-<?php echo $id; ?> .pp-table-content.tablesaw-sortable th.tablesaw-sortable-head button {
	<?php if( $settings->header_padding['right'] >= 0 ) { ?>
	padding-right: <?php echo $settings->header_padding['right']; ?>px;
	<?php } ?>
}
<?php } ?>

.fl-node-<?php echo $id; ?> .pp-table-content tbody {
	border-left: 1px solid #<?php echo $settings->rows_border; ?>;
	border-right: 1px solid #<?php echo $settings->rows_border; ?>;
	border-top: 1px solid #<?php echo $settings->rows_border; ?>;
	<?php if( $settings->cells_border == 'horizontal' || $settings->cells_border == 'vertical' ) { ?>
		border-left: 0;
		border-right: 0;
	<?php } ?>
	<?php if( $settings->cells_border == 'vertical' ) { ?>
		border-top: 0;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-table-content tbody tr {
	background: #<?php echo $settings->rows_background; ?>;
    border-bottom: 1px solid <?php echo ( $settings->rows_border ) ? '#' . $settings->rows_border : 'transparent'; ?>;
	<?php if( $settings->cells_border == 'vertical' ) { ?>
		border-bottom: 0;
	<?php } ?>
}

<?php if( $settings->cells_border == 'horizontal' ) { ?>
	.fl-node-<?php echo $id; ?> .pp-table-content tbody tr:last-child {
		border-bottom: 0;
	}
<?php } ?>

.fl-node-<?php echo $id; ?> .pp-table-content tbody tr td {
    border-left: 1px solid <?php echo ( $settings->rows_border ) ? '#' . $settings->rows_border : 'transparent'; ?>;
	<?php if( $settings->cells_border == 'horizontal' ) { ?>
		border-left: 0;
	<?php } ?>
	vertical-align: <?php echo $settings->rows_vertical_alignment; ?>;
}

.fl-node-<?php echo $id; ?> .pp-table-content tbody tr td:first-child {
	border-left: 0;
}

.fl-node-<?php echo $id; ?> .pp-table-content thead tr:first-child th {
	border-style: solid;
	border-width: 1px;
	border-color: <?php echo ( $settings->header_border ) ? '#' . $settings->header_border : 'transparent'; ?>;
}

.fl-node-<?php echo $id; ?> .pp-table-content thead tr:first-child th:last-child {

}

.fl-node-<?php echo $id; ?> .pp-table-content tbody tr td {
	<?php if($settings->row_font_size != 'default') { ?>
	    font-size: <?php echo $settings->row_custom_font_size['desktop']; ?>px;
	<?php } ?>
    <?php if( $rowfamily['family'] != 'Default' ) { ?><?php FLBuilderFonts::font_css( $rowfamily ); ?><?php } ?>
    color: #<?php echo $settings->rows_font_color; ?>;
	text-transform: <?php echo $settings->rows_text_transform; ?>;
	text-align: <?php echo $settings->rows_text_alignment; ?>;
	<?php if( $settings->rows_padding['top'] >= 0 ) { ?>
	padding-top: <?php echo $settings->rows_padding['top']; ?>px;
	<?php } ?>
	<?php if( $settings->rows_padding['right'] >= 0 ) { ?>
	padding-right: <?php echo $settings->rows_padding['right']; ?>px;
	<?php } ?>
	<?php if( $settings->rows_padding['bottom'] >= 0 ) { ?>
	padding-bottom: <?php echo $settings->rows_padding['bottom']; ?>px;
	<?php } ?>
	<?php if( $settings->rows_padding['left'] >= 0 ) { ?>
	padding-left: <?php echo $settings->rows_padding['left']; ?>px;
	<?php } ?>
}

.fl-node-<?php echo $id; ?> .tablesaw-sortable .tablesaw-sortable-head button {
	text-align: <?php echo $settings->header_text_alignment; ?>;
}

.fl-node-<?php echo $id; ?> .pp-table-content .odd {
    <?php if( $settings->rows_odd_background ) { ?>background: #<?php echo $settings->rows_odd_background; ?>;<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-table-content .odd td {
    <?php if( $settings->rows_font_odd ) { ?>color: #<?php echo $settings->rows_font_odd; ?>;<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-table-content .even {
    <?php if( $settings->rows_even_background ) { ?>background: #<?php echo $settings->rows_even_background; ?>;<?php } ?>
}

.fl-node-<?php echo $id; ?> .pp-table-content .even td {
    <?php if( $settings->rows_font_even ) { ?>color: #<?php echo $settings->rows_font_even; ?>;<?php } ?>
}


@media only screen and (max-width: 768px) {

	.fl-node-<?php echo $id; ?> .pp-table-content thead tr th {
		<?php if( $settings->header_font_size != 'default' && $settings->header_custom_font_size['tablet'] != '' ) { ?>
		    font-size: <?php echo $settings->header_custom_font_size['tablet']; ?>px;
		<?php } ?>
	}

	.fl-node-<?php echo $id; ?> .pp-table-content tbody tr td {
		<?php if($settings->row_font_size != 'default' && $settings->row_custom_font_size['tablet'] != '' ) { ?>
		    font-size: <?php echo $settings->row_custom_font_size['tablet']; ?>px;
		<?php } ?>
	}

}

@media only screen and (max-width: 639px) {

	.fl-node-<?php echo $id; ?> .pp-table-content thead tr th {
		<?php if( $settings->header_font_size != 'default' && $settings->header_custom_font_size['mobile'] != '' ) { ?>
		    font-size: <?php echo $settings->header_custom_font_size['mobile']; ?>px;
		<?php } ?>
	}

	.fl-node-<?php echo $id; ?> .pp-table-content-cell-label {
		<?php if( $settings->header_font_size != 'default' && $settings->header_custom_font_size['mobile'] != '' ) { ?>
		    font-size: <?php echo $settings->header_custom_font_size['mobile']; ?>px;
		<?php } ?>
		text-transform: <?php echo $settings->header_text_transform; ?>;
	}

	.fl-node-<?php echo $id; ?> .pp-table-content tbody tr td {
		<?php if( $settings->row_font_size != 'default' && $settings->row_custom_font_size['mobile'] != '' ) { ?>
		    font-size: <?php echo $settings->row_custom_font_size['mobile']; ?>px;
		<?php } ?>
	}

}
