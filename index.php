<html>
	<head>
		<title>AJAX Player</title>
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body onload="update()">
		<div class="center">
		<?php
			include 'connection.php';

			if(!isset($_GET['room'])) {
				$cmd = 'SELECT * FROM `rooms`;';
				$result = mysqli_query($connect, $cmd);

				echo '<h1>Lobby</h1>';

				while($row = mysqli_fetch_array($result)) {
					echo '<a class="item" href="index.php?room='.$row['id'].'">'.$row['name'].'</a>';
					echo '<br/>';
				}

				echo '<form action="add_room.php" method="get">';
				echo '<input type="text" name="name" placeholder="Room name" required>';
				echo '<br/>';
				echo '<input type="submit" value="+">';
				echo '</form>';
			}else {
				$room_id = $_GET['room'];
				
				$cmd = 'SELECT `name` FROM `rooms` WHERE `id`='.$room_id.';';
				$result = mysqli_query($connect, $cmd);
				
				while($row = mysqli_fetch_array($result)) {
					$room_name = $row['name'];
				}

				echo '<h1>'.$room_name.'</h1>';

				echo '<form action="upload.php?room='.$room_id.'" method="post" enctype="multipart/form-data">';
				echo '<label class="myLabel">';
			    echo '<input type="file" name="song" required/>';
			    echo '<span>Upload</span>';
				echo '</label>';
				echo '<input type="submit" value="&#8593;">';
				echo '</form>';

				echo '<p id="status">No track</p>';
				
				echo '<a class="button" href="index.php">&#8592;</a>';
				echo '<a id="button" class="button" onclick="setStatus()">&#9658;</a>';
				echo '<a class="button" href="index.php?room='.$room_id.'"><b>R</b></a>';
			}

			mysqli_close($connect);
		?>
		</div>
		<script>
			var audio = new Audio("<?php echo $room_name; ?>/song.mp3");

			function update() {
				change();
				setInterval(getStatus, 50);
			}

			function change() {
				audio = new Audio("<?php echo $room_name; ?>/song.mp3");
			}

			function play() {
				audio.play();
			}

			function pause() {
				audio.pause();	
			}
		
			function setStatus() {
				var xhttp = new XMLHttpRequest();
				xhttp.open("GET", "set_status.php?room=<?php echo $room_id; ?>", true);
				xhttp.send();

				return false;
			}

			function getStatus() {
				var xhttp = new XMLHttpRequest();

				xhttp.onreadystatechange = function() {
					if(this.readyState == 4 && this.status == 200) {
						var element = document.getElementById("status");
						var button = document.getElementById("button");			

						switch(this.responseText) {
							case "play":
								play();
								button.innerHTML = "&#10074;&#10074;";
								element.innerHTML = "Playing";
								break;
							case "pause":
								pause();
								button.innerHTML = "&#9658;";
								element.innerHTML = "Paused";
								break;
							case "change":
								change();
								button.innerHTML = "&#9658;";
								element.innerHTML = "Ready";
								break;
							case "empty":
								button.innerHTML = "&#9658;";
								element.innerHTML = "No track";
								break;
						}
					}
				};
										    
				xhttp.open("GET", "get_status.php?room=<?php echo $room_id; ?>", true);
				xhttp.send();
			}
		</script>
	</body>
</html>
