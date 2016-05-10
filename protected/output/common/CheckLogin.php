<?php
if(empty($_SESSION['username'])){
	echo '<script>alert("你还没有登录！");location.href="login.php";</script>';	
}

?>