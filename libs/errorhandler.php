<?php 
	class ErrorHandler {
		private $current_class;
		private $_view;
		

		public function __construct($class) {
			$this->_view = Craft::gI();
			$this->current_class = $class;
		}

		public function craft($page, $errInfo = null) {
			$props['errInfo'] = $errInfo != null ? $errInfo : ' ';
			$props['title'] = $errInfo;

			if($page = '404')
			// Sending 404 response if not found
				header('HTTP/1.0 404 Not Found');

			$this->_view->set_prop($props);
			$this->_view->craft($page);
			exit;
		}
	}
?>