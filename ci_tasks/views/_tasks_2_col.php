<?php foreach( $task_data as $task_cols => $task_cats ) { ?>

	<div class="tasks-col tasks-col-<?php echo $task_cols + 1; ?>">
		
		<?php foreach($task_cats as $task_cat) { ?>
		
			<?php $tasks = $task_cat['tasks']; ?>
			
			<div class="category" id="cat-<?php echo $task_cat['cat_id']; ?>" data-cat-id="<?php echo $task_cat['cat_id']; ?>">
			
				<h2><?php echo $task_cat['cat_name']; ?></h2>
				<?php include(APPPATH . 'views/_tasks.php'); ?>

			</div><!-- /.category -->						
			
		<?php } // end categories foreach ?>
	
	</div><!-- /.col -->
	
<?php } // end task_data foreach