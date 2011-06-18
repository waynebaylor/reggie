
<h3>Cell Details</h3>

<div class="fragment-list">
	<table class="admin">
		<?php if($this->selectedCell['hasBarcode'] === 'T'): ?>
			<tr>
				<th>Content</th>
				<th>Options</th>
			</tr>
			<?php if(empty($this->selectedCell)): ?>
			<tr>
				<td colspan="2">No Details</td>
			</tr>
			<?php else: ?>
			<?php foreach($this->selectedCell['barcodeFields'] as $barcodeField): ?>
			<tr>
				<td>
					<?php echo $this->escapeHtml("<{$barcodeField['contactFieldName']}>") ?>
				</td>
				<td>
					<?php echo $this->HTML->link(array(
						'label' => 'Remove',
						'href' => '/admin/badge/EditBadgeTemplate',
						'parameters' => array(
							'a' => 'removeCellContent',
							'cellId' => $this->selectedCell['id'],
							'id' => $barcodeField['id']
						),
						'class' => 'remove'
					)) ?>
				</td>
			</tr>
			<?php endforeach; ?>
			<?php endif; ?>
		<?php else: ?>
			<tr>
				<th></th>
				<th>Content</th>
				<th>Options</th>
			</tr>
			<?php if(empty($this->selectedCell)): ?>
			<tr>
				<td colspan="3">No Details</td>
			</tr>
			<?php else: ?>
			<?php foreach($this->selectedCell['content'] as $content): ?>
			<tr>
				<td>
					<?php echo $this->arrows(array(
						'href' => '/admin/badge/EditBadgeTemplate',
						'parameters' => array(
							'cellId' => $this->selectedCell['id']
						),
						'up' => array(
							'a' => 'moveCellContentUp',
							'id' => $content['id']
						),
						'down' => array(
							'a' => 'moveCellContentDown',
							'id' => $content['id']
						)
					)) ?>
				</td>
				<td>
					<?php if(empty($content['contactFieldId'])): ?>
						<?php echo $this->escapeHtml($content['text']) ?>
					<?php else: ?>
						<?php echo $this->escapeHtml("<{$content['contactFieldName']}>") ?>
					<?php endif; ?>
				</td>
				<td>
					<?php echo $this->HTML->link(array(
						'label' => 'Remove',
						'href' => '/admin/badge/EditBadgeTemplate',
						'parameters' => array(
							'a' => 'removeCellContent',
							'cellId' => $this->selectedCell['id'],
							'id' => $content['id']
						),
						'class' => 'remove'
					)) ?>
				</td>
			</tr>
			<?php endforeach; ?>
			<?php endif; ?>
		<?php endif; ?>
	</table>
</div>
