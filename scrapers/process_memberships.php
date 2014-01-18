<?php
	include('connect.php');

 	function delete_all_between($beginning, $end, $string) {
	  $beginningPos = strpos($string, $beginning);
	  $endPos = strpos($string, $end);
	  if (!$beginningPos || !$endPos) {
	    return $string;
	  }

	  $textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);

	  return str_replace($textToDelete, '', $string);
	}

	$home = 'United Kingdom';

	$homedb = mysql_query("SELECT * FROM memberships WHERE country='$home'");
	$homedb = mysql_fetch_assoc($homedb);

	$homememberships = explode('; ',$homedb['memberships']);
	for($i=0;$i<count($homememberships);$i++){
		$homememberships[$i] = delete_all_between(' (',')',$homememberships[$i]);
	}

	$results = mysql_query("SELECT * FROM memberships WHERE country!='$home'");
	while ($row = mysql_fetch_array($results)) {
		$memberships = explode('; ',$row['memberships']);
		$array[$row['country']]['country_name'] = $row['country'];
		$array[$row['country']]['shared_memberships'] = 0;
	    for($i=0;$i<count($memberships);$i++){
			$memberships[$i] = delete_all_between(' (',')',$memberships[$i]);
			if(in_array($memberships[$i],$homememberships)){
				$array[$row['country']]['shared_memberships'] += 1;
			}
		}
	}
		
	$array = array_values($array);

	$json = '{';
	foreach($array as $country) {
		$json .= '"'.$country['country_name'].'":{';
		$json .= '"shared_memberships": '.$country['shared_memberships'].'';
		$json .= '},';
	}
	$json = rtrim($json, ",");
	$json .= '}';

	echo $json;
?>