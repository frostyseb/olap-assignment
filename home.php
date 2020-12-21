<!DOCTYPE html>
<?php 
include 'config.php'; 
$conn = createConn();

?>


<html>
	<head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
		<title>OLAP</title>
		<link rel="stylesheet" href="styles.css">
		
		
	</head>

	<body>
		<div id="homepage">
			<form id="xmlform" name="xmlform" method="post" action="olap.php">
				<fieldset id="sourceFieldset">
					<legend>Data Source</legend>
					<label for="databases">Database: </label>
					<select name="databases" id="databases">
						<option selected value="select">- Select one -</option>
						<?php
							$databases = mysqli_query($conn, "SHOW DATABASES");
							while ($row = mysqli_fetch_array($databases)) {
								echo '<option value="' . $row[0] . '">' . $row[0] . '</option>';
							}
						?>
					</select>
					<br><br>
					<label for="schema">Schema: </label>
					<select name="schema" id="schema">			
						<option selected value="select">- Select one -</option>
						<?php
							foreach(glob("schema/*") as $filename) {
								echo '<option value="' . str_replace('schema/', '', $filename) . '">' . str_replace('schema/', '', $filename) . '</option>';
							}
						?>					
					</select> 
					<br><br>					
					<!-- <input type="file" name="schema" id="schema" accept=".xml"> -->
					<label for="cube">Cube: </label>
					<select name="cube" id="cube">
						<option value="select">- Select One -</option>
					</select>
				</fieldset>
				<!-- <script>
					var readerxml=null;
					$('#xmlform').submit(function(event) {
						event.preventDefault();
						var xmlfile = document.getElementById('schema').files[0];
						console.log(xmlfile);
						var reader = new FileReader();
						reader.onload = function(e) {
							var doc = $.parseXML(e.target.result);
							var xmlcube = $(doc).find("Cube");
							for (var i=0, i< xmlcube.length; i++) {
								$.text()
							}
								//readerxml=e.target.result;
								//console.log(readerxml);
								//var parser = new DOMParser();
								//var doc = parser.parseFromString(readerxml, "application/xml");
								//console.log(doc);
						}
						//reader.readAsText(selectedFile);
					});
				</script> -->
				<fieldset>
					<legend>Dimension</legend>
					<ul id="dimension">
					</ul>
				</fieldset>
				
				<!-- <fieldset>
					<legend>Column</legend>
					<ul id="dimColumn">
					</ul>
				</fieldset> -->
				
				<fieldset>
					<legend>Measure</legend>
					<label for="measure">Measure: </label> 
					<div id="measure">
					</div>
				</fieldset>
				
				<br><br>
				<input type="submit" value="Create Report"> <input type="reset" value="clear">
			</form>
			
			<script>
			//update cube select options
			$('#schema').on('change', function() {
				$('#cube').find('option').remove().end().append(new Option("- Select one -", "select"));
				$.ajax({
					url: 'http://localhost/GitHub/olap-assignment/schema/' + $('#schema').val(),
					dataType: 'xml',
					success: function(response) {
						$(response).find("Cube").each(function() {
							var option = new Option($(this).attr("name"), $(this).attr("name"));
							$('#cube').append(option);
						});
					},
					error: function(jqXHR, textStatus, errorThrown){
						alert('error');
					}       
				});
				
				
			});
			
			//update dimension checkboxes
			$('#cube').on('change', function() {
				$('#dimension').find('input').remove();
				$('#dimension').find('a').remove();
				$('#dimension').find('li').remove();
				$('#dimension').find('ul').remove();
				$.ajax({
					url: 'http://localhost/GitHub/olap-assignment/schema/' + $('#schema').val(),
					dataType: 'xml',
					success: function(response) {
						$(response).find("Cube").each(function() {
							if ($(this).attr("name") == $('#cube').val()) {
								$(this).find("DimensionUsage").each(function () {									
									$('#dimension').append('<li><a>' + $(this).attr("name") +'</a></li>');
									var parentUL = $(this).attr("name");
									parentUL = parentUL.replace(' ', '');
									$(response).find("Dimension[name=" + $(this).attr('source') + "]").each(function() {										
										$('#dimension').append('<ul style="list-style-type: square;" id="dim' + parentUL +'">');
										
										$(this).find("Level").each(function() {
											$('#dim' + parentUL).append('<li><input type="radio" name="dim' + parentUL + '" value="' + $(this).attr("name") +'">' + $(this).attr("name") + '</li>');
										});
										$('#dimension').append('</ul>');
									});
									//$('<li />', {text: $(this},attr("name"), id: )
									//$('<input />', {type: 'checkbox', name: $(this).attr("name"), value: $(this).attr("name")}).appendTo($('#dimension'));									
									//$('<label />', {'for': $(this).attr("name"), text: $(this).attr("name")}).appendTo($('#dimension'));
									//$('<br/>').appendTo($('#dimension'));
								});
							}
						});
					},
					error: function(jqXHR, textStatus, errorThrown){
						alert('error');
					}       
				});				
			});
			
			//update column checkboxes
			//$('#cube').on('change', function() {
			//	$('#dimColumn').find('input').remove();
			//	$('#dimColumn').find('a').remove();
			//	$('#dimColumn').find('li').remove();
			//	$('#dimColumn').find('ul').remove();
			//	$.ajax({
			//		url: 'http://localhost/GitHub/olap-assignment/schema/' + $('#schema').val(),
			//		dataType: 'xml',
			//		success: function(response) {
			//			$(response).find("Cube").each(function() {
			//				if ($(this).attr("name") == $('#cube').val()) {
			//					$(this).find("DimensionUsage").each(function () {									
			//						$('#dimColumn').append('<li><a>' + $(this).attr("name") +'</a></li>');
			//						var parentUL = $(this).attr("name");
			//						parentUL = parentUL.replace(' ', '');
			//						$(response).find("Dimension[name=" + $(this).attr('source') + "]").each(function() {										
			//							$('#dimColumn').append('<ul id="col' + parentUL +'">');
			//							
			//							$(this).find("Level").each(function() {
			//								$('#col' + parentUL).append('<li><input type="radio" name="col' + parentUL + '"value="' + $(this).attr("name") +'">' + $(this).attr("name") + '</li>');
			//							});
			//							$('#dimColumn').append('</ul>');
			//						});
			//						//$('<li />', {text: $(this},attr("name"), id: )
			//						//$('<input />', {type: 'checkbox', name: $(this).attr("name"), value: $(this).attr("name")}).appendTo($('#dimension'));									
			//						//$('<label />', {'for': $(this).attr("name"), text: $(this).attr("name")}).appendTo($('#dimension'));
			//						//$('<br/>').appendTo($('#dimension'));
			//					});
			//				}
			//			});
			//		},
			//		error: function(jqXHR, textStatus, errorThrown){
			//			alert('error');
			//		}       
			//	});				
			//});
			
			//$('#colTime input[class=colTime]').change(function() {
			//	$('#dimRow').find('ul[id=rowTime]').remove();
			//});
			
			//update measure checkboxes
			$('#cube').on('change', function() {
				$('#measure').find('input').remove();
				$('#measure').find('label').remove();
				$('#measure').find('br').remove();
				$.ajax({
					url: 'http://localhost/GitHub/olap-assignment/schema/' + $('#schema').val(),
					dataType: 'xml',
					success: function(response) {
						$(response).find("Cube").each(function() {
							if ($(this).attr("name") == $('#cube').val()) {
								$(this).find("Measure").each(function () {
									$('<input />', {type: 'checkbox', name: $(this).attr("name"), value: $(this).attr("name")}).appendTo($('#measure'));									
									$('<label />', {'for': $(this).attr("name"), text: $(this).attr("name")}).appendTo($('#measure'));
									$('<br/>').appendTo($('#measure'));
								});
							}
						});
					},
					error: function(jqXHR, textStatus, errorThrown){
						alert('error');
					}       
				});				
			});
			</script>
		</div> <!-- END homepage -->
		
		
	</body>
</html>