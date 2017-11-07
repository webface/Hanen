<div class="pp-content-category-list pp-post-meta">
    <?php $i = 1;
    foreach ($terms_list as $term):
        ?>
    <?php if( $i == count($terms_list) ) { ?>
        <?php echo $term->name; ?>
    <?php } else { ?>
        <?php echo $term->name . ' /'; ?>
    <?php } ?>
    <?php $i++; endforeach; ?>
</div>
