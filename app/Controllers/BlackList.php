<?php

namespace App\Controllers;

class BlackList extends BaseController
{
	public function __construct(){
		helper('ScisSystem');
	}

	public function index()
    {
        return redirect()->to('blacklist/list');
    }

    public function list($position_id = "")
    {
    	\ScisSystem::refreshData1();

		$permission_model = new \App\Models\PermissionsModel();

		$session = session();

		$data = [
			'page_title' => 'Black List | PUPT SCIS',
			'Name'			=> $session->get('user_name'),
		    'profilePic'			=> $session->get('user_pic'),
		    'clearanceData'			=> $session->get('clearance_data'),
		    'activeClearance' => $session->get('ongoingClearance'),
		    'user_title'	=> $session->get('title'),
		    'user_notifications' => $session->get('user_notifications'),
		    'user_fields' => $session->get('user_co_fields'),
			    'user_gradFields' => $session->get('user_co_gradFields'),
			    'user_subject_resp' => $session->get('user_subject_handled'),	
		];

		$uID = $session->get('user_id');
		
		if($permission_model->hasPermission($uID,['view_black_list']))
		{
        	if(empty($position_id))
        	{
        		echo "Invalid Link";
        		echo "<br><a href='/pupt_scis'>Back</a>";
        	}
        	else
        	{
        		$db = \Config\Database::connect();
				$scis_model = new \App\Models\ScisModel($db);
                $cField_model = new \App\Models\ClearanceFieldsModel();
                $courses_model = new \App\Models\CoursesModel();
                $pos_model = new \App\Models\PositionsModel();

                $posData = $pos_model->find($position_id);
                $fieldData = $cField_model->find($posData['clearance_field_id']);

        		$list = $scis_model->getBlackList($position_id,$session->get('user_org'));

        		$students = $scis_model->getStudents("","",$session->get('user_org'));

        		$req = $scis_model->getRequirementsList($fieldData['id']);

        		$data['courses'] = $courses_model->where('deleted',0)->findAll();
	            $data['field_name'] = $fieldData['field_name'];
	            $data['pos_name'] = $posData['position_name'];
	            $data['blacklist'] = $list;
	            $data['students'] = $students;
	            $data['requirements'] = $req;
	            $data['posID'] = $position_id;

	            $data['AddBlackList'] = ($permission_model->hasPermission($uID,'add_black_list')) ? TRUE : FALSE ;
				$data['EditBlackList'] = ($permission_model->hasPermission($uID,'edit_black_list')) ? TRUE : FALSE ;
				$data['DeleteBlackList'] = ($permission_model->hasPermission($uID,'delete_black_list')) ? TRUE : FALSE ;

	            return view('/black_list' ,$data);
	        }
        }
        else
        {
            return view('site/no_permission' ,$data);
        }
    }

    public function studentsList($course,$yearLevel)
    {
    	$db = \Config\Database::connect();
		$scis_model = new \App\Models\ScisModel($db);

		$course = ($course == "all") ? FALSE : $course;
		$yearLevel = ($yearLevel == "all") ? FALSE : $yearLevel;

		$students = $scis_model->getStudents($course,$yearLevel);

		$options = "";
		foreach($students as $stud) { 
			$options .= " <option value='".$stud->id."' data-subtext='".$stud->course." ".$stud->year."-1'>".$stud->student_name."</option>";
		} 

		echo "This is it";
		echo "$('#student').html(".$options.");";
    }

    public function addStudent()
    {
		$session = session();
        
        $fieldID = $this->request->getPOST('fieldID');
    	$students = $this->request->getPOST('students');
    	$deficiencies = $this->request->getPOST('deficiencies');

    	$blacklist_model = new \App\Models\BlackListModel();

    	$existing_list = $blacklist_model->where('deleted',0)->findAll();

    	$exist = false;

    	foreach($existing_list as $list)
    	{
    		if($list['student_id'] == $students &&	$list['clearance_field_id'] == $fieldID && $list['requirement_id'] == $deficiencies )
    		{
    			$exist = true;
    		}

    		if($list['student_id'] == "0" &&	$list['clearance_field_id'] == $fieldID && $list['requirement_id'] == $deficiencies )
    		{
    			$exist = true;
    		}
    	}

    	if($exist)
    	{
			$session->setFlashdata('err_messages',['Student Was Already Listed']);
    	}
    	else
    	{
    		$data = [
	    		'student_id' => $students,
	    		'clearance_field_id' => $fieldID,
	    		'requirement_id' => $deficiencies,
	    	];

	    	if($blacklist_model->insert($data) == false)
	    	{
	    		$errors = $blacklist_model->errors();
				$session->setFlashdata('err_messages',$errors);
	    	}
	    	else
	    	{
	    		$session->setFlashdata('success_messages',['Student Successfully Listed']);

	    		$addedBlackList = $blacklist_model->getInsertID();

	    		//Log Activity
	    		$blacklistDetail = $blacklist_model->getInfo($addedBlackList);

				$log_user = $session->get('user_id');
				$log_message = 'Listed a Student in BlackList: '.$blacklistDetail->student_name.' | '.$blacklistDetail->requirement.'['.$blacklistDetail->cField.']';

				\ScisSystem::logActivity($log_user,$log_message);
	    	}
    	}

    	return redirect()->to('/BlackList/list/'.$fieldID);
    }

    public function removeStudent($field_bl)
    {
		$session = session();
        
        $id = explode("-",$field_bl);
        $field_id = $id[0];
        $bl_id = $id[1];

        date_default_timezone_set('Asia/Manila');
		$deleted_stamp = date('Y-m-d H:i:s', time());

		$blacklist_model = new \App\Models\BlackListModel();

    	$data = [
			'deleted' => 1, 
			'deleted_date' => $deleted_stamp, 
		];

	    if($blacklist_model->update($bl_id,$data) == false)
	    {
	    	$errors = $blacklist_model->errors();
			$session->setFlashdata('err_messages',$errors);
	    }
	    else
	    {
	    	$session->setFlashdata('success_messages',['Student Successfully Removed from the List']);

	    	//Log Activity
	    	$blacklistDetail = $blacklist_model->getInfo($bl_id);

			$log_user = $session->get('user_id');
			$log_message = 'Remove a Student in BlackList: '.$blacklistDetail->student_name.' | '.$blacklistDetail->requirement.'['.$blacklistDetail->cField.']';

			\ScisSystem::logActivity($log_user,$log_message);
	    }

    	return redirect()->to('/BlackList/list/'.$field_id);
    }

}