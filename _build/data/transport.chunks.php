<?php

$chunks = array();

$tmp = array(
	'pdf.html' => array(
        'file' => 'pdfhtml',
        'description' => '',
    ),
    'pdf.header' => array(
        'file' => 'pdfheader',
        'description' => '',
    ),
	'pdf.footer' => array(
        'file' => 'pdffooter',
        'description' => '',
    )
);

// Save chunks for setup options
$build_chunks = array();

foreach ($tmp as $k => $v) {
	/* @avr modChunk $chunk */
	$chunk = $modx->newObject('modChunk');
	$chunk->fromArray(array(
		'id' => 0,
		'name' => $k,
		'description' => @$v['description'],
		'snippet' => file_get_contents($sources['source_core'] . '/elements/chunks/chunk.' . $v['file'] . '.tpl'),
		'static' => BUILD_CHUNK_STATIC,
		'source' => 1,
		'static_file' => 'core/components/' . PKG_NAME_LOWER . '/elements/chunks/chunk.' . $v['file'] . '.tpl',
	), '', true, true);

	$chunks[] = $chunk;

	$build_chunks[$k] = file_get_contents($sources['source_core'] . '/elements/chunks/chunk.' . $v['file'] . '.tpl');
}

unset($tmp);
return $chunks;