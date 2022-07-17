<?php 

	namespace App\Models;

	use CodeIgniter\Model;

	class PermissionsModel extends Model{

		protected $table = 'permissions';
		protected $primaryKey = 'id';
		
		protected $useAutoIncrement = true;

	    protected $returnType     = 'array';
	    //protected $useSoftDeletes = true;

	    protected $allowedFields = ["permission","created","modified","deleted","deleted_date"];

	    //protected $useTimestamps = true;
	    //protected $createdField  = 'created';
	    //protected $updatedField  = 'modified';
	    //protected $deletedField  = 'deleted_date';

	    //for validating DATA
	    // protected $validationRules    = [];
	    // protected $validationMessages = [];
	    // protected $skipValidation     = false;

	    public function hasPermission($user_id,$permission)
	    {
	    	$required_permissions = array();
	    	if(!is_array($permission))
	    	{
	    		$required_permissions[0] = $permission;
	    	}
	    	else
	    	{
	    		$required_permissions = $permission;
	    	}

	    	$lastPerm = end($required_permissions);

	    	$string1 = "SELECT a.user_id, b.permission 
            FROM user_permissions a
            LEFT JOIN permissions b on b.id = a.permission_id
            WHERE a.deleted = 0 AND b.deleted = 0 AND a.user_id = '".$user_id."' AND ( ";

            foreach($required_permissions as $perm)
            {
            	$string1 .= " b.permission = '".$perm."' ";

            	if($perm != $lastPerm)
            	{
            		$string1 .= " OR ";
            	}
            	else
            	{
            		$string1 .= " ); ";
            	}
            }
            

            $query1 = $this->db->query($string1);

            $string2 = "SELECT a.id, a.username, e.permission 
            FROM users a
            LEFT JOIN user_roles b on b.user_id = a.id
            LEFT JOIN roles c on c.id = b.role_id
            LEFT JOIN role_permissions d on d.role_id = c.id 
            LEFT JOIN permissions e on e.id = d.permission_id
            WHERE b.deleted = 0 AND c.deleted = 0 AND d.deleted = 0 AND e.deleted = 0 AND a.id = '".$user_id."' AND ( ";

            foreach($required_permissions as $perm)
            {
            	$string2 .= " e.permission = '".$perm."' ";

            	if($perm != $lastPerm)
            	{
            		$string2 .= " OR ";
            	}
            	else
            	{
            		$string2 .= " ); ";
            	}
            }

            $query2 = $this->db->query($string2);
                  
       		if(!empty($query1->getRow()) || !empty($query2->getRow()))
       		{
       			return TRUE;
       		}
       		else
       		{
       			return FALSE;
       		}
	    }
	}
?>