<?php
	require_once("../res/x5engine.php");
	$login_error = "";
	if (isset($_GET['logout'])) {
		$login = new imPrivateArea();
		$login->logout();
		@header("Location: ../");
	}
	if (isset($_GET['error']))
		$login_error = $l10n['private_area_login_error'];
	if (isset($_POST['uname']) && $_POST['uname'] != "" && isset($_POST['pwd']) && $_POST['pwd'] != "") {
		$login = new imPrivateArea();
		if ($login->login($_POST['uname'], $_POST['pwd']) == 0) {
   			$url = $login->getSavedPage() ? $login->getSavedPage() : "index.php";
   			exit('<!DOCTYPE html><html><head><title>Loading...</title><meta http-equiv="refresh" content="1; url=' . $url . '"></head><body><p style="text-align: center;">Loading...</p></body></html>');
		} else
			$login_error = $l10n['private_area_login_error'];
	}
	require_once("header.php");
?>
<div class="navbar">
</div>
<div class="main-container">
	<div class="sub-container">
		<div class="login-form">			
			<form action="<?php echo basename($_SERVER['PHP_SELF']) ?>" method="post">
				<?php echo ($login_error != "") ? $login_error . "<br />" : ""; ?>
				<input type="text" name="uname" id="uname" data-label="Username" placeholder="Username"/>
				<input type="password" name="pwd" class="" data-label="Password" placeholder="Password"/>
				<div style="text-right"><input type="submit" class="button green" value="<?php echo l10n('blog_login'); ?>" /></div>
			</form>
			<script type="text/javascript">
				x5engine.boot.push(function () {
					// Enable the labels only if the placeholder is not supported
					var field = document.createElement( "input" );
					if (!("placeholder" in field)) {
						x5engine.imForm.setLabels("form");
					}
					$("#uname").focus();
				});
			</script>
		</div>
	</div>
</div>
<?php
	require_once("footer.php");
?>
