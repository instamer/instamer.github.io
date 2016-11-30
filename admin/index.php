<?php
	require_once("../res/x5engine.php");
	require_once("checkaccess.php");
	if ($logged)
	{
		require_once("../res/x5settings.php");
		if (isset($imSettings['blog']) && $imSettings['blog'] != FALSE)
		{
			header("Location: blog.php");
			exit;
		} else if (isset($imSettings['guestbooks']) && count($imSettings['guestbooks'])) {
			header("Location: guestbook.php");
			exit;
		} else if (isset($imSettings['access']['entrancepage'])) {
			header("Location: privatearea.php");
			exit;
		} else if (isset($imSettings['ecommerce']) && isset($imSettings['ecommerce']['database'])) {
			header("Location: orders.php?status=inbox");
			exit;
		} else if (isset($imSettings['dynamicobjects']) && count($imSettings['dynamicobjects'])) {
			header("Location: dynamicobjects.php");
		} else {
			header("Location: website_test.php");
		}
	}

// End of file index.php
