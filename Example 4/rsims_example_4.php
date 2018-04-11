<?php

 
// Robert Sims - Web Dev Candidate - Example 4
// 
// Feed building and parsing (JSON, XML)
// 
// - Build a fully valid JSON and XML feed using any items/info you want. 
// 
// - Feed must consist of at least 5 items and validate error free at 
// 	http://feedvalidator.org/ or http://validator.w3.org/feed/. 
// 
// - Feed must consist of your own items (title, description, etc.) and cannot 
//    come from any online resources.
// 
// - Extra credit: parse feed and display in table
// ************************************************************
// DEV NOTES:
// Before runtime, permissions will need to be set to allow file writing.
//
// Each file format validates without error, but in some cases there are warnings.
//
// The warnings appear to be a result of the copy/paste validation due to a 
//    doctype or doc header not being available since no file is represented.


// echo '<pre>';

$filename = 'my_work.csv';
$strout = '';

$strinfo = <<<EOS
<link rel="stylesheet" type="text/css" href="rsims_example_4.css" />
<style>
.content {
	display: inline-block;
	width: 50%;
	min-width: 600px;
}
</style>
<div class="content">
<h3>Robert Sims - Web Dev Candidate - Example 4</h3>
<p><span>Feed building and parsing (JSON, XML)</span></p>
<p>Build a fully valid JSON and XML feed using any items/info you want. Feed must consist of at least 5 items and validate error free at http://feedvalidator.org/ or http://validator.w3.org/feed/. Feed must consist of your own items (title, description, etc.) and cannot come from any online resources.</p>
<p>Extra credit: parse feed and display in table</p>

EOS;

$strinfo .= '<li>Data file used for this example: <a href="' . $filename . '" target="_blank">' . $filename . '</a></li>' . PHP_EOL;


// read CSV and convert to Array
$aryData = csvToArray($filename);



// ***********************************************************
// *** Encode Array to JSON file
// ***********************************************************

$outfile = str_replace('.csv', '', $filename) . '.json';
file_put_contents($outfile, json_encode($aryData) );
$strinfo .= '<li>JSON file written: <a href="' . $outfile . '" target="_blank">' . $outfile . '</a></li>' . PHP_EOL;



// ***********************************************************
// *** Write XML file
// ***********************************************************

$objXML = new SimpleXMLElement('<history></history>');

foreach ($aryData as $data) {
	$job = $objXML->addChild('job');
	foreach ($data as $key => $value) {
		// echo "<li>$key :: $value";
		
		$job->addChild($key, $value);
	}
}

$outfile = str_replace('.csv', '', $filename) . '.xml';
$objXML->asXML($outfile);
$strinfo .= '<li>XML file written: <a href="' . $outfile . '" target="_blank">' . $outfile . '</a></li>' . PHP_EOL;



// ***********************************************************
// *** Write RSS file
// ***********************************************************

$objXML = new SimpleXMLElement('<rss xmlns:atom="http://www.w3.org/2005/Atom"></rss>');

$objXML->addAttribute("version", "2.0");

$channel = $objXML->addChild("channel");

$atom = $channel->addChild('atom:atom:link'); 
// $atom->addAttribute('href', 'http://' . $_SERVER['SERVER_NAME'] .  '/my_work_rss.xml' );
$atom->addAttribute('href', 'http://robertsims.net/my_work_rss.xml' );
$atom->addAttribute('rel', 'self');
$atom->addAttribute('type', 'application/rss+xml');

$metafile = 'meta.csv';

// read CSV and convert to Array
$aryMeta = csvToArray($metafile);

// $channel->addChild('title', 'My work.');
// $channel->addChild('link', 'http://robertsims.net');
// $channel->addChild('description', 'A short synopsis of my work history.');
// $channel->addChild('language', 'en-us');

// build channel meta
foreach ($aryMeta as $meta) {
	foreach ($meta as $key => $value) {
		$channel->addChild($key, $value);
	}

}

// build items
foreach ($aryData as $data) {
	$item = $channel->addChild('item');

	//build item meta
	foreach ($data as $key => $value) {
		$item->addChild($key, $value);
	}

	$item->addChild('guid', $data['link']);
}

// $objXML->asXML(str_replace('.csv', '', $filename) . '_rss.xml');

$outfile = str_replace('.csv', '', $filename) . '_rss.xml';
$objXML->asXML($outfile);
$strinfo .= '<li>RSS file written: <a href="' . $outfile . '" target="_blank">' . $outfile . '</a></li>' . PHP_EOL;

// echo '<a href="https://validator.w3.org/feed/check.cgi?url=http%3A//robertsims.net/my_work_rss.xml"><img src="valid-rss-rogers.png" alt="[Valid RSS]" title="Validate my RSS feed" /></a>';



// ***********************************************************
// *** Read RSS file then display a table.
// ***********************************************************

$xml = file_get_contents('my_work_rss.xml');
$objXML = simplexml_load_string($xml);

$strout .= '<h3>' . $objXML->channel->title . '</h3>' . PHP_EOL;
$strout .= '<p>' . $objXML->channel->description . '</h3>' . PHP_EOL;

$strout .= '<table>' . PHP_EOL;

// write header
$aryKeys = array_keys((array)$objXML->channel->item);
$strout .= '	<tr>' . PHP_EOL;
foreach ($aryKeys as $key) {
	$strout .= '		<th>' . $key . '</th>' . PHP_EOL;
}
$strout .= PHP_EOL . '	</tr>' . PHP_EOL;

// write table data
foreach ($objXML->channel->item as $item) {
	$strout .= '	<tr>' . PHP_EOL;

	foreach ($aryKeys as $key) {
		if ($key === 'link') {
			$strout .= '		<td><a href="' . $item->$key . '" target="_blank">' . $item->$key . '</td>' . PHP_EOL;
		} else {
			$strout .= '		<td>' . $item->$key . '</td>' . PHP_EOL;
		}
	}

	$strout .= PHP_EOL . '	</tr>' . PHP_EOL;

}
$strout .= '</table>' . PHP_EOL;

$strout = buildHTML($strout);



$outfile = str_replace('.csv', '', $filename) . '.html';
file_put_contents($outfile, $strout );
$strinfo .= '<li>Extra credit file: <a href="' . $outfile . '" target="_blank">' . $outfile . '</a></li>' . PHP_EOL;

$strinfo .= <<<EOS
<div class="devnotes">
	<p><span>DEV NOTES:</span></p>
	
	<p>Before runtime, permissions will need to be set to allow file writing.</p>
	
	<p>Each file format validates without error, but in some cases there are warnings. The warnings appear to be a result of the copy/paste validation due to a doctype or doc header not being available since no file is represented.</p>
</div>
</div>
EOS;


echo $strinfo;
exit;



function csvToArray($_strFileName) {
	$_aryCSV = file($_strFileName);
	$_keys = array_shift($_aryCSV);
	$_aryKeys = str_getcsv($_keys, ',');

	for ($_k = 0; $_k < count($_aryCSV); $_k++) {
		$_aryLine = str_getcsv($_aryCSV[$_k], ',') ;

		for ($_i = 0; $_i < count($_aryKeys); $_i++) {
			$_aryData[$_k][$_aryKeys[$_i]] = $_aryLine[$_i];
		}
	}

	return $_aryData;
}

function buildHTML($_str) {
return <<< EOS
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
	<meta charset="utf-8">
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="-1" />
	<meta http-equiv="X-UA-Compatible" content="IE=9">
	<title>R.Sims Example 4</title>

	<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	
	<link rel="stylesheet" type="text/css" href="rsims_example_4.css" />
	
</head>
<body>

<div class="content">

$_str

</body>
</html>

EOS;
}

?>