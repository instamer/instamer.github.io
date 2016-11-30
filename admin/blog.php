<?php
	require_once("header.php");
	@include_once("../res/blog.inc.php");
?>
<div class="main-container">
	<div class="sub-container">
			<h1 class="section-title"><?php echo l10n("blog_title", "Blog") ?></h1>
			<!-- Show the available categories -->
			<script>
			function showCategory( obj ) {
				var cat = $( obj ).val();
				if ( cat !== "" )
					window.top.location.href = '<?php echo basename($_SERVER['PHP_SELF']) ?>?category=' + cat;
				else
					window.top.location.href = '<?php echo basename($_SERVER['PHP_SELF']) ?>';
			}

			function showPost( obj, objcat ) {
				var post = $( obj ).val(),
					cat = $( objcat ).val();
				if ( post !== "" && cat !== "" )
					window.top.location.href = '<?php echo basename($_SERVER['PHP_SELF']) ?>?category=' + cat + '&post=' + post;
				else
					window.top.location.href = '<?php echo basename($_SERVER['PHP_SELF']) ?>';	
			}
			</script>
			<div class="page-navbar">
				<div class="page-navbar-item">
					<label for="category"><?php echo l10n("admin_category_select") ?></label>
					<select name="category" id="category" onchange="showCategory(this)">
						<option value="">-</option>
<?php foreach($imSettings['blog']['posts_cat'] as $category => $posts): ?>
						<option value="<?php echo $category ?>"<?php echo $category == @$_GET['category'] ? " selected" : "" ?>><?php echo str_replace("_", " ", $category) ?></option>
<?php endforeach; ?>
					</select>
				</div>
<?php if (isset($_GET['category'])): ?>
				<div class="page-navbar-item">
					<label for="post"><?php echo l10n("admin_post_select") ?></label>
					<select name="post" id="post" onchange="showPost(this, '#category')">
						<option value="">-</option>
<?php foreach($imSettings['blog']['posts_cat'][$_GET['category']] as $post): ?>
						<option value="<?php echo $post ?>"<?php echo $post == @$_GET['post'] ? " selected" : "" ?>><?php echo $imSettings['blog']['posts'][$post]['title'] ?></option>
<?php endforeach; ?>
					</select>
				</div>
<?php endif; ?>

<?php 
	$topic = false;
	if (isset($_GET['category']) && isset($_GET['post']))
	{
		$data = $imSettings['blog'];
		$topic = new ImTopic($data['file_prefix'] . 'pc' . $_GET['post'], "../");
		$topic->setPostUrl('blog.php?category=' . $_GET['category'] . '&post=' . $_GET['post']);
		switch($data['sendmode'])
		{
			case "file":
				$topic->loadXML($data['folder']);
			break;
			case "db":
				$topic->loadDb($data['dbhost'], $data['dbuser'], $data['dbpassword'], $data['dbname'], $data['dbtable']);
			break;
		}
		if ($topic->hasComments())
		{
			$topic->showAdminSummary($data['comment_type'] != "comment");
		}
	}
?>
				<div style="clear: both;"></div>
			</div>
<?php 
	if ($topic)
	{
		$topic->showAdminComments($data['comment_type'] != "comment", $data['comments_order']);
	}
?>
	</div>
</div>
<?php require_once("footer.php"); ?>
