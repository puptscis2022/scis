<?php 

	namespace App\Models;

	use CodeIgniter\Model;

	class CoursesModel extends Model{

		protected $table = 'courses';
		protected $primaryKey = 'id';
		
		protected $useAutoIncrement = true;

	    protected $returnType     = 'array';
	    //protected $useSoftDeletes = true;

	    protected $allowedFields = ["course_name", "student_organization_id", "abbreviation", "max_year_level","created","modified","deleted","deleted_date"];

	    //protected $useTimestamps = true;
	    //protected $createdField  = 'created';
	    //protected $updatedField  = 'modified';
	    //protected $deletedField  = 'deleted_date';

	    //for validating DATA
	    protected $validationRules    = [
	    									"course_name" => "required|regex_match[/^[a-zA-Z0-9' ]+$/]",
	    									"student_organization_id" => 'integer',
	    ];
	    protected $validationMessages = [
	    									"course_name" => [
	    														'required' => 'Course Name is Required',
	    														'regex_match' => 'Course Name must only contain alpanumeric characters'
	    													],
	    									"student_organization_id" => [ 
	    														'integer' => 'Invalid Organization'
	    													],
	    ];
	    protected $skipValidation     = true;
	}
?>