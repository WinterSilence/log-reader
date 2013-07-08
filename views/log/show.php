<?php defined('SYSPATH') OR die('No direct script access.') ?>

<?php extract($param) ?>

<div class="row">
	<div class="span5">
		<h4><?php echo __('Log at date: ').$day.' / '.$month.' / '.$year ?></h4>
	</div>
	<div class="span5">
		<?php echo Form::open(Request::current()->url(), 
					array('class' => 'pull-right form-inline', 'method' => 'post','name' => 'mapping-filter')); ?>
			
			<?php echo Form::label('level', __('Level'), array('class' => 'help-inline')) ?>: &nbsp;
			<?php echo Form::select('level', Arr::merge(array(0 => __('ALL')), array_combine($log->levels,$log->levels)),
				$param['level'], array('class' => 'span2')); ?>
			
			<?php echo Form::submit('show', _('Show'), array('class' => 'btn btn-info')) ?>
			
			<?php echo HTML::anchor(
				Route::url('log', array('year' => $year, 'month' => $month, 'day' => $day, 'action' => 'delete')),
				__('Delete this file'), 
				array('class' => 'btn btn-danger', 'title' => __('Are you sure to delete?'))) ?>

		<?php echo Form::close() ?>
	</div>
</div>

<?php if ( ! empty($day) AND ! empty($year)): ?>

<table class="table table-bordered zebra-striped">
	<thead>
		<tr>
			<th width="5%"><?php echo __('Level') ?></th>
			<th width="10%"><?php echo __('Time') ?></th>
			<th width="20%"><?php echo __('Exception') ?></th>
			<th width="65%"><?php echo __('File') ?></th>
		</tr>
	</thead>
	<tbody>
	
	<?php foreach ($log->get_messages($level) as $message): ?>
	
		<?php $message['label'] = str_replace(
				array('EMERGENCY', 'CRITICAL',  'ERROR',     'ALERT',     'WARNING', 'NOTICE', 'INFO',    'DEBUG',   'STRACE'),
				array('important', 'important', 'important', 'important', 'warning', 'notice', 'success', 'warning', 'inverse'),
				$message['level']) ?>
				
		<tr class="<?php echo strtolower($message['level']) ?>">
			<td>
				<span class="label label-<?php echo $message['label'] ?>"> <?php echo $message['level'] ?> </span>
			</td>
			<td><?php echo $message['time'] ?></td>
			<td><?php echo $message['exception'] ?></td>
			<td><?php echo $message['file'] ?></td>
		</tr>
		
		<tr class="<?php echo $message['label'] ?>">
			<td colspan="4">
			
				<?php if ( ! empty($message['string'])): ?>
				<div>
					<b><?php echo __('Error in string') ?>:</b><br>
					<?php echo $message['string'] ?>
				</div>
				<?php endif; ?>
				
				<div>
					<b><?php echo __('Message') ?>:</b>
					<pre class="source"><?php echo $message['text'] ?></pre>
				</div>
				
				<hr>
				
			</td>
		</tr>
	<?php endforeach; ?>
	
	</tbody>
</table>

<?php endif; ?>