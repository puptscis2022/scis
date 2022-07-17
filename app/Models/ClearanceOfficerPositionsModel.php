<?php 

	namespace App\Models;

	use CodeIgniter\Model;

	class ClearanceOfficerPositionsModel extends Model{

		protected $table = 'clearance_officer_positions';
		protected $primaryKey = 'id';
		
		protected $useAutoIncrement = true;

	    protected $returnType     = 'array';
	    //protected $useSoftDeletes = true;

	    protected $allowedFields = ["clearance_officer_id", "position_id","created","modified","deleted","deleted_date"];

	    //protected $useTimestamps = true;
	    //protected $createdField  = 'created';
	    //protected $updatedField  = 'modified';
	    //protected $deletedField  = 'deleted_date';

	    // for validating DATA
	    protected $validationRules    = [
	    									"clearance_officer_id" => 'integer', 
	    									"position_id" => 'integer',
	    								];
	    protected $validationMessages = [
	    									"clearance_officer_id" => ['integer' => "Invalid Officer"], 
	    									"position_id" => ['integer' => 'invalid position'],
	    								];
	    protected $skipValidation     = true;
	}
?>