<?php 

	namespace App\Models;

	use CodeIgniter\Model;

	class StudentOrganizationOfficersModel extends Model{

		protected $table = 'student_organization_officers';
		protected $primaryKey = 'id';
		
		protected $useAutoIncrement = true;

	    protected $returnType     = 'array';
	    //protected $useSoftDeletes = true;

	    protected $allowedFields = ["clearance_officer_id", "student_organization_id","created","modified","deleted","deleted_date"];

	    //protected $useTimestamps = true;
	    //protected $createdField  = 'created';
	    //protected $updatedField  = 'modified';
	    //protected $deletedField  = 'deleted_date';

	    // for validating DATA
	    protected $validationRules    = [
	    									"clearance_officer_id" => 'integer', 
	    									"student_organization_id" => 'integer',
	    								];
	    protected $validationMessages = [
	    									"clearance_officer_id" => ['integer' => "Invalid Officer"], 
	    									"student_organization_id" => ['integer' => 'Invalid Organization'],
	    								];
	    protected $skipValidation     = true;
	}
?>