<?php

namespace App\Controllers;

class RejectReasons extends BaseController
{
	public function __construct(){
		helper('ScisSystem');
	}


	public function index()
	{
		\ScisSystem::refreshData1();

		$session = session();
		if($session->get('logged_in'))
		{
			return redirect()->to('/RejectReasons/list/');
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function List($requirement_id) //field-position-requirement
	{
		\ScisSystem::refreshData1();

		$id = explode("-",$requirement_id);
		$goback_id = $id[0]."-".$id[1];
		$reqId = $id[2]; 

		$permission_model = new \App\Models\PermissionsModel();

		$session = session();

		$data = [
			'page_title' => 'Rejection Reasons | PUPT SCIS',
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
		
		if($permission_model->hasPermission($uID,['view_reject_reasons']) || TRUE)
		{
			$rejectReasons_model = new \App\Models\RejectReasonsModel();

			$rejectReasons_condition = [
				'deleted' => 0,
				'requirement_id' => $reqId,
			];
			$reasons = $rejectReasons_model->where($rejectReasons_condition)->orderBy('id','DESC')->findAll();

			$data['RejectionReasons'] = $reasons;
			$data['pageLinkData'] = $requirement_id;
			$data['backData'] = $goback_id;

			print_r($reasons);
			// return view('', $data);
		}
		else
		{
			return view('site/no_permission', $data);
		}
	}

	public function newReason()
	{
		$session = session();
		
		$rejectReasons_model = new \App\Models\RejectReasonsModel();

		$reason = $this->request->getPOST('rejectReason');
		$backLink = $this->require->getPOST('back');

		$data = [
			'requirement_id' = $this->request->getPOST("reqId"),
			'reason' = $reason,
			'deleted' => 0,
		];

		$check_reason_exist = $rejectReasons_model->where($data)->first();

		$error = array();
		if(!empty($check_reason_exist))
		{
			$session->setFlashdata('err_messages',["Reason Already Exist"]);
		}
		else
		{
			$rejectReasons_model->skipValidation(false);
			if($rejectReasons_model->insert($data) == false)
			{
				$errors = $rejectReasons_model->errors();
				$session->setFlashdata('err_messages',$errors);
			}
			else
			{
				$session->setFlashdata('success_messages',["<b>".$reason."</b> Successfully Added"]);

				//Log Activity
				$log_user = $session->get('user_id');
				$log_message = 'Added a Rejection Reason : '.$reason;

				\ScisSystem::logActivity($log_user,$log_message);
			}				
		}

		return redirect()->to('/RejectReasons/List/'.$backLink)->withInput();
	}

	public function editReason()
	{
		$session = session();
		
		$rejectReasons_model = new \App\Models\RejectReasonsModel();
		$id = $this->request->getPOST('rejectReasonID');

		$editedReason = $this->request->getPOST('Reason');
		$backLink = $this->require->getPOST('back');

		date_default_timezone_set('Asia/Manila');
		$modify_stamp = date('Y-m-d H:i:s', time());

		$reasonOldInfo = $rejectReasons_model->find($id);

		$data = [
			'requirement_id' => $reasonOldInfo['requirement_id'],
			'reason' => $editedReason,
			'deleted' => 0,
		];

		$check_reason_exist = $rejectReasons_model->where($data)->first();

		$error = array();
		if(!empty($check_reason_exist) && $check_reason_exist['id'] != $id)
		{
			$session->setFlashdata('err_messages',["Reason Already Exist"]);
		}
		else
		{
			$rejectReasons_model->skipValidation(false);
			if($rejectReasons_model->update($id,$data) == false)
			{
				$errors = $rejectReasons_model->errors();
				$session->setFlashdata('err_messages',$errors);
								
			}
			else
			{
				$session->setFlashdata('success_messages',["<b>".$editedReason."</b> Successfully Edited"]);

				//Log Activity
				$log_user = $session->get('user_id');
				$log_message = 'Edited a Reason : '.$editedReason;

				\ScisSystem::logActivity($log_user,$log_message);
			}
		}

		return redirect()->to('/RejectReasons/List/'.$backLink)->withInput();

	}

	public function deleteReason($deleteId = '') //id = reasonID + rejection reason page link 
	{
		$session = session();

		$id = explode("-",$deleteId);
		$reqId = $id[0]; 
		$goback_id = $id[1]."-".$id[2]."-".$id[3]; //field-position-requirement
		
		if($id != '')
		{
			$rejectReasons_model = new \App\Models\RejectReasonsModel();

			date_default_timezone_set('Asia/Manila');
			$delete_stamp = date('Y-m-d H:i:s', time());

			$data = [
				'deleted' => 1,
				'deleted_date' => $delete_stamp,
			];
			
			$deleted_reason = $rejectReasons_model->where('id',$id)->first();
			$rejectReasons_model->update($id,$data);

			$session->setFlashdata('success_messages',["<b>".$deleted_reason['reason']."</b> Successfully Deleted"]);

			//Log Activity
			$log_user = $session->get('user_id');
			$log_message = 'Deleted a Rejection Reason : '.$deleted_reason['reason'];

			\ScisSystem::logActivity($log_user,$log_message);
		}

		return redirect()->to('/RejectReasons/List/'.$backLink)->withInput();
	}
}
}