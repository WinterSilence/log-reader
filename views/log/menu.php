<?php defined('SYSPATH') OR die('No direct script access.') ?>

<ul class="nav nav-list">
<?php foreach($logs as $year => $months): ?>
	<li>
		<?php echo $year ?>
		<ul>
			<?php foreach($months as $month => $days): ?>
			<li>
				<?php echo $month ?>
				<ul>
					<?php foreach($days as $day): ?>
					<li class="<?php if($param['year'] == $year AND $param['month'] == $month AND $param['day'] == $day) echo 'active' ?>">
						<?php echo HTML::anchor(Route::url('log', array('year' => $year, 'month' => $month, 'day' => $day)), $day) ?>
					</li>
					<?php endforeach; ?>
				</ul>
			</li>
			<?php endforeach; ?>
		</ul>
	</li>
<?php endforeach; ?>
</ul>