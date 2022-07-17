<?php 

	namespace App\Models;

	use CodeIgniter\Model;

	class RespectiveProfessorsModel extends Model{

		protected $table = 'respective_professors';
		protected $primaryKey = 'id';
		
		protected $useAutoIncrement = true;

	    protected $returnType     = 'array';
	    //protected $useSoftDeletes = true;

	    protected $allowedFields = ["graduation_clearance_id","professor_id", "subject_id", "signature", "days", "time","created", "modified", "deleted", "deleted_date"];

	    //protected $useTimestamps = true;
	    //protected $createdField  = 'created';
	    //protected $updatedField  = 'modified';
	    //protected $deletedField  = 'deleted_date';

	    //for validating DATA
	    // protected $validationRules    = [];
	    // protected $validationMessages = [];
	    // protected $skipValidation     = false;

	    public function getList($gForm_id) //get List Using Graduation Clearance ID
	    {
	    	$query = $this->db->query("SELECT a.id as 'entry_id', c.code as 'sub_code', c.subject as 'sub_name', CONCAT(b.first_name,' ',b.middle_name,' ',b.last_name) as 'professor_name', a.days, a.time, a.signature as 'status'
					FROM respective_professors a
					LEFT JOIN users b on b.id = a.professor_id
					LEFT JOIN subjects c on c.id = a.subject_id
					WHERE a.graduation_clearance_id = ".$gForm_id);

	        return $query->getResult();
	    }

	    public function getList2($cForm_id) //get List Using Clearance Form ID
	    {
	    	$query = $this->db->query("SELECT a.id as 'id', c.code as 'sub_code', c.subject as 'sub_name', CONCAT(b.first_name,' ',b.middle_name,' ',b.last_name) as 'professor_name', a.days, a.time, a.signature as 'status'
					FROM respective_professors a
					LEFT JOIN users b on b.id = a.professor_id
					LEFT JOIN subjects c on c.id = a.subject_id
					LEFT JOIN graduation_clearances d on a.graduation_clearance_id = d.id
					LEFT JOIN clearance_forms e on d.form_id = e.id
					WHERE e.id = ".$cForm_id);

	        return $query->getResult();
	    }

	    public function getStudentEntryList($prof_id, $status = 0, $course = FALSE)
	    {
	    	$string = "SELECT CONCAT(e.first_name,' ',e.middle_name,' ',e.last_name) as 'student_name', d.student_number AS 'student_number', d1.abbreviation as 'course', CONCAT(f.code,' | ',f.subject) as 'subject', a.signature as 'status', f.id as 'sub_id', f.subject as 'sub_name',a.id as 'entry_id'
				FROM respective_professors a
			    LEFT JOIN graduation_clearances b on b.id = a.graduation_clearance_id
			    LEFT JOIN clearance_forms c on c.id = b.form_id
			    LEFT JOIN students d on d.id = c.student_id
			    LEFT JOIN courses d1 on d1.id = d.course_id
			    LEFT JOIN users e on e.id = d.user_id
			    LEFT JOIN subjects f on f.id = a.subject_id
			    WHERE a.deleted = 0 AND b.deleted = 0 AND c.deleted = 0 AND b.status = 1 AND c.clearance_status = ".$status." AND a.professor_id = ".$prof_id;

			if($course)
			{
				$string .= " AND d1.id = ".$course;
			}

			$query = $this->db->query($string);

	        return $query->getResult();
	    }

	    public function getStudentInfo($entID)
	    {
	    	$string = "SELECT CONCAT(e.first_name,' ',e.middle_name,' ',e.last_name) as 'student_name', d.student_number AS 'student_number', d1.abbreviation as 'course', CONCAT(f.code,' | ',f.subject) as 'subject', a.signature as 'status', f.id as 'sub_id', f.subject as 'sub_name',a.id as 'entry_id', d.year_level as 'year', g.type as 'studType', e.contact_no as 'contact'
				FROM respective_professors a
			    LEFT JOIN graduation_clearances b on b.id = a.graduation_clearance_id
			    LEFT JOIN clearance_forms c on c.id = b.form_id
			    LEFT JOIN students d on d.id = c.student_id
			    LEFT JOIN courses d1 on d1.id = d.course_id
			    LEFT JOIN users e on e.id = d.user_id
			    LEFT JOIN subjects f on f.id = a.subject_id
			    LEFT JOIN student_types g on g.id = d.student_type_id
			    WHERE a.id = ".$entID;

			$query = $this->db->query($string);

	        return $query->getRow();
	    }

	    public function getDeficiencies($entID)
	    {
	    	$query = $this->db->query("SELECT `id`,`subject_requirement` as 'requirement', `note`, `status`
				FROM `subject_deficiencies` 
			    WHERE `deleted` = 0 AND `respective_professor_id` = ".$entID);

	        return $query->getResult();
	    }

	    public function getEntryInfo($entID)
	    {
	    	$query = $this->db->query("SELECT b.subject as 'sub_name', b.code as 'sub_code'
				FROM respective_professors a
			    LEFT JOIN subjects b on b.id = a.subject_id
			    WHERE a.id = ".$entID);

	        return $query->getRow();
	    }
	}
?>