<div class="container-fluid">
	<div class="row-fluid">
		<div class="span3">
			<div class="well sidebar-nav">
			<?php echo View::factory('log/menu')->set('logs', $log->logs)->set('param', $param) ?>
			</div>
		</div>
		<div class="span9">
			<?php echo $content ?>
		</div>
	</div> <!-- /container -->
</div> 