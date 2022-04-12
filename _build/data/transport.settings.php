<?php

$settings = array();

$tmp = array(
	//main
	'pdfCSS' => array(	
        'xtype'    =>'textfield',
        'value'    =>'',
        'area'     =>'mpdf_main'
    ),
    'pdfPath' => array(	
        'xtype'    =>'textfield',
        'value'    =>'{assets_path}pdf/',
        'area'     =>'mpdf_main'
    ),
	'pdfUrl' => array(	
        'xtype'    =>'textfield',
        'value'    =>'{assets_url}pdf/',
        'area'     =>'mpdf_main'
    ),
//document
	'title' => array(	
        'xtype'    =>'textfield',
        'value'    =>'',
        'area'     =>'mpdf_document'
    ),
	'creator' => array(	
        'xtype'    =>'textfield',
        'value'    =>'',
        'area'     =>'mpdf_document'
    ),
	'author' => array(	
        'xtype'    =>'textfield',
        'value'    =>'',
        'area'     =>'mpdf_document'
    ),
//pdf
	'mode' => array(	
        'xtype'    =>'textfield',
        'value'    =>'utf-8',
        'area'     =>'mpdf_pdf'
    ),
	'format' => array(	
        'xtype'    =>'textfield',
        'value'    =>'A4',
        'area'     =>'mpdf_pdf'
    ),
	'orientation' => array(	
        'xtype'    =>'textfield',
        'value'    =>'P',
        'area'     =>'mpdf_pdf'
    ),
	'configuration' => array(	
        'xtype'    =>'textfield',
        'value'    =>'',
        'area'     =>'mpdf_pdf'
    ),
	'defaultFont' => array(	
        'xtype'    =>'textfield',
        'value'    =>'',
        'area'     =>'mpdf_pdf'
    ),
	'marginTop' => array(	
        'xtype'    =>'numberfield',
        'value'    =>'7',
        'area'     =>'mpdf_pdf'
    ),
	'marginBottom' => array(	
        'xtype'    =>'numberfield',
        'value'    =>'7',
        'area'     =>'mpdf_pdf'
    ),
	'marginLeft' => array(	
        'xtype'    =>'numberfield',
        'value'    =>'10',
        'area'     =>'mpdf_pdf'
    ),
	'marginRight' => array(	
        'xtype'    =>'numberfield',
        'value'    =>'10',
        'area'     =>'mpdf_pdf'
    ),
	'marginHeader' => array(	
        'xtype'    =>'numberfield',
        'value'    =>'10',
        'area'     =>'mpdf_pdf'
    ),
	'marginFooter' => array(	
        'xtype'    =>'numberfield',
        'value'    =>'10',
        'area'     =>'mpdf_pdf'
    ),
	'defaultFontSize' => array(	
        'xtype'    =>'numberfield',
        'value'    =>'8',
        'area'     =>'mpdf_pdf'
    ),
	'fontDirs' => array(	
        'xtype'    =>'textfield',
        'value'    =>'',
        'area'     =>'mpdf_pdf'
    ),
	'fontData' => array(	
        'xtype'    =>'textfield',
        'value'    =>'',
        'area'     =>'mpdf_pdf'
    ),
//protection
	'permissions' => array(	
        'xtype'    =>'textfield',
        'value'    =>'',
        'area'     =>'mpdf_protection'
    ),
	'userPassword' => array(	
        'xtype'    =>'text-password',
        'value'    =>'',
        'area'     =>'mpdf_protection'
    ),
	'ownerPassword' => array(	
        'xtype'    =>'text-password',
        'value'    =>'',
        'area'     =>'mpdf_protection'
    ),
//tv
	'processTVs' => array(	
        'xtype'    =>'combo-boolean',
        'value'    =>false,
        'area'     =>'mpdf_tv'
    ),
	'tvPrefix' => array(	
        'xtype'    =>'textfield',
        'value'    =>'',
        'area'     =>'mpdf_tv'
    ),
	'tvPDF' => array(	
        'xtype'    =>'textfield',
        'value'    =>'',
        'area'     =>'mpdf_tv'
    ),
	'tvPDFlive' => array(	
        'xtype'    =>'textfield',
        'value'    =>'',
        'area'     =>'mpdf_tv'
    ),
	'tvPDFoptions' => array(	
        'xtype'    =>'textfield',
        'value'    =>'',
        'area'     =>'mpdf_tv'
    ),
//tpl
	'tpl' => array(	
        'xtype'    =>'textfield',
        'value'    =>'pdf.html',
        'area'     =>'mpdf_tpl'
    ),
	'tplHeader' => array(	
        'xtype'    =>'textfield',
        'value'    =>'pdf.header',
        'area'     =>'mpdf_tpl'
    ),
	'tplFooter' => array(	
        'xtype'    =>'textfield',
        'value'    =>'pdf.footer',
        'area'     =>'mpdf_tpl'
    )
);

foreach ($tmp as $k => $v) {
	/* @var modSystemSetting $setting */
	$setting = $modx->newObject('modSystemSetting');
	$setting->fromArray(array_merge(
		array(
			'key' => PKG_NAME_LOWER.'.' . $k,
			'namespace' => PKG_NAME_LOWER,
		), $v
	), '', true, true);

	$settings[] = $setting;
}

unset($tmp);
return $settings;
