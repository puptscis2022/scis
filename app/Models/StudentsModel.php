<?php 

	namespace App\Models;

	use CodeIgniter\Model;

	class StudentsModel extends Model{

		protected $table = 'students';
		protected $primaryKey = 'id';

		protected $useAutoIncrement = true;

	    protected $returnType     = 'array';
	    //protected $useSoftDeletes = true;

	    protected $allowedFields = ["user_id", "student_number", "year_level", "course_id", "student_type_id","created","modified","deleted","deleted_date"];

	    //protected $useTimestamps = true;
	    //protected $createdField  = 'created';
	    //protected $updatedField  = 'modified';
	    //protected $deletedField  = 'deleted_date';

	    //for validating DATA Inputs
	    protected $validationRules    = [
	    									'user_id' 			=> 'required|is_unique[students.user_id]',
	    									"student_number"	=> 'required|alpha_dash|exact_length[15]|is_unique[students.student_number,id,{id}]',
	    									"year_level"		=> 'required|integer',
	    									"course_id"			=> 'required|integer',
	    									"student_type_id"	=> 'required|integer'
	    ];
	    protected $validationMessages = [
	    									'user_id' 			=> ['required' 	=> 'User Login Not Registered',
	    															'is_unique' => 'User Login Already Existed'
	    														],	    									
	    									"student_number"	=> ['required' 	=> 'Student Number is Required',
	    															'alpha_dash'=> 'Student Number is must only contain alphanumeric characters and dashes',
	    															'exact_length[15]' => 'Invalid Student Number',
	    															'is_unique' => 'Student Number already Exist',
	    														],
	    									"year_level"		=> ['required' => 'Year Level is Required',
	    															'integer'  => 'Invalid Year Level'
	    														],
	    									"course_id"			=> ['required' => 'Course is Required',
	    															'integer'  => 'Invalid Course'
	    														],
	    									"student_type_id"	=> ['required' => 'Student Type is Required',
	    															'integer'  => 'Invalid Student Type'
	    														]
	    ];
	    protected $skipValidation     = false;
	}
?>