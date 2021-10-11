<?php
/*
 * Cron для сниппета mPDFHook, 
 */
$day = 14;
$dir = $_SERVER['DOCUMENT_ROOT'] . '/assets/pdf/form/';

if($handle = opendir($dir)){
	while (false !== ($file = readdir($handle))) { 
		if ($file != "." && $file != ".."){
			$date = str_replace('.pdf','',substr($file, strrpos($file, '-') + 1));
			$date = $date + ($day * 24 * 60 * 60);
			if(time() > $date){
				unlink($dir.$file);
			}
		} 
	}
}
?>