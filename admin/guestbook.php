<?php
require_once("header.php");

$id = isset($_GET['id']) ? $_GET['id'] : "";

// If there's only one guestbook just show it and don't ask for more
if ($id == "" && count($imSettings['guestbooks']) < 2)
{
	$keys = array_keys($imSettings['guestbooks']);
	$id = $imSettings['guestbooks'][$keys[0]]['id'];
}
?>
<div class="main-container">
	<div class="sub-container">
		<h1 class="section-title"><?php echo l10n("admin_guestbook", "Guestbook") ?></h1>
		<div class="page-navbar">
<?php if (count($imSettings['guestbooks']) > 1): // Select a guestbook?>
				<script type="text/javascript">
					function showGb( obj ) {
						var val = $( obj ).val();
						if (val !== "")
							window.top.location.href = "guestbook.php?id=" + val;
						else
							window.top.location.href = "guestbook.php";
					}
				</script>
			<label for="gbid"><?php echo l10n("admin_guestbook_select", "Select a guestbook:") ?></label>
			<select onchange="showGb(this)" id="gbid">
				<option value="">-</option>
<?php foreach($imSettings['guestbooks'] as $gbid => $gb): ?>
				<option value="<?php echo $gbid?>"<?php echo ($gbid == $id ? " selected" : "") ?>><?php echo $gb['pagetitle'] . " - " . (strlen($gb['celltitle']) ? $gb['celltitle'] : $gbid) ?></option>
<?php endforeach; ?>
			</select>
<?php endif; ?>
<?php
	$gb = false;
	if (strlen($id)) {
		$data = $imSettings['guestbooks'][$id];
		$gb = new ImTopic($id, "../");
		$gb->setPostUrl('guestbook.php?id=' . $id);
		switch($data['sendmode'])
		{
			case "file":
				$gb->loadXML($data['folder']);
			break;
			case "db":
				$gb->loadDb($data['host'], $data['user'], $data['password'], $data['database'], $data['table']);
			break;
		}
		if ($gb->hasComments())
		{
			$gb->showAdminSummary($data['rating'], TRUE);
		}
	}
?>
		<div style="clear: both;"></div>
		</div>
<?php if ($id != ""):
// Show the comments of a guestbook ?>
			<div class="imBlogPostComment">
<?php
	if ($gb) {
		$gb->showAdminComments($data['rating'], $data['order']);
	}
endif; ?>
		</div>
	</div>
</div>
<?php require_once("footer.php"); ?>
