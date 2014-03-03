
jQuery(document).ready(function($) {
	var metrics = $.parseJSON($('.storage.storage-profile').html());
	var ticks = [];
	var series = [];
	var values = [];
	var legendItemHeight = 18;
	for (var filename in metrics) {
		series.push({
			label: filename,
			neighborThreshold: 0
		});
		var dataset = [];
		for (var metricName in metrics[filename]) {
			var value = metrics[filename][metricName];
			if (-1 == ticks.indexOf(metricName)) {
				ticks.push(metricName);
			};
			maxHeight = Math.max(maxHeight, value);
			dataset.push([metricName, value]);
		};
		values.push(dataset);
	};
	var barMargin = 0;
	var barWidth = Math.round((800 / ticks.length) / series.length);
	var maxHeight =+ (300 + (series.length * legendItemHeight));
	var scale = ticks.length * values.length;
	var plotter = $('.graph').jqplot('graph', values, {
		width: ((barWidth + (barMargin * 2)) * scale) + 35,
		height: maxHeight,
		seriesColors: ['#002b36', '#586e75', '#839496', '#b58900', '#cb4b16', '#dc322f', '#d33682', '#6c71c4', '#268bd2', '#2aa198', '#859900', '#073642', '#657b83', '#93a1a1'],
		seriesDefaults:{
			renderer: $.jqplot.BarRenderer,
			rendererOptions: {
				//barDirection: 'horizontal',
				shadow: false,
				barWidth: barWidth,
				barMargin: barMargin,
				barPadding: 0
			},
			trendline: {
				show: true
			}
		},
		legend: {
			show: true,
			placement: 'insideGrid',
			location: 'ne'
		},
		series: series,
		grid: {
			background: '#FAFAFA'
		},
		cursor:{
			show: true,
			zoom: true,
			showTooltip: false,
			followMouse: true
		},
		axes: {
			yaxis: {
				padMin: 0
			},
			xaxis: {
				renderer: $.jqplot.CategoryAxisRenderer,
				tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
				tickOptions: {
					angle: -90,
					fontSize: '12pt'
				}
			}
		}
	});
});
