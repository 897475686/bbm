<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>TEST Login</title>
</head>
<body>
	<h1>TEST Login</h1>
	<form action="<?php echo base_url().'account/login';?>" method="post">
		Phone: <input name="phone" type="text">
		Password: <input name="pwd" type="password">
		<input type="submit" value="submit">
	</form>

</body>
</html>