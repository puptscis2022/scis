<?php 

	namespace App\Models;

	use CodeIgniter\Model;

	class PositionsModel extends Model{

		protected $table = 'positions';
		protected $primaryKey = 'id';
		
		protected $useAutoIncrement = true;

	    protected $returnType     = 'array';
	    //protected $useSoftDeletes = true;

	    protected $allowedFields = ["position_name", "clearance_field_id","created","modified","deleted","deleted_date"];

	    //protected $useTimestamps = true;
	    //protected $createdField  = 'created';
	    //protected $updatedField  = 'modified';
	    //protected $deletedField  = 'deleted_date';

	    //for validating DATA
	    protected $validationRules    = [
	    									"position_name" => "required|regex_match[/^[a-zA-Z0-9' ]+$/]", 
	    									"clearance_field_id" => 'integer',
	    ];
	    protected $validationMessages = [
	    									"position_name" => [	
	    													'required' => 'Position Name is Required',
	    													'regex_match' => 'Position Name must only contain alpanumeric characters' 
	    													], 
	    									"clearance_field_id" => [ 'integer' => 'Invalid Clearance Field'],
	    ];
	    protected $skipValidation     = true;

	     public function getInfo($id)
	    {
	    	$query = $this->db->query("SELECT a.position_name as 'position', b.field_name as 'clearance_field'
				FROM positions a
			    LEFT JOIN clearance_fields b on b.id = a.clearance_field_id
			    WHERE a.id = ".$id);

	        return $query->getRow();
	    }
	}
?>