<?php
namespace FM;

class Views{
	private $__Variables = [];

	private $__viewFolder = 'views';

	private $__layoutFolder = 'layout';

	private $__content;

	public function __construct(){

	}

	public function __get($key){
		return isset($this->__Variables[$key]) ? $this->__Variables[$key] : null;
	}

	public function __set($key, $val){
		$this->__Variables[$key] = $val;
	}

	public function __isset($key){
		return !empty($this->__Variables[$key]);
	}

	/**
	 * Compile view file and layout (if $useLayout === true)
	 *
	 * @param string $view		View file name or path to them in folder
	 * 							with view files. File extension not necessary
	 * @param array  $Data		List of variables used in view
	 * @param bool   $useLayout If true, method return HTML in variable
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function makeView($view, $Data, $useLayout = true){
		$view = $this->__viewPath($view);

		$this->__fillData($Data);

		ob_start();
		if ($useLayout === true){
			include $view;
			$this->__content = ob_get_contents();
			ob_clean();
			$layoutFilePath = rtrim(ROOT_DIR, '/') . DS . rtrim(APP_FOLDER, '/') .
				DS . rtrim($this->__viewFolder, '/') . DS .
				$this->__layoutFolder . DS .
				'main.php';

			if (!file_exists($layoutFilePath)){
				throw new \Exception('Layout script not found');
			}

			include $layoutFilePath;
		} else {
			include $view;
		}

		$content = ob_get_contents();
		ob_clean();
		return $content;
	}

	/**
	 * Return content part to layout
	 *
	 * @return string
	 */
	public function content(){
		return $this->__content;
	}

	/**
	 * Build path to view file
	 *
	 * @param string $view
	 *
	 * @return string
	 * @throws \Exception
	 */
	private function __viewPath($view){
		$viewPath = rtrim(ROOT_DIR, '/') . DS . rtrim(APP_FOLDER, '/') .
					DS . rtrim($this->__viewFolder, '/') . DS .
					preg_replace('|\.php$|', '', $view) . '.php';

		if (file_exists($viewPath) === false){
			throw new \Exception('View file not found in ' . $viewPath);
		}

		return $viewPath;
	}

	private function __fillData($Data){
		if (!empty($Data)){
			foreach ($Data as $key => $val){
				$this->$key = $val;
			}
		}
	}
}