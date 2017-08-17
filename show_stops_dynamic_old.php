<?php
$jsword = "<script> var _route = '".$_GET['route']."';";
$jsword .= "var _citycode = '".$_GET['citycode']."';";
$jsword .= "var _direct = '".$_GET['direct']."';</script>";
echo $jsword;
?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
	<!--<link type="text/css" rel="Stylesheet" href="EX5.css" />-->
	<script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.2.1.min.js"></script>
	<script src="clock.js"></script>
    <meta charset="utf-8">
    <title>Simple markers</title>
	<style>
	td{
		font-size:150%;
	}
	</style>
  </head>
  <body>
  <h1 id='routecode'></h1>
  <!--<h1 id='routename'></h1>-->
  <div id='main'></div>
  <BR>
  <div>上次更新時間：<span id='remain'></span><span>秒</span></div>
   <!--地圖主體-->
   <!--<div id="map"></div>-->
   <!--附屬資訊_介紹欄位-->
   <script>
   var sys={
	   UpdateTime:'',
	   Timer:''
   };
	function wrap_td(str){
		return '<td>'+str+'</td>';
	}
	function wrap_tr(str){
		return '<tr>'+str+'</tr>';
	}
	function wrap_tb(str){
		return '<table border=1>'+str+'</table>';
	}
	function initial(){//ajax once data
	var RouteName = '';
		$.getJSON( "crawler/motc_bus_dynamic.php?route="+_route+"&direct="+_direct+"&citycode="+_citycode+"&func=0", function( data ) {
			console.log(data);
			for(key in data){
				RouteName = data[key]['RouteName']['Zh_tw'];
				}
				$('#routecode').html(RouteName);
			});
			
	}
	function touch(){//ajax 試探值的變化
		$.getJSON( "crawler/motc_bus_dynamic.php?route="+_route+"&direct="+_direct+"&citycode="+_citycode+"&func=1", function( data ) {
			console.log("crawler/motc_bus_dynamic.php?route="+_route+"&direct="+_direct+"&citycode="+_citycode+"&func=1");
			console.log(data);
			var UpdateTime = '';
			for(key in data){
				UpdateTime = data[key]['UpdateTime'];
			}
			console.log(Date.parse(UpdateTime)-sys.UpdateTime);
			if(sys.UpdateTime == ''){
				sys.UpdateTime = Date.parse(UpdateTime);
				//if(sys.Timer == ''){
				renew();
				sys.Timer = startTimer(sys.UpdateTime);
				
				//}
			}
			else if(sys.UpdateTime != Date.parse(UpdateTime)){
				sys.UpdateTime = Date.parse(UpdateTime);
				renew();
			}
			else{//no change
				;
			}
			});
	}
	function renew(){//ajax抓值
		var cnt = 0;
		var cnt_total = 0;
		
		var context = '';//stop name
		var context2 = '';//stop num
		var context22 = '';//stop bus num
		var context23 = '';//stop bus predict time
		var context3 = '';//total
		
		//

		$.getJSON( "crawler/motc_bus_dynamic.php?route="+_route+"&direct="+_direct+"&citycode="+_citycode+"", function( data ) {
			console.log(data);
			cnt = 0;
			var RouteName = '';
			var UpdateTime = '';
			for(key in data){
				//for(keysu in data[key]['Stops']){
					cnt_total++;
					cnt++;
					var StopName = data[key]['StopName']['Zh_tw'];
					var StopIndex = data[key]['StopSequence'];
					//var StopUID = data[key]['StopUID'];
					var PlateNumb = data[key]['PlateNumb'];
					var EstimateTime = data[key]['EstimateTime'];
					context = wrap_td(StopName);
					context2 = wrap_td(StopIndex);	
					//context22 = wrap_td(StopUID);	
					context22 = wrap_td(PlateNumb);	
					context23 = wrap_td(EstimateTime);	
					context3 += wrap_tr(context2+context+context22+context23);
				//}
			}		
				$('#main').html(wrap_tb(context3));
			});
	}
	////
	  $(function(){
		 initial();
		 touch();
		 setInterval(function(){ touch(); }, 3000);
		
	});
	
	  
    </script>
  </body>
</html>