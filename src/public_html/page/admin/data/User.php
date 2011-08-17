
{
	"userId": <?php echo $this->user['id'] ?>,
	"email": "<?php echo $this->user['email'] ?>",
	"roles": [
		<?php foreach($this->user['roles'] as $roleIndex => $role): ?>
		{
			"roleId": <?php echo $role['id'] ?>,
			"name": "<?php echo $role['name'] ?>",
			"eventId": <?php echo empty($role['eventId'])? 'null' : $role['eventId'] ?>,
			"eventCode": "<?php echo $role['eventCode'] ?>",
			"eventDisplayName": "<?php echo $role['eventDisplayName'] ?>"
		}
		<?php echo ($roleIndex < count($this->user['roles'])-1)? ',' : '' ?>
		<?php endforeach; ?>
	]
}


