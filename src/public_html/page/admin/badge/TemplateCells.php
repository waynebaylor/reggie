
<h3>Badge Cells</h3>

<div class="fragment-list">
	<div class="badge-template-cells">
		<table style="width:100%;">
			<?php foreach($this->badgeCells as $cell): ?>
			<tr>
				<td style="text-align:left;">
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
				</td>
				<td style="text-align:right;">
					<?php echo $this->HTML->link(array(
						'label' => 'Remove',
						'href' => '/admin/badge/EditBadgeTemplate',
						'parameters' => array(
							'a' => 'removeBadgeCell',
							'id' => $cell['id']
						),
						'class' => 'remove-cell-link'
					)) ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</table>
	</div>
</div>