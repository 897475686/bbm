<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>TEST get_comment</title>
</head>
<body>
	<h1>TEST get_comment</h1>
	<form action="<?php echo base_url().'task/detail/get_comment_by_id';?>" method="post">
		Session ID:<input name="session_id" type="text" />
		Task id <input name="id" type="text" />
		<input type="submit" value="submit">
	</form>

</body>
</html>