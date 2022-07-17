<?php 

	namespace App\Models;

	use CodeIgniter\Model;

	class SubjectSubmissionsModel extends Model{

		protected $table = 'subject_submissions';
		protected $primaryKey = 'id';
		
		protected $useAutoIncrement = true;

	    protected $returnType     = 'array';
	    //protected $useSoftDeletes = true;

	    protected $allowedFields = ["subject_deficiency_id", "file_path","created","modified","deleted","deleted_date"];

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
	    	$query = $this->db->query("SELECT b.subject_requirement as 'requirement', d.id as 'professor_id', CONCAT(d.first_name,' ',d.middle_name,' ',d.last_name,' ',d.suffix_name) as 'professor_assigned_name' 
				FROM subject_submissions a 
				LEFT JOIN subject_deficiencies b on b.id = a.subject_deficiency_id
			    LEFT JOIN respective_professors c on c.id = b.respective_professor_id
			    LEFT JOIN users d on d.id = c.professor_id				
			    WHERE a.id = ".$id);

	        return $query->getRow();
	    }

	    public function submissionDetail2($id) //using def_id
	    {
	    	$query = $this->db->query("SELECT b.subject_requirement as 'requirement', d.id as 'professor_id', CONCAT(d.first_name,' ',d.middle_name,' ',d.last_name,' ',d.suffix_name) as 'professor_assigned_name', CONCAT(h.first_name,' ',h.middle_name,' ',h.last_name,' ',h.suffix_name) as 'student_name', h.id as 'student_user_id', a.id as 'submission_id', CONCAT(i.subject,'(',i.code,')') as 'subject'
				FROM subject_submissions a 
				LEFT JOIN subject_deficiencies b on b.id = a.subject_deficiency_id
			    LEFT JOIN respective_professors c on c.id = b.respective_professor_id
			    LEFT JOIN users d on d.id = c.professor_id
			    LEFT JOIN graduation_clearances e on e.id = c.graduation_clearance_id
                LEFT JOIN clearance_forms f on f.id = e.form_id
                LEFT JOIN students g on g.id = f.student_id
                LEFT JOIN users h on h.id = g.user_id
                LEFT JOIN subjects i on i.id = c.subject_id	
			    WHERE a.deleted = 0 AND b.id = ".$id);

	        return $query->getRow();
	    }
	}
?>