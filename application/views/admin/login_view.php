<?php $this->load->file(APPPATH.'views/common.php');?>

	<style type="text/css">
		
		.box {
			width: 560px;
			min-height: 200px;
			background-color: #CCCCCC;
			margin-right: auto;
			margin-left: auto;
			margin-top: 85px;
			border-radius: 5%;
		}
		.myform {
			padding: 10%;
		}
	</style>

<div class="container">
	<div class="box">

		<form class="myform" method="post" action="<?php echo base_url()."Admin/login/login_process"?>">

		  <div class="form-group">
		    <label>用户名</label>
		    <input name="username" type="text" class="form-control" id="name" placeholder="Administrator">
		  </div>

		  <div class="form-group">
		    <label>密码</label>
		    <input name="pwd" type="password" class="form-control" id="password">
		  </div>

		  <button type="submit" class="btn btn-default">Submit</button>

		</form>

	</div>
</div>

<?php $this->load->file(APPPATH.'views/footer.php');?>