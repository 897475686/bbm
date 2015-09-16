<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>TEST MY_Controller</title>
</head>
<body>
	<h1>TEST MY_Controller</h1>
	<form action="<?php echo base_url().'test/test_mycontroller';?>" method="post">
		Session ID:<input name="session_id" type="text" />
		<input type="submit" value="submit">
	</form>

</body>
</html>