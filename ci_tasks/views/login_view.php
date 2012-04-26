<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Please Log In</title>
	<link rel="stylesheet" href="<?php echo base_url(); ?>style.css" />
	<link rel="shortcut icon" href="<?php echo base_url(); ?>favicon.ico" />
</head>
<body class="login-page">

    <?php echo $this->session->flashdata('message'); ?>

	<?php echo validation_errors(); ?>

    <?php if( isset($notice) ) { ?>
		<?php echo $notice; ?>
	<?php } ?>

	<div class="login clearfix">
	
		<h1>Please Log In</h1>
		
		<?php
			echo form_open('login', 'id="loginForm"');
			?>
			<div class="clearfix">
				<?php
					echo form_label('Username', 'username');
					echo form_input('username', set_value('username'), 'id="username"');		
				?>
			</div>
			<div class="clearfix">
				<?php
					echo form_label('Password', 'password');
					echo form_password('password', '', 'id="password"');
				?>
			</div>
			<?php
				echo form_submit('login', 'Login', 'id="submit"');
				
			echo form_close();
		?>
			
	</div>
	
	<script type="text/javascript">
		document.getElementById('username').focus();
	</script>
	
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