<?php 

	namespace App\Models;

	use CodeIgniter\Model;

	class GraduationClearancesModel extends Model{

		protected $table = 'graduation_clearances';
		protected $primaryKey = 'id';
		
		protected $useAutoIncrement = true;

	    protected $returnType     = 'array';
	    //protected $useSoftDeletes = true;

	    protected $allowedFields = ["form_id","address","gender", "major_id", "admitted_scyear", "admitted_sem", "graduation_school_year_id", "date_of_birth", "elementary", "elementary_graduated_year", "highschool", "highschool_graduated_year", "certificate_of_candidacy", "note", "status" , "created","modified","deleted","deleted_date"];

	    //protected $useTimestamps = true;
	    //protected $createdField  = 'created';
	    //protected $updatedField  = 'modified';
	    //protected $deletedField  = 'deleted_date';

	    //for validating DATA
	    protected $validationRules    = [
	    	"form_id"	=> 'required',
	    	"address"	=> 'required',
	    	"gender"	=> 'required', 
	    	"major_id"	=> 'required', 
	    	"admitted_scyear"	=> 'required', 
	    	"admitted_sem"	=> 'required', 
	    	"date_of_birth"	=> 'required', 
	    	"elementary"	=> 'required', 
	    	"elementary_graduated_year"	=> 'required|integer|exact_length[4]', 
	    	"highschool"	=> 'required', 
	    	"highschool_graduated_year"	=> 'required|integer|exact_length[4]',
	    	"status" => 'required'
	    ];
	    protected $validationMessages = [];
	    protected $skipValidation     = false;

	    public function getList($status, $filters = [])
		{
			$query = $this->db->query("SELECT a.id as 'grad_form_id', a.status as 'application_status', b.id as 'clearance_form_id', CONCAT(d.last_name,', ',d.first_name,' ',d.middle_name) as 'student_name', c.student_number as 'student_id', e.abbreviation as 'course' 
					FROM graduation_clearances a 
					LEFT JOIN clearance_forms b on b.id = a.form_id 
					LEFT JOIN students c on c.id = b.student_id 
					LEFT JOIN users d on d.id = c.user_id
					LEFT JOIN courses e on e.id = c.course_id
					WHERE a.deleted = 0 AND a.status = ".$status);

	        return $query->getResult();
		}

		public function getForm($id)
		{
			$query = $this->db->query("SELECT a.id as 'grad_form_id', a.status as 'application_status', b.id as 'clearance_form_id', CONCAT(d.last_name,', ',d.first_name,' ',d.middle_name) as 'student_name', e.abbreviation as 'course', c.student_number, c.year_level, f.type as 'student_type', d.contact_no as 'contact', a.address, a.date_of_birth as 'dob', g.major, h.school_year as 'admitted_year', a.admitted_sem as 'admitted_term', a.elementary as 'elem', a.elementary_graduated_year as 'elem_year', a.highschool as 'hs', a.highschool_graduated_year as 'hs_year', a.certificate_of_candidacy as 'coc', d.id as 'student_id', d.email, a.gender, i.school_year as 'graduation_sy'
					FROM graduation_clearances a 
					LEFT JOIN clearance_forms b on b.id = a.form_id 
					LEFT JOIN students c on c.id = b.student_id 
					LEFT JOIN users d on d.id = c.user_id
					LEFT JOIN courses e on e.id = c.course_id
					LEFT JOIN student_types f on f.id = c.student_type_id
					LEFT JOIN majors g on g.id = a.major_id
					LEFT JOIN sc_years h on h.id = a.admitted_scyear
					LEFT JOIN sc_years i on i.id = a.graduation_school_year_id
					WHERE a.id = ".$id." ORDER BY a.id DESC");

	        return $query->getRow();
		}
	}
?>