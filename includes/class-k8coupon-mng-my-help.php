<?php
class K8coupon_Mng_My_Help
{
  static function getNumz( $str ){
 		preg_match_all('!\d+!', $str, $matches);
 		$matches_str = implode( '', $matches[0] );
 		return $matches_str;
 	}
}