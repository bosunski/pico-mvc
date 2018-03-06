<?php 
	class OutSource {
		private $transLen = 6;

		public function __construct() {
			$this->dbc = Dbcore::getInstance();
		}

		public function load_options() {
			$sql = 'SELECT * FROM options';
			$res = $this->dbc->get_result($sql);
			return $res;
		}

		public function get_user($field, $value) {
			//exit($field.$value);
			$sql = 'SELECT * FROM user_info WHERE '.$field.' = ? LIMIT 1';
			$res = $this->dbc->get_single_result($sql, array($value));
			if(!is_array($res)) {
				return false;
			} else {
				return $res;
			}
		}

		public function get_my_direct_referal_details($id) {
			$sql = 'SELECT * FROM user_info WHERE id = ? LIMIT 1';
			$res = $this->dbc->get_single_result($sql, array($id));
			if(!is_array($res)) {
				return false;
			} else {
				return $res;
			}
		}

		// Assigns a user to another
		public function assign_to($options, $sender, $reciever, $level = 0, $amount, $type = 'regular') {
			// Generates transation ID
			$transaction_ID = $this->getRand($this->transLen);
			$date = date('Y-m-d H:i:s', strtotime('+'.$options['pay_referal_due_date'].'days'));
			$sql = 'INSERT INTO pending_payment (transaction_ID, amount, sender, reciever, date_assigned, 
					due_date, status, level, type) VALUES (?, ?, ?, ?, NOW(), ?, ?, ?, ?)';
			$params = array($transaction_ID, $amount, $sender, $reciever, $date, 'pending', $level, $type);
			$res = $this->dbc->prepare($sql, $params);
			if($res) {
				return true;
			}
			return false;
		}


		private function getRand($len = 6) {
			$auth = new Auth;
			$rand = $auth->_getRand($len);
			$sql = 'SELECT * FROM pending_payment WHERE transaction_ID = ?';
			$sql = $this->dbc->get_single_result($sql, array($rand));
			if($this->dbc->rowsReturned > 0) {
				$this->getRand($len);
			}
			return strtoupper($rand);
		}

		public function increment_count_for_payment($id, $field) {
			$sql = 'UPDATE pending_payment SET '.$field.' = '.$field.'+1 WHERE id = ?';
			$res = $this->dbc->prepare($sql, array($id));
			if($res) {
				return true;
			}
			return false;
		}

		public function stepup($sender, $status) {
			$a = $this->get_pending_payment($sender);
			/*
			 * Trying to reset the normal due time incase it has been altered
			 * when Payment notification was sent.
			 * You know, i added 1 Day to the normal due days to pause the time.
			 */
			$new_due_date = strtotime($a['date_assigned']); 
			$new_due_date = strtotime('+4 Days', $new_due_date);
			$new_due_date = date('Y-m-d H:i:s', $new_due_date);

			$sql = 'UPDATE pending_payment SET due_date = ?, status = ? WHERE sender = ? ';
			$params = array($new_due_date, $status, $sender);
			$res = $this->dbc->prepare($sql, $params);
		}

		public function count_downlines($user) {
			$sql = 'SELECT * FROM user_info WHERE direct_referal_id = ? AND status != ?';
			$res = $this->dbc->get_result($sql, array($user, 'terminated'));
			if(is_array($res)) {
				return count($res);
			}
			return false;
		}

		private function new_level($username, $level = 0) {
			$sql = 'UPDATE user_info SET level_entry_date = NOW(), level = level+1 WHERE username = ?';
			$res = $this->dbc->prepare($sql, array($username));
			if($res) {
				return true;
			}
			return false;
		}

		public function approve_payment($sender, $reciever, $amount = 0) {

			//$sql = 'DELETE FROM pending_payment WHERE sender = ?';
			$sql = 'UPDATE pending_payment SET status = ? WHERE  sender = ? AND reciever = ?;
					UPDATE user_info SET status = "active", payments_recieved = payments_recieved+1, amount_recieved = amount_recieved+?, date_recieved_last = NOW() WHERE username = ?;
					UPDATE user_info SET status = "active" WHERE username = ?';

			$res = $this->dbc->prepare($sql, array('approved', $sender, $reciever, $amount, $reciever, $sender));
			if($res) {
				$this->new_level($sender);
				return true;
			}
			return false;
		}


		//Sets the upgrade_ready fiels to yes
		public function ready_to_upgrade($user) {
			$sql = 'UPDATE user_info SET upgrade_ready = ? WHERE username = ?';
			$res = $this->dbc->prepare($sql, array('yes', $user));
			if($res) {
				return true;
			}
			// Correction is needed here
			return true;
		}

		//Sets the upgrade_ready field to no
		public function not_ready_to_upgrade($user) {
			$sql = 'UPDATE user_info SET upgrade_ready = ? WHERE username = ?';
			$res = $this->dbc->prepare($sql, array('no', $user));
			if($res) {
				return true;
			}
			return false;
		}

		public function disapprove_payment($sender, $reciever, $status = null, $id = null) {
			//$status = $status == null ? ' ' : ' AND status = "'.$status.'"';
			//$sql = 'DELETE FROM pending_payment WHERE sender = ?';
			$sql = 'UPDATE pending_payment SET status = ? WHERE sender = ? AND reciever = ?';
			$res = $this->dbc->prepare($sql, array($status, $sender, $reciever));
			if($res) {
				if($this->increment_count_for_payment($id, 'disapproved_times')) {
					return true;
				}
			}
			return false;
		}

		/*
		 * Gets ANYTHING using ANYTHING from ANY table
		 */
		public function get_from_table($table, $filter_array = array(), $limit = 1, $bind = 'AND') {
			$counter = 0;
			$query_append = (empty($filter_array)) ? ' ' : ' WHERE ';
			$query_append_last = ($limit == null) ? '' : ' LIMIT ' . $limit;
			if(!empty($filter_array)) {
				foreach($filter_array as $col => $value) {
					$query_append .= $value . ' ';
					$counter++;
					$query_append .= ($counter != count($filter_array)) ? $bind. ' ' : '';
				}
			}
			$sql = 'SELECT * FROM ' . $table . $query_append  . $query_append_last;
			if($limit > 1) {
				$res = $this->dbc->get_result($sql, array());
			} else {
				$res = $this->dbc->get_single_result($sql, array());
			}
			if(is_array($res)) {
				return $res;
			}
			return false;
		}
		
		public function get_pending_payment($sender, $status = null) {
			$status = $status == null ? ' ' : ' AND status = "'.$status.'"';
			$sql = 'SELECT * FROM pending_payment WHERE sender = ?'.$status;
			$res = $this->dbc->get_single_result($sql, array($sender));
			if(is_array($res)) {
				return $res;
			}
			return false;
		}

		// Gets the pending payment of a particular user
		public function get_payment($sender) {
			$sql = 'SELECT * FROM pending_payment WHERE status = ? AND sender = ?';
			$res = $this->dbc->get_single_result($sql, array('pending', $sender));
			if(is_array($res)) {
				return $res;
			}
			return false;
		}

		// Deletes payment assigned
		public function delete_payment($id) {
			$sql = 'UPDATE pending_payment SET status = "deleted" WHERE id = ?';
			$res = $this->dbc->prepare($sql, array($id));
			if($res) {
				return true;
			}
			return false;
		}

		public function get_payment_approval($sender) {
			$sql = 'SELECT * FROM pending_payment WHERE status = ? AND sender = ?';
			$res = $this->dbc->get_single_result($sql, array('waiting_for_approval', $sender));
			if(is_array($res)) {
				return $res;
			}
			return false;
		}


		public function check_user_assigned($sender, $level) {
			$sql = 'SELECT * FROM pending_payment WHERE level = ? AND sender = ?';
			$res = $this->dbc->get_single_result($sql, array($level, $sender));
			//var_dump($res);
			//echo '<br/><br/><br/><br/><br/>';
			if($res) {
				return true;
			}
			return false;
		}


		public function get_pending_approval($reciever) {
			$sql = 'SELECT * FROM pending_payment WHERE reciever = ? AND status = ?';
			$res = $this->dbc->get_result($sql, array($reciever, 'waiting_for_approval'));
			if(is_array($res)) {
				return $res;
			}
			return false;
		}

		public function get_potential_donors($reciever) {
			$sql = 'SELECT * FROM pending_payment WHERE reciever = ?';
			$res = $this->dbc->get_single_result($sql, array($sender));
			if(is_array($res)) {
				return $res;
			}
			return false;
		}

		public function terminate_account($username) {
			$sql = 'UPDATE user_info SET status = ? WHERE username = ?';
			$res = $this->dbc->prepare($sql, array('terminated', $username));
			if($res) {
				return true;
			}
			return false;
		}

		// Handles temination and suspension of accounts
		public function ban($id, $status, $message) {
			$sql = 'UPDATE user_info SET status = ?, last_message = ? WHERE id = ?';
			$params = array($status, $message, $id);
			$res = $this->dbc->prepare($sql, $params);
			if($res) {
				return true;
			}
			return false;
		}


		// Writes to the Database for notification of payment
		public function notify_payment($sender, $reciever) {
			$a = $this->get_pending_payment($sender);
			$new_due_date = strtotime($a['due_date']); 
			$new_due_date = strtotime('+1 Days', $new_due_date);
			$new_due_date = date('Y-m-d H:i:s', $new_due_date);

			$date = strtotime("+1 days");
			$app_due_date = date('Y-m-d H:i:s', $date);
			$sql = 'UPDATE pending_payment SET due_date = ?, status = ?, approval_sent_date = NOW(), approval_due_date = ? WHERE sender = ?';
			$res = $this->dbc->prepare($sql, array($new_due_date, 'waiting_for_approval', $app_due_date, $sender));
			if($res) {
				return true;
			}
			return false;
		}
				
		public function profile_update($submit_botton) {
			foreach ($_POST as $key => $value) {
				if($value !=='' && $value !== $submit_botton) {
					$result =$this->dbc->prepare("UPDATE user_info SET ".$key." =? WHERE username =?",array($value,Session::get_var('username')));
					if($this->dbc->rowsAffected > 0){
						$_SESSION[$key] = $value;	
					//continue working from here
					}
				}
			}
		}

		// Fetsches all the users donating to $reciever
		public function get_all_donors($reciever, $status = 'waiting_for_approval') {
			$sql = 'SELECT * FROM pending_payment WHERE reciever = ? AND status = ?';
			$params = array($reciever, $status);
			$res = $this->dbc->get_result($sql, $params);
			if(is_array($res) && !empty($res)) {
				return $res;
			}
			return false;
		}

		// Fetches all the users who signed up through me
		public function get_all_signups($user) {
			$sql = 'SELECT * FROM user_info WHERE direct_referal_id = ?';
			$params = array($user);
			$res = $this->dbc->get_result($sql, $params);
			if(is_array($res)) {
				return $res;
			}
			return false;
		}
		
		public function get_level_sorted($level, $order = 'level_entry_date', $level_payments = null) {
			$sql = 'SELECT * FROM user_info WHERE (status = ? OR status = ?) AND level = ? ORDER BY level_entry_date ASC';
			$params = array('active', 'locked_payment', $level);
			$res = $this->dbc->get_result($sql, $params);
			if(is_array($res)) {
				return $res;
			}
			return false;
		}

		public function get_level_payments_approved($reciever, $level) {
			$sql = 'SELECT * FROM pending_payment WHERE status = ? AND reciever = ? AND level = ?';
			$res = $this->dbc->get_result($sql, array('approved', $reciever, $level));
			if(is_array($res)) {
				return $res;
			}
			return false;
		}

		public function get_level_payment($reciever, $level) {
			$sql = 'SELECT * FROM pending_payment WHERE status != ? AND reciever = ? AND level = ?';
			$res = $this->dbc->get_result($sql, array('approved', $reciever, $level));
			if(is_array($res)) {
				return $res;
			}
			return false;
		}

		

		public function get_level_payments($reciever, $level) {
			$sql = 'SELECT * FROM pending_payment WHERE (status = ? OR status = ? OR status = ?) AND reciever = ? AND level = ?';
			$res = $this->dbc->get_result($sql, array('approved', 'pending', 'waiting_for_approval', $reciever, $level));
			if(is_array($res)) {
				return $res;
			}
			return false;
		}


		public function get_user_specific_field($user, $field) {
			$sql = 'SELECT ? FROM user_info WHERE username = ?';
			$res = $this->dbc->get_single_result($sql, array($field, $user));
			if($res) {
				return $res[$field];
			} 
			return false;
		}

		public function assign($sender, $reciever, $level) {
			$sql = 'INSERT INTO pending_payment, sender, reciever, level VALUES(?, ?, ?)';
			$params = array($sender, $reciever, $level);
			$res = $this->dbc->prepare($sql, $params);
			echo $this->dbc->error;
			if($res) {
				return true;
			}
			return false;
		}

		// This function insert support message into the database
		public function log_support($params) {
			//exit;
			$sql = 'INSERT INTO support (username, email, subject, message, sdate) VALUES(?, ?, ?, ?, NOW())';
			$res = $this->dbc->prepare($sql, $params);
			if($res) {
				return true;
			}
			return false;
		}

		public function verify_input() {
  			$input=$_POST;
		 	$dynamic_sql = "SELECT * FROM user_info WHERE id!=?".$this->unset_empty();
 			var_dump($dynamic_sql);
  			$verify = $this->dbc->get_result($dynamic_sql,$_SESSION['valid_input']);
			var_dump($verify);
			exit();
			if($this->dbc->rowsReturned<1){
  				return true;
			} else { 
  				return false;
  			} 
		}

		public function verify_profile_update() {
 			$dynamic_sql ="SELECT * FROM user_info WHERE id!=?".$this->unset_empty();

  			$verify = $this->dbc->get_result($dynamic_sql,$_SESSION['valid_input']);

			if($this->dbc->rowsReturned<=0){
  				return true;
			} else { 
  				return false;
  			} 

		}

		public function unset_empty(){
			$feed = '';
			$valid_input[] = NULL;
			foreach ($_POST as $key => $value) {
				if($value == ''){
					unset($_POST[$key]);
				} else {
					$valid_input[] =$value;
					$feed .= "OR ".$key."=? ";
				}
			}
			
			$_SESSION['valid_input'] =$valid_input;
			return $feed;
		}


		public function verify_update($exceptions) {
			foreach ($_POST as $key => $value) {
				if($value =='' && !in_array($key,$exceptions)){
					unset($_POST[$key]);
				} else {
					$get =$this->dbc->get_result("SELECT * FROM user_info WHERE ".$key." =?",array($value));
					if($this->dbc->rowsReturned>0){
						$error[] ="<li style ='color:red'>the ".$key." you entered needs to be changed</li>";
					}
				}
			}
			if(empty($error)) {
				return 'done';
			} else {
				$feed = "";
				foreach ($error as $key => $value) {
					$feed .= $value;
				}
			}
			return $feed;
		}

		public function validate_user_input(){
			$error=array();
			foreach ($_POST as $key => $value) {
			  switch ($key) {
			    case 'phone':
				    if(!is_numeric($value)){
				        $error[]='the phone number is not valid ';
				    }
			    break;

			    case 'password':
				    if(strlen($value) < 7 || strlen($value) > 17) {
				      	$error[]='invalid password lenght';
				    }
			    break;

			    case 'account_no':
		           if(!is_numeric($value)){
		        		$error[]='the account number is not valid ';
			    	}
			     break;

			    case 'repeat_password':
			       	if($_POST['password'] !==$_POST['repeat_password']) {
			        	$error[] ="password do not match";
			       	}
			     break;

			    default:
			      # code...
			      break;
			  }
			}

			if(!empty($error)) {
				$feed = "";
			   	foreach ($error as $key => $value) {
			 		$feed .= "<li style ='color:red'><b><strong>".$value."</strong></b></li>";
				}
			 	return $feed;
			 } else {
			  	return 'done';
			 }
		}
	}
?>
