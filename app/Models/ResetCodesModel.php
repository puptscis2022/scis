<?php 

	namespace App\Models;

	use CodeIgniter\Model;

	class ResetCodesModel extends Model{

		protected $table = 'reset_codes';
		protected $primaryKey = 'id';
		
		protected $useAutoIncrement = true;

	    protected $returnType     = 'array';
	    //protected $useSoftDeletes = true;

	    protected $allowedFields = ["user_id","code","created","modified","deleted","deleted_date"];

	    //protected $useTimestamps = true;
	    //protected $createdField  = 'created';
	    //protected $updatedField  = 'modified';
	    //protected $deletedField  = 'deleted_date';

	    //for validating DATA
	    protected $validationRules    = [];
	    protected $validationMessages = [];
	    protected $skipValidation     = true;
	}
?>