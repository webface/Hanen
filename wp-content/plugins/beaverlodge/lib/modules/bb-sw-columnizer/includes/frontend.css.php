.sw-columnizer-<?php echo $id; ?> .column {
    text-align: <?php echo $settings->alignment; ?>;
    padding-left: <?php echo $settings->padding; ?>px;
    padding-right: <?php echo $settings->padding; ?>px;
<?php if ($settings->border == 'yes') { ?>
    border-right: solid <?php echo $settings->borderWidth; ?>px;
<?php } ?>
<?php if ($settings->borderColor != '') { ?>
    border-color: #<?php echo $settings->borderColor; ?>;
<?php } ?>
}

.sw-columnizer-<?php echo $id; ?> .last.column {
    border-right: none;
}