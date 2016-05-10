$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container1',
                type: 'spline',
				height: 300
				
				
            },
            title: {
                text: ''
            },
			credits: {
            text: ''
            
        },
            xAxis: {
				lineColor: '#868686',
            	lineWidth: 2,
                categories: p3
            },
            yAxis: {
				  lineColor: '#868686',
            	lineWidth: 2,
                title: {
                    text: '用户数（人）'
                },
                labels: {
                    formatter: function() {
                        return this.value ;
                    }
                }
            },
            tooltip: {
                crosshairs: true,
                shared: true
            },
            plotOptions: {
                spline: {
                    marker: {
                        radius: 3,
                        lineColor: '#666666',
                        lineWidth: 1
                    }
                },
				 series: {
                	events:{
                    legendItemClick: function (event) {                                    
                        return false;
                        }
                }
              }
            },
            series: [{
			name: '当前用户数',
                data: p4
            }]
        });
    });
    
});