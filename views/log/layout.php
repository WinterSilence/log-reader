<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Kohana Log Viewer</title>
		<base href="/">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<style media="all" type="text/css">
		<?php include_once MODPATH.'log/media/bootstrap.min.css' ?>
		<?php include_once MODPATH.'log/media/style.css' ?>
		</style>
		<script type="text/javascript">
		<?php include_once MODPATH.'log/media/jquery.min.js' ?>
		</script>
	</head>
	<body>
		<div class="container">
			<div class="content">
				<div class="row">
					<div class="span2">
						<div class="well sidebar-nav">
						<?php echo View::factory('log/menu')
										->set('logs', $log->logs)
										->set('param', $param) ?>
						</div>
					</div>
					<div class="span10">
						<?php echo $content ?>
					</div>
				</div><!-- /container -->
			</div>
		</div>
	</body>
</html>