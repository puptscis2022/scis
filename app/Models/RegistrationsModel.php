<?php 

	namespace App\Models;

	use CodeIgniter\Model;

	class RegistrationsModel extends Model{

		protected $table = 'registrations';
		protected $primaryKey = 'id';
		
		protected $useAutoIncrement = true;

	    protected $returnType     = 'array';
	    //protected $useSoftDeletes = true;

	    protected $allowedFields = ["username","password","role_id","last_name", "first_name", "middle_name", "suffix_name", "email", "contact_no", "student_number", "year_level", "course_id", "student_type_id","status","created","modified","deleted","deleted_date"];

	    //protected $useTimestamps = true;
	    //protected $createdField  = 'created';
	    //protected $updatedField  = 'modified';
	    //protected $deletedField  = 'deleted_date';

	    //for validating DATA
	    protected $validationRules    = [	"username"	=> 'required|alpha_numeric_punct|is_unique[users.username,id,{id}]',
	    									"password"	=> 'required|min_length[6]',
	    									"role_id"	=> 'required|integer',
	    									"last_name" 		=> 'required|alpha_space',
	    									"first_name"		=> 'required|alpha_space',
	    									"middle_name"		=> 'alpha_space',
	    									"suffix_name"		=> 'regex_match[/^[a-zA-Z.]*$/]',
	    									"email"				=> 'required|valid_email|is_unique[users.email]',
	    									"contact_no"		=> 'integer|exact_length[11]',
	    ];
	    protected $validationMessages = [
	    									"username"	=> ['required' 		=> 'Username is Required',
	    													'alpha_numeric'	=> 'Username must only contain alphanumeric characters',
	    													'is_unique' 	=> 'Username Already Exist'],
	    									"password"	=> ['required'		=> 'Password is Required',
	    													'min_length[6]'	=> 'Password must have a minimum of 6 characters'
	    												],
	    									"role_id"	=> [
	    													'required' => "User's Role is Required",
	    													'integer' => 'Invalid Role'
	    												],
	    									"last_name" 		=> ['required'	=> "User's Last Name is Required",
	    															'alpha_space'		=> 'Last Name must only contain alphabet'
	    														],
	    									"first_name"		=> ['required'	=> "User's First Name is Required",
	    															'alpha_space'		=> 'First Name must only contain alphabet'
	    														],
	    									"middle_name"		=> ['alpha_space'	=> 'Middle Name must only contain alphabet' ],
	    									"suffix_name"		=> ['regex_match[/^[a-zA-Z.]*$/]'	=> 'Suffix Name must only contain alphabet and period' ],
	    									"email"				=> ['required'		=> "User's Email is Required",
	    															'valid_email'	=> 'Invalid Email',
	    															'is_unique' => "Email Already Exist",
	    														],
	    									"contact_no"		=> ['integer' => 'Contact Number must only contain numeric values',
	    															'exact_length[11]' =>'Invalid Contact Number'
	    														],
	    ];
	    protected $skipValidation     = false;
	}
?>