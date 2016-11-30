<?php
require_once("header.php");
?>
<div class="main-container">
	<div class="sub-container">
		<h1 class="section-title"><?php echo l10n("cart_title", "Orders") ?></h1>
		<!-- Tabs -->
		<div class="page-navbar">
			<div class="order-tabs-select no-desktop no-tablet">
				<select onchange="location.href=$(this).val()">
					<option value="orders.php?status=inbox"><?php echo l10n('cart_inbox', 'Inbox') ?></option>
					<option value="orders.php?status=evaded"><?php echo l10n('cart_evaded', 'Evaded') ?></option>
					<option value="orders.php?status=waiting"><?php echo l10n('cart_waiting', 'Waiting') ?></option>
					<option value="availability.php" selected><?php echo l10n('cart_availability', 'Availability') ?></option>
					<option value="orders-charts.php"><?php echo l10n('cart_order_charts', 'Charts') ?></option>
				</select>
			</div>
			<div class="order-tabs no-phone no-small-phone">
				<a class="button darkblue" href="orders.php?status=inbox"><?php echo l10n('cart_inbox', 'Inbox') ?></a>
				<a class="button darkblue" href="orders.php?status=evaded"><?php echo l10n('cart_evaded', 'Evaded') ?></a>
				<a class="button darkblue" href="orders.php?status=waiting"><?php echo l10n('cart_waiting', 'Waiting') ?></a>
				<a class="button green" href="availability.php"><?php echo l10n('cart_availability', 'Availability') ?></a>
				<a class="button darkblue" href="orders-charts.php"><?php echo l10n('cart_order_charts', 'Charts') ?></a>
			</div>
		</div>
<?php
if (isset($imSettings['ecommerce']) && isset($imSettings['ecommerce']['database'])) { // Global check
	$dbconf = getDbData($imSettings['ecommerce']['database']['id']);
	$prefix = $imSettings['ecommerce']['database']['table'];
	$pagination_length = 15;
	$pagination_start = (isset($_GET['page']) ? $_GET['page'] * $pagination_length : 0);
	$ecommerce = new ImCart();
	// Clean the temp files
	$ecommerce->deleteTemporaryFiles("../");
	if ($ecommerce->setDatabaseConnection($dbconf['host'], $dbconf['user'], $dbconf['password'], $dbconf['database'], $prefix)) { // ecommerce connection

		/*
		|-----------------------------
		|	Show the  table
		|-----------------------------
		 */
		$results = $ecommerce->getDynamicProductsAvailabilityTable(0, 0);
		if (count($results)): // Available data
			foreach ($results as $category => $data): ?>
	<div class="store-product-category"><?php echo $category; ?></div>
				<?php for ($i = 0; $i < count($data); $i++): ?><div class="store-product-card">
	<div class="store-product-card-image" data-bg-image="<?php echo $data[$i]['image'] ?>"></div>
	<div class="store-product-card-text">
		<div style="margin-left: 6px;"><b><?php echo $data[$i]['name']; ?></b></div>
		<span class="availability <?php echo $data[$i]['status'] ?>"></span>
		<span class="text-small"><?php echo l10n("cart_quantity", "Quantity:") . " " . $data[$i]['availableQuantity'] ?></span>
	</div>
</div><?php	endfor; ?>
<?php 		endforeach; ?>
	<script type="text/javascript">
	$(document).ready(function () {
		$( ".store-product-card-image" ).each(function () {
			var div = $( this ),
				url = div.attr( "data-bg-image" ),
				body = $( "body" ),
				img = new $( "<img style=\"position: absolute; top: -1000px; left: -1000px;\"/>" );

			// Usage of background-cover does not suit us. Let's load the image and get its size, then decide what to do.
			img.load(function () {
				// Attach the image to the DOM or the width and height won't work
				body.append( img );
				// Get the image size
				var imageWidth = img.width(),
					imageHeight = img.height(),
					containerWidth = div.innerWidth(),
					containerHeight = div.innerHeight();

				// Make sure the image fits inside the container
				if ( imageWidth > containerWidth ) {
					imageHeight = containerWidth / imageWidth * imageHeight;
					imageWidth = containerWidth;
				}
				if ( imageHeight > containerHeight ) {
					imageWidth = containerHeight / imageHeight * imageWidth;
					imageHeight = containerHeight;	
				}

				div.css({
					'background-image': "url('" + url + "')",
					'background-position': Math.floor( (containerWidth - imageWidth) / 2 ) + "px " + Math.floor( (containerHeight - imageHeight) / 2 ) + "px",
					'background-size': imageWidth + "px " + imageHeight + "px"
				});

				img.remove(); // Puff!
			}).attr( "src", url );
		});
	});
	</script>
	<?php else: // Else available data ?>
		<div style="clear: both;"></div>
		<div style="padding: 20px; text-align: center;">
			<?php echo l10n('search_empty', 'Empty results') ?>
		</div>
	<?php endif; // End available data ?>
<?php
	} else { // End connection check
		echo "DB Connection error";
	}
} // End Global check
?>
	</div>
</div>
<?php require_once("footer.php"); ?>
