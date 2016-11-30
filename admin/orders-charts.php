<?php
$useCharts = true;
require_once("header.php");

$plot_type = "noncumulative";
if (isset($_GET['plot_type'])) {
	$plot_type = $_GET['plot_type'];
}
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
					<option value="availability.php"><?php echo l10n('cart_availability', 'Availability') ?></option>
					<option value="orders-charts.php" selected><?php echo l10n('cart_order_charts', 'Charts') ?></option>
				</select>
			</div>
			<div class="order-tabs no-phone no-small-phone">
				<a class="button darkblue" href="orders.php?status=inbox"><?php echo l10n('cart_inbox', 'Inbox') ?></a>
				<a class="button darkblue" href="orders.php?status=evaded"><?php echo l10n('cart_evaded', 'Evaded') ?></a>
				<a class="button darkblue" href="orders.php?status=waiting"><?php echo l10n('cart_waiting', 'Waiting') ?></a>
				<a class="button darkblue" href="availability.php"><?php echo l10n('cart_availability', 'Availability') ?></a>
				<a class="button green" href="orders-charts.php"><?php echo l10n('cart_order_charts', 'Charts') ?></a>
			</div>
		</div>
		<script>
		Chart.defaults.global.responsive = true;
		var legendTemplate = "<ul class=\"plot-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].strokeColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>";
		</script>
		<div class="plot-container">
			<!-- plot type selection -->
			<div class="plot-type-container">
				<label for="plot_type"><?php echo l10n('cart_plot_type', 'Plot type:') ?></label>
				<select onchange="location.href=$(this).val()" id="plot_type">
					<option value="?plot_type=noncumulative"<?php echo $plot_type == 'noncumulative' ? " selected" : "" ?>><?php echo l10n('cart_plot_noncumulative', 'Non cumulative amounts') ?></option>
					<option value="?plot_type=cumulative"<?php echo $plot_type == 'cumulative' ? " selected" : "" ?>><?php echo l10n('cart_plot_cumulative', 'Cumulative amounts') ?></option>
					<option value="?plot_type=products"<?php echo $plot_type == 'products' ? " selected" : "" ?>><?php echo l10n('cart_plot_productscount', 'Products count') ?></option>
				</select>
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
		// Build the plot data
		$json = "";
		$colors = array(
			"rgb(250,164,58)",
			"rgb(96,189,104)",
			"rgb(77,77,77)",
			"rgb(93,165,218)",
			"rgb(241,124,176)",
			"rgb(222,207,63)",
			"rgb(178,118,178)",
			"rgb(178,145,47)",
			"rgb(241,88,84)"
		);
		$bgcolors = array(
			"rgba(250,164,58,0.2)",
			"rgba(96,189,104,0.2)",
			"rgba(77,77,77,0.2)",
			"rgba(93,165,218,0.2)",
			"rgba(241,124,176,0.2)",
			"rgba(222,207,63,0.2)",
			"rgba(178,118,178,0.2)",
			"rgba(178,145,47,0.2)",
			"rgba(241,88,84,0.2)"
		);
		switch ($plot_type) {
			/**
			 * -------------------
			 * Non Cumulative Plot
			 * -------------------
			 */
			case "noncumulative":
				$data = $ecommerce->getNonCumulativeSellings();
				if (!count($data)) {
					echo l10n("cart_plot_nodata", "Tere is no data about evaded orders to show.");
					break;
				}
				$json .= "{";
				// Months
				$json .= "labels: [";
				foreach(l10n("date_full_months", array()) as $month) {
					$json .= '"' . $month . '",';
				}
				$json = trim($json, ",");
				$json .= "],"; // Close labels
				// Data sets
				$json .= "datasets:[";
				$datasetCounter = 0;
				foreach ($data as $year => $yearData) {
					$json .= "{";
					$json .= '"label": "' . $year . '",';
					$json .= '"strokeColor": "' . $colors[$datasetCounter % count($colors)] . '",';
					$json .= '"pointColor": "' . $colors[$datasetCounter % count($colors)] . '",';
					$json .= '"pointStrokeColor": "' . $colors[$datasetCounter % count($colors)] . '",';
					$json .= '"fillColor": "' . $bgcolors[$datasetCounter % count($bgcolors)] . '",';
					$json .= '"data": [';
					for ($i = 1; $i <= 12; $i++) {
						if (isset($yearData[$i])) {
							$json .= $yearData[$i] . ",";
						}
					}
					$json = trim($json, ",");
					$json .= "]";
					$json .= "},";
					$datasetCounter++;
				}
				$json = trim($json, ",");
				$json .= "]"; // Close datasets
				$json .= "}";
?>
			<canvas id="plot"><canvas>
			<script type="text/javascript">
			// The legend template is defined up above
			var context = document.getElementById("plot").getContext("2d");
			var chart = new Chart(context).Line(<?php echo $json; ?>, {
				"scaleLabel": "<%=value%>" + x5CartData.settings.currency,
				"bezierCurve"    : false,
				"datasetFill"    : true,
				"legendTemplate" : legendTemplate
			});
			var legend = chart.generateLegend();
			$( "#plot" ).after( legend );
			</script>
<?php				
			break;
			/**
			 * -------------------
			 * Cumulative Plot
			 * -------------------
			 */
			case "cumulative":
				$data = $ecommerce->getCumulativeSellings();
				if (!count($data)) {
					echo l10n("cart_plot_nodata", "Tere is no data about evaded orders to show.");
					break;
				}
				$json .= "{";
				// Months
				$json .= "labels: [";
				foreach(l10n("date_full_months", array()) as $month) {
					$json .= '"' . $month . '",';
				}
				$json = trim($json, ",");
				$json .= "],"; // Close labels
				// Data sets
				$json .= "datasets:[";
				$datasetCounter = 0;
				foreach ($data as $year => $yearData) {
					$json .= "{";
					$json .= '"label": "' . $year . '",';
					$json .= '"strokeColor": "' . $colors[$datasetCounter % count($colors)] . '",';
					$json .= '"pointColor": "' . $colors[$datasetCounter % count($colors)] . '",';
					$json .= '"pointStrokeColor": "' . $colors[$datasetCounter % count($colors)] . '",';
					$json .= '"fillColor": "' . $bgcolors[$datasetCounter % count($bgcolors)] . '",';
					$json .= '"data": [';
					for ($i = 1; $i <= 12; $i++) {
						if (isset($yearData[$i])) {
							$json .= $yearData[$i] . ",";
						}
					}
					$json = trim($json, ",");
					$json .= "]";
					$json .= "},";
					$datasetCounter++;
				}
				$json = trim($json, ",");
				$json .= "]"; // Close datasets
				$json .= "}";
?>
			<canvas id="plot"><canvas>
			<script type="text/javascript">
			// The legend template is defined up above
			var context = document.getElementById("plot").getContext("2d");
			var chart = new Chart(context).Line(<?php echo $json; ?>, {
				"bezierCurve"    : false,
				"datasetFill"    : true,
				"legendTemplate" : legendTemplate
			});
			var legend = chart.generateLegend();
			$( "#plot" ).after( legend );
			</script>
<?php				
			break;
			/**
			 * -------------------
			 * Bars Chart
			 * -------------------
			 */
			case "products":
				$barsNumber = 10;
				$data = $ecommerce->getSoldItemsNumber($barsNumber);
				if (!count($data)) {
					echo l10n("cart_plot_nodata", "Tere is no data about evaded orders to show.");
					break;
				}
				$json .= "{";
				// Months
				$json .= "labels: [";
				foreach($data as $name => $value) {
					$json .= '"' . $name . '",';
				}
				$json = trim($json, ",");
				$json .= "],"; // Close labels
				// Data sets
				$json .= "datasets:[";
				$json .= "{";
				$json .= '"strokeColor": "' . $colors[0] . '",';
				$json .= '"fillColor": "' . $bgcolors[0] . '",';
				$json .= '"data": [';
				foreach ($data as $name => $value) {
					$json .= $value . ",";
				}
				$json = trim($json, ",");
				$json .= "]";
				$json .= "},";
				$json = trim($json, ",");
				$json .= "]"; // Close datasets
				$json .= "}";
?>
			<canvas id="plot"><canvas>
			<script type="text/javascript">
			Chart.defaults.global.showTooltips = false;
			// The legend template is defined up above
			var context = document.getElementById("plot").getContext("2d");
			var chart = new Chart(context).Bar(<?php echo $json; ?>, {
				"bezierCurve"    : false,
				"datasetFill"    : true
			});
			// Color the bars
			<?php for ($i = 0; $i < $barsNumber && $i < count($data); $i++): ?>
chart.datasets[0].bars[<?php echo $i ?>].strokeColor = "<?php echo $colors[$i % count($colors)] ?>";
			chart.datasets[0].bars[<?php echo $i ?>].highlightStroke = "<?php echo $colors[$i % count($colors)] ?>";
			chart.datasets[0].bars[<?php echo $i ?>].fillColor = "<?php echo $bgcolors[$i % count($bgcolors)] ?>";
			chart.datasets[0].bars[<?php echo $i ?>].highlightFill = "<?php echo $bgcolors[$i % count($bgcolors)] ?>";
			<?php endfor; ?>
			chart.update();
			</script>
<?php				
			break;
		}
	} else { // Connection check
		echo "DB Connection error";
	}
}
?>
		</div>
	</div>
</div>
<?php require_once("footer.php"); ?>
