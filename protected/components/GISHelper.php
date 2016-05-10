<?php
class GISHelper
{
	/**
	 * @name 根据两点的经纬度求距离 
	 * @return 返回两点之间的距离，单位为米
	 * @author 张洪源
	 * @date 2014-04-01
	 */
	public static function getDis($beginLng,$beginLat,$endLng,$endLat)
	{
		$distance = sqrt(pow(($endLng-$beginLng)*M_PI/180*6371229*cos(($beginLat+$endLat)/2*M_PI/180),2)+pow(($endLat-$beginLat)*M_PI/180*6371229,2));//平均半径
		return intval($distance);
	}
}