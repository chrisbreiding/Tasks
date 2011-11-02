<?php 
	if($task_data) {

		foreach( $task_data as $task_cols => $task_cats ) {
	?>
		<div class="tasks-col tasks-col-<?php echo $task_cols + 1; ?>">
			
			<?php foreach($task_cats as $task_cat) { ?>
				
				<div class="category">
				
					<h2><?php echo $task_cat['cat_name']; ?></h2>
					<?php
						foreach($task_cat['tasks'] as $task) {
							$attr = array(
								'id' 	=> 'task-' . $task->id,
								'class'	=> 'task-row' . ($task->important ? ' important' : '') . ($task->link_href ? ' linked' : ''),
								'data-id' => $task->id
							);
							
							echo form_open('tasks/update', $attr);
						?>
							
							<div class="completed">
								<a class="check<?php echo $task->completed == 1 ? ' checked" title="Mark Incomplete' : '" title="Mark Complete'; ?>" href="#">Toggle Completion</a>
							</div>
						
							<?php echo form_input( 'task', $task->task, 'class="task"' ); ?>
														
							<div class="edit-bar">
							
								<div class="handle" title="Change Position">Change Position</div>
			
								<?php if($task->completed == 0) { ?>
									<a class="flagger" title="Toggle Importance" href="#">Toggle Importance</a>
								<?php } ?>
								
								<a class="delete" title="Delete" href="#">Delete</a>
								<a class="confirm-delete" title="Confirm Deletion" href="/tasks/tasks/destroy/<?php echo $task->id; ?>">Confirm Delete</a>
								
								<?php if($task->completed == 1) { ?>
									<div class="date-change">
										<a title="Change Completed Date" href="#"><?php echo $date['today_short']; ?></a>
										<div class="date-changer"></div>
									</div>
								<?php } ?>
																
								<?php if(!empty($task->link_href)) { ?>
									<a class="linker break-link" title="Remove Link" href="#">Remove Link</a>
								<?php } ?>
								
								<a class="linker add-link" title="Add Link" href="#">Add Link</a>
								
								<?php if(!empty($task->link_href)) { ?>
									<a class="link" href="<?php echo $task->link_href; ?>" target="_blank"><?php echo $task->link_text; ?></a>
								<?php } ?>
								
							</div><!-- /.edit-bar -->
							
							<input type="submit" class="save-task" value="Save" />
								
						<?php echo form_close(); ?>

					<?php } // end tasks foreach ?>

				</div><!-- /.category -->
					
			<?php } // end categories foreach ?>
		
		</div><!-- /.col -->
		
	<?php } // end task_data foreach ?>

<?php } else {
	$message = $this->uri->segment(3) ? 'No completed tasks' : 'No tasks';
?>

	<div class="no-results"><?php echo $message; ?></div>

<?php }
