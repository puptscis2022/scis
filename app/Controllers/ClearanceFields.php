<?php

namespace App\Controllers;
use App\Libraries\Hash;
use App\Libraries\Reports;

use TCPDF;

class ClearanceFields extends BaseController
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
		
		if($permission_model->hasPermission($uID,['view_clearance_fields']))
		{
			$db = \Config\Database::connect();
			$scis_model = new \App\Models\ScisModel($db);
			$cField_list = $scis_model->getClearanceFieldsList();

			$CT_model = new \App\Models\ClearanceTypesModel();
			$clearanceTypes = $CT_model->where('deleted',0)->findAll();

			$data['ClearanceFields'] = $cField_list;
			$data['ClearanceTypes']	= $clearanceTypes;

			$data['AddClearanceFields'] = ($permission_model->hasPermission($uID,'add_clearance_fields')) ? TRUE : FALSE ;
			$data['EditClearanceFields'] = ($permission_model->hasPermission($uID,'edit_clearance_fields')) ? TRUE : FALSE ;
			$data['DeleteClearanceFields'] = ($permission_model->hasPermission($uID,'delete_clearance_fields')) ? TRUE : FALSE ;

			return view('admin/manage_clearance_fields', $data);
		}
		else
		{
			return view('site/no_permission', $data);
		}
	}

	public function newCField()
	{
		$session = session();

		$CFields_model = new \App\Models\ClearanceFieldsModel();

		$addedField = $this->request->getPOST('fieldName');

		$data = [
			'field_name' => $addedField,
			'clearance_type_id'=> $this->request->getPOST('fieldType'),
			'deleted' => 0,
		];

		$check_cField_exist = $CFields_model->where($data)->first();

		$error = array();
		if(!empty($check_cField_exist))
		{
			$session->setFlashdata('err_messages',["Clearance Field Already Exist"]);
		}
		else
		{
			$data["description"] = $this->request->getPOST('fieldDesc'); 
			$CFields_model->skipValidation(false);
			if($CFields_model->insert($data) == false)
			{
				$errors = $CFields_model->errors();
				$session->setFlashdata('err_messages',$errors);
							
			}
			else
			{
				$session->setFlashdata('success_messages',["<b>".$addedField."</b> Successfully added"]);

				//Log Activity
				$log_user = $session->get('user_id');
				$log_message = 'Added a Clearance Field : '.$this->request->getPOST('fieldName');

				\ScisSystem::logActivity($log_user,$log_message);
			}				
		}

		return redirect()->to('/ClearanceFields')->withInput();
	}

	public function editCField()
	{
		$session = session();

		$CFields_model = new \App\Models\ClearanceFieldsModel();
		$id = $this->request->getPOST('fieldID');

		$editedField = $this->request->getPOST('fieldName');

		$data = [
			'field_name' => $editedField, 
			'clearance_type_id'=> $this->request->getPOST('fieldType'),
			'deleted' => 0,
		];

		$check_cField_exist = $CFields_model->where($data)->first();

		$error = array();
		if(!empty($check_cField_exist) && $check_cField_exist['id'] != $id) 
		{
			$session->setFlashdata('err_messages',["Clearance Field Already Exist"]);
		}
		else
		{
			$data["description"] = $this->request->getPOST('fieldDesc');
			$CFields_model->skipValidation(false);
			if($CFields_model->update($id,$data) == false)
			{
				$errors = $CFields_model->errors();
				$session->setFlashdata('err_messages',$errors);
			}
			else
			{
				$session->setFlashdata('success_messages',["<b>".$editedField."</b> Successfully edited"]);

				//Log Activity
				$log_user = $session->get('user_id');
				$log_message = 'Edited a Clearance Field : '.$editedField;

				\ScisSystem::logActivity($log_user,$log_message);
			}
		}

		return redirect()->to('/ClearanceFields')->withInput();
	}

	public function deleteCField($id = '')
	{
		$session = session();
		
		if($id != '')
		{
			$CFields_model = new \App\Models\ClearanceFieldsModel();

			date_default_timezone_set('Asia/Manila');
			$delete_stamp = date('Y-m-d H:i:s', time());

			$data = [
				'deleted' => 1,
				'deleted_date' => $delete_stamp,
			];
			
			$cField = $CFields_model->where('id',$id)->first();
			$CFields_model->update($id,$data);

			$Pos_model = new \App\Models\PositionsModel();
			$Positions = $Pos_model->select('id')->where('clearance_field_id',$id)->findALL();

			$Pos_builder = $Pos_model->builder();
			$Pos_builder->set($data)->where('clearance_field_id',$id)->update();

			$coPos_model = new \App\Models\ClearanceOfficerPositionsModel();
			$coPos_builder = $coPos_model->builder();
			foreach ($Positions as $row) {
				$coPos_builder->set($data)->where('position_id',$row['id'])->update();
			}

			$session->setFlashdata('success_messages',["<b>".$cField['field_name']."</b> Successfully Deleted"]);
				
			//Log Activity
			$log_user = $session->get('user_id');
			$log_message = 'Deleted a Clearance Field : '.$cField['field_name'];

			\ScisSystem::logActivity($log_user,$log_message);
		}

		return redirect()->to('/ClearanceFields')->withInput();
	}
}