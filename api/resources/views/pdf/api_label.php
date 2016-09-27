<html>
	<head>
		<style>
			@page { margin: 180px 50px; }
			#header { position: fixed; left: 0px; top: -180px; right: 0px; height: 150px; background-color: orange; text-align: center; }
			#footer { position: fixed; left: 0px; bottom: -180px; right: 0px; height: 150px; background-color: lightblue; }
			#footer .page:after { content: counter(page, upper-roman); }
			#header_logo{width: 120px; height:100px;}
		</style>
		<title>Box label</title>
	</head>
	<body>
		<table>
		<?php 
		foreach($boxes as $box)
		{
		?>
			<tr>
				<td align="center">
					<img src="data:image/jpeg;base64,'<?php echo $box->label_image; ?>'">
				</td>
			</tr>
		<?php
		}
		?>
		</table>
	</body>
</html>