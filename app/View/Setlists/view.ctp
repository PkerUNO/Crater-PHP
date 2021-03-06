<?php
$keyPreferenceOptions = array('c' => 'Camelot', 'o' => 'Open Key', 'n' => 'Notation');
switch ($setlist['Setlist']['key_preference']) {
	case 'o':
		$keyPreference = 'openkey';
		$keyDisplay = 'Open Key';
		break;
	case 'n':
		$keyPreference = 'notation';
		$keyDisplay = 'Notation';
		break;
	case 'c':
	default:
		$keyPreference = 'camelot';
		$keyDisplay = 'Camelot';
		break;
};
?>
<div class="row">
	<div class="col-md-12">
		<h2><?php echo h($setlist['Setlist']['name']); ?></h2>
	</div>
</div>

<div class="row">
	<div class="col-md-9">
		<table class="table table-bordered table-condensed">
			<tr>
				<td><strong>Author</strong></td>
				<td><?php echo h($setlist['Setlist']['author']); ?></td>
			</tr>
			<tr>
				<td><strong>Genre</strong></td>
				<td><?php echo h($setlist['Setlist']['genre']); ?></td>
			</tr>
			<tr>
				<td><strong>Modified</strong></td>
				<td><?php echo $this->Time->niceShort($setlist['Setlist']['modified']); ?></td>
			</tr>
			<tr>
				<td><strong>Master BPM</strong></td>
				<td><?php echo h($setlist['Setlist']['master_bpm']); ?></td>
			</tr>
		</table>
	</div>

	<div class="col-md-3">
		<div class="alert alert-info">
			<p class="lead">Available actions</p>
			<div class="row">
			<?php echo $this->Form->create(false, array('type' => 'get', 'url' => array('action' => 'edit', $setlist['Setlist']['urlhash']), 'id' => 'editKeyForm')); ?>
				<div class="col-md-8">
					<?php echo $this->Form->input('editkey', array('label' => false, 'placeholder' => 'Edit Key', 'class' => 'form-control')); ?>
				</div>
				<div class="col-md-4">
					<?php echo $this->Form->submit('Edit', array('class' => 'btn btn-primary', 'div' => false)); ?>
				</div>
			<?php echo $this->Form->end(); ?>
			</div>
			<p class="help-popover" data-placement="bottom" data-title="Help" data-content="An Edit Key is required to edit or delete this setlist"><a href="#">What's this?</a></p>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<table class="table table-striped table-bordered table-condensed">
			<thead>
				<tr>
				<?php echo $this->Form->create('Setlist', array('type' => 'post')); ?>
					<th>#</th>
					<th>Artist - Title</th>
					<th>Label</th>
					<th>Length</th>
					<th>BPM</th>
					<th>Key <?php echo $this->Form->select('key_preference', $keyPreferenceOptions, array('empty' => false)); ?></th>
				<?php echo $this->Form->end(); ?>
				</tr>
			</thead>
			<tbody>
		<?php foreach ($setlist['Track'] as $track): ?>
				<tr>
					<td><?php echo h($track['setlist_order']); ?></td>
					<td><?php echo h($track['artist']) . " - " . h($track['title']); ?></td>
					<td><?php echo h($track['label']); ?></td>
					<td><?php echo h($track['length']); ?></td>
					<td><?php echo h($track['bpm_start']);
						if (isset($setlist['Setlist']['master_bpm']) && isset($track['bpm_difference'])) {
							echo $this->Track->displayBPM($track['bpm_difference']);
						}
						?></td>
					<td><?php echo isset($track['KeyStart'][$keyPreference]) ? h($track['KeyStart'][$keyPreference]) : "";
							if (isset($setlist['Setlist']['master_bpm']) && isset($track['key_start_modified'])) {
								echo $this->Track->displayKey($track['bpm_difference'], $track['key_start_modified']['Key'][$keyPreference]); } ?></td>
				</tr>
		<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function () {
	$('.help-popover').on('click', function(e) {
		e.preventDefault();
	}).popover();
	
	$('#SetlistKeyPreference').change(function() {
		$('#SetlistViewForm').submit();
	});
});
//]]>
</script>