<?PHP
    // Dan Berkowitz, berkod2@rpi.edu, dansberkowitz@gmail.com, January 2013

    include('./core.php');
	
    if(!phpCAS::isAuthenticated())
    {
        header("Location: ./index.php");
    }
?>
<!DOCTYPE html>
<html>
    <head>
	<title class = "title">Time Tracker Templates</title>
	<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />
	<link rel="stylesheet" type="text/css" href="./style.css"/>
	<link href="http://www.rpi.edu/favicon.ico" type="image/ico" rel="icon"/>
	<script src="./static/jquery.js"></script> <!--Only used for easy ajax requests-->
    </head>
    <body>
	<div id="main">
	    <div id="title">
		<a href="./index.php"><div class="logo"></div><div id="logo">Time Tracker</div></a>
		<div id="result"></div>
	    </div>
	    <div class="red_bar"></div>
	    <div class="gray_bar"></div>
	    <div id="working_area">
	    <table border="1" style='width: 300px; margin: auto; text-align: left;'>
		<thead>
			<tr><th>Templates:</th></tr>
		    <tr>
			<th>Name</th>
			<th>Total Hours:</th>
			<th>Delete</th>
		    </tr>
		</thead>
		<tbody>
		<?PHP
			function getHours($passedString)
			{
				$items = substr_count($passedString, ',');
				$returnString = round ($items / 2) . ":";
				if (($items % 2) == 1)
				{
					$returnString .= "30";
				}else{
					$returnString .= "00";
				}
				return $returnString;
			}
		
		
		    $templates = database_helper::db_return_array("SELECT * FROM `templates` WHERE `owner`=(SELECT `id` FROM `users` WHERE `username`='" . phpCAS::getUser() . "') AND `status`=1");
		    foreach($templates as $template) //$template['id'], $template['name']
		    {
			echo "<tr id='tem" . $template['id'] . "'><td>" .  $template['name'] . "</td><td>" . getHours($template['data']) . "</td><td><button onclick=\"DeleteConfirm('" . $template['id'] . "', '" . $template['name'] . "');\">Delete Template</td></tr>";
		    }
		?>
		</tbody>
	    </table>
	    
	    </div>
	    <script>
		function DeleteConfirm(PassedTemplateID, PassedTemplateName)
		{
			var result = confirm("Delete template " + PassedTemplateName + "?");
			if (result) {
				order = $.ajax({
					type: 'POST',
					url: './ajax.php',
					data: {type: 'deleteTemplate', templateID: PassedTemplateID},
					success: function(data) {
					    if (data === "deleted") {
						$("#tem"+PassedTemplateID).remove()
					    }else{
						alert("Error deleting template");
					    }
					},
					error: function(data) {
					    //error calling names
					    alert("Error deleting template");
					}, 
				});
			}
		}
	    </script>
	    <!-- NEW SECTION! -->
	    <hr class='splitter'>
	    <div id="footer">
		<?PHP include("./footer.php"); ?>
	    </div>
	</div>
    </body>
</html>