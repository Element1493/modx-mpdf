<?php
$corePath = $modx->getOption('mpdf.core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH).'components/mpdf/');
if (!$mpdf = $modx->getService('mpdf','mPDF', $corePath.'model/', array('corePath' => $corePath))){
    $modx->log(1,"Не удалось найти службу mPDF");
    return;
}

switch ($modx->event->name) {
    case 'OnDocFormSave':
		$mpdf->savePDF($resource);
    break;
    case 'OnWebPagePrerender':
        $mpdf->prerenderPDF($modx->resource);
    break;
    case 'pdoToolsOnFenomInit':
        $fenom->addModifier('mPDFLink', function ($input) use ($mpdf){
            if(is_numeric($input)){
                return $mpdf->linkPDF($input);
            }
        });
    break;
}