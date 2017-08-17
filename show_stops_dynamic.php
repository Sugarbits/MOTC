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
	#map{
		z-index: 1;
		top:0%;
		//position:absolute;
		float:right;
		width:70%;
		height:100vh;
		background-color:black;
	}
	#main{
		z-index: 1;
		top:0%;
		//position:absolute;
		float:right;
		width:30%;
		height:100vh;
	}
	tr:nth-child(even) {background: #CCC}
	tr:nth-child(odd) {background: #FFF}
	</style>
  </head>
  <body>
  <h1 id='routecode'></h1>
  <!--<h1 id='routename'></h1>-->
  <div id='main'></div>
  <!--地圖主體-->
  <div id="map"></div>
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
   var markers =[];
   var map;
   var now_icon_url = 'pic/busicon.png';
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
			//console.log(data);
			for(key in data){
				if(key == _direct){
					RouteName = data[key]['RouteName']['Zh_tw'];
					//console.log(data[key]['Stops']);
					for(key2 in data[key]['Stops']){
						//console.log('initial');
						//console.log(data[key]['Stops'][key2]);
						var lat = data[key]['Stops'][key2]['StopPosition']['PositionLat'];
						var lon = data[key]['Stops'][key2]['StopPosition']['PositionLon'];
						var title = {'name':data[key]['Stops'][key2]['StopName']['Zh_tw']};
						LatLng = {lat : lat,lng :　lon};//google map latlng obj 
						var sa = add_marker(map,LatLng,title);
						markers.push(sa);
						//console.log(markers);
						//console.log('initial');
						/*if(key2 == 'Stops'){
							console.log(data[key][key2]['StopPosition']);
						}*/
					}
				}
			}
				$('#routecode').html(RouteName);
				
			}).done(setInterval(function(){ panto_muti_marker(markers); }, 1000));
			
			
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
	function google_map_initial(){
		var myLatLng = {lat: 23.7, lng: 121.4};
		map = new google.maps.Map(document.getElementById('map'), {
			zoom: 15,
			center: myLatLng
        });
		// Create the DIV to hold the control and call the CenterControl()
        // constructor passing in this DIV.
		//renew();
      }
	function add_marker(a_map,a_latlng,a_title){
			var marker = new google.maps.Marker({
				position: a_latlng,
				map: a_map,
				title : a_title['name'] /*+ "\n" + a_title['time']*/
				//icon : (now_icon_url)
			});
			 
			//map.panTo(tmpLatLng);
			//bindInfoWindow(marker, map, infowindow, '<b>'+places[p].name + "</b><br>" + places[p].geo_name);
			// not currently used but good to keep track of markers
			//markers[data[key]['PlateNumb']].push(marker);
			//http://maps.google.com/mapfiles/ms/icons/blue-dot.png
			//console.log(markers);
			return marker;
	}
	function panto_muti_marker(pmarkers){
		console.log('panto_muti_marker');
		console.log(pmarkers);
		console.log('panto_muti_marker');
		if(pmarkers.length != 0){
			var bounds = new google.maps.LatLngBounds();
			for (var i = 0; i < pmarkers.length; i++) {
				var point = new google.maps.LatLng(
				parseFloat(pmarkers[i].getPosition().lat()),
				parseFloat(pmarkers[i].getPosition().lng()));
				// add each marker's location to the bounds
				bounds.extend(point);
			}
		map.fitBounds(bounds);
		}else{
			alert('沒有公車！');
		}
	}
	  $(function(){
		 $(window).on('load',function(){
			google_map_initial();
			initial();
			touch();
			setInterval(function(){ touch(); }, 3000);
		});
		
		
	});
	
	  
    </script>
	 <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA0bdKmBEMTJH7qsTjjG_1rfteVrNXzxQk"></script>
  </body>
</html>