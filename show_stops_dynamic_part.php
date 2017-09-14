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
	td{
		min-width:2em;
	}
	tr:nth-child(even) {background: #CCC}
	tr:nth-child(odd) {background: #FFF}
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
   var ssdp={
	   wrap_td:function(str){
		   return '<td>'+str+'</td>';
	   },
	   wrap_tr:function(str){
		   return '<tr>'+str+'</tr>';
	   },
	   wrap_tb:function(str){
		   return '<table border=1>'+str+'</table>';
	   },
	   word_trans_time:function(t){
		if(t==undefined){
			return '尚未發車';
		}
		else if(t<=120){
			return '進站中';
		}
		else if(t>120){
			return Math.floor(t/60)+'分鐘';
			}
		},
		initial:function(){//ajax once data
			var RouteName = '';
			$.getJSON( "crawler/motc_bus_dynamic.php?route="+_route+"&direct="+_direct+"&citycode="+_citycode+"&func=0", function( data ) {
			console.log(data);
			for(key in data){
				RouteName = data[key]['RouteName']['Zh_tw'];
				}
				$('#routecode').html(RouteName);
			});
		},
		touch:function(){//ajax 試探值的變化
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
   }
   var sys={
	   UpdateTime:'',
	   Timer:''
   };
	/*function wrap_td(str){
		return '<td>'+str+'</td>';
	}
	function wrap_tr(str){
		return '<tr>'+str+'</tr>';
	}
	function wrap_tb(str){
		return '<table border=1>'+str+'</table>';
	}
	function word_trans_time(t){
		if(t==undefined){
			return '尚未發車';
		}
		else if(t<=120){
			return '進站中';
		}/*else if(t>=1800){
			
		}
		else if(t>120){
			return Math.floor(t/60)+'分鐘';
		}
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
			
	}*/
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
				ssdp.renew();
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
					context = ssdp.wrap_td(StopName);
					context2 = ssdp.wrap_td(StopIndex);	
					//context22 = wrap_td(StopUID);	
					context22 = ssdp.wrap_td((PlateNumb==-1)?'&nbsp;':PlateNumb);	
					context23 = ssdp.wrap_td(ssdp.word_trans_time(EstimateTime));	
					context3 += ssdp.wrap_tr(context2+context+context22+context23);
				//}
			}		
				$('#main').html(ssdp.wrap_tb(context3));
			});
	}
	////
	  $(function(){
		 ssdp.initial();
		 ssdp.touch();
		 setInterval(function(){ ssdp.touch(); }, 3000);
		
	});
	
	  
    </script>
  </body>

</html>