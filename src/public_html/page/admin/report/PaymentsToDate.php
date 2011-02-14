
<div id="content">
	<h3>
		<?php echo $this->title ?>
	</h3>

	<table class="admin">
		<tr>
			<th>
				<?php echo implode('</th><th>', $this->data[0]) ?>
			</th>
		</tr>
		<?php for($i=1; $i<count($this->data); ++$i): ?>
		<tr>
			<td>
				<?php echo implode('</td><td>', $this->data[$i]) ?>
			</td>
		</tr>
		<?php endfor; ?>
	</table>	
</div>
