<?php echo form_open('/tasks/create', array('id' => 'task-creator', 'class' => 'clearfix')); ?>

	<?php echo form_input( 'task', '', 'id="task"' ); ?>

	<div class="creator-edit-bar">
		
		<?php echo form_dropdown('categories', $categories['cats'], $categories['ids'][0], 'id="categories"'); ?>
		
		<div class="creator-link-options">
			<?php echo form_input( 'create-link-text', 'Label', 'id="create-link-text" title="Label"' ); ?>
			<?php echo form_input( 'create-link-href', 'URL', 'id="create-link-href" title="URL"' ); ?>
			<a class="flagger" title="Toggle Importance" href="#">Toggle Importance</a>
		</div>

	</div><!-- /.edit-bar -->
	
	<div class="actions">
		<a href="#" id="cancel-task">Cancel</a>
		<a href="#" id="save-task">Save</a>
		<?php echo form_submit('submit-task', 'Save', 'id="submit-task"'); ?>
	</div>
	
<?php echo form_close();