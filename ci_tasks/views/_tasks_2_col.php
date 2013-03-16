<?php $no_tasks = true; ?>
<?php foreach( $task_data as $task_cols => $task_cats ) { ?>

  <?php if($task_cats) { $no_tasks = false; } ?>

  <div class="tasks-col tasks-col-<?php echo $task_cols; ?>">
    <?php
      $order = array();
      foreach ($task_cats as $index => $task_cat) {
        $order[$index] = $task_cat['cat_order'];
      }
      array_multisort($order, SORT_ASC, $task_cats);
    ?>

    <?php foreach($task_cats as $task_cat) { ?>

      <div class="category" id="cat-<?php echo $task_cat['cat_id']; ?>" data-cat-id="<?php echo $task_cat['cat_id']; ?>">

        <h2><?php echo $task_cat['cat_name']; ?></h2>
        <div class="task-list">
          <?php
            if( isset($task_cat['tasks']) ) {
              $tasks = $task_cat['tasks'];
              include(APPPATH . 'views/_tasks.php');
            }
          ?>
        </div><!-- /.task-list -->

      </div><!-- /.category -->

    <?php } // end categories foreach ?>

  </div><!-- /.col -->
<?php } // end task_data foreach ?>

<?php if($no_tasks) { ?>

  <div class="no-results"><?php echo $this->uri->segment(2) ? 'No Tasks Completed On This Date' : 'No Incomplete Tasks'; ?></div>

<?php }
