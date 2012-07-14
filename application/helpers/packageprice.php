<?php

class Helper_PackagePrice {
	public static function calculate($iWeight, $iWidth, $iHeight, $iDepth)
	{
		$iPackagePrice = 9;
		if ($iWeight > 10) {
			// dopłata za ciężar
			$iPackagePrice += 12;
		}
			
		if ($iWeight > 50) {
			// dopłata za mega ciężar
			$iPackagePrice += 32;
		}
			
		if ($iWidth > 100
				|| $iHeight > 100
				|| $iDepth > 100
		) {
			// dopłata za przekroczenie metra na jakimś rozmiarze
			$iPackagePrice += 8;
		}
		
		return $iPackagePrice;
	} 
}