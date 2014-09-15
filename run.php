<?
date_default_timezone_set("UTC");

$filename = "output.gpx";
$firstDate = new DateTime("2014-04-01");
$date = new DateTime("2014-05-01");
//$date = new DateTime(); <- for today's date

$segments = [];

@unlink($filename);

while ($date > $firstDate)
{
	$formattedDate = $date->format('Ymd');
	$date->sub(new DateInterval("P1D"));

	echo "Getting segments for $formattedDate (total " . count($segments) . ")...\n";
	foreach (getSegments($formattedDate) as $s)
		array_push($segments, $s);
}

file_put_contents($filename, makeXml($segments));

function getSegments($date)
{
	$data = json_decode(file_get_contents("https://api.moves-app.com/api/v1/user/storyline/daily/$date?trackPoints=true&access_token=<<INSERT ACCESS TOKEN HERE>>"));
	return $data[0]->segments;
}

function makeXml($segments)
{
	$xml = '<?xml version="1.0"?><gpx xmlns="http://www.topografix.com/GPX/1/1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
			<metadata>
	        <link href="http://moves-to-gpx.ppy.sh">
	            <text>moves-to-gpx</text>
	        </link>
	    </metadata>
	    <trk>
        <name>outpu</name>';

	foreach ($segments as $segment)
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