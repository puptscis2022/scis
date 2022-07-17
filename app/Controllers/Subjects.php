<?php

namespace App\Controllers;
use App\Libraries\Hash;
use App\Libraries\Reports;

use TCPDF;

class Subjects extends BaseController
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
			'page_title' => 'Subjects | PUPT SCIS',
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
		
		if($permission_model->hasPermission($uID,['view_subjects']))
		{
			$subject_model = new \App\Models\SubjectsModel();
			$subject_list = $subject_model->where('deleted',0)->findAll();
			
			$data['Subjects'] = $subject_list;

			$data['AddSubjects'] = ($permission_model->hasPermission($uID,'add_subjects')) ? TRUE : FALSE ;
			$data['EditSubjects'] = ($permission_model->hasPermission($uID,'edit_subjects')) ? TRUE : FALSE ;
			$data['DeleteSubjects'] = ($permission_model->hasPermission($uID,'delete_subjects')) ? TRUE : FALSE ;

			return view('admin/manage_subjects', $data);
		}
		else
		{
			return view('site/no_permission', $data);
		}
	}

	public function newSubject()
	{
		$session = session();
		
		$subject_model = new \App\Models\SubjectsModel();

		$addedSubject = $this->request->getPOST('subjectCode');

		$data = [
			'code' => $addedSubject, 
			'deleted' => 0,
		];

		$check_subject_exist = $subject_model->where($data)->first();

		$error = array();
		if(!empty($check_subject_exist))
		{
			$session->setFlashdata('err_messages',["Subject Already Exist"]);
		}
		else
		{
			$data["subject"] = $this->request->getPOST('subjectDesc');

			$subject_model->skipValidation(false);
			if($subject_model->insert($data) == false)
			{
				$errors = $subject_model->errors();
				$session->setFlashdata('err_messages',$errors);
			}
			else
			{
				$session->setFlashdata('success_messages',["<b>".$addedSubject."</b> Successfully Added"]);

				//Log Activity
				$log_user = $session->get('user_id');
				$log_message = 'Added a Subject : '.$addedSubject;

				\ScisSystem::logActivity($log_user,$log_message);
			}				
		}

		return redirect()->to('/Subjects')->withInput();
	}

	public function editSubject()
	{
		$session = session();
		
		$subject_model = new \App\Models\SubjectsModel();
		$id = $this->request->getPOST('subjectID');

		$editedSubject = $this->request->getPOST('subjectCode');

		date_default_timezone_set('Asia/Manila');
		$modify_stamp = date('Y-m-d H:i:s', time());

		$data = [
			'code' => $editedSubject,
			'deleted' => 0,
		];

		$check_subject_exist = $subject_model->where($data)->first();

		$error = array();
		if(!empty($check_subject_exist) && $check_subject_exist['id'] != $id)
		{
			$session->setFlashdata('err_messages',["Subject Already Exist"]);
		}
		else
		{
			$data["subject"] = $this->request->getPOST('subjectDesc');
			$data['modified'] = $modify_stamp;
			
			$subject_model->skipValidation(false);
			if($subject_model->update($id,$data) == false)
			{
				$errors = $subject_model->errors();
				$session->setFlashdata('err_messages',$errors);
								
			}
			else
			{
				$session->setFlashdata('success_messages',["<b>".$editedSubject."</b> Successfully Edited"]);

				//Log Activity
				$log_user = $session->get('user_id');
				$log_message = 'Edited a Subject : '.$editedSubject;

				\ScisSystem::logActivity($log_user,$log_message);
			}
		}

		return redirect()->to('/Subjects')->withInput();

	}

	public function deleteSubject($id = '')
	{
		$session = session();
		
		if($id != '')
		{
			$subject_model = new \App\Models\SubjectsModel();

			date_default_timezone_set('Asia/Manila');
			$delete_stamp = date('Y-m-d H:i:s', time());

			$data = [
				'deleted' => 1,
				'deleted_date' => $delete_stamp,
			];
			
			$deleted_subject = $subject_model->where('id',$id)->first();
			$subject_model->update($id,$data);

			$session->setFlashdata('success_messages',["<b>".$deleted_subject['code']."</b> Successfully Deleted"]);

			//Log Activity
			$log_user = $session->get('user_id');
			$log_message = 'Deleted a Subject : '.$deleted_subject['code'];

			\ScisSystem::logActivity($log_user,$log_message);
		}

		return redirect()->to('/Subjects')->withInput();
	}
}