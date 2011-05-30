
<h3>Badge Cells</h3>

<div class="fragment-list">
	<div class="badge-template-cells">
		<?php foreach($this->badgeCells as $cell): ?>
		<div class="badge-cell">
			<?php echo $this->escapeHtml($cell) ?>
		</div>
		<?php endforeach; ?>
	</div>
</div>