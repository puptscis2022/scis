<?php

namespace App\Controllers;
use App\Libraries\Hash;
use App\Libraries\Reports;

use TCPDF;

class Requirements extends BaseController
{
	public function __construct(){
		helper('ScisSystem');
	}

	public function list($id)
	{
		\ScisSystem::refreshData1();

		$permission_model = new \App\Models\PermissionsModel();

		$session = session();

		$data = [
			'page_title' => 'Clearance Fields | PUPT SCIS',
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
		
		if($permission_model->hasPermission($uID,['view_requirements']))
		{
			$cFieldID_PosID = explode("-",$id);
			$cFieldID = $cFieldID_PosID[0];

			$db = \Config\Database::connect();
			$scis_model = new \App\Models\ScisModel($db);
            $cField_model = new \App\Models\ClearanceFieldsModel();
            $co_req_list = $scis_model->getRequirementsList($cFieldID);

			$user_co_fields = $session->get('user_co_fields');

			// $CF_model = new \App\Models\ClearanceFieldsModel();
			// $clearanceFields = $CF_model->where('deleted',0)->findAll();
			
			// $user_fields = array();
			// $array_count = 0;
			// foreach($user_co_fields as $row) //Getting Administrator Field dropdown
			// {
			// 	$data['id'] = $row['field_id'];
			// 	$data['name'] = $row['field_name'];
			// 	$user_fields[$array_count] = $data;
			// 	$array_count += 1;
			// }

			$fileType_model = new \App\Models\FileTypesModel();
			$fileTypes = $fileType_model->where('deleted',0)->findAll();

            $fieldData = $cField_model->find($cFieldID);
            $fieldName = $fieldData['field_name'];

			$data['Requirements'] = $co_req_list;
			$data['userFields']	= $user_co_fields;
			$data['FileTypes'] = $fileTypes;
			$data['cField'] = $id;
            $data['fieldName'] = $fieldName;

			$data['AddRequirements'] = ($permission_model->hasPermission($uID,'add_requirements')) ? TRUE : FALSE ;
			$data['EditRequirements'] = ($permission_model->hasPermission($uID,'edit_requirements')) ? TRUE : FALSE ;
			$data['DeleteRequirements'] = ($permission_model->hasPermission($uID,'delete_requirements')) ? TRUE : FALSE ;

			return view('manage_requirements', $data);
		}
		else
		{
			return view('site/no_permission', $data);
		}
	}

	public function newRequirement()
	{
		$session = session();
		
		$req_model = new \App\Models\RequirementsModel();

		$addedReq = $this->request->getPOST('reqName');
		$cFieldID_PosID = explode("-",$this->request->getPOST('reqFieldID'));
		$cFieldID = $cFieldID_PosID[0];

		$data = [
			'requirement_name' => $addedReq, 
			'clearance_field_id' => $cFieldID, 
			'deleted' => 0,
		];

		$check_req_exist = $req_model->where($data)->first();

		$error = array();
		if(!empty($check_req_exist))
		{
			$session->setFlashdata('err_messages',["Requirement Already Exist for this Field"]);
		}
		else
		{
			$data['submission_type'] = $this->request->getPOST('SubmissionType');
			$data['file_type_id'] = $this->request->getPOST('FileType');
			$data['instruction'] = $this->request->getPOST('reqIns');
			$req_model->skipValidation(false);
			if($req_model->insert($data) == false)
			{
				$errors = $req_model->errors();
				$session->setFlashdata('err_messages',$errors);							
			}
			else
			{
				$cField_model = new \App\Models\ClearanceFieldsModel();

				$cField = $cField_model->find($cFieldID);

				$session->setFlashdata('success_messages',["<b>".$addedReq."</b> Successfully Added in ".$cField['field_name']]);

				//Log Activity
				$log_user = $session->get('user_id');
				$log_message = 'Added a Requirement : '.$this->request->getPOST('reqName');

				\ScisSystem::logActivity($log_user,$log_message);
			}				
		}

		return redirect()->to('/Requirements/list/'.$cFieldID)->withInput();
	}

	public function editRequirement()
	{
		$session = session();
		
		$req_model = new \App\Models\RequirementsModel();
		$id = $this->request->getPOST('reqID');

		$requirement = $req_model->find($id);

		date_default_timezone_set('Asia/Manila');
		$modify_stamp = date('Y-m-d H:i:s', time());

		$submission_type = $this->request->getPOST('EditSubmissionType');
		$file_type_id = $this->request->getPOST('EditFileType');
		if($submission_type == 0)
		{
			$file_type_id = 0;
		}

		$editedReq = $this->request->getPOST('reqName');

		$data = [
			'requirement_name' => $editedReq, 
			'clearance_field_id' => $this->request->getPOST('reqFieldID'),
			'submission_type'=> $submission_type,
			'file_type_id' => $file_type_id,
			'instruction'	=> $this->request->getPOST('reqIns'),
			'deleted' => 0, 
		];

		$check_req_exist = $req_model->where($data)->first();

		$error = array();
		if(!empty($check_req_exist))
		{
			$session->setFlashdata('err_messages',["Requirement Already Exist for this Field"]);
		}
		else
		{
			$data['modified'] = $modify_stamp;
			$req_model->skipValidation(false);

			if($req_model->update($id,$data) == false)
			{
				$errors = $req_model->errors();
				$session->setFlashdata('err_messages',$errors);
			}
			else
			{
				$cField_model = new \App\Models\ClearanceFieldsModel();

				$cField = $cField_model->find($requirement['clearance_field_id']);

				$session->setFlashdata('success_messages',["<b>".$editedReq."</b> Successfully Edited in ".$cField['field_name']]);

				//Log Activity
				$log_user = $session->get('user_id');
				$log_message = 'Edited a Requirement : '.$this->request->getPOST('reqName');

				\ScisSystem::logActivity($log_user,$log_message);
			}
		}

		return redirect()->to('/Requirements/list/'.$this->request->getPOST('reqFieldID'))->withInput();
	}

	public function deleteRequirement($id = '')
	{
		$session = session();
		
		if($id != '')
		{
			$req_model = new \App\Models\RequirementsModel();

			$req = $req_model->find($id);

			date_default_timezone_set('Asia/Manila');
			$delete_stamp = date('Y-m-d H:i:s', time());

			$data = [
				'deleted' => 1,
				'deleted_date' => $delete_stamp,
			];
			
			$requirement = $req_model->where('id',$id)->first();
			$req_model->update($id,$data);

			//Log Activity
			$log_user = $session->get('user_id');
			$log_message = 'Deleted a Requirement : '.$requirement['requirement_name'];

			\ScisSystem::logActivity($log_user,$log_message);

			$cField_model = new \App\Models\ClearanceFieldsModel();

			$cField = $cField_model->find($req['clearance_field_id']);

			$session->setFlashdata('success_messages',["<b>".$req['requirement_name']."</b> Successfully Deleted in ".$cField['field_name']]);
		}

		return redirect()->back()->withInput();
	}

}