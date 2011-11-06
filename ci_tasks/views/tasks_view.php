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

		<ul class="options-bar">
			<li class="settings-link"><a title="Settings" href="/tasks/settings">Settings</a></li>
			<li class="logout"><a href="/tasks/logout">Log Out</a></li>
		</ul>

		<div class="header clearfix">
		
			<h1><a href="/tasks/">Tasks</a></h1>
			<?php if( !$this->uri->segment(2) ) { ?>
				<div class="create-task">
					<a id="create-task" href="#">+</a>
				</div>
			<?php } ?>
			
			<div id="date-pick">
				<input id="date-input" type="hidden" />
			</div>
			
			<?php if( $this->uri->segment(2) ) { ?>
				<a class="next-day" href="/tasks/completed/<?php echo $date['tomorrow']; ?>">&gt;</a>
				<div class="date">
					<?php echo $date['today_long']; ?>
				</div>
				<a class="previous-day" href="/tasks/completed/<?php echo $date['yesterday']; ?>">&lt;</a>
			<?php } else { ?>
				<a class="today-shortcut" title="Today's Completed Tasks" href="/tasks/completed/<?php echo $date['today_slug']; ?>">Today</a>
			<?php } ?>
			
		</div><!-- /.header -->
	
		<div id="tasks" class="clearfix">
			<?php
				if($columns == 1) {
					include(APPPATH . 'views/_tasks_1_col.php');
				} else {
					include(APPPATH . 'views/_tasks_2_col.php'); 
				}
			?>
		</div><!-- /#tasks -->
		
	</div><!-- /.container -->
	
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/jquery-ui.min.js"></script>	
	<script type="text/javascript" src="<?php echo base_url(); ?>js/scripts.js"></script>

</body>
</html>