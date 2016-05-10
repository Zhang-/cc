  $(function() {  
    //走势图加外边框
	 Highcharts.theme = {
   
   chart: {
    backgroundColor: {
         linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
         stops: [
            [0, 'rgb(255, 255, 255)'],
            [1, 'rgb(240, 240, 255)']
         ]
      },
      borderWidth: 1,
      plotBackgroundColor: 'rgba(254 , 252, 245, .9)', 
      plotShadow: false,
      plotBorderWidth: 1
	}
   }
 // Apply the theme
var highchartsOptions = Highcharts.setOptions(Highcharts.theme);

	var chart = new Highcharts.StockChart({  
        chart: {  
            renderTo: 'container1'//指向的div的id属性  
        },  
        xAxis: {  
            tickPixelInterval: 200,//x轴上的间隔  
            type: 'datetime', //定义x轴上日期的显示格式  
            labels: {  
				formatter: function() {  
					var vDate=new Date(this.value);  
					//alert(this.value);  
					var day=vDate.getDate();
					var moth=vDate.getMonth();
					var year=vDate.getFullYear();
					return year+"-"+(moth+1)+'-'+day;  
				},  
				align: 'center'  
			}  
        },  
		credits: { 
            enabled: false   //不显示LOGO 
        }, 
        yAxis : {    
              title: {    
                  text: '用户数'  //y轴上的标题  
              }    
         },    
        tooltip: {  
			shared :true,
            //xDateFormat: "%Y年%m月",//鼠标移动到趋势线上时显示的日期格式  
			formatter: function() {  
					var vDate=new Date(this.x);    
					var day=vDate.getDate();
					var moth=vDate.getMonth();
					var year=vDate.getFullYear();
					return '<p style="color:blue">时间：'+year+"-"+(moth+1)+'-'+day+'</p><br>用户数：<p style="color:red">'+this.y+'人</p>';  
				}
        },  
        rangeSelector: {  
			buttons: [{
				type: 'month',
				count: 1,
				text: '1月'
			}, {
				type: 'month',
				count: 3,
				text: '3月'
			}, {
				type: 'month',
				count: 6,
				text: '6月'
			}, {
				type: 'year',
				count: 1,
				text: '1年'
			}, {
				type: 'all',
				text: '全部'
			}],
			inputEnabled: false,
            selected: 0//表示以上定义button的index,从0开始  
        },  
		navigator :{
		 xAxis: {  
            tickPixelInterval: 400,//x轴上的间隔  
            type: 'datetime', //定义x轴上日期的显示格式  
            labels: {  
				formatter: function() {  
					var vDate=new Date(this.value);
					var day=vDate.getDate();
					var moth=vDate.getMonth();
					var year=vDate.getFullYear();
					return year+"-"+(moth+1)+"-"+day;  
				},  
				align: 'left'  
			}  
        }
		
		},
        series: [{  
            name: '用户数(人)',//鼠标移到趋势线上时显示的属性名  
            data: p4,//属性值  
            marker : {  
                  enabled : true,  
                  radius : 3  
              },  
            shadow : true  
        }]  
    });  
});  
