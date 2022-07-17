<?php 

	namespace App\Models;

	use CodeIgniter\Model;

	class UsersModel extends Model{

		protected $table = 'users';
		protected $primaryKey = 'id';

		protected $allowedFields = ["username","password", "last_name", "first_name", "middle_name", "suffix_name", "email", "contact_no", "profile_picture", "created","deleted","modified","deleted_date"];

		protected $useAutoIncrement = true;

	    protected $returnType     = 'array';
	    //protected $useSoftDeletes = true;

	    // protected $useTimestamps = true;
	    // protected $dateFormat = "Y-m-d H-i-s";
	    // protected $createdField  = 'created';
	    // protected $updatedField  = 'modified';
	    // protected $deletedField  = 'deleted_date';

	    //for validating DATA
	    protected $validationRules    = [	
	    									"username"	=> 'required|alpha_numeric_punct|is_unique[users.username,id,{id}]',
	    									"password"	=> 'required|min_length[6]',
	    									"last_name" 		=> 'required|alpha_space',
	    									"first_name"		=> 'required|alpha_space',
	    									"middle_name"		=> 'alpha_space',
	    									"suffix_name"		=> 'alpha_numeric_space',
	    									"email"				=> 'required|valid_email|is_unique[users.email,id,$primaryKey]',
	    									"contact_no"		=> 'integer|exact_length[11]',
	    								];
	    protected $validationMessages = [
	    									"username"	=> ['required' 		=> 'Username is Required',
	    													'alpha_numeric_punct'	=> 'Username must only contain alphanumeric characters',
	    													'is_unique' 	=> 'Username Already Exist'
	    												],
	    									"password"	=> ['required'		=> 'Password is Required',
	    													'min_length[6]'	=> 'Password must have a minimum of 6 characters'
	    												],
	    									"last_name" 	=> ['required'	=> "User's Last Name is Required",
	    															'alpha_space'		=> 'Last Name must only contain alphabet'
	    														],
	    									"first_name"	=> ['required'	=> "User's First Name is Required",
	    															'alpha_space'		=> 'First Name must only contain alphabet'
	    														],
	    									"middle_name"	=> ['alpha_space'	=> 'Middle Name must only contain alphabet' ],
	    									"suffix_name"	=> ['alpha_numeric_space'	=> 'Suffix Name must only contain alphabet' ],
	    									"email"			=> ['required'		=> "User's Email is Required",
	    															'valid_email'	=> 'Invalid Email',
	    															'is_unique' => 'Email Already Exist',
	    														],
	    									"contact_no"	=> ['integer' => 'Contact Number must only contain numeric values',
	    														'exact_length[11]' =>'Invalid Contact Number'
	    														]
	    								];
	    protected $skipValidation     = true;

	    public function getUsersByRole($role = FALSE)
	    {
	    	if($role)
	    	{
	    		$query = $this->db->query("SELECT CONCAT(a.first_name,' ',a.middle_name,' ',a.last_name,' ',a.suffix_name) as 'user_name',a.id as 'user_id'
					FROM users a
				    LEFT JOIN user_roles b on b.user_id = a.id
				    LEFT JOIN roles c on c.id = b.role_id
				    WHERE a.deleted = 0 AND c.role = '".$role."'");

		        return $query->getResult();
		    }
		    else
		    {
		    	return 0;
		    }
	    }
	}
?>