
<h3>Cell Details</h3>

<?php foreach($this->template['cells'] as $index => $cell): ?>
<div class="cell-details <?php echo ($index === 0)? 'cell-details-selected' : 'hide' ?>">
	<?php foreach($cell['content'] as $content): ?>
	<p><?php echo $content['text']. ' -- '.$content['contactFieldName']?>
	<?php endforeach; ?>
</div>
<?php endforeach; ?>
