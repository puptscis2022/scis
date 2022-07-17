<?php 

	namespace App\Models;

	use CodeIgniter\Model;

	class ClearanceFieldsModel extends Model{

		protected $table = 'clearance_fields';
		protected $primaryKey = 'id';
		
		protected $useAutoIncrement = true;

	    protected $returnType     = 'array';
	    //protected $useSoftDeletes = true;

	    protected $allowedFields = ["field_name", "description", "clearance_type_id", "auto_tag","created","modified","deleted","deleted_date"];

	    //protected $useTimestamps = true;
	    //protected $createdField  = 'created';
	    //protected $updatedField  = 'modified';
	    //protected $deletedField  = 'deleted_date';

	    //for validating DATA
	    protected $validationRules    = [
	    									"field_name" => "required|regex_match[/^[a-zA-Z0-9' ]+$/]", 
	    									"description" => 'alpha_numeric_space', 
	    									"clearance_type_id" => 'integer',
	    ];
	    protected $validationMessages = [
	    									"field_name" => [	'required' => 'Field Name is Required',
	    														"regex_match" => 'Field Name must only contain alpanumeric characters' 
	    													], 
	    									"description" => [ 'alpha_numeric_space' => 'Description must only contain alpanumeric characters'],
	    									"clearance_type_id" => [ 'integer' => 'Invalid Clearance Type'],
	    ];
	    protected $skipValidation     = true;
	}
?>