<?php

session_start();

if(!empty($_SESSION['admin_user'])) {
	header('Location: /action/MainMenu?action=view');
	return;	
}

if(!empty($_POST['username']) && !empty($_POST['password'])) {
	$username = $_POST['username'];
	$password = $_POST['password'];
		
	if($username === 'useyour' && $password === 'illusions') {
		$_SESSION['admin_user'] = true;
		header('Location: /action/MainMenu?action=view');
		return;
	}
}

echo <<<_
	<html>
		<head>
			<title>Registration Login</title>
			<style type="text/css">
				body {
					background-color: #336699;
				}
			</style>
		</head>
		<body>
			<div style="margin:50px;">
				<form action="/login.php" method="post">
				<table>
					<tr>
						<td>Username:
						<td>
							<input type="text" name="username"/>
						</td>
					</tr>
					<tr>
						<td>Password:</td>
						<td>
							<input type="password" name="password"/>
						</td>
					</tr>
				</table>
				<input type="submit" value="Log In"/>
				</form>
			</div>
			<script type="text/javascript">
				document.getElementsByName("username")[0].focus();
			</script>
		</body>
	</html>
_;
?>