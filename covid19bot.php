<?php

$botToken = "1165224811:AAH0t7tGJ44F2DABL7MXg6Wx8LmFaBC8YSo";
$website = "https://api.telegram.org/bot".$botToken;

$request = file_get_contents( 'php://input' );

$request = json_decode( $request, TRUE );

if( !$request )
{
    file_get_contents($website."/sendmessage?chat_id=649020417&text=novalidjson");
}
elseif( !isset($request['update_id']) || !isset($request['message']) )
{
    file_get_contents($website."/sendmessage?chat_id=649020417&text=nochat");
}
else
{
       // file_get_contents($website."/sendmessage?chat_id=649020417&text=allok");
		$update_id = $request['update_id'];
		$chat_id = $request['message']['from']['id'];
		$first_name = $request['message']['from']['first_name'];
		$username = $request['message']['from']['username'];
		$text = ucwords($request['message']['text']);

		
		if($text == '/start')
		{
			$msg = "Hi ".$first_name."\n\nBelow are commands on how to use bot\nCount - get total count of cases in india.\World - get total count of global cases.\nNews - latest update on covid-19\nStateName(eg : Gujarat) : get state count with district count.\nCityName(eg : Ahmedabad) : get count of particular city / district.";

			file_get_contents($website."/sendmessage?chat_id=".$chat_id."&text=".urlencode($msg));
		}
		elseif($text == "count" || $text == "COUNT" || $text == "Count" || $text == "India" || $text == "india")
		{
			$countval = file_get_contents('https://api.covid19india.org/data.json');
			$indiavalue = json_decode($countval, 1);
			foreach ($indiavalue['statewise'] as $item) {	
					if ($item['state'] == "Total") {
			        $total = $item['confirmed'];
					$recoverd = $item['recovered'];
					$active = $item['active'];
					$death = $item['deaths'];
					$creply = "Total Positive Cases : ".$total."\n";
					$creply .= "Total Recovered Cases : ".$recoverd."\n";
					$creply .= "Total Active Cases : ".$active."\n";
					$creply .= "Total Deaths : ".$death;
					}
			}
			file_get_contents($website."/sendmessage?chat_id=".$chat_id."&text=".urlencode($creply));
		}
		elseif($text == "Global" || $text == "global" || $text == "World" || $text == "world")
		{
				  $countval = file_get_contents('https://api.covid19api.com/summary');
	      		  $indiavalue = json_decode($countval, 1);
	     		  $NewConfirmed = $indiavalue['Global']['NewConfirmed'];
		          $TotalConfirmed = $indiavalue['Global']['TotalConfirmed'];
		          $NewDeaths = $indiavalue['Global']['NewDeaths'];
		          $TotalDeaths = $indiavalue['Global']['TotalDeaths'];
		          $NewRecovered = $indiavalue['Global']['NewRecovered'];
		          $TotalRecovered = $indiavalue['Global']['TotalRecovered'];

		          $creply = "New Confirmed : ".$NewConfirmed."\n";
		          $creply .= "Total Confirmed : ".$TotalConfirmed."\n";
		          $creply .= "New Deaths : ".$NewDeaths."\n";
		          $creply .= "Total Deaths : ".$TotalDeaths."\n";
		          $creply .= "New Recovered : ".$NewRecovered."\n";
		          $creply .= "Total Recovered : ".$TotalRecovered; 
				file_get_contents($website."/sendmessage?chat_id=".$chat_id."&text=".urlencode($creply));
		}
		elseif($text == "news" || $text == "News" || $text == "NEWS")
		{
			$newsapikey = 'd46dc8b642564d91951a9fefba124f87';
			$sterm = 'Corona';
			$countval = file_get_contents('https://newsapi.org/v2/top-headlines?country=in&apiKey='.$newsapikey.'&q='.$sterm);
			$indiavalue = json_decode($countval, 1);
			$i = 0;
			foreach ($indiavalue['articles'] as $item) {	
			        $title = $item['title'];
					$url = $item['url'];
					$date = $item['publishedAt'];
					$reply = $title."\r\n".$url."\r\n".$date;
			    	file_get_contents($website."/sendmessage?chat_id=".$chat_id."&text=".nl2br($reply));
			    	$i++;
					if($i==5){ break; }
					}
		}
		else
		{
			$countval = file_get_contents('https://api.covid19india.org/v2/state_district_wise.json');
			$indiavalue = json_decode($countval, 1);
			//$final = $indiavalue['country'];
			foreach ($indiavalue as $item) {
			    if ($item['state'] == $text) {
			    	$i = 0;
			    	$len = count($item['districtData']);
			    	foreach ($item['districtData'] as $key) {
			    		$total += $key['confirmed'];
			    		$msg .= $key['district'].' : '.$key['confirmed']."\n";				    		
			    	}
			    	if($total !== '' || $total !== null)
			    	{
			    		$reply = "Total Positive Cases in ".$text." : ".$total;
			    		$reply2 = $msg;
			    	}
			    	
			    }
			    else
			    {
			    	foreach ($item['districtData'] as $key) {	 
			    	if($text == 'Ahmedabad') { $text = 'Ahmadabad'; $map = '<iframe src="https://www.google.com/maps/d/embed?mid=1KWpVysiwDSZD_gUdoiGkEQcaA33kc1zb" width="640" height="480"></iframe>'; }  		
			    		if ($key['district'] == $text) {
			    			$total = $key['confirmed'];	  			    			  		
			    		}
			    	}
			    	if(isset($total) AND !empty($total))
			    	{
			    		$reply = "Total Positive Cases in ".$text." : ".$total;
			    	}
			    	else
			    	{
			    		$reply = "Nothing Found";
			    	}    	
			    }	    
			}
			file_get_contents($website."/sendmessage?chat_id=".$chat_id."&text=".$reply);
			file_get_contents($website."/sendmessage?chat_id=".$chat_id."&text=".urlencode($reply2));
			file_get_contents($website."/sendmessage?chat_id=".$chat_id."&text=".urlencode($map));
		}
}
?>
