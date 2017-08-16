<?php
$jsword = "<script> var _citycode = '".$_GET['citycode']."';";
$jsword .= "</script>";
echo $jsword;
?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
	<!--<link type="text/css" rel="Stylesheet" href="EX5.css" />-->
	<script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.2.1.min.js"></script>
    <meta charset="utf-8">
    <title>Simple markers</title>
	<style>
	td{
		font-size:150%;
	}
	</style>
  </head>
  <body>
  <div id='main'></div>
   <!--地圖主體-->
   <!--<div id="map"></div>-->
   <!--附屬資訊_介紹欄位-->
   <script>
	function wrap_td(str){
		return '<td>'+str+'</td>';
	}
	function wrap_tr(str){
		return '<tr>'+str+'</tr>';
	}
	function wrap_tb(str){
		return '<table border=1>'+str+'</table>';
	}
	function renew(){//ajax抓值
		var cnt = 0;
		var cnt_total = 0;
		
		var context = '';//r name
		var context2 = '';//direct 0
		var context3 = '';//direct 1
		//
		$.getJSON( "crawler/motc_bus_route.php?index=0&citycode="+_citycode+"", function( data ) {
			console.log(data);
			cnt = 0;
			for(key in data){
				cnt_total++;
				cnt++;
				var RouteName = data[key]['RouteName']['Zh_tw'];
				var OperatorIDs = data[key]['OperatorIDs'][0];
				var dirct0 = data[key]['SubRoutes'][0]['Headsign'];
				var dirct1 = data[key]['SubRoutes'][1]['Headsign'];
				console.log(RouteName+'|'+cnt);
				context += wrap_td(RouteName+'<br>');
				context2 += wrap_td("<a href=show_stops_dynamic.php?route="+RouteName+"&direct="+0+"&citycode="+_citycode+">"+dirct0+"</a>");
				context3 += wrap_td("<a href=show_stops_dynamic.php?route="+RouteName+"&direct="+1+"&citycode="+_citycode+">"+dirct1+"</a>");
				}
				$('#main').html(wrap_tb(wrap_tr(context)+wrap_tr(context2)+wrap_tr(context3)));
			});
	}
	////
	  $(function(){
		 renew();
	});
	
	  
    </script>
  </body>
</html>