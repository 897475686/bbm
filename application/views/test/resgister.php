<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>TEST Resgister</title>
</head>
<body>
	<h1>TEST Resgister</h1>
	<form action="<?php echo base_url().'account/register/register_process';?>" method="post">
		User name:<input name="username" type="text" />
		Password:<input name="pwd" type="password">
		Phone:<input name="phone" type="text">
		Sex:<input name="sex" type="text">
		School:<input name="school" type="text">
		<input type="submit" value="submit">
	</form>

</body>
</html>