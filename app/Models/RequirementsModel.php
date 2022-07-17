<?php 

	namespace App\Models;

	use CodeIgniter\Model;

	class RequirementsModel extends Model{

		protected $table = 'requirements';
		protected $primaryKey = 'id';
		
		protected $useAutoIncrement = true;

	    protected $returnType     = 'array';
	    //protected $useSoftDeletes = true;

	    protected $allowedFields = ["requirement_name", "clearance_field_id","submission_type","file_type_id","instruction","created","modified","deleted","deleted_date"];

	    //protected $useTimestamps = true;
	    //protected $createdField  = 'created';
	    //protected $updatedField  = 'modified';
	    //protected $deletedField  = 'deleted_date';

	    //for validating DATA
	    protected $validationRules    = [
	    									"requirement_name" => "required|regex_match[/^[a-zA-Z0-9' ]+$/]", 
	    									"clearance_field_id" => 'integer',
	    ];
	    protected $validationMessages = [
	    									"requirement_name" => [	
	    													'required' => 'Requirement Name is Required',
	    													'regex_match' => 'Requirement Name must only contain alpanumeric characters' 
	    													], 
	    									"clearance_field_id" => [ 'integer' => 'Invalid Clearance Field'],
	    ];
	    protected $skipValidation     = true;
	}
?>