<?php 

	namespace App\Models;

	use CodeIgniter\Model;

	class RejectReasonsModel extends Model{

		protected $table = 'reject_reasons';
		protected $primaryKey = 'id';
		
		protected $useAutoIncrement = true;

	    protected $returnType     = 'array';
	    //protected $useSoftDeletes = true;

	    protected $allowedFields = ["requirement_id", "reason", "created", "modified", "deleted", "deleted_date"];

	    //protected $useTimestamps = true;
	    //protected $createdField  = 'created';
	    //protected $updatedField  = 'modified';
	    //protected $deletedField  = 'deleted_date';

	    //for validating DATA
	    protected $validationRules    = [
	    									"requirement_id" => 'integer',
	    									"reason" => "required|regex_match[/^[a-zA-Z0-9' ]+$/]",
	    ];
	    protected $validationMessages = [
	    									"requirement_id" => [ 'integer' => 'Invalid Clearance Field'],
	    									"reason" => [	
	    												'required' => 'Requirement Name is Required',
	    												'regex_match' => 'Requirement Name must only contain alpanumeric characters' 
	    											], 
	    ];
	    protected $skipValidation     = true;
	}
?>