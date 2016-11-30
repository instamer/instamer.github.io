<?php
require_once("header.php");
?>
<div class="main-container">
	<div class="sub-container">
<?php
if (isset($imSettings['ecommerce']) && isset($imSettings['ecommerce']['database'])) {
	$dbconf = getDbData($imSettings['ecommerce']['database']['id']);
	$prefix = $imSettings['ecommerce']['database']['table'];
	$pagination_length = 15;
	$pagination_start = (isset($_GET['page']) ? $_GET['page'] * $pagination_length : 0);
	$ecommerce = new ImCart();
	// Clean the temp files
	$ecommerce->deleteTemporaryFiles("../");
	if ($ecommerce->setDatabaseConnection($dbconf['host'], $dbconf['user'], $dbconf['password'], $dbconf['database'], $prefix)) {
		if (isset($_GET['id'])) {

			if (isset($_GET['evade'])) {
				$ecommerce->evadeOrder($_GET['id']);
				header('Location: order.php?id=' . $_GET['id']);
				exit();
			}
			if (isset($_GET['exportcsv'])) {
				ob_end_clean(); // Clear the output buffer
				$zip = $ecommerce->zipOrder($_GET['id'], "../");
				if (false !== $zip) {
					header('Content-Description: File Transfer');
				    header('Content-Type: application/octet-stream');
				    header('Content-Disposition: attachment; filename=' . substr(basename($zip), 0, strlen(basename($zip)) - 4) . ".zip");
				    header('Expires: 0');
				    header('Cache-Control: must-revalidate');
				    header('Pragma: public');
				    header('Content-Length: ' . readfile($zip)); // Read the file and automatically output it to the output buffer, return the file size
				    exit();
				}
				// As fallback, export the products csv only
				$csv = $ecommerce->getProductsCSV($_GET['id']);
				header('Content-Description: File Transfer');
			    header('Content-Type: application/octet-stream');
			    header('Content-Disposition: attachment; filename=' . $_GET['id'] . ".csv");
			    header('Expires: 0');
			    header('Cache-Control: must-revalidate');
			    header('Pragma: public');
			    header('Content-Length: ' . strlen($csv));
			    echo $csv;
			    exit();
			}

			/*
			|-----------------------------
			|	Show the order page
			|-----------------------------
			 */
			
			$orderArray = $ecommerce->getOrder($_GET['id']);
			if (count($orderArray)) {
				$order = $orderArray['order'];
?>
			<h1 class="section-title"><?php echo l10n('cart_order_no') . ": " . $order['id'] ?></h1>
			<!--_________________________
				
				Invoice and shipping data
				_________________________
			-->
				<table class="div-phone">
					<tbody class="div-phone">
					<tr class="div-phone">
						<td class="store-large-icon text-center top no-margin no-phone">
							<div class="page-navbar" style="margin-bottom: 10px;">&nbsp;</div>
							<img src="images/icn_address.jpg" alt="" />
						</td>
						<td class="top no-margin div-phone">
<?php if (count($orderArray['shipping']) === 0) : ?>
							<div class="page-navbar"><?php echo l10n('cart_vat_address') . " / " . l10n('cart_shipping_address') ?></div>
<?php else: ?>
							<div class="page-navbar"><?php echo l10n('cart_vat_address') ?></div>
<?php endif; ?>
							<table style="<?php echo (count($orderArray['shipping']) === 0) ? "width: auto;" : "" ?>">
							<?php foreach ($orderArray['invoice'] as $line): ?>
								<tr>
									<td class="small"><?php echo $line['label'] . ":" ?></td>
									<td class="small"><b><?php echo $line['value'] ?></b></td>
								</tr>
							<?php endforeach; ?>	
							</table>
						</td>
						<?php if (count($orderArray['shipping']) > 0): ?>
						<td class="top no-margin div-phone">
							<div class="page-navbar"><?php echo l10n('cart_shipping_address') ?></div>
							<table>
							<?php foreach ($orderArray['shipping'] as $line): ?>
								<tr>
									<td class="small"><?php echo $line['label'] . ":" ?></td>
									<td class="small"><b><?php echo $line['value'] ?></b></td>
								</tr>
							<?php endforeach; ?>
							</table>
						</td>
						<?php endif; // End shipping table ?>
					</tr>
					</tbody>
				</table>
			<!--_________________________
				
				Products data
				_________________________
			-->
				<table class="no-phone">
					<thead>
						<tr class="page-navbar">
							<th class="small"><span class="text-big"><?php echo l10n('cart_product_list') ?></span></th>
							<th class="small"><?php echo l10n('cart_descr') ?></th>
							<th class="small"><?php echo l10n('cart_price') ?></th>
							<th class="small"><?php echo l10n('cart_qty') ?></th>
							<?php if ($order['vat_type'] != "none"): ?>
							<th class="small print-text-right"><?php echo l10n($order['vat_type'] == "excluded" ? 'cart_vat' : 'cart_vat_included') ?></th>
							<?php endif; ?>
							<th class="small print-text-right"><?php echo l10n('cart_subtot') ?></th>
						</tr>
					</thead>
					<tbody>
<?php $p = 0; foreach ($orderArray['products'] as $product): ?>
						<tr class="<?php echo $p % 2 == 0 ? 'even' : 'odd' ?>">
<?php if($p == 0): ?>
							<td class="store-large-icon text-center" rowspan="<?php echo count($orderArray['products']) ?>"><img src="images/icn_product.jpg" alt=""></td>
<?php endif; ?>
							<td><?php echo $product['name'] . ($product['option'] != "" ? " - " . $product['option'] . ($product['suboption'] != "" ? " - " . $product['suboption'] : "") : "") ?></td>
							<td class="text-left"><?php echo cnumber_format($product['price'] / $product['quantity']) ?></td>
							<td class="text-left"><?php echo $product['quantity'] ?></td>
							<?php if ($order['vat_type'] != "none"): ?>
							<td class="text-left print-text-right"><?php echo cnumber_format($product['vat']) ?></td>
							<?php endif; ?>
							<td class="text-left print-text-right"><b><?php echo cnumber_format($order['vat_type'] == "excluded" ? $product['price'] : $product['price_plus_vat']) ?></b></td>
						</tr>
<?php $p++; endforeach; ?>
<?php if ($order['shipping_name'] != "" || $order['shipping_price'] != 0): ?>
			<!--_________________________
				
				Shipping data
				_________________________
			-->
						<tr class="page-navbar">
							<th class="small"><span class="text-big"><?php echo l10n('cart_shipping') ?></span></th>
							<th class="small" colspan="3"></th>
							<?php if ($order['vat_type'] != "none"): ?>
							<th class="small print-text-right"><?php echo l10n($order['vat_type'] == "excluded" ? 'cart_vat' : 'cart_vat_included') ?></th>
							<?php endif; ?>
							<th class="small print-text-right"><?php echo l10n('cart_price') ?></th>
						</tr>
						<tr>
							<td class="store-large-icon text-center"><img src="images/icn_send.jpg" alt="" class="no-phone" /></td>
							<td colspan="3"><?php echo $order['shipping_name'] ?></td>
							<?php if ($order['vat_type'] != "none"): ?>
							<td class="text-left print-text-right"><?php echo cnumber_format($order['shipping_vat']) ?></td>
							<?php endif; ?>
							<td class="text-left print-text-right"><b><?php echo cnumber_format($order['vat_type'] == "excluded" ? $order['shipping_price'] : $order['shipping_price_plus_vat']) ?></b></td>
						</tr>
<?php endif; ?>
<?php if ($order['payment_name'] != "" || $order['payment_price'] != 0): ?>
			<!--_________________________
				
				Payment data
				_________________________
			-->
						<tr class="page-navbar">
							<th class="small"><span class="text-big"><?php echo l10n('cart_payment') ?></span></th>
							<th class="small" colspan="3"></th>
							<?php if ($order['vat_type'] != "none"): ?>
							<th class="small print-text-right"><?php echo l10n($order['vat_type'] == "excluded" ? 'cart_vat' : 'cart_vat_included') ?></th>
							<?php endif; ?>
							<th class="small print-text-right"><?php echo l10n('cart_price') ?></th>
						</tr>
						<tr>
							<td class="store-large-icon text-center"><img src="images/icn_pay.jpg" alt="" class="no-phone" /></td>
							<td colspan="3"><?php echo $order['payment_name'] ?></td>
							<?php if ($order['vat_type'] != "none"): ?>
							<td class="text-left print-text-right"><?php echo cnumber_format($order['payment_vat']) ?></td>
							<?php endif; ?>
							<td class="text-left print-text-right"><b><?php echo cnumber_format($order['vat_type'] == "excluded" ? $order['payment_price'] : $order['payment_price_plus_vat']) ?></b></td>
						</tr>
<?php endif; ?>
<!-- Coupon Code -->
<?php if (isset($order['coupon']) && $order['coupon'] != ""): ?>
						<tr>
							<td colspan="4" style="border-left: none; border-bottom: none; border-top: none;"></td>
							<td class="text-left"><?php echo l10n('cart_coupon', "Coupon Code") ?></td>
							<td class="text-left print-text-right"><b><?php echo $order['coupon'] ?></b></td>
						</tr>
<?php endif; ?>
<!-- Total Amounts -->
						<tr>
							<td colspan="4"></td>
							<td colspan="<?php echo ($order['vat_type'] != 'none') ? '2' : '1' ?>" class="page-navbar"><?php echo l10n('cart_total', 'Total') ?></td>
						</tr>
<?php switch($order['vat_type']) {
	case "included": ?>
						<tr>
							<td colspan="4" style="border: none;"></td>
							<td class="text-left"><?php echo l10n('cart_total_vat') ?></td>
							<td class="text-left print-text-right"><b><?php echo cnumber_format($order['price_plus_vat']) ?></b></td>
						</tr>
						<tr>
							<td colspan="4" style="border: none;"></td>
							<td class="text-left"><?php echo l10n('cart_vat_included') ?></td>
							<td class="text-left print-text-right"><b><?php echo cnumber_format($order['vat']) ?></b></td>
						</tr>
<?php
	break;
	case "excluded": ?>
						<tr>
							<td colspan="4" style="border: none;"></td>
							<td class="text-left"><?php echo l10n('cart_total') ?></td>
							<td class="text-left print-text-right"><b><?php echo cnumber_format($order['price']) ?></b></td>
						</tr>
						<tr>
							<td colspan="4" style="border: none;"></td>
							<td class="text-left"><?php echo l10n('cart_vat') ?></td>
							<td class="text-left print-text-right"><b><?php echo cnumber_format($order['vat']) ?></b></td>
						</tr>
						<tr>
							<td colspan="4" style="border: none;"></td>
							<td class="text-left"><?php echo l10n('cart_total_vat') ?></td>
							<td class="text-left print-text-right"><b><?php echo cnumber_format($order['price_plus_vat']) ?></b></td>
						</tr>
<?php
	break;
	case "none":?>
						<tr>
							<td colspan="2" style="border: none;"></td>
							<td class="head"><?php echo l10n('cart_total') ?></td>
							<td class="text-left print-text-right"><b><?php echo cnumber_format($order['price_plus_vat']) ?></b></td>
						</tr>
<? break; ?>
<?php } ?>
					</tbody>
				</table>
				<!--_________________________
				
					Phone summary
					_________________________
				-->
				<table class="noprint no-tablet no-desktop">
					<tr>
						<td colspan="3" class="page-navbar text-center"><?php echo l10n('cart_product_list') ?></td>
					</tr>
<?php $p = 0; foreach ($orderArray['products'] as $product): ?>
					<tr class="<?php echo $p % 2 == 0 ? 'even' : 'odd' ?>">
						<td><?php echo $product['name'] . ($product['option'] != "" ? " - " . $product['option'] . ($product['suboption'] != "" ? " - " . $product['suboption'] : "") : "") ?></td>
						<td class="text-left"><?php echo $product['quantity'] ?></td>
						<td class="text-right print-text-right"><b><?php echo cnumber_format($order['vat_type'] == "excluded" ? $product['price'] : $product['price_plus_vat']) ?></b></td>
					</tr>
<?php $p++; endforeach; ?>
					<tr>
						<td colspan="3" class="page-navbar text-center"><?php echo l10n('cart_shipping') ?></td>
					</tr>
					<tr>
						<td colspan="2"><?php echo $order['shipping_name'] ?></td>
						<td class="text-right print-text-right"><b><?php echo cnumber_format($order['vat_type'] == "excluded" ? $order['shipping_price'] : $order['shipping_price_plus_vat']) ?></b></td>
					</tr>
					<tr>
						<td colspan="3" class="page-navbar text-center"><?php echo l10n('cart_payment') ?></td>
					</tr>
					<tr>
						<td colspan="2"><?php echo $order['payment_name'] ?></td>
						<td class="text-right print-text-right"><b><?php echo cnumber_format($order['vat_type'] == "excluded" ? $order['payment_price'] : $order['payment_price_plus_vat']) ?></b></td>
					</tr>
<!-- Total Amounts -->
					<tr>
						<td colspan="3" class="page-navbar"><?php echo l10n('cart_total', 'Total') ?></td>
					</tr>
<?php if (isset($order['coupon']) && $order['coupon'] != ""): ?>
					<tr>
						<td colspan="2" class="text-left"><?php echo l10n('cart_coupon', "Coupon Code") ?></td>
						<td class="text-left print-text-right"><b><?php echo $order['coupon'] ?></b></td>
					</tr>
<?php endif; ?>
<?php switch($order['vat_type']) {
	case "included": ?>
					<tr>
						<td colspan="2" class="text-left"><?php echo l10n('cart_total_vat') ?></td>
						<td class="text-right print-text-right"><b><?php echo cnumber_format($order['price_plus_vat']) ?></b></td>
					</tr>
					<tr>
						<td colspan="2" class="text-left"><?php echo l10n('cart_vat_included') ?></td>
						<td class="text-right print-text-right"><b><?php echo cnumber_format($order['vat']) ?></b></td>
					</tr>
<?php
	break;
	case "excluded": ?>
					<tr>
						<td colspan="2" class="text-left"><?php echo l10n('cart_total') ?></td>
						<td class="text-right print-text-right"><b><?php echo cnumber_format($order['price']) ?></b></td>
					</tr>
					<tr>
						<td colspan="2" class="text-left"><?php echo l10n('cart_vat') ?></td>
						<td class="text-right print-text-right"><b><?php echo cnumber_format($order['vat']) ?></b></td>
					</tr>
					<tr>
						<td colspan="2" class="text-left"><?php echo l10n('cart_total_vat') ?></td>
						<td class="text-right print-text-right"><b><?php echo cnumber_format($order['price_plus_vat']) ?></b></td>
					</tr>
<?php
	break;
	case "none":?>
					<tr>
						<td colspan="2"><?php echo l10n('cart_total') ?></td>
						<td class="text-right print-text-right"><b><?php echo cnumber_format($order['price_plus_vat']) ?></b></td>
					</tr>
<? break; ?>
<?php } ?>
				</table>
				<div class="noprint border-top text-center" style="padding: 10px 0;">
					<a class="button" href="orders.php?status=<?php echo $order['status'] ?>"><?php echo l10n('cart_goback', "Back") ?></a>
					<a class="button no-phone no-tablet" href="#" onclick="window.print(); return false;"><?php echo l10n('cart_print', "Print") ?></a>
					<?php if ($order['status'] == 'inbox'): ?>
					<a class="button green" href="order.php?id=<?php echo $order['id'] ?>&amp;evade=true"><?php echo l10n('cart_evade', "Evade") ?></a>
					<?php endif; ?>
					<a class="button" href="order.php?id=<?php echo $order['id'] ?>&amp;exportcsv=true"><?php echo l10n('cart_export', "Export") ?></a>
				</div>
<?php
			}
		}
	} else {
		echo "DB Connection error";
	}
}
?>
	</div>
</div>
<?php require_once("footer.php"); ?>
