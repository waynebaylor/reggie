
<h3>Badge Cells</h3>

<div class="fragment-list">
	<div class="badge-template-cells">
		<?php foreach($this->badgeCells as $index => $cell): ?>
		<?php if($index === 0): ?>
		<div class="badge-cell badge-cell-selected">
			<?php echo $this->escapeHtml($cell) ?>
		</div>
		<?php else: ?>
		<div class="badge-cell">
			<?php echo $this->escapeHtml($cell) ?>
		</div>
		<?php endif; ?>
		
		<?php endforeach; ?>
	</div>
</div>