<?php
	$buses = [];
	$busStop = "";

	if (isset($_POST["btnFindStop"]))
	{
		$busStop = $_POST["txtStop"];
		$curl = curl_init();

		curl_setopt_array(
			$curl, 
			[
				CURLOPT_URL => "https://datamall2.mytransport.sg/ltaodataservice/v3/BusArrival?BusStopCode=" . $busStop,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_POSTFIELDS => "",
				CURLOPT_HTTPHEADER => 
			  	[
			    	"Content-Type: application/json",
			    	"accountKey: jA6FF90AQqSbpAPRs9XJAg=="
			  	],
			]
		);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) 
		{
			echo "cURL Error #:" . $err;
		} 
		else 
		{
			$obj = json_decode($response);
			$buses = $obj->Services;
		}
	}

	function formatArrivalTime($strTime)
	{
		$newStr = str_replace("+08:00", "", $strTime);
		$newStr = str_replace("T", " ", $newStr);
		return date("h:i A", strtotime($newStr));
	}

	function busArrivalDisplay($obj)
	{
		$html = "<h2 class='capacity_" . $obj->Load . "'>";
		$html .= formatArrivalTime($obj->EstimatedArrival);
		$html .= "&nbsp;<img height='20' src='icon_" . $obj->Type . ".png' />";
		$html .= "</h2>";

		return $html;
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Singapore Bus Arrival</title>

		<style>
			body
			{
				background-color: rgb(200, 150, 0);
				font-family: sans-serif;
				font-size: 16px;
			}

			#container
			{
				border-radius: 20px;
				border: 3px solid rgba(255, 255, 255, 0.5);
				padding: 2em;
			}

			#container div
			{
				padding: 0.5em;
				color: rgb(0, 0, 0);
			}

			#stop input
			{
				border-radius: 5px;
				border: 0px solid rgba(0, 0, 0, 0);
				padding: 5px;
				width: 10em;
				height: 1em;
			}

			#stop button
			{
				background-color: rgb(255, 200, 0);
				color: rgb(255, 255, 255);
				border-radius: 5px;
				border: 0px solid rgba(0, 0, 0, 0);
				padding: 5px;
				width: 10em;
			}

			#stop button:hover
			{
				background-color: rgb(150, 50, 0);
			}

			.arrival h1
			{
				background-color: rgb(255, 200, 0);
				color: rgb(255, 255, 255);
				border-radius: 5px;
				border: 0px solid rgba(0, 0, 0, 0.5);
				padding: 5px;
				width: 5em;
				font-size: 20px;
				font-weight: bold;
				float: left;
				text-align: center;
			}

			.arrival h2
			{
				padding: 5px;
				width: 10em;
				font-size: 20px;
				font-weight: bold;
				float: left;
				text-align: center;
			}

			.capacity_SEA
			{
				color: rgb(100, 255, 100);
			}	

			.capacity_SDA
			{
				color: rgb(255, 255, 100);
			}		

			.capacity_LSD
			{
				color: rgb(255, 100, 100);
			}
		</style>
	</head>

	<body>
		<div id="container">
			<div id="stop">
				<h1>BUS STOP <?php echo $busStop;?></h1>
				<form method="POST">
					<input type="number" name="txtStop" placeholder="e.g, 9810007" />
					<button name="btnFindStop">FIND THIS STOP</button>
				</form>
			</div>

			<br/>

			<?php 
				foreach($buses as $bus)
				{
			?>
				<div class="arrival">
					<h1><?php echo $bus->ServiceNo; ?></h1>
					<?php 
						if ($bus->NextBus)
						{
							echo busArrivalDisplay($bus->NextBus);
						}

						if ($bus->NextBus2)
						{
							echo busArrivalDisplay($bus->NextBus2);
						}

						if ($bus->NextBus3)
						{
							echo busArrivalDisplay($bus->NextBus);
						}
					?>
				</div>
				<br style="clear: both" />
			<?php			
				}
			?>						
		</div>
	</body>
</html>
