<?php 

	namespace App\Models;

	use CodeIgniter\Model;

	class StudentOrganizationsModel extends Model{

		protected $table = 'student_organizations';
		protected $primaryKey = 'id';
		
		protected $useAutoIncrement = true;

	    protected $returnType     = 'array';
	    //protected $useSoftDeletes = true;

	    protected $allowedFields = ["organization_name", "organization_type_id","created","modified","deleted","deleted_date"];

	    //protected $useTimestamps = true;
	    //protected $createdField  = 'created';
	    //protected $updatedField  = 'modified';
	    //protected $deletedField  = 'deleted_date';

	    //for validating DATA
	    protected $validationRules    = [
	    									"organization_name" => "required|regex_match[/^[a-zA-Z0-9' ]+$/]", 
	    									"organization_type_id" => 'integer',
	    ];
	    protected $validationMessages = [
	    									"organization_name" => [
	    														'required' => 'Organization Name is Required',
	    														'regex_match' => 'Organization Name must only contain alpanumeric characters' 
	    													], 
	    									"organization_type_id" => [ 'integer' => 'Invalid Organization Type'],
	    ];
	    protected $skipValidation     = true;
	}
?>