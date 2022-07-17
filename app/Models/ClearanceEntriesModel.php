<?php 

	namespace App\Models;

	use CodeIgniter\Model;

	class ClearanceEntriesModel extends Model{

		protected $table = 'clearance_entries';
		protected $primaryKey = 'id';
		
		protected $useAutoIncrement = true;

	    protected $returnType     = 'array';
	    //protected $useSoftDeletes = true;

	    protected $allowedFields = ["clearance_form_id", "clearance_field_id", "clearance_officer_id", "clearance_field_status","created","modified","deleted","deleted_date"];

	    //protected $useTimestamps = true;
	    //protected $createdField  = 'created';
	    //protected $updatedField  = 'modified';
	    //protected $deletedField  = 'deleted_date';

	    //for validating DATA
	    // protected $validationRules    = [];
	    // protected $validationMessages = [];
	    // protected $skipValidation     = false;

	    public function getList($form_id)
	    {
	    	$query = $this->db->query("SELECT a.id as 'entry_id', c.field_name as 'field', CONCAT(d.first_name,' ',d.middle_name,' ',d.last_name,' ',d.suffix_name) as 'officer_name', a.clearance_field_status as 'status', c.id as 'field_id', f.position_name as 'position' , f.id as 'position_id'
                FROM clearance_entries a
                INNER JOIN clearance_fields c on c.id = a.clearance_field_id
                INNER JOIN users d on d.id = a.clearance_officer_id
                INNER JOIN clearance_officer_positions e on e.clearance_officer_id = a.clearance_officer_id 
                INNER JOIN positions f on f.id = e.position_id AND f.clearance_field_id = a.clearance_field_id
                WHERE a.clearance_form_id = ".$form_id." AND e.deleted = 0 AND f.deleted = 0");

	        return $query->getResult();
	    }
	}
?>