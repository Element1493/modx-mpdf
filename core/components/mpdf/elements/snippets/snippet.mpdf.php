<?php
$corePath = $modx->getOption('mpdf.core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH).'components/mpdf/');
if (!$mpdf = $modx->getService('mpdf','mPDF', $corePath.'model/', array('corePath' => $corePath))){
    $modx->log(1,"Не удалось найти службу mPDF");
    return;
}
if($input){
    return $mpdf->linkPDF($input);
}else{
    if($live){
        header('Content-Type: application/pdf');
        header('Content-Disposition:inline;filename='.$modx->resource->get('alias').'.pdf');
        echo $mpdf->prerenderPDF($scriptProperties?:$modx->resource);
        exit;
    }else{
        return $mpdf->savePDF($scriptProperties?:$modx->resource);
    }
}