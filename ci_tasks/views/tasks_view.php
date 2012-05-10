<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title><?php echo $title; ?></title>
	<link rel="stylesheet" href="<?php echo base_url(); ?>style.css" />
	<link rel="shortcut icon" href="<?php echo base_url(); ?>favicon.ico" />
</head>
<body class="<?php echo $body_class; ?>">

	<div class="super-bar">

		<a class="home" href="<?php echo base_url(); ?>">Home</a>

		<ul class="options">
			<li class="settings-link"><a title="Settings" href="<?php echo base_url(); ?>settings">Settings</a></li>
			<li class="logout"><a title="Log Out" href="<?php echo base_url(); ?>logout">Log Out</a></li>
		</ul>

		<div class="container">

			<h1>Tasks</h1>

			<ul class="utilities">

				<?php if( $this->uri->segment(3) ) { ?>

					<li class="previous-day">
						<a href="/tasks/completed/<?php echo $date['yesterday']; ?>">&lt;</a>
					</li>

					<li class="date">
						<?php echo $date['today_long']; ?>
					</li>

					<li class="next-day">
						<a href="/tasks/completed/<?php echo $date['tomorrow']; ?>">&gt;</a>
					</li>

				<?php } else { ?>

					<li class="today-shortcut">
						<a title="Today's Completed Tasks" href="/tasks/completed/<?php echo $date['today_slug']; ?>">Today</a>
					</li>

				<?php } ?>

				<li id="date-pick">
					<input id="date-input" type="hidden" />
				</li>

				<?php if( !$this->uri->segment(3) ) { ?>

					<li class="create-task">
						<a id="create-task" title="New Task" href="#">+</a>
					</li>

				<?php } ?>

			</ul>

		</div><!-- /.container -->

	</div><!-- /.super-bar -->

	<div class="container">

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
	<script type="text/javascript" src="<?php echo base_url(); ?>js/scripts.min.js"></script>

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-30342988-1']);
  _gaq.push(['_setDomainName', 'crbapps.com']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

</body>
</html>