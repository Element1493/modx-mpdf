<?php
$corePath = $modx->getOption('mpdf.core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH).'components/mpdf/');
if (!$mpdf = $modx->getService('mpdf','mPDF', $corePath.'model/', array('corePath' => $corePath))){
    $modx->log(1,"Не удалось найти службу mPDF");
    return;
}

$mpdf->emailPDFLink($hook);

return true;