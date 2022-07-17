<?php 

	namespace App\Models;

	use CodeIgniter\Model;

	class SubmissionsModel extends Model{

		protected $table = 'submissions';
		protected $primaryKey = 'id';
		
		protected $useAutoIncrement = true;

	    protected $returnType     = 'array';
	    //protected $useSoftDeletes = true;

	    protected $allowedFields = ["deficiency_id", "file_path","created","modified","deleted","deleted_date"];

	    //protected $useTimestamps = true;
	    //protected $createdField  = 'created';
	    //protected $updatedField  = 'modified';
	    //protected $deletedField  = 'deleted_date';

	    //for validating DATA
	    // protected $validationRules    = [];
	    // protected $validationMessages = [];
	    // protected $skipValidation     = false;

	    public function submissionDetail($id)
	    {
	    	$query = $this->db->query("SELECT c.requirement_name as 'requirement', d.clearance_officer_id as 'officer_assigned_id', CONCAT(e.first_name,' ',e.middle_name,' ',e.last_name,' ',e.suffix_name) as 'officer_assigned_name' 
				FROM submissions a 
				LEFT JOIN deficiencies b on b.id = a.deficiency_id
				LEFT JOIN requirements c on c.id = b.requirement_id
			    LEFT JOIN clearance_entries d on d.id = b.clearance_entry_id
			    LEFT JOIN users e on e.id = d.clearance_officer_id
			    WHERE a.id = ".$id);

	        return $query->getRow();
	    }
	}
?>