<?php
/*
* Библиотека: mPDF [https://mpdf.github.io/]
* Автор плагина: Сергей Зверев <element1493@yandex.ru>
*/
class mPDF {
	/** @var modX $modx */
	public $modx;
	/** @var string $namespace */
	public $namespace = 'mpdf';
	/** @var array $options*/
	public $options = array();
	/** @var mPDF $mpdf*/
	public $mpdf;
	
	/** Конструктор плагина mPDF
	 * @param modX $modx
     * @param array $options
     */
	public function __construct(modX & $modx, array $options = array()){
		
		$this->modx	 = &$modx;
		
		$core_path   = $this->getOption('core_path', $options, $this->modx->getOption('core_path') . 'components/mpdf/');
		$assets_path = $this->getOption('assets_path', $options, $this->modx->getOption('assets_path') . 'components/' . $this->namespace . '/');
        $assets_url  = $this->getOption('assets_url', $options, $this->modx->getOption('assets_url') . 'components/' . $this->namespace . '/');
		$base_path   = $this->getOption('base_path', $options, $this->modx->getOption('base_path'));
		$http_host   = $this->getOption('http_host', $options, $this->modx->getOption('http_host'));
		$pdf_path 	 = $this->getOption('pdfPath', $options, $this->modx->getOption('assets_path') . 'pdf/');
		$pdf_url 	 = $this->getOption('pdfUrl', $options, $this->modx->getOption('assets_url') . 'pdf/');
		$pdf_css 	 = $this->getOption('pdfCSS', $options, false);
		
		$this->options = array(
			'nameoptions'	=> $this->namespace,
			'corePath'		=> $core_path,
			'vendorPath' 	=> $core_path.'vendor/',
			'modelPath' 	=> $core_path.'model/',
			'assetsPath' 	=> $assets_path,
            'assetsUrl' 	=> $assets_url,
			'basePath' 		=> $base_path,
            'httpHost' 		=> $http_host,
			'pdfPath' 		=> $pdf_path,
            'pdfUrl' 		=> $pdf_url,
			'pdfCSS' 		=> $pdf_css
		);
	}
	
	/** Парсер
	 * @param string $value
	*/
	public function parser($value){
        $maxIterations = (integer)$this->modx->getOption('parser_max_iterations', null, 10);
        $this->modx->getParser()->processElementTags('', $value, false, false, '[[', ']]', array(), $maxIterations);
        $this->modx->getParser()->processElementTags('', $value, true, true, '[[', ']]', array(), $maxIterations);
        return $value;
    }
	
	/** Параметр локальной конфигурации или системных настроек
	 * @param string $key
     * @param array $options
     * @param mixed $default
	*/
	public function getOption($key, $options = array(), $default = null){
        $option = $default;
        if (!empty($key) && is_string($key)) {
            if ($options != null && array_key_exists($key, $options)) {
                $option = $options[$key];
            } elseif (array_key_exists($key, $this->options)) {
                $option = $this->options[$key];
            } elseif (array_key_exists("{$this->namespace}.{$key}", $this->modx->config)) {
				$option = $this->modx->getOption("{$this->namespace}.{$key}");
            }
        }
        return $option;
    }
	
	/**
     * Получить массив TV
     * @param numeric $parent
	 * @param modResource $resource
	 * @param string $tvPrefix
     */
	public function getTemplateVars($resource,$processTVs = false,$tvPrefix = ''){
		$id	 	= $resource->get('id');
		$tvs 	= $resource->getTemplateVars();
		$result = array();
        foreach ($tvs as $tv) {
            $result[$tvPrefix . $tv->get('name')] = ($processTVs) ? $tv->renderOutput($id) : $tv->getValue($id);
        }
		return $result;
	}
	
	/**
     * Получить родительский путь к ресурсу MODX
     * @param numeric $parent
     */
	public function getParentPath($parent){
		if(is_numeric($parent)){
			return $parent ? preg_replace('#(\.[^./]*)$#', '', rtrim($this->modx->makeUrl($parent), $this->modx->getOption('container_suffix'))) . '/' : '';
		}
	}
	
	/** Инициализация библиотеки mPDF 
	 * @param array $options
	*/
	public function initPDF($options = array()){

        require $this->options['vendorPath'].'autoload.php';

		$defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
		$fontDirs = $defaultConfig['fontDir'];

		$defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
		$fontData = $defaultFontConfig['fontdata'];

		// Конфигурации
		$config = array(
			'mode'             => $this->getOption('mode', $options, 'utf-8'),                // Кодировка; [по умолчанию: UTF-8]
			'format'           => $this->getOption('format', $options, 'A4'),                 // Формат документа; [по умолчанию: A4]
			'orientation'      => $this->getOption('orientation', $options, 'P'),             // Ориентация; [по умолчанию: P] (P - вертикальная ориентация, L - горизонтальная ориентация)
			'default_font_size'=> intval($this->getOption('defaultFontSize', $options, 8)),   // Размер шрифта; (pt)[по умолчанию: 8]
			'default_font'     => $this->getOption('defaultFont', $options, ''),              // Семейство шрифта;
			'margin_left'      => intval($this->getOption('marginLeft', $options, 10)),       // Отступ от левого края;
			'margin_right'     => intval($this->getOption('marginRight', $options, 10)),      // Отступ от правого края;
			'margin_top'       => intval($this->getOption('marginTop', $options, 7)),         // Отступ от верхнего края;
			'margin_bottom'    => intval($this->getOption('marginBottom', $options, 7)),      // Отступ от нижнего края;
			'margin_header'    => intval($this->getOption('marginHeader', $options, 10)),     // Отступ Header от верхнего края;
			'margin_footer'    => intval($this->getOption('marginFooter', $options, 10))      // Отступ Footer от нижнего края;
		);	

		// Каталог шрифтов
		if($options_fontDirs = $this->getOption('fontDirs', $options)){
			$config['fontDir'] = array_merge($fontData, explode(",",$options_fontDirs));
		}
		// Имена шрифтов
		if($options_fontData = $this->getOption('fontData', $options)){
			$config['fontdata'] = $fontData + json_decode($options_fontData,true);
		}
		// Дополнительные конфигурации
		if($options_config = $this->getOption('configuration', $options)){
			$config = array_merge($config, json_decode($options_config,true));
		}
		
		$this->mpdf = new \Mpdf\Mpdf($config);
    }
		
	/** Генерирует HTML в PDF.
	 * @param array $options
	*/
	public function createPDF($options = array()){
		if(empty($options)) return;

		// Создание папок
		if (!@is_dir($this->options['pdfPath'])) {
            if (!$this->modx->cacheManager->writeTree($this->options['pdfPath'])) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, 'mPDF - Не удалось создать абсолютный путь к PDF: ' .  $this->options['pdfPath'], '', $this->namespace);
                return;
            };
        }
		$alias_path = $options['alias_path']?:null;
        if (!is_null($alias_path) && !@is_dir( $this->options['pdfPath'].$alias_path)) {
            if (!$this->modx->cacheManager->writeTree($this->options['pdfPath'].$alias_path)) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, 'mPDF - Не удалось создать относительный путь к PDF: ' . $this->options['pdfPath'].$alias_path, '', $this->namespace);
                return;
            };
        }
		
		try {
			//Информация
			$this->initPDF($options);
			$this->mpdf->debug = true;
			$this->mpdf->SetTitle($this->getOption('title', $options, ''));   									// Заголовок PDF
			$this->mpdf->SetAuthor($this->getOption('author', $options, $this->modx->getOption('site_name')));  // Автор PDF
			$this->mpdf->SetCreator($this->getOption('creator', $options, $this->modx->getOption('site_url'))); // Создатель PDF
	
			//Шифрует и устанавливает права доступа к PDF-документу.						
			$permissions 	= json_decode($this->getOption('permissions', $options, ''), true); //Массив: ['print','modify','annot-forms','fill-forms','extract','assemble','print-highres']
			$user_password 	= $this->getOption('userPassword', $options);                     	//Пароль пользователя
			$owner_password = $this->getOption('ownerPassword', $options);                  	//Пароль администратора
			if ($user_password || $ownerPassword) {
				$this->mpdf->SetProtection($permissions, $user_password, $owner_password, 128);
			}
						
			//Чанки
			$placeholder= $options['placeholder']?:array();
			$tplHeader	= $this->getOption('tplHeader', $options, false);
			$tplHtml	= $this->getOption('tpl', $options, false);
			$tplFooter	= $this->getOption('tplFooter', $options, false);
			if($pdo = $this->modx->getService('pdoFetch')){
				$header = $this->parser($pdo->getChunk($tplHeader,$placeholder));
				$html 	= $this->parser($pdo->getChunk($tplHtml,$placeholder));
				$footer = $this->parser($pdo->getChunk($tplFooter,$placeholder));
			}else{
				$header = $this->parser($this->modx->getChunk($tplHeader,$placeholder));
				$html 	= $this->parser($this->modx->getChunk($tplHtml,$placeholder));
				$footer = $this->parser($this->modx->getChunk($tplFooter,$placeholder));
			}
			if($tplHeader) $this->mpdf->SetHTMLHeader($header);
			if($tplFooter) $this->mpdf->SetHTMLFooter($footer);
					
			// Дополнительные методы
            $methods = json_decode($this->getOption('methods', $options), true);
            $methods = (is_array($methods)) ? $methods : array();
            foreach ($methods as $key => $value) {
				$value = (is_array($value)) ? $value : json_decode($value, true);
				if ($value && method_exists($this->mpdf, $key)) {
                    call_user_func_array(array($this->mpdf, $key), $value);
                }
            }
			
			//Генерируем PDF
			if($this->options['pdfCSS']){
				$css = $this->options['basePath'].$this->options['pdfCSS'];
				if(file_exists($css)){
					$this->mpdf->WriteHTML(file_get_contents($css), 1);
					$this->mpdf->WriteHTML($html, 2);
				}else{
					$this->modx->log(modX::LOG_LEVEL_ERROR, 'mPDF - Не удалось найти CSS-файл', '', 'mPDF');
					return;
				}
			}else{
				$this->mpdf->WriteHTML($html);
			}
			
			//Выводим PDF
			if (!is_null($alias_path)) {
				$alias = $options['alias']?:'';
                return $this->mpdf->Output( $this->options['pdfPath'].$alias_path.$alias.'.pdf', 'F');
            } else {
                return $this->mpdf->Output();
            }
				
		} catch (\Mpdf\MpdfException $e) { 
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'mPDF - Не удалось создать PDF-файл: ' . $e->getMessage(), '', 'mPDF');
            return;
        }
	}
	/** Создаёт PDF и сохраняет в указанную папку.
     * @param modResource $resource
	 * @param array $options
     */
	public function savePDF($resource,$options = array()){
		if ($tv = $this->getOption('tvPDF', null, false)){
			if($resource->getTVValue($tv)){
				$this->modx->switchContext($resource->context_key);
				
				$processTVs = $this->getOption('processTVs', null, false);
				$tvPrefix = $this->getOption('tvPrefix', null, '');
				
				$options = array(
					'placeholder'	=>array_merge($resource->toArray(), $this->getTemplateVars($resource,$processTVs,$tvPrefix)),
					'title'			=>$resource->get('pagetitle'),
					'alias_path'	=>$resource->context_key.'/'.$this->getParentPath($resource->get('parent')),
					'alias'			=>$resource->get('alias')
				);
				$this->modx->switchContext('mgr');
				
				$pdf = $this->options['pdfPath'].$options['alias_path'].$options['alias'].'.pdf';
				if (file_exists($pdf)) {@unlink($pdf);}
				$this->modx->invokeEvent('OnHandleRequest', array());
				if( $optionsTV = $resource->getTVValue($this->getOption('tvPDFoptions', null, false)) ){
					$options = array_merge($options,json_decode($optionsTV,true));
				}
				$this->createPDF($options);
			}
        }
	}
	/** Создаёт PDF и выводить.
     * @param modResource $resource
	 * @param array $options
     */
	public function prerenderPDF($resource,$options = array()){
		if ($tv = $this->getOption('tvPDFlive', null, false)){
			if($resource->getTVValue($tv)){
				$this->modx->invokeEvent('OnHandleRequest', array());
				
				$processTVs = $this->getOption('processTVs', null, false);
				$tvPrefix = $this->getOption('tvPrefix', null, '');
				
				$options = array(
					'placeholder'	=>array_merge($resource->toArray(), $this->getTemplateVars($resource,$processTVs,$tvPrefix)),
					'title'			=>$resource->get('pagetitle'),
					'alias'			=>$resource->get('alias')
				);
				header('Content-Type: application/pdf');
				header('Content-Disposition:inline;filename='.$options['alias'].'.pdf');
				if( $optionsTV = $resource->getTVValue($this->getOption('tvPDFoptions', null, false)) ){
					$options = array_merge($options,json_decode($optionsTV,true));
				}
				echo $this->createPDF($options);
				exit;
			}
        }
	}
	/** Выводить сгенерированную ссылку к PDF документу.
     * @param numeric $id
     */
	public function linkPDF($id){
		$resource = $this->modx->getObject('modResource', $id);
		if (is_numeric($id) && !is_null($resource)){
			$options = array(
				'title'     =>$resource->get('pagetitle'),
				'alias_path'=>$resource->context_key.'/'.$this->getParentPath($resource->get('parent')),
				'alias'     =>$resource->get('alias')
			);
			if(file_exists($this->options['pdfPath'].$options['alias_path'].$options['alias'].'.pdf')){
				return $this->options['pdfUrl'].$options['alias_path'].$options['alias'].'.pdf';
			}
        }
	}
	/** Hook для создание и генерации ссылки - PDF документа.
     * @param formIt $hook
	 * @param array $config
     */
	public function emailPDFLink($hook = null, $options = array()){
		if(!is_null($hook)){	
			$config = array(
				'placeholder'	=> [
					'_hook' 	=> $hook
				],
				'tpl'           => $hook->formit->config['tplPDF'],
				'tplHeader'     => $hook->formit->config['tplPDFHeader'],
				'tplFooter'     => $hook->formit->config['tplPDFFooter'],
				'title'         => $hook->formit->config['pdfTitle']?:$hook->formit->config['emailSubject'],
				'alias_path'    => $hook->formit->config['pdfAliasPath']?:'form/',
				'alias'         => $hook->formit->config['pdfAlias']?:'document',
				'options'		=> $hook->formit->config['pdfOptions'],
				'date'			=> $hook->formit->config['pdfDate']?:'d.m.Y G:i'
			);
			$options = array_merge($config,$options);
			$options['alias'] = $options['alias'].'-'.time();
			if($options['options']){
				$options = array_merge($options,json_decode($options['options'],true));
			}
			$this->modx->invokeEvent('OnHandleRequest', array());
			
			$pdf = $this->options['pdfPath'].$options['alias_path'].$options['alias'].'.pdf';
			if (file_exists($pdf)) {@unlink($pdf);}
			
			$this->createPDF($options);
			$hook->setValue(array(
				'pdfTitle' 		=> $options['title'],
				'pdfTime'  		=> time(),
				'pdfDate'  		=> date($options['date'],time()),
				'pdfUrl'   		=> $this->options['pdfUrl'],
				'pdfAliasPath'	=> $options['alias_path'],
				'pdfAlias'		=> $options['alias'],
				'pdfLinkUrl'	=> $this->modx->getOption('site_url').trim($this->options['pdfUrl'].$options['alias_path'].$options['alias'].'.pdf','/')),
				'pdfLinkPath' 	=> $this->modx->getOption('base_path').trim($this->options['pdfUrl'].$options['alias_path'].$options['alias'].'.pdf','/'))
			));
		}
    }
}