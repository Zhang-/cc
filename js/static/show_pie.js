$(function () {
				var chart;
				$(document).ready(function() {			
					var colors = Highcharts.getOptions().colors,
						categories = p1,
						name = 'Browser brands',
						data = p2;
					// Build the data arrays
					var phoneBrandData = [];
					var phoneModelData = [];
					for (var i = 0; i < data.length; i++) {	
						// add browser data
						phoneBrandData.push({
							name: categories[i],
							y: data[i].y,
							color: colors[data[i].color]
						});
						// add version data
						for (var j = 0; j < data[i].drilldown.data.length; j++) {
							var brightness = 0.2 - (j / data[i].drilldown.data.length) / 5 ;
							phoneModelData.push({
								name: data[i].drilldown.categories[j],
								y: data[i].drilldown.data[j],
								color: Highcharts.Color(colors[data[i].color]).brighten(brightness).get()
							});
						}
					}
				
					// Create the chart
					chart = new Highcharts.Chart({
						chart: {
							renderTo: 'container',
							type: 'pie',
							height: 350
							
						},
						credits: {
						text: ''
						
					},
						title: {
							text: ''
						},
						yAxis: {
							title: {
								text: ''
							}
						},
						plotOptions: {
							
							pie: {
								shadow: false
							}
						},
						tooltip: {
							valueSuffix: '%'
						},
						series: [{
							name: '占总手机数量的',
							data: phoneBrandData,
							size: '60%',
							dataLabels: {
								formatter: function() {
									return this.y > 5 ? this.point.name : null;
								},
								color: 'white',
								distance: -30
							}
						}, {
							name: '占总手机数量的',
							data: phoneModelData,
							innerSize: '60%',
							dataLabels: {
								formatter: function() {
									// display only if larger than 1
									return this.y > 2.4 ? '<b>'+ this.point.name +':</b> '+ this.y +'%'  : null;
								}
							}
						}]
					});
				});
				
			});