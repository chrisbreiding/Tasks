<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title><?php echo $title; ?></title>
	<link rel="stylesheet" href="<?php echo base_url(); ?>style.css" />
	<link rel="shortcut icon" href="<?php echo base_url(); ?>favicon.ico" />
</head>
<body class="<?php echo $body_class; ?>">

	<div class="container">
			
		<a class="logout" href="/tasks/logout">Log Out</a>

		<div class="header clearfix">
			<h1><a href="/tasks/">Tasks</a></h1>
			<div id="date-pick">
				<input id="date-input" type="hidden" />
			</div>
			<?php if( $this->uri->segment(3) ) { ?>
				<a class="next-day" href="/tasks/tasks/completed/<?php echo $date['tomorrow']; ?>">&gt;</a>
				<div class="date">
					<?php echo $date['today_long']; ?>
				</div>
				<a class="previous-day" href="/tasks/tasks/completed/<?php echo $date['yesterday']; ?>">&lt;</a>
			<?php } else { ?>
				<a class="today-shortcut" title="Today's Completed Tasks" href="/tasks/tasks/completed/<?php echo $date['today_slug']; ?>">Today</a>
			<?php } ?>
			
		</div><!-- /.header -->
	
		<div id="tasks">
			<?php include(APPPATH . 'views/_tasks.php'); ?>
		</div><!-- /#tasks -->
		
		<div class="options clearfix">
			<!-- <a class="edit-tasks" href="#">Edit</a> -->
			<?php if( !$this->uri->segment(3) ) { ?>
				<a class="create-task" href="#">+</a>
			<?php } ?>
		</div><!-- /.options -->
	
	</div><!-- /.container -->
	
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/jquery-ui.min.js"></script>	
	<script type="text/javascript" src="<?php echo base_url(); ?>js/scripts.js"></script>

</body>
</html>