<html>
	<body style="font-family:Arial, sans-serif; font-size:12px; padding: 0; margin: 0px; color:#555;">

		<h1 style="font-family:Arial, sans-serif; font-size: 16px;"><?php echo $data->subject; ?></h1>
		<h2 style="font-family:Arial, sans-serif; font-size: 14px;">from <?php echo $data->emailFrom; ?> | Reference Number: <?php echo $data->referenceNo; ?></h2>
		<p style="font-family:Arial, sans-serif; font-size: 12px;">Thanks for getting in touch. We have received your message and we'll get back to you as soon as possible.</p>
		<p style="font-family:Arial, sans-serif; font-size: 12px;">A copy of your message is included below.</p>
		
		<table style="padding:2%; margin:0; width: 96%; background-color: #fafafa;">

			<?php foreach ($data->rows as $name => $row) : ?>

				<tr style="border-bottom: 1px solid #ccc;">

					<td valign="center" style="width: 20%; background-color: #fff; font-family:Arial, sans-serif; padding:8px 10px; margin:0; font-size:12px; font-weight: bold;"><?php echo ucfirst($name); ?></td>
					<td valign="center" style="width: 65%; background-color: #fff; font-family:Arial, sans-serif; font-size: 12px; padding:8px 10px;margin:0;"><?php echo $row; ?></td>

				</tr>

			<?php endforeach; ?>

		</table>

		<p style="display: block; font-family: Arial; font-size: 12px;">You have received this Email because you completed a form on our website.</p>

	</body>
</html>