
<h3>Badge Cells</h3>

<div class="fragment-list">
	<div class="badge-template-cells">
		<?php foreach($this->badgeCells as $cell): ?>
		<div class="badge-cell <?php echo ($cell['selected'])? 'badge-cell-selected' : '' ?>">
			<?php echo $this->HTML->link(array(
				'label' => $this->escapeHtml($cell['text']),
				'href' => '/admin/badge/EditBadgeTemplate',
				'parameters' => array(
					'a' => 'view',
					'id' => $this->template['id'],
					'selectedCellId' => $cell['id']
				),
				'fragment' => 'template-layout'
			)) ?>
		</div>
		<?php endforeach; ?>
	</div>
</div>