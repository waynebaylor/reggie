
{
	"identifier": "userId",
	"items": [
		<?php foreach($this->users as $userIndex => $u): ?>
		{
			"userId": <?php echo $u['id'] ?>,
			"email": "<?php echo $u['email'] ?>",
			"roles": [
				<?php foreach($u['roles'] as $roleIndex => $role): ?>
				{
					"roleId": <?php echo $role['id'] ?>,
					"name": "<?php echo $role['name'] ?>",
					"eventId": <?php echo empty($role['eventId'])? 'null' : $role['eventId'] ?>,
					"eventCode": "<?php echo $role['eventCode'] ?>",
					"eventDisplayName": "<?php echo $role['eventDisplayName'] ?>"
				}
				<?php echo ($roleIndex < count($u['roles'])-1)? ',' : '' ?>
				<?php endforeach; ?>
			]
		}
		<?php echo ($userIndex < count($this->users)-1)? ',' : '' ?>
		<?php endforeach; ?>
	]
}