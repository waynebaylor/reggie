
<h3>Current Cell</h3>

<?php foreach($this->template['cells'] as $index => $cell): ?>
<div class="current-cell <?php echo ($index === 0)? 'current-cell-selected' : 'hide' ?>">
	<table>
		<tr>
			<td class="label">X</td>
			<td>
				<?php echo $this->HTML->text(array(
					'name' => 'xCoord',
					'value' => $cell['xCoord'],
					'size' => 5
				)) ?>
				in
			</td>
		</tr>
		<tr>
			<td class="label">Y</td>
			<td>
				<?php echo $this->HTML->text(array(
					'name' => 'yCoord',
					'value' => $cell['yCoord'],
					'size' => 5
				)) ?>
				in
			</td>
		</tr>
		<tr>
			<td class="label">Width</td>
			<td>
				<?php echo $this->HTML->text(array(
					'name' => 'width',
					'value' => $cell['width'],
					'size' => 5
				)) ?>
				in
			</td>
		</tr>
		<tr>
			<td class="label">Font</td>
			<td>
				<?php echo $this->HTML->select(array(
					'name' => 'font',
					'value' => $cell['font'],
					'items' => array(
						array('label' => 'Courier', 'value' => 'courier'),
						array('label' => 'Helvetica', 'value' => 'helvetica'),
						array('label' => 'Times Roman', 'value' => 'times')
					)
				)) ?>
			</td>
		</tr>
		<tr>
			<td class="label">Font Size</td>
			<td>
				<?php echo $this->HTML->text(array(
					'name' => 'fontSize',
					'value' => $cell['fontSize'],
					'size' => 2
				)) ?>
			</td>
		</tr>
		<tr>
			<td class="label">Align</td>
			<td>
				<?php echo $this->HTML->select(array(
					'name' => 'horizontalAlign',
					'value' => $cell['horizontalAlign'],
					'items' => array(
						array('label' => 'Left', 'value' => 'L'),
						array('label' => 'Center', 'value' => 'C'),
						array('label' => 'Right', 'value' => 'R')
					)
				)) ?>
			</td>
		</tr>
	</table>
</div>
<?php endforeach; ?>
