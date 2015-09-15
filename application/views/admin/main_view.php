<?php $this->load->file(APPPATH.'views/common.php');?>

<style type="text/css">
	.side_bar {
		float: left;
		position: absolute;
		left: 0;
		width: 15%;
		height: 100%;
		background-color: #393939;
		padding: 20px 20px;
	}
	.main {
		float: left;
		position: absolute;
		right: 0;
		/*background-color: blue;*/
		height: 99%;
		width: 85%;
		/*margin-left: 260px;*/
	}
	.main iframe{
		width: 100%;
		height: 100%;
		border: 0;
	}
	.box {
		width: 100%;
		height: 800px;
		background-color: red;
	}
	.list {
		margin: 0;
		padding: 0;
		list-style: none;
		margin-top: 20px;
	}
	.list li {
		display: list-item;
	}
/*	.list li a {
		display: block;
		line-height: 40px;
		font-size: 16px;
		font-weight: bold;
		text-align: center;
		text-decoration: none;

	}*/
	a:link,a:visited {
		display: block;
		font-weight:bold;
		color:white;
		background-color:#FF5A09;
		text-align:center;
		line-height: 40px;
		text-decoration:none;
	}
	a:hover,a:active {
		background-color:#FF9900;
	}
	
</style>

<nav class="side_bar">
	<img src="<?php echo base_url()."public/image/logo.jpg";?>">
	<ul id="nva_ul" class="list">
		<li>
			<a id="nva" href="javascript:;">用户管理</a>
			<ul style="display:none;">
				<li><a href="">用户列表</a></li>
			</ul>
		</li>

		<li>
			<a href="javascript:;">任务系统</a>
			<ul style="display:none;">
				<li><a href="">任务列表</a></li>
			</ul>
		</li>
	</ul>
</nav>

<div class="main">
	<iframe src=""></iframe>
</div>

<script type="text/javascript">
 
	$(function(){
		$("#nva_ul").children("li").children("a").each(function(){
			$(this).click(function(){
				var tar = $(this).siblings("ul");
				if(tar.css("display") == "none") {
					tar.slideDown(300);
				}
				else {
					tar.slideUp(300);
				}
			});
		});
	});
</script>

<?php $this->load->file(APPPATH.'views/footer.php');?>