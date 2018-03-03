<?php 

	/*
	 * Each controller class is designed in a way such that only methods associated with 
	 * links and those called by the parent class is defined here
	 * Every other methods are placed in, perhaps another class(like a middle man) whose instance can 
	 * be gotten from the constructor of this class.
	 *
	 * In other words, all methods defined here are private and protected and they cannot be called
	 * publicly.
	 */
	
	class Dashboard extends Controller {
		public static $class = __CLASS__;
		private $_register;
		private $_instance;

		public function __construct() {
			parent::__construct();
			$this->_registry->create_registry('pages', self::registers());
		}

		public function index($url) {
			//Checks if there is an existing session
			if(!Session::check_session()) {
				header('location: '. HOME . '/user/login');
			}

			// Gets the user details
			$this->_view->_props['username'] = Session::get_var('username');
			//Sets the title bar
			$this->_view->_props['title'] = 'User dashboard';
			// Getting current user statistics
			$this->_fundcore->get_user_stats();

			// Runs a check on the current user account
			$this->_fundcore->check_account_status(Session::get_var('username'));
			
			// Runnig all payment checks
			$this->_fundcore->run_payment_check(Session::get_var('username'));
			// If no other URI, load the Dashboard main page
			if(empty($url)) {
				$this->_view->craft('main');
			}

			
			// Calling the parent class {Controller} index
			parent::index($url);
		}

		private static function registers() {
			return array(
					'profile' => 'updateProfile',
					'genealogy' => 'gegtGenealogy',
					'signups' => 'getSignUps',
					'pdonation' => 'pending_donations',
					'cdonation' => 'confirmed_donations',
					'bank_detail' => 'uBank',
					'settings' => 'goSettings',
					'reflink' => 'refLink',
					'stepup' => 'upgrade',
					);
		}

		/* Functions called by the parents*/
		protected function updateProfile() {
			$this->_fundcore->updateProfile();
			$this->_view->craft('profile');
		}

		protected function getGenealogy() {
			$this->_view->craft('geneology');
		}

		protected function getSignUps() {
			$this->_fundcore->list_signups();
			$this->_view->craft('signups');
		}

		protected function pending_donations() {
			$this->_fundcore->list_pending_donations();
			$this->_view->craft('pdonation');
		}

		protected function confirmed_donations() {
			$this->_fundcore->list_unpaid_donations();
			$this->_view->craft('cdonation');
		}

		protected function uBank() {
			$this->_view->craft('ubank');
		}

		protected function goSettings() {
			$this->_view->craft('settings');
		}

		protected function refLink() {
			$this->_fundcore->manage_ref_link();
			$this->_view->craft('reflink');
		}

		protected function upgrade() {
			$this->_fundcore->assign_me();
			$this->_fundcore->manage_upgrade();
			//$this->_fundcore->genP();
			if(isset($_POST['send_app'])) {
				$this->_fundcore->notify_payment(Session::get_var('username'));
			}
			$this->_view->craft('upgrade');
		}
	}
?>