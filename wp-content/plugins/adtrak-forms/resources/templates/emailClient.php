<html>
	<body style="font-family:Arial, sans-serif; font-size:12px; padding: 0; margin: 0px; color:#555;">
		<div style="padding: 20px">
			<h1 style="font-family:Arial, sans-serif; font-size: 16px;"><?php echo $data->site_name; ?> | <?php echo $data->subject; ?></h1>
			<h2 style="font-family:Arial, sans-serif; font-size: 14px;">from <?php echo $data->emailFrom; ?> | Reference Number: <?php echo $data->referenceNo; ?></h2>

			<table style="padding:2%; margin:0; width: 96%; background-color: #fafafa;">

				<?php foreach ($data->rows as $name => $row) : ?>

					<tr style="border-bottom: 1px solid #ccc;">
						<td valign="center" style="width: 20%; background-color: #fff; font-family:Arial, sans-serif; padding:8px 10px; margin:0; font-size:12px; font-weight: bold;"><?php echo ucfirst($name); ?></td>
						<td valign="center" style="width: 65%; background-color: #fff; font-family:Arial, sans-serif; font-size: 12px; padding:8px 10px;margin:0;"><?php echo $row; ?></td>
					</tr>

				<?php endforeach; ?>

			</table>

			<p style="display: block; font-family: Arial; font-size: 12px;">This email has come from your Adtrak Website</p>
	</body>
</html>