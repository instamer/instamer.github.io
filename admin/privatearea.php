<?php
require_once("header.php");
?>
<div class="main-container">
	<?php if (isset($_GET['ok'])): ?>
	<p class="alert alert-green" style="text-align: center;"><?php echo l10n('private_area_success', 'Completed Succesfully') ?></p>
	<?php endif; ?>
	<div class="sub-container">
		<h1 class="section-title"><?php echo l10n("admin_privatearea", "Private Area") ?></h1>
		<?php
			$db = getDbData($imSettings['access']['dbid']);
			$pa = new ImPrivateArea();
			$pa->setDbData($db['host'], $db['user'], $db['password'], $db['database'], $imSettings['access']['dbtable']);
			if (isset($_GET['validate'])) {
				$pa->validateWaitingUserById($_GET['validate']);
				echo "<script>location.href='privatearea.php#user_'" . $_GET['validate'] . ";</script>";
				exit;
			}
			if (isset($_GET['passwordemail'])) {
				$pa->sendLostPasswordEmail($_GET['passwordemail'], $imSettings['access']['emailfrom']);
				echo "<script>location.href='privatearea.php?ok';</script>";
				exit;
			}
			if (isset($_GET['validationemail'])) {
				$pa->sendValidationEmail($_GET['validationemail'], $imSettings['access']['emailfrom']);
				echo "<script>location.href='privatearea.php?ok';</script>";
				exit;
			}
		?>
		<table>
			<thead>
				<tr class="page-navbar">
					<th><?php echo l10n("private_area_username", "Username") ?></th>
					<th class="no-phone"><?php echo l10n("private_area_realname", "Full name") ?></th>
					<th class="no-phone"><?php echo l10n("private_area_email", "Email") ?></th>
					<th class="no-phone"><?php echo l10n("private_area_ip", "IP Address") ?></th>
					<th class="no-small-phone"><?php echo l10n("private_area_ts", "Registration date") ?></th>
					<th><?php echo l10n("private_area_status", "Status") ?></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			<?php $i = 0; foreach ($pa->getUsersById() as $user): $i++ ?>
				<tr id="id_<?php echo $user['id']?>" class="<?php echo $i % 2 ? 'even' : 'odd' ?>">
					<td><?php echo $user['username'] ?></td>
					<td class="no-phone"><?php echo $user['realname'] ?></td>
					<td class="no-phone"><?php echo $user['email'] ?></td>
					<td class="no-phone"><?php echo $user['ip'] ?></td>
					<td class="no-small-phone"><?php echo $user['ts'] ?></td>
					<?php if ($user['validated']): ?>
					<td class="green"><?php echo l10n("private_area_status_validated", "Validated") ?></td>
					<?php else: ?>
					<td class="no-phone">
						<?php echo l10n("private_area_status_not_validated", "Not validated") ?>
					</td>
					<?php endif; ?>
					<td class="<?php echo $i % 2 ? 'even-dark' : 'odd-dark' ?>">
						<?php if (!$user['validated']): ?>
						<a href="privatearea.php?validate=<?php echo $user['id'] ?>" onclick="return confirm('<?php echo str_replace("'", "\\'", l10n("private_area_confirm_validation", "Do you want to validate this user?")) ?>');"><img src="../res/tick.png" alt="<?php echo l10n("private_area_send_validate", "Manually validate this user") ?>" title="<?php echo l10n("private_area_send_validate", "Manually validate this user") ?>" style="margin-right: 5px;"></a>
						<a href="privatearea.php?validationemail=<?php echo $user['id'] ?>"><img src="../res/email_go.png" alt="<?php echo l10n("private_area_send_validate", "Resend the validation email") ?>" title="<?php echo l10n("private_area_send_validate", "Resend the validation email") ?>" style="margin-right: 5px;"></a>
						<?php endif; ?>
						<a href="privatearea.php?passwordemail=<?php echo urlencode($user['username']) ?>"><img src="../res/key_go.png" alt="<?php echo l10n("private_area_send_password", "Email the password to this user") ?>" title="<?php echo l10n("private_area_send_password", "Email the password to this user") ?>"></a>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
<?php require_once("footer.php"); ?>
