<?
date_default_timezone_set("UTC");

$date = new DateTime();

$firstDate = new DateTime("2013-04-16");

while ($date > $firstDate)
{
	$formattedDate = $date->format('Ymd');
	$date->sub(new DateInterval("P1D"));

	echo "$formattedDate...\n";
	file_put_contents("$formattedDate.gpx", makeXml($formattedDate));
}

function makeXml($date)
{
	$data = json_decode(file_get_contents("https://api.moves-app.com/api/v1/user/storyline/daily/$date?trackPoints=true&access_token=<insert access token>"));


	$xml = '<?xml version="1.0"?><gpx xmlns="http://www.topografix.com/GPX/1/1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
			<metadata>
	        <link href="http://moves-to-gpx.ppy.sh">
	            <text>moves-to-gpx</text>
	        </link>
	    </metadata>
	    <trk>
        <name>' . $data[0]->date . '</name>';

	foreach ($data[0]->segments as $segment)
	{
		$xml .=  "<trkseg>";
		if ($segment->type == 'place')
		{
			$xml .= makeTrackPoint($segment);
		}
		else
		{
			foreach ($segment->activities as $activity)
			{
				if ($activity->trackPoints)
					foreach ($activity->trackPoints as $point)
						$xml .= makeTrackPoint($point);
			}
		}
		$xml .=  "</trkseg>";
	}
	$xml .= '</trk>
	</gpx>';

	return $xml;
}

function getIsoTime($date)
{
	$date = new DateTime($date);
	return $date->format('c');
}

function makeTrackPoint(&$data)
{
	$return = '';
	if ($data->type == 'place')
	{
		$startTime = getIsoTime($data->startTime);
		$endTime = getIsoTime($data->endTime);

		$return .= "<trkpt lat=\"" . $data->place->location->lat . "\" lon=\"" . $data->place->location->lon . "\"><time>$startTime</time><location>" . $data->place->name . "</location></trkpt>";
		$return .= "<trkpt lat=\"" . $data->place->location->lat . "\" lon=\"" . $data->place->location->lon . "\"><time>$endTime</time><location>" . $data->place->name . "</location></trkpt>";
	}
	else
	{
		$time = getIsoTime($data->time);
		$return .= "<trkpt lat=\"$data->lat\" lon=\"$data->lon\"><time>$time</time></trkpt>";
	}

	return $return;
}

?>