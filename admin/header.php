<?php
ob_start();
require_once "../res/x5engine.php";
require_once "../res/l10n.php";
require_once "../res/x5settings.php";
require_once "checkaccess.php";

/**
 * Format a number to look like a currency
 * @param  float $number The number
 * @return strng         The formatted string
 */
function cnumber_format($number) {
	global $imSettings;
	return number_format($number, 2) . $imSettings['ecommerce']['database']['currency'];
}
?>
<!doctype html>
<html lang="en">
<head>
	<title><?php echo str_replace(array("http://", "https://"), "", $imSettings['general']['url']) ?> Manager</title>
	<meta charset="UTF-8">
	<script type="text/javascript" src="../res/jquery.js"></script>
	<script type="text/javascript" src="../res/x5engine.js"></script>
<?php if (isset($useCharts)): ?>
	<script type="text/javascript" src="../cart/x5cart.js"></script>
	<script type="text/javascript" src="Chart.min.js"></script>
<?php endif; ?>
	<script type="text/javascript">
		x5engine.settings.currentPath = "../";
		x5engine.settings.imAdv.show = false;
	</script>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="../style/reset.css"/>
	<link rel="stylesheet" type="text/css" href="../style/style.css"/>
	<link rel="stylesheet" type="text/css" href="../style/print.css" media="print"/>
	<link rel="stylesheet" type="text/css" href="css/template.css"/>
	<link rel="stylesheet" type="text/css" href="css/print.css" media="print"/>
	<style>
	<?php
		// Find the existing star image
		$n = 0;
		while (!file_exists("../images/star0" . ++$n . "-big-empty.png") && $n < 10);
	?>
		.topic-star-container-big { background-image: url("../images/star0<?php echo $n ?>-big-empty.png")}
		.topic-star-fixer-big  { background-image: url("../images/star0<?php echo $n ?>-big-full.png")}
		.topic-star-container-small { background-image: url("../images/star0<?php echo $n ?>-small-empty.png")}
		.topic-star-fixer-small  { background-image: url("../images/star0<?php echo $n ?>-small-full.png")}
	</style>
</head>
<body>
<?php if (isset($logged) && $logged): ?>
	<div class="navbar noprint">
		<div class="main-container">
			<div class="navbar-hamburger" onclick="$('.navbar-menu').slideToggle()">Menu</div>
			<span class="navbar-menu">
<?php if (isset($imSettings['blog'])): ?>
				<a href="blog.php" class="navbar-link<?php if (isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) == "blog.php") echo " active"; ?>">
					<img src="images/blog.png" alt="<?php echo l10n("blog_title", "Blog") ?>"/>
					<span class="navbar-link-text"><?php echo l10n("blog_title", "Blog") ?></span>
				</a>
<?php endif; ?>
<?php if (isset($imSettings['guestbooks']) && count($imSettings['guestbooks'])): ?>
				<a href="guestbook.php" class="navbar-link<?php if (isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) == "guestbook.php") echo " active"; ?>">
					<img src="images/guestbook.png" alt="<?php echo l10n("admin_guestbook", "Guestbook") ?>"/>
					<span><?php echo l10n("admin_guestbook", "Guestbook") ?></span>
				</a>
<?php endif; ?>
<?php if (isset($imSettings['access']['entrancepage'])): ?>
				<a href="privatearea.php" class="navbar-link<?php if (isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) == "privatearea.php") echo " active"; ?>">
					<img src="images/privatearea.png" alt="<?php echo l10n('private_area_title', 'Private Area') ?>"/>
					<span><?php echo l10n('private_area_title', 'Private Area') ?></span>
				</a>
<?php endif; ?>
<?php if (isset($imSettings['ecommerce']) && isset($imSettings['ecommerce']['database'])): ?>
				<a href="orders.php" class="navbar-link<?php if (isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) == "orders.php") echo " active"; ?>">
					<img src="images/ecommerce.png" alt="<?php echo l10n('cart_title', 'E-Commerce') ?>"/>
					<span><?php echo l10n('cart_title', 'E-Commerce') ?></span>
				</a>
<?php endif; ?>
<?php if (isset($imSettings['dynamicobjects']) && count($imSettings['dynamicobjects'])): ?>
				<a href="dynamicobjects.php" class="navbar-link<?php if (isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) == "dynamicobjects.php") echo " active"; ?>">
					<img src="images/dynamicobjects.png" alt="<?php echo l10n('dynamicobj_name', 'Dynamic objects') ?>"/>
					<span><?php echo l10n('dynamicobj_name', 'Dynamic objects') ?></span>
				</a>
<?php endif; ?>
				<a href="website_test.php" class="navbar-link<?php if (isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) == "website_test.php") echo " active"; ?>">
					<img src="images/test.png" alt="Test"/>
					<span><?php echo l10n('admin_test', 'Website Test') ?></span>
				</a>
				<div class="navbar-link navbar-logout"><a class="button" href="login.php?logout"><?php echo l10n("admin_logout", "Logout") ?></a></div>
			</span>
		</div>
	</div>
<?php endif; ?>
