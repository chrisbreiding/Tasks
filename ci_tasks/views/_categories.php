<?php
  $order = array();
  foreach ($categories as $index => $category) {
    $order[$index] = $category->order;
  }
  array_multisort($order, SORT_ASC, $categories);

  foreach($categories as $category) {
?>

  <li id="cat-<?php echo $category->id; ?>" class="settings-category<?php echo $category->display == 1 ? '' : ' no-display'; ?> in-col-<?php echo $category->column; ?>" data-id="<?php echo $category->id; ?>">

    <span class="cat-handle"></span>

    <span class="cat-name"><?php echo $category->category; ?></span>

    <span class="cat-options">
      <a href="#" class="toggle-display" title="Toggle Display On Incompleted Tasks Page">Toggle Display</a>
    </span>

  </li><!-- /.settings-category -->

<?php } // end categories foreach
