<?php

namespace App\Controllers;
use App\Libraries\Hash;
use App\Libraries\Reports;

use TCPDF;

class Majors extends BaseController
{
	public function __construct(){
		helper('ScisSystem');
	}

	public function index()
	{
		\ScisSystem::refreshData1();

		$permission_model = new \App\Models\PermissionsModel();

		$session = session();

		$data = [
			'page_title' => 'Majors | PUPT SCIS',
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
		
		if($permission_model->hasPermission($uID,['view_majors']))
		{
			$db = \Config\Database::connect();
			$scis_model = new \App\Models\ScisModel($db);
			$major_list = $scis_model->getMajorsList();

			$course_model = new \App\Models\CoursesModel();
			$course_list = $course_model->where('deleted',0)->findAll();
			
			$data['Majors'] = $major_list;
			$data['Courses'] = $course_list;

			$data['AddMajors'] = ($permission_model->hasPermission($uID,'add_majors')) ? TRUE : FALSE ;
			$data['EditMajors'] = ($permission_model->hasPermission($uID,'edit_majors')) ? TRUE : FALSE ;
			$data['DeleteMajors'] = ($permission_model->hasPermission($uID,'delete_majors')) ? TRUE : FALSE ;

			return view('admin/manage_majors', $data);
		}
		else
		{
			return view('site/no_permission', $data);
		}
	}

	public function newMajor()
	{
		$session = session();
		
		$major_model = new \App\Models\MajorsModel();

		$addedMajor = $this->request->getPOST('majorName');

		$data = [
			'major' => $addedMajor, 
			'deleted' => 0,
		];

		$check_major_exist = $major_model->where($data)->first();

		$error = array();
		if(!empty($check_major_exist))
		{
			$session->setFlashdata('err_messages',["Major Already Exist"]);
		}
		else
		{
			$data["course_id"] = $this->request->getPOST('course');

			$major_model->skipValidation(false);
			if($major_model->insert($data) == false)
			{
				$errors = $major_model->errors();
				$session->setFlashdata('err_messages',$errors);
			}
			else
			{
				$session->setFlashdata('success_messages',["<b>".$addedMajor."</b> Successfully Added"]);

				//Log Activity
				$log_user = $session->get('user_id');
				$log_message = 'Added a Major : '.$addedMajor;

				\ScisSystem::logActivity($log_user,$log_message);
			}				
		}

		return redirect()->to('/Majors')->withInput();
	}

	public function editMajor()
	{
		$session = session();
		
		$major_model = new \App\Models\MajorsModel();
		$id = $this->request->getPOST('majorID');

		$editedMajor = $this->request->getPOST('majorName');

		date_default_timezone_set('Asia/Manila');
		$modify_stamp = date('Y-m-d H:i:s', time());

		$data = [
			'major' => $editedMajor,
			'deleted' => 0,
		];

		$check_major_exist = $major_model->where($data)->first();

		$error = array();
		if(!empty($check_major_exist) && $check_major_exist['id'] != $id)
		{
			$session->setFlashdata('err_messages',["Major Already Exist"]);
		}
		else
		{
			$data["course_id"] = $this->request->getPOST('course');
			$data['modified'] = $modify_stamp;
			
			$major_model->skipValidation(false);
			if($major_model->update($id,$data) == false)
			{
				$errors = $major_model->errors();
				$session->setFlashdata('err_messages',$errors);
								
			}
			else
			{
				$session->setFlashdata('success_messages',["<b>".$editedMajor."</b> Successfully Edited"]);

				//Log Activity
				$log_user = $session->get('user_id');
				$log_message = 'Edited a Major : '.$editedMajor;

				\ScisSystem::logActivity($log_user,$log_message);
			}
		}

		return redirect()->to('/Majors')->withInput();

	}

	public function deleteMajor($id = '')
	{
		$session = session();
		
		if($id != '')
		{
			$major_model = new \App\Models\MajorsModel();

			date_default_timezone_set('Asia/Manila');
			$delete_stamp = date('Y-m-d H:i:s', time());

			$data = [
				'deleted' => 1,
				'deleted_date' => $delete_stamp,
			];
			
			$deleted_major = $major_model->where('id',$id)->first();
			$major_model->update($id,$data);

			$session->setFlashdata('success_messages',["<b>".$deleted_major['major']."</b> Successfully Deleted"]);

			//Log Activity
			$log_user = $session->get('user_id');
			$log_message = 'Deleted a Major : '.$deleted_major['major'];

			\ScisSystem::logActivity($log_user,$log_message);
		}

		return redirect()->to('/Majors')->withInput();
	}
}