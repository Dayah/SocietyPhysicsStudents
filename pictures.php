<?
// Michael Dayah
// mdayah@utk.edu
// (865) 974-4987

function GetChildren($vals, &$i) {
	$unique_keys = array ("DATE", "INTRO");
	$children = array ();

	if ($vals[$i]["attributes"])
		foreach ($vals[$i]["attributes"] as $key => $attribs)
			$children[$key] = $attribs;

	while (++$i < count($vals)) {
		switch ($vals[$i]["type"]) {
			case "complete":
				if ($vals[$i]["value"] || array_search($vals[$i]["tag"], $unique_keys) !== FALSE)
					$children[$vals[$i]["tag"]] = $vals[$i]["value"];
				if ($vals[$i]["attributes"]) {
					if (array_search($vals[$i]["tag"], $unique_keys) !== FALSE)
						$children[$vals[$i]["tag"]] = $vals[$i]["attributes"];
					else
						array_push($children, $vals[$i]["attributes"]);
				}
				break;

			case "open":
				if ($vals[$i]["tag"] == "MENU")
					$children[$vals[$i]["tag"]] = GetChildren($vals, $i);
				else
					array_push($children, GetChildren($vals, $i));
				break;

			case "close":
				return $children;
		}
	}
}

function GetXMLTree($file) {
	$data = implode("", file($file));
	$p = xml_parser_create();
	xml_parser_set_option($p, XML_OPTION_SKIP_WHITE, 1);
	xml_parse_into_struct($p, $data, &$vals, &$index);
	xml_parser_free($p);

	$i = 0;
	return GetChildren($vals, $i);
}

function GetXMLType($file) {
	$data = implode("", file($file));
	$p = xml_parser_create();
	xml_parser_set_option($p, XML_OPTION_SKIP_WHITE, 1);
	xml_parse_into_struct($p, $data, &$vals, &$index);
	xml_parser_free($p);
	return $vals[0]["tag"];
}

function GetDirArray($sPath) {
	$handle = opendir($sPath);
	while ($file = readdir($handle)) {
		if ($file != "." && $file != "..") {
			if (is_dir($sPath . "/" . $file)) $retVal[$file] = GetDirArray($sPath . "/" . $file);
			else $retVal[] = $file;
		}
	}
	closedir($handle);
	return $retVal;
}

$slideshow = GetXMLTree("http://web.utk.edu/~sps/pictures.xml");
$current_image = $_GET["src"];

if (is_numeric($current_image)) {
	if (array_key_exists($current_image - 2, $slideshow)) {
		?><a href="<?= $current_image - 1 ?>">&larr;</a><?
	}

	?><a href="./">&uarr;</a><?
	if (array_key_exists($current_image, $slideshow)) {
		?><a href="<?= $current_image + 1 ?>">&rarr;</a><?
	}

	foreach ($slideshow as $key => $item) {
		if ($key + 1 == $current_image) {
			$image_size = getimagesize("images/pictures/" . $current_array["IMAGEDIR"] . "/" . $item["SRC"]); ?>
			<p><img src="<?= "images/pictures/" . $item["SRC"] ?>" <?= $image_size[3] ?> border="1" alt=""></p>
		<?	unset ($item["SRC"]);
			foreach ($item as $by => $attrib) { ?>
			<div><?= str_replace($search, $replace, $by) . " " . $attrib ?></div>
			<? }
		}
	}
} else { ?>
	<h2><a href="<?= (($uri[sizeof($uri) - 1] == "") ? "" : $current_array["ADDRESS"] . "/") . 1 ?>">Start</a></h2>
	<ol>
<?		foreach ($slideshow as $key => $item) {
			if ($item["DESCRIPTION"]) { ?>
		<li><a href="pictures/<?= $key + 1 ?>"><?= $item["DESCRIPTION"] ?></a></li>
<?		}	}?>
	</ol>
<? } ?>