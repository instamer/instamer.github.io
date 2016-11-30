<?php
	require_once("header.php");
?>
<div class="main-container">
	<div class="sub-container">
		<h1 class="section-title"><?php echo l10n("dynamicobj_list", "List of the Dynamic Objects in your Site") ?></h1>
		<div class="page-navbar">&nbsp;</div>
<?php
		if (isset($imSettings['dynamicobjects'])) {
			foreach ($imSettings['dynamicobjects'] as $objId => $obj): ?>
			<div class="dynamicobject-card">
				<table class="middle">
					<tr>
						<td><img src="images/dynamicobjects.png" alt="" /></td>
						<td>
							Page: <?php echo $obj['PageTitle'] ?><br/>
							Object: <a href="../<?php echo $obj['Page'] . "#" . $obj['ObjectId'] ?>" target="_blank"><?php echo (strlen($obj['ObjectTitle']) ? $obj['ObjectTitle'] : $obj['ObjectId']) ?></a>
						</td>
						<td>
							<a href="../<?php echo $obj['Page'] . "#" . $obj['ObjectId'] ?>" target="_blank"><img src="images/zoom.png" alt="" /></a>
						</td>
					</tr>
				</table>
			</div>
<?php
			endforeach;
		}
?>
		<div style="clear: both;"></div>
	</div>
</div>
<?php
require_once("footer.php");
?>
