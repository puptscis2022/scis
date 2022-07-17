<?php 

	namespace App\Models;

	use CodeIgniter\Model;

	class BlackListModel extends Model{

		protected $table = 'black_list';
		protected $primaryKey = 'id';
		
		protected $useAutoIncrement = true;

	    protected $returnType     = 'array';
	    //protected $useSoftDeletes = true;

	    protected $allowedFields = ["student_id","clearance_field_id","requirement_id","created","modified","deleted","deleted_date"];

	    //protected $useTimestamps = true;
	    //protected $createdField  = 'created';
	    //protected $updatedField  = 'modified';
	    //protected $deletedField  = 'deleted_date';

	    //for validating DATA
	    // protected $validationRules    = [];
	    // protected $validationMessages = [];
	    // protected $skipValidation     = false;

	    public function getInfo($id)
	    {
	    	$query = $this->db->query("SELECT CONCAT(c.first_name,' ',c.last_name) as 'student_name', d.field_name as 'cField',e.requirement_name as 'requirement'
				FROM black_list a
			    LEFT JOIN students b on b.id = a.student_id
			    LEFT JOIN users c on c.id = b.user_id
			    LEFT JOIN clearance_fields d on d.id = a.clearance_field_id
			    LEFT JOIN requirements e on e.id = a.requirement_id
			    WHERE a.id = ".$id);

	        return $query->getRow();
	    }
	}
?>