
{
	"identifier": "userId",
	"items": [
		<?php foreach($this->users as $userIndex => $u): ?>
		{
			"userId": <?php echo $u['id'] ?>,
			"email": "<?php echo $u['email'] ?>",
			"roles": [
				<?php foreach($u['roles'] as $roleIndex => $role): ?>
					<?php echo json_encode(array(
						'roleId' => $role['id'],
						'name' => $role['name'],
						'eventId' => empty($role['eventId'])? 'null' : $role['eventId'],
						'eventCode' => $role['eventCode'],
						'eventDisplayName' => $role['eventDisplayName']
					)) ?>
					<?php echo ($roleIndex < count($u['roles'])-1)? ',' : '' ?>
				<?php endforeach; ?>
			]
		}
		<?php echo ($userIndex < count($this->users)-1)? ',' : '' ?>
		<?php endforeach; ?>
	]
}