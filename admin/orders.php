<?php
require_once("header.php");
$status = @$_GET['status'];
if ($status == "") {
	$status = "inbox";
}
?>
<div class="main-container">
	<div class="sub-container">
		<h1 class="section-title"><?php echo l10n("cart_title", "Orders") ?></h1>
		<!-- Tabs -->
		<div class="page-navbar">
			<!-- search form -->
			<div class="order-search">
				<form method="GET" action="orders.php">
					<input name="search" id="search" type="text" value="<?php echo @$_GET['search'] ?>"/>
					<a href="#" onclick="$(this).parent().submit(); return false;" class="button white"><?php echo l10n('search_search') ?></a>
				</form>
				<script type="text/javascript">$(document).ready(function () { $("#search").focus(); });</script>
			</div>
			<div class="order-tabs-select no-desktop no-tablet">
				<select onchange="location.href=$(this).val()">
					<option value="orders.php?status=inbox" <?php if ($status == 'inbox') echo "selected" ?>><?php echo l10n('cart_inbox', 'Inbox') ?></option>
					<option value="orders.php?status=evaded" <?php if ($status == 'evaded') echo "selected" ?>><?php echo l10n('cart_evaded', 'Evaded') ?></option>
					<option value="orders.php?status=waiting" <?php if ($status == 'waiting') echo "selected" ?>><?php echo l10n('cart_waiting', 'Waiting') ?></option>
					<option value="availability.php"><?php echo l10n('cart_availability', 'Availability') ?></option>
					<option value="orders-charts.php"><?php echo l10n('cart_order_charts', 'Charts') ?></option>
				</select>
			</div>
			<div class="order-tabs no-phone no-small-phone">
				<a class="button <?php echo $status == 'inbox' ? 'green' : 'darkblue' ?>" href="?status=inbox"><?php echo l10n('cart_inbox', 'Inbox') ?></a>
				<a class="button <?php echo $status == 'evaded' ? 'green' : 'darkblue' ?>" href="?status=evaded"><?php echo l10n('cart_evaded', 'Evaded') ?></a>
				<a class="button <?php echo $status == 'waiting' ? 'green' : 'darkblue' ?>" href="?status=waiting"><?php echo l10n('cart_waiting', 'Waiting') ?></a>
				<a class="button darkblue" href="availability.php"><?php echo l10n('cart_availability', 'Availability') ?></a>
				<a class="button darkblue" href="orders-charts.php"><?php echo l10n('cart_order_charts', 'Charts') ?></a>
			</div>
		</div>
<?php
if (isset($imSettings['ecommerce']) && isset($imSettings['ecommerce']['database'])) {
	$dbconf = getDbData($imSettings['ecommerce']['database']['id']);
	$prefix = $imSettings['ecommerce']['database']['table'];
	$pagination_length = 15;
	$pagination_start = (isset($_GET['page']) ? $_GET['page'] * $pagination_length : 0);
	$ecommerce = new ImCart();
	// Clean the temp files
	$ecommerce->deleteTemporaryFiles("../");
	if ($ecommerce->setDatabaseConnection($dbconf['host'], $dbconf['user'], $dbconf['password'], $dbconf['database'], $prefix)) { // Connection check
		if (isset($_GET['delete'])) {
			$ecommerce->deleteOrderFromDb($_GET['delete']);
		}
		if (isset($_GET['waiting'])) {
			$ecommerce->moveOrderToWaiting($_GET['waiting']);
		}
		if (isset($_GET['inbox'])) {
			$ecommerce->moveOrderToInbox($_GET['inbox']);
		}
		if (isset($_GET['evade'])) {
			$ecommerce->evadeOrder($_GET['evade']);
		}
		if (isset($_GET['unevade'])) {
			$ecommerce->unevadeOrder($_GET['unevade']);
		}

		/*
		|-----------------------------
		|	Show the summary table
		|-----------------------------
		 */
		
		$result = $ecommerce->getOrders($pagination_start, $pagination_length, @$_GET['search'], $status);
		if (count($result['orders']) > 0): // Orders count
?>
		<!-- orders -->
		<table class="orders-table">
			<tbody>
<?php // Empty search! ?>
<?php if (isset($_GET['search']) && !count($result['orders'])): ?>
				<tr>
					<td colspan="6" class="text-center"><?php echo l10n('search_empty', 'Empty results') ?></td>
				</tr>
<?php else:?>
<?php // Orders ?>
<?php $t = 0; foreach ($result['orders'] as $order): $t++; ?>
				<tr class="<?php echo $t % 2 ? 'even' : 'odd' ?>">
					<td>
						<a class="no-phone no-tablet left order-img-view" href="order.php?id=<?php echo $order['id']?>&status=<?php echo $status ?>" title="<?php echo l10n('cart_show', 'Show') ?>">
							<img src="images/zoom.png" class="icon"/>
						</a>
						<div class="left">
							<div class="order-number"><a href="order.php?id=<?php echo $order['id'] ?>"><?php echo $order['id'] ?></a></div>
							<div class="text-small gray order-date"><?php echo date("d M Y", strtotime($order['ts'])) ?><span class="no-phone"><?php echo date("H:i:s", strtotime($order['ts'])) ?></span></div>
						</div>
					</td>
					<td class="no-phone">
						<?php
						$fields = array();

						// Convert the array into an associative array
						for ($i = 0; $i < count($order['invoice']); $i++) { 
							$field = $order['invoice'][$i];
							if (isset($field['field_id'])) {
								$fields[$field['field_id']] = $field;
							}
						}

						// Get only the useful fields
						$big = "";
						if (isset($fields['Company'])) {
							$big .= $fields['Company']['value'] . " - ";
						}
						if (isset($fields['Name'])) {
							$big .= $fields['Name']['value'] . " ";
						}
						if (isset($fields['LastName'])) {
							$big .= $fields['LastName']['value'] . " ";
						}
						$small = "";
						if (isset($fields['Address1'])) {
							$small .= $fields['Address1']['value'] . "<br />";
						}
						if (isset($fields['Address2'])) {
							$small .= $fields['Address2']['value'] . "<br />";
						}
						if (isset($fields['ZipPostalCode'])) {
							$small .= $fields['ZipPostalCode']['value'] . ", ";
						}
						if (isset($fields['City'])) {
							$small .= $fields['City']['value'] . " ";
						}
						if (isset($fields['StateRegion'])) {
							$small .= "(" . $fields['StateRegion']['value'] . ") ";
						}
						if (isset($fields['Country'])) {
							$small .= $fields['Country']['value'];
						}
						if (isset($fields['Email'])) {
							//$small .= "<br /><a href=\"mailto:" . $fields['Email']['value'] . "\">" . $fields['Email']['value'] . "</a>";
						}
						?>
						<div class="order-name"><?php echo $big ?></div>
						<div class="text-small"><?php echo $small ?></div>
					</td>
					<td class="no-phone">
						<?php
							if(strlen($order['coupon'])) {
								echo $order['coupon'] . "<br />";
							}
							if (isset($order['payment_icon']) && strlen($order['payment_icon'])) {
								echo "<img src=\"../" . $order['payment_icon'] . "\" title=\"" . $order['payment_name'] . "\"/>";
							} else {
								echo $order['payment_name'] . " - ";
							}
							if (isset($order['shipping_icon']) && strlen($order['shipping_icon'])) {
								echo "<img src=\"../" . $order['shipping_icon'] . "\" title=\"" . $order['shipping_name'] . "\"/>";
							} else {
								echo $order['shipping_name'];
							}
						?>
					</td>
					<td class="text-center"><?php echo cnumber_format($order['vat_type'] != "excluded" ? $order['price_plus_vat'] : $order['price']) ?></td>
					<td class="<?php echo $t % 2 ? 'even-dark' : 'odd-dark' ?> text-center">
						<a href="?delete=<?php echo $order['id']?>&status=<?php echo $status ?>" onclick="return confirm('<?php echo str_replace("'", "\\'", l10n('cart_delete_order_q', 'Are you sure?')) ?>')" title="<?php echo l10n('cart_delete_order', 'Delete') ?>"><img class="icon" src="images/cancel.png" alt="<?php echo l10n('cart_delete_order', 'Delete') ?>"/></a>
						<?php if ($status=='inbox'): ?>
						<a href="orders.php?waiting=<?php echo $order['id']?>&status=<?php echo $status ?>" title="<?php echo l10n('cart_move_to_wait', 'Move to waiting') ?>"><img class="icon" src="images/move_right.png" alt="<?php echo l10n('cart_move_to_wait', 'Move to waiting') ?>" /></a>
						<?php endif; ?>
						<?php if ($status=='waiting'): ?>
						<a href="orders.php?inbox=<?php echo $order['id']?>&status=<?php echo $status ?>" title="<?php echo l10n('cart_move_to_inbox', 'Move to inbox') ?>"><img class="icon" src="images/move_left.png" alt="<?php echo l10n('cart_move_to_inbox', 'Move to inbox') ?>" /></a>
						<?php endif; ?>
						<?php if ($status=='inbox'): ?>
						<a href="orders.php?evade=<?php echo $order['id']?>&status=<?php echo $status ?>" title="<?php echo l10n('cart_evade', 'Evade') ?>"><img class="icon" src="images/evade.png" alt="<?php echo l10n('cart_evade', 'Evade') ?>" /></a>
						<?php endif; ?>
						<?php if ($status=='evaded'): ?>
						<a href="orders.php?unevade=<?php echo $order['id']?>&status=<?php echo $status ?>" title="<?php echo l10n('cart_move_to_inbox', 'Move to inbox') ?>"><img class="icon" src="images/move_left.png" alt=""></a>
						<?php endif; ?>
					</td>
				</tr>
<?php endforeach; ?>
<?php endif; ?>
			</tbody>
		</table>
<?php // PAGINATION ?>
	<?php if ($result['paginationCount'] > $pagination_length): ?>
	<?php $limit = ceil($result['paginationCount'] / $pagination_length); ?>
			<div style="text-align: center; margin: 5px;">
	<?php if (@$_GET['page'] != 0): ?>
				<a href="orders.php?page=0&amp;search=<?php echo @$_GET['search'] ?>">&lt;&lt;</a>
	<?php endif; ?>
	<?php if (@$_GET['page'] - 2 >= 0): ?>
				<a href="orders.php?page=<?php echo @$_GET['page'] - 2 ?>&amp;search=<?php echo @$_GET['search'] ?>">&lt;</a>
	<?php endif; ?>
	<?php for ($i = max(@$_GET['page'] - 3, 0); $i < min($limit, max(@$_GET['page'] - 3, 0) + 6); $i++): ?>
				<a href="orders.php?page=<?php echo $i ?>&amp;search=<?php echo @$_GET['search'] ?>"><?php echo $i + 1?></a>
	<?php endfor; ?>
	<?php if (@$_GET['page'] + 2 < $limit): ?>
				<a href="orders.php?page=<?php echo @$_GET['page'] + 1 ?>&amp;search=<?php echo @$_GET['search'] ?>">&gt;</a>
	<?php endif; ?>
	<?php if (@$_GET['page'] != $limit - 1): ?>
				<a href="orders.php?page=<?php echo $limit - 1?>&amp;search=<?php echo @$_GET['search'] ?>">&gt;&gt;</a>
	<?php endif; ?>
			</div>
	<?php endif; ?>
<?php else: // Else orders count?>
	<div style="padding: 20px; text-align: center;">
		<?php echo l10n('search_empty', 'Empty results') ?>
	</div>
<?php endif; // Endif orders count ?>
<?php
	} else { // Connection check
		echo "DB Connection error";
	}
}
?>
	</div>
</div>
<?php require_once("footer.php"); ?>
