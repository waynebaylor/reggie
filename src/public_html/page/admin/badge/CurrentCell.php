<tr>
	<td></td>
	<td>
		<table><tr>
			<td>
				<table>				
					<tr>
						<td class="label required">X</td>
						<td>
							<?php echo $this->HTML->hidden(array(
								'name' => 'id',
								'value' => $this->selectedCell['id']
							)) ?>
							<?php echo $this->HTML->text(array(
								'name' => 'xCoord',
								'value' => $this->selectedCell['xCoord'],
								'size' => 5
							)) ?>
							<?php echo $this->HTML->hidden(array(
								'name' => 'eventId',
								'value' => $this->eventId
							)) ?>
							in
						</td>
					</tr>
					<tr>
						<td class="label required">Y</td>
						<td>
							<?php echo $this->HTML->text(array(
								'name' => 'yCoord',
								'value' => $this->selectedCell['yCoord'],
								'size' => 5
							)) ?>
							in
						</td>
					</tr>
					<tr>
						<td class="label required">Width</td>
						<td>
							<?php if($this->selectedCell['hasBarcode'] === 'T'): ?>
								<?php echo $this->selectedCell['width'] ?>
							<?php else: ?>
								<?php echo $this->HTML->text(array(
									'name' => 'width',
									'value' => $this->selectedCell['width'],
									'size' => 5
								)) ?>							
							<?php endif; ?>
							in
						</td>
					</tr>	
				</table>					
			</td>
			<td style="padding-left: 75px;">
				<?php if($this->selectedCell['hasBarcode'] !== 'T'): ?>
				<table>
					<tr>
						<td class="label required">Font</td>
						<td>
							<?php echo $this->HTML->select(array(
								'name' => 'font',
								'value' => $this->selectedCell['font'],
								'items' => array(
									array('label' => 'Arial', 'value' => 'arial'),
									array('label' => 'Courier', 'value' => 'courier'),
									array('label' => 'Helvetica', 'value' => 'helvetica'),
									array('label' => 'Times Roman', 'value' => 'times')
								)
							)) ?>
						</td>
					</tr>
					<tr>
						<td class="label required">Font Size</td>
						<td>
							<?php echo $this->HTML->text(array(
								'name' => 'fontSize',
								'value' => $this->selectedCell['fontSize'],
								'size' => 4
							)) ?>
						</td>
					</tr>
					<tr>
						<td class="label required">Align</td>
						<td>
							<?php echo $this->HTML->select(array(
								'name' => 'horizontalAlign',
								'value' => $this->selectedCell['horizontalAlign'],
								'items' => array(
									array('label' => 'Left', 'value' => 'L'),
									array('label' => 'Center', 'value' => 'C'),
									array('label' => 'Right', 'value' => 'R')
								)
							)) ?>
						</td>
					</tr>			
				</table>		
				<?php endif; ?>
			</td>
		</tr></table>
	</td>
</tr>

	
	
