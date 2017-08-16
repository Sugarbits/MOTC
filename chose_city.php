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
  </body>
  </html>
<script>
var CityName = ['臺北市','新北市','桃園市','臺中市','臺南市','高雄市','基隆市','新竹市','新竹縣','苗栗縣','彰化縣','南投縣','雲林縣','嘉義縣','嘉義市','屏東縣','宜蘭縣','花蓮縣','臺東縣','金門縣','澎湖縣','連江縣','新北市(雙北雲)'];
var CityCode = ['Taipei','NewTaipei','Taoyuan','Taichung','Tainan','Kaohsiung','Keelung','Hsinchu','HsinchuCounty','MiaoliCounty','ChanghuaCounty','NantouCounty','YunlinCounty','ChiayiCounty','Chiayi','PingtungCounty','YilanCounty','HualienCounty','TaitungCounty','KinmenCounty','PenghuCounty','LienchiangCounty','TaipeiCloud'];
var UsageType = [-1,-1,-1,-1,-2,-1,-2,-1,-1,-3,-1,-3,-1,0,-4,0,-1,0,-1,-1,-3,-1,-1];
var context = '';//
function wrap_td(str){
		return '<td>'+str+'</td>';
	}
	function wrap_tr(str){
		return '<tr>'+str+'</tr>';
	}
	function wrap_tb(str){
		return '<table border=1>'+str+'</table>';
	}
function initial(){
	for(key in CityName){
		if(UsageType[key] >= 0){
			context += wrap_td("<a href='chose_route.php?citycode="+CityCode[key]+"'>"+CityName[key]+"</a>");
		}else{
			//context += wrap_td("<span>"+CityName[key]+"</span>");
			;
		}
	}
	$('#main').html(wrap_tb(wrap_tr(context)));
}
initial();
</script>