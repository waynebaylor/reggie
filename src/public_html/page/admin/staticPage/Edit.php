
<tr>
	<td class="label required">Name</td>
	<td>
		<?php echo $this->HTML->hidden(array(
			'name' => 'id',
			'value' => $this->page['id']
		)) ?>
		<?php echo $this->HTML->hidden(array(
			'name' => 'eventId',
			'value' => $this->page['eventId']
		)) ?>
		<?php echo $this->HTML->text(array(
			'name' => 'name',
			'value' => $this->escapeHtml($this->page['name']),
			'size' => 30,
			'maxlength' => 100
		)) ?>
	</td>
</tr>
<tr>
	<td class="label">Title</td>
	<td>
		<?php echo $this->HTML->text(array(
			'name' => 'title',
			'value' => $this->escapeHtml($this->page['title']),
			'size' => 30,
			'maxlength' => 255
		)) ?>
	</td>
</tr>
<tr>
	<td class="label">Page&nbsp;Content</td>
	<td style="width:100%;">
		<?php echo $this->HTML->hidden(array(
			'id' => 'static-content-input',
			'name' => 'content',
			'value' => ''
		)) ?>
		
		<div id="static-content-value">
			<?php echo $this->page['content'] ?>
		</div>
		
		<div id="static-content-editor">
			<div id="static-page-content"></div>
		</div>
	</td>
</tr>