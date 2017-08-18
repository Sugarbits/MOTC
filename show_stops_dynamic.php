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
   const timer_config = ['尚未發車','進站中','x分鐘','xx:oo'];
   //const timer_config_word = ['undefined','<=2分鐘','>2分鐘','>30分鐘'];
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
	function word_trans_time(t){
		if(t==undefined){
			return '尚未發車';
		}
		else if(t<=120){
			return '進站中';
		}/*else if(t>=1800){
			
		}*/
		else if(t>120){
			return Math.floor(t/60)+'分鐘';
		}
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
						var word = data[key]['Stops'][key2]['StopSequence'];
						LatLng = {lat : lat,lng :　lon};//google map latlng obj 
						//var sa = add_marker(map,LatLng,title);
						var sa = add_word_marker(map,LatLng,title,word);
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
				
			}).done(setTimeout(function(){ panto_muti_marker(markers); }, 1000));
			
			
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
	function renew_car(){//ajax抓值
	console.log('renew');
		$.getJSON( "crawler/motc_bus_dynamic.php?route="+_route+"&direct="+_direct+"&citycode="+_citycode+"&func=2", function( data ) {
		$( "#foobar_left" ).html('');
				if(firsttime == true){//第一次撈
					;//firsttime = false;
					}else{	
					initail();//清除上一次資料
				}
				$.each( data, function( key, val ) {
				//console.log(data);
				var car_no = data[key]['PlateNumb'];//頻繁使用車號
				if(car_no_filter.indexOf(car_no)==-1){//過濾不是本車隊的車號(放在car_no_filter)，
				//REF:http://www.victsao.com/blog/81-javascript/159-javascript-arr-indexof
					return;
				}
				//data[key]['PlateNumb']
				$( "#foobar_left" ).append( "<div class='"+btn_css_render(car_no)+"' data-val='"+car_no+"'>&nbsp;&nbsp;"+car_no+"</div>" );//按鈕生成,觸發自訂義
				//add_button(data[key]['PlateNumb']);
				/*
				console.log(data[key]);
				console.log(data[key]['BusPosition']['PositionLat']);
				console.log(data[key]['BusPosition']['PositionLon']);
				console.log(data[key]['GPSTime']);
				console.log(data[key]['PlateNumb']);
				*/
				//create gmap latlng obj
				tmpLatLng = {lat : data[key]['BusPosition']['PositionLat'],lng :　data[key]['BusPosition']['PositionLon']};//google map latlng obj 
				var tmptitle = {name:data[key]['PlateNumb'],time:data[key]['GPSTime']};//google map marker.title 
				var tmpcontent = "時速: " + data[key]['Speed'] +"km"+  '<br></h3>' + "車號" + car_no; 
				
				//給附屬資訊_內容
				//$('#speed').html(data[key]['UpdateTime']);
				$('#speed').html(data[key]['Speed']+"KM");
				$('#car_name').html(car_no);
				$('#latlng').html( data[key]['BusPosition']['PositionLat']+'<br>'+data[key]['BusPosition']['PositionLon']);
				var marker = add_marker(map,tmpLatLng,tmptitle,tmpcontent);
				var info = add_info(map,tmpLatLng,tmpcontent);
				
				marker.infowindow = new google.maps.InfoWindow(
				{
					content: tmpcontent
				});
				markers.push(marker);
				infos.push(info);
				//
			});//$.each END
			if(firsttime == true){//第一次撈
			//alert();
				panto_muti_marker(markers);//轉移
				firsttime = false;
				}else{	
					;//
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
					context22 = wrap_td((PlateNumb==-1)?'&nbsp;':PlateNumb);	
					context23 = wrap_td(word_trans_time(EstimateTime));	
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
	function add_word_marker(a_map,a_latlng,a_title,a_word){
			var marker = new google.maps.Marker({
				position: a_latlng,
				map: a_map,
				title : a_title['name'], /*+ "\n" + a_title['time']*/
				icon : 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld='+a_word+'|FF0000|000000'
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