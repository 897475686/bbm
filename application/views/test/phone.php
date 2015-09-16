<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>TEST Resgister</title>
</head>
<body>
	<h1>TEST Resgister</h1>
	<form action="<?php echo base_url().'account/resgister/check_phone';?>" method="post">
		Phone:<input name="phone" type="text">
		<input type="submit" value="submit">
	</form>

</body>
</html>