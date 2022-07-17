<?php 
	
namespace App\Libraries;

class Hash{

	public static function encrypt_pass($password){
		return password_hash($password, PASSWORD_BCRYPT);
	}
	public static function check_pass($inputted_password, $db_password){
		if(password_verify($inputted_password, $db_password)){
			return true;
		}else{
			return false;
		}
	}

	public static function clean_data($data)
	{
  		$data = trim($data);
  		$data = stripslashes($data);
  		$data = htmlspecialchars($data);
  		return $data;
	}
}

?>