/* layer_style */
var layer_style = OpenLayers.Util.extend({}, OpenLayers.Feature.Vector.style['default']);
            layer_style.fillOpacity = 1;
            layer_style.graphicOpacity = 1;
			
/*
 *  blue style
 */
var style_blue = OpenLayers.Util.extend({}, layer_style);
style_blue.strokeColor = "#0009ff";
style_blue.fillColor = "#0009ff";
style_blue.graphicName = "star";
style_blue.pointRadius = 10;
style_blue.strokeWidth = 0.5;
style_blue.rotation = 45;
style_blue.strokeLinecap = "butt";

/*
 *  cyan style
 */
var style_cyan = OpenLayers.Util.extend({}, layer_style);
style_cyan.strokeColor = "#059ef7";
style_cyan.fillColor = "#059ef7";
style_cyan.graphicName = "star";
style_cyan.pointRadius = 10;
style_cyan.strokeWidth = 0.5;
style_cyan.rotation = 45;
style_cyan.strokeLinecap = "butt";

/*
 *  brilliantBlue style
 */
var style_brilliantBlue = OpenLayers.Util.extend({}, layer_style);
style_brilliantBlue.strokeColor = "#1ffdf6";
style_brilliantBlue.fillColor = "#1ffdf6";
style_brilliantBlue.graphicName = "star";
style_brilliantBlue.pointRadius = 10;
style_brilliantBlue.strokeWidth = 0.5;
style_brilliantBlue.rotation = 45;
style_brilliantBlue.strokeLinecap = "butt";

/*
 *  green style
 */
var style_green = OpenLayers.Util.extend({}, layer_style);
style_green.strokeColor = "#1cf411";
style_green.fillColor = "#1cf411";
style_green.graphicName = "star";
style_green.pointRadius = 10;
style_green.strokeWidth = 2;
style_green.rotation = 45;
style_green.strokeLinecap = "butt";

/*
 *  deep_blue style
 */
var style_light_green = OpenLayers.Util.extend({}, layer_style);
style_light_green.strokeColor = "#0009ff";
style_light_green.fillColor = "#0009ff";
style_light_green.graphicName = "star";
style_light_green.pointRadius = 10;
style_light_green.strokeWidth = 2;
style_light_green.rotation = 45;
style_light_green.strokeLinecap = "square";

/*
 *  orange style
 */
var style_orange = OpenLayers.Util.extend({}, layer_style);
style_orange.strokeColor = "#f1a41e";
style_orange.fillColor = "#f1a41e";
style_orange.graphicName = "star";
style_orange.pointRadius = 10;
style_orange.strokeWidth = 0.5;
style_orange.rotation = 45;
style_orange.strokeLinecap = "butt";


/*
 *  yellow style
 */
var style_yellow = OpenLayers.Util.extend({}, layer_style);
style_yellow.strokeColor = "#f1ea1e";
style_yellow.fillColor = "#f1ea1e";
style_yellow.graphicName = "star";
style_yellow.pointRadius = 10;
style_yellow.strokeWidth = 0.5;
style_yellow.rotation = 45;
style_yellow.strokeLinecap = "butt";


/*
 *  purple style
 */
var style_purple = OpenLayers.Util.extend({}, layer_style);
style_purple.strokeColor = "#b153f3";
style_purple.fillColor = "#b153f3";
style_purple.graphicName = "star";
style_purple.pointRadius = 10;
style_purple.strokeWidth = 0.5;
style_purple.rotation = 45;
style_purple.strokeLinecap = "butt";

/*
 *  pink style
 */
var style_pink = OpenLayers.Util.extend({}, layer_style);
style_pink.strokeColor = "#fd1fe3";
style_pink.fillColor = "#fd1fe3";
style_pink.graphicName = "star";
style_pink.pointRadius = 10;
style_pink.strokeWidth = 0.5;
style_pink.rotation = 45;
style_pink.strokeLinecap = "butt";


/*
 *  red style
 */
var style_red = OpenLayers.Util.extend({}, layer_style);
style_red.strokeColor = "#fd0606";
style_red.fillColor = "#fd0606";
style_red.graphicName = "star";
style_red.pointRadius = 10;
style_red.strokeWidth = 0.5;
style_red.rotation = 45;
style_red.strokeLinecap = "butt";

function style_model(color)
{
	var style_model;
	switch(color)
	{
		case 'red': style_model = style_red; break;
		case 'pink': style_model = style_pink; break;
		case 'purple': style_model = style_purple; break;
		case 'yellow': style_model = style_yellow; break;
		case 'orange': style_model = style_orange; break;
		case 'green': style_model = style_green; break;
		case 'brilliantBlue': style_model = style_brilliantBlue; break;
		case 'cyan': style_model = style_cyan; break;
		case 'blue': style_model = style_blue; break;
		case 'deep_blue': style_model = style_light_green; break;
	}
	return style_model;
}


//line style
var line_layer_style = OpenLayers.Util.extend({}, OpenLayers.Feature.Vector.style['default']);
            line_layer_style.fillOpacity = 0.2;
			line_layer_style.strokeWidth = 1.5;
			line_layer_style.strokeOpacity = 1;
/*
 *  line black style
 */
var line_style_black = OpenLayers.Util.extend({}, line_layer_style);
line_style_black.strokeColor = "#000000";
line_style_black.fillColor = "#000000";

/*
 *  line red style
 */
var line_style_red = OpenLayers.Util.extend({}, line_layer_style);
line_style_red.strokeColor = "#CC0033";
line_style_red.fillColor = "#CC0033";


//stylemap
var ppStyleMap = new OpenLayers.StyleMap({'default':{
		strokeColor: "${favorColor}",
		strokeOpacity: 1,
		strokeWidth: 1.5,
		fillColor: "${favorColor}",
		fillOpacity: 0.2,
		label : "${lac},${cellid}",
		fontColor: "${favorColor}",
		fontSize: "12px",
		fontFamily: "Courier New, monospace",
		fontWeight: "bold",
		labelOutlineColor: "white",
		labelOutlineWidth: 3,
		labelAlign: "cm",
		labelXOffset: "${xOffset}",
		labelYOffset: "${yOffset}"
}});
