<?php

namespace App\Controllers;
use App\Libraries\Hash;
use App\Libraries\Reports;

use TCPDF;

class Positions extends BaseController
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
			'page_title' => 'Field Positions | PUPT SCIS',
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
		
		if($permission_model->hasPermission($uID,['view_positions']))
		{
			$db = \Config\Database::connect();
			$scis_model = new \App\Models\ScisModel($db);
			$position_list = $scis_model->getPositionsList();

			$organizationOfficers = $scis_model->getOrganizationOfficersList();

			$organization_model = new \App\Models\StudentOrganizationsModel();
			$organizations =  $organization_model->where('deleted',0)->findAll();

			$CF_model = new \App\Models\ClearanceFieldsModel();
			$clearanceFields = $CF_model->where('deleted',0)->findAll();

			$clearanceOfficers = $scis_model->getRoleUsers(2);

			$data['Positions'] = $position_list;
			$data['ClearanceFields'] = $clearanceFields;
			$data['organizations'] = $organizations;
			$data['orgOfficers'] = $organizationOfficers;
			$data['ClearanceOfficers'] = $clearanceOfficers;

			$data['AddPositions'] = ($permission_model->hasPermission($uID,'add_positions')) ? TRUE : FALSE ;
			$data['EditPositions'] = ($permission_model->hasPermission($uID,'edit_positions')) ? TRUE : FALSE ;
			$data['DeletePositions'] = ($permission_model->hasPermission($uID,'delete_positions')) ? TRUE : FALSE ;

			$data['AddClearanceFieldOfficers'] = ($permission_model->hasPermission($uID,'add_clearance_officer_positions')) ? TRUE : FALSE ;
			$data['EditClearanceFieldOfficers'] = ($permission_model->hasPermission($uID,'edit_clearance_officer_positions')) ? TRUE : FALSE ;

			return view('admin/manage_positions', $data);
		}
		else
		{
			return view('site/no_permission', $data);
		}
	}

	public function newPosition()
	{
		$session = session();
		
		$position_model = new \App\Models\PositionsModel();

		$addedPosition = $this->request->getPOST('posName');
		$cFieldID = $this->request->getPOST('posField');

		$data = [
			'position_name' => $addedPosition, 
			'clearance_field_id' =>  $cFieldID,
			'deleted' => 0, 
		];

		$check_position_exist = $position_model->where($data)->first();

		$error = array();
		if(!empty($check_position_exist))
		{
			$session->setFlashdata('err_messages',["Position Already Exist for this Field"]);
		}
		else
		{
			$position_model->skipValidation(false);
			if($position_model->insert($data) == false)
			{
				$errors = $position_model->errors();
				$session->setFlashdata('err_messages',$errors);
			}
			else
			{
				$cField_model = new \App\Models\ClearanceFieldsModel();

				$cField = $cField_model->find($cFieldID);

				$session->setFlashdata('success_messages',["<b>".$addedPosition."</b> Successfully Added in ".$cField["field_name"]]);

				//Log Activity
				$log_user = $session->get('user_id');
				$log_message = 'Added a Position : '.$this->request->getPOST('posName');

				\ScisSystem::logActivity($log_user,$log_message);
			}
		}

		return redirect()->to('/Positions')->withInput();
		
	}

	public function editPosition()
	{
		$session = session();
		
		$position_model = new \App\Models\PositionsModel();
		$id = $this->request->getPOST('posID');

		$editedPosition = $this->request->getPOST('posName');
		$cFieldID = $this->request->getPOST('posFieldID');

		date_default_timezone_set('Asia/Manila');
		$modify_stamp = date('Y-m-d H:i:s', time());

		$data = [
			'position_name' => $editedPosition, 
			'clearance_field_id' =>  $cFieldID,
			'deleted' => 0,
		];

		$check_position_exist = $position_model->where($data)->first();

		$error = array();
		if(!empty($check_position_exist))
		{
			$session->setFlashdata('err_messages',["Position Already Exist for this Field"]);
		}
		else
		{
			$data["modified"] = $modify_stamp;
			$position_model->skipValidation(false);
			if($position_model->update($id,$data) == false)
			{
				$errors = $position_model->errors();
				$session->setFlashdata('err_messages',$errors);
			}
			else 
			{
				$cField_model = new \App\Models\ClearanceFieldsModel();

				$cField = $cField_model->find($cFieldID);

				$session->setFlashdata('success_messages',["<b>".$editedPosition."</b> Successfully Edited in ".$cField['field_name']]);

				//Log Activity
				$log_user = $session->get('user_id');
				$log_message = 'Edited a Position : '.$this->request->getPOST('posName');

				\ScisSystem::logActivity($log_user,$log_message);
			}
		}

		return redirect()->to('/Positions')->withInput();
	}

	public function deletePosition($id = '')
	{
		$session = session();
		
		if($id != '')
		{
			$position_model = new \App\Models\PositionsModel();

			$position = $position_model->find($id);

			$cFieldID = $position['clearance_field_id'];

			date_default_timezone_set('Asia/Manila');
			$delete_stamp = date('Y-m-d H:i:s', time());

			$data = [
				'deleted' => 1,
				'deleted_date' => $delete_stamp,
			];
			
			$deleted_position = $position_model->where('id',$id)->first();
			$position_model->update($id,$data);

			$coPos_model = new \App\Models\ClearanceOfficerPositionsModel();
			$coPos_builder = $coPos_model->builder();

			$coPos_builder->set($data)->where('position_id',$id)->update();

			//Log Activity
			$log_user = $session->get('user_id');
			$log_message = 'Deleted a Position : '.$deleted_position['position_name'];

			\ScisSystem::logActivity($log_user,$log_message);

			$cField_model = new \App\Models\ClearanceFieldsModel();

			$cField = $cField_model->find($cFieldID);

			$session->setFlashdata('success_messages',["<b>".$deleted_position['position_name']."</b> Successfully Deleted in ".$cField['field_name']]);
		}

		return redirect()->to('/Positions')->withInput();
			
	}
	//Maintenance | Assigning Position======================
	public function assignOfficerToPosition()
	{
		$session = session();
		
		$coPositions_model = new \App\Models\ClearanceOfficerPositionsModel();
		$users_model = new \App\Models\UsersModel();
		$Positions_model = new \App\Models\PositionsModel();

		$coID = $this->request->getPOST('clearanceofficer');
		$positionID = $this->request->getPOST('posID');

		$co = $users_model->find($coID);
		$position = $Positions_model->find($positionID); 
			
		$data = [
			'position_id' => $positionID, 
			'clearance_officer_id' => $coID, 
		];

		$coPositions_model->skipValidation(false);
		if($coPositions_model->insert($data) == false)
		{
			$errors = $coPositions_model->errors();
			$session->setFlashdata('err_messages',$errors);
		}
		else
		{
			$co_name = $co['first_name']." ".$co['middle_name']." ".$co['last_name']." ".$co['suffix_name'];
			$positionDetail = $Positions_model->getInfo($positionID);

			$session->setFlashdata('success_messages',["<b>".$co_name."</b> Successfully Assigned as ".$position['position_name']]);

			//Log Activity
			$log_user = $session->get('user_id');
			$log_message = 'Assigned officer for '.$position['position_name'].' : '.$co_name;

			\ScisSystem::logActivity($log_user,$log_message);

			//Create Notification
			$sender_id = $session->get('user_id');
			$Subject = "Clearance Officer Update";
			$Message = "Congratulations ".$co_name.", you have been assigned as the ".$position['position_name']." of ".$positionDetail->clearance_field.".";
			$receiver_id = $coID;

			\ScisSystem::CreateNotification($sender_id,$Subject,$Message,$receiver_id);
		}

		return redirect()->to('/Positions')->withInput();
	}

	public function assignOfficerToOrganizationPosition()
	{
		$session = session();
		
		$coPositions_model = new \App\Models\ClearanceOfficerPositionsModel();
		$users_model = new \App\Models\UsersModel();
		$Positions_model = new \App\Models\PositionsModel();
			
		$coID = $this->request->getPOST('clearanceofficer');
		$positionID = $this->request->getPOST('posID');
		$return = $this->request->getPOST('retID');

		$co = $users_model->find($coID);
		$position = $Positions_model->find($positionID); 

		$data = [
			'position_id' => $positionID, 
			'clearance_officer_id' => $coID, 
		];

		$coPositions_model->skipValidation(false);
		if($coPositions_model->insert($data) == false)
		{
			$errors = $coPositions_model->errors();
			$session->setFlashdata('err_messages',$errors);
		}
		else
		{
			$soo_model = new \App\Models\StudentOrganizationOfficersModel();
			$so_model = new \App\Models\StudentOrganizationsModel();

			$studentOrgID = $this->request->getPOST('orgID');

			$organization = $so_model->find($studentOrgID);
			
			$data2 = [
				'clearance_officer_id' => $coID, 
				'student_organization_id' => $studentOrgID, 
			];

			$soo_model->skipValidation(false);
			if($soo_model->insert($data2) == false)
			{
				$errors = $soo_model->errors();
				$session->setFlashdata('err_messages',$errors);
			}

			$co_name = $co['first_name']." ".$co['middle_name']." ".$co['last_name']." ".$co['suffix_name'];
			$positionDetail = $Positions_model->getInfo($positionID);

			$session->setFlashdata('success_messages',["<b>".$co_name."</b> Successfully Assigned as ".$position['position_name']." of ".$organization['organization_name']]);

			//Log Activity
			$log_user = $session->get('user_id');
			$log_message = 'Assigned officer for '.$position['position_name'].' ('.$organization['organization_name'].') : '.$co_name;

			\ScisSystem::logActivity($log_user,$log_message);

			//Create Notification
			$sender_id = $session->get('user_id');
			$Subject = "Clearance Period Extension";
			$Message = "Congratulations ".$co_name.", you have been assigned as the ".$position['position_name']." of ".$positionDetail->clearance_field.".";
			$receiver_id = $coID;

			\ScisSystem::CreateNotification($sender_id,$Subject,$Message,$receiver_id);
		}	

		return redirect()->to('/Positions/OrganizationsOfficers/'.$return)->withInput();

	}

	public function editPositionOfficer()
	{
		$session = session();
		
		$coPositions_model = new \App\Models\ClearanceOfficerPositionsModel();
		$id = $this->request->getPOST('coPosID');
		$coPos = $coPositions_model->find($id);

		$users_model = new \App\Models\UsersModel();
		$Positions_model = new \App\Models\PositionsModel();

		$oldOfficerAssigned = $coPos['clearance_officer_id'];
		$oldOfficerDetail = $users_model->find($oldOfficerAssigned);
		$oldOfficerName = $oldOfficerDetail['first_name']." ".$oldOfficerDetail['middle_name']." ".$oldOfficerDetail['last_name']." ".$oldOfficerDetail['suffix_name'];

		$coID = $this->request->getPOST('clearanceofficer');
		$positionID = $coPos['position_id'];

		$co = $users_model->find($coID);
		$position = $Positions_model->find($positionID);

		$data = [				 
			'clearance_officer_id' => $coID, 
		];

		$coPositions_model->skipValidation(false);
		if($coPositions_model->update($id,$data) == false)
		{
			$errors = $coPositions_model->errors();
			$session->setFlashdata('err_messages',$errors);
		}
		else
		{
			$co_name = $co['first_name']." ".$co['middle_name']." ".$co['last_name']." ".$co['suffix_name'];
			$positionDetail = $Positions_model->getInfo($positionID);

			$session->setFlashdata('success_messages',["<b>".$co_name."</b> Successfully Assigned as the new ".$position['position_name']]);

			//Log Activity
			$log_user = $session->get('user_id');
			$log_message = 'Assigned new officer for '.$position['position_name'].' : '.$co_name;

			\ScisSystem::logActivity($log_user,$log_message);

			//Create Notification for new Officer
			$sender_id = $session->get('user_id');
			$Subject = "Clearance Period Extension";
			$Message = "Congratulations ".$co_name.", you have been assigned as the new ".$position['position_name']." of ".$positionDetail->clearance_field.".";
			$receiver_id = $coID;

			\ScisSystem::CreateNotification($sender_id,$Subject,$Message,$receiver_id);

			//Create Notification for old Officer
			$sender_id = $session->get('user_id');
			$Subject = "Clearance Period Extension";
			$Message = "Good Day ".$oldOfficerName.", you have been relieved on your position as ".$position['position_name']." of ".$positionDetail->clearance_field.".";
			$receiver_id = $oldOfficerAssigned;

			\ScisSystem::CreateNotification($sender_id,$Subject,$Message,$receiver_id);
		}

		return redirect()->to('/Positions')->withInput();
	}

	public function editOrganizationPositionOfficer()
	{
		$session = session();
		
		$coPositions_model = new \App\Models\ClearanceOfficerPositionsModel();
		$id = $this->request->getPOST('coPosID');
		$id2 = $this->request->getPOST('coOrgID');
		$return = $this->request->getPOST('retID');

		$coPos = $coPositions_model->find($id);

		$users_model = new \App\Models\UsersModel();
		$Positions_model = new \App\Models\PositionsModel();

		$oldOfficerAssigned = $coPos['clearance_officer_id'];
		$oldOfficerDetail = $users_model->find($oldOfficerAssigned);
		$oldOfficerName = $oldOfficerDetail['first_name']." ".$oldOfficerDetail['middle_name']." ".$oldOfficerDetail['last_name']." ".$oldOfficerDetail['suffix_name'];

		$coID = $this->request->getPOST('clearanceofficer');
		$positionID = $coPos['position_id'];

		$co = $users_model->find($coID);
		$position = $Positions_model->find($positionID);

		$data = [				 
			'clearance_officer_id' => $this->request->getPOST('clearanceofficer'), 
		];

		$coPositions_model->skipValidation(false);
		if($coPositions_model->update($id,$data) == false)
		{
			$errors = $coPositions_model->errors();
			$session->setFlashdata('err_messages',$errors);
		}
		else
		{
			$soo_model = new \App\Models\StudentOrganizationOfficersModel();
			$so_model = new \App\Models\StudentOrganizationsModel();

			$studentOrganizationOfficer = $soo_model->find($id2);

			$studentOrgID = $studentOrganizationOfficer['student_organization_id'];

			$organization = $so_model->find($studentOrgID);
				
			$data2 = [
				'clearance_officer_id' => $coID,  
			];

			$soo_model->skipValidation(false);
			if($soo_model->update($id2,$data2) == false)
			{
				$errors = $soo_model->errors();
				$session->setFlashdata('err_messages',$errors);
			}

			$co_name = $co['first_name']." ".$co['middle_name']." ".$co['last_name']." ".$co['suffix_name'];

			$session->setFlashdata('success_messages',["<b>".$co_name."</b> Successfully Assigned as the new".$position['position_name']." of ".$organization['organization_name']]);

			//Log Activity
			$log_user = $session->get('user_id');
			$log_message = 'Assigned new officer for '.$position['position_name'].' ('.$organization['organization_name'].') : '.$co_name;

			\ScisSystem::logActivity($log_user,$log_message);

			//Create Notification for new Officer
			$sender_id = $session->get('user_id');
			$Subject = "Clearance Period Extension";
			$Message = "Congratulations ".$co_name.", you have been assigned as the new ".$position['position_name']." of ".$positionDetail->clearance_field.".";
			$receiver_id = $coID;

			\ScisSystem::CreateNotification($sender_id,$Subject,$Message,$receiver_id);

			//Create Notification for old Officer
			$sender_id = $session->get('user_id');
			$Subject = "Clearance Period Extension";
			$Message = "Good Day ".$oldOfficerName.", you have been relieved on your position as ".$position['position_name']." of ".$positionDetail->clearance_field.".";
			$receiver_id = $oldOfficerAssigned;

			\ScisSystem::CreateNotification($sender_id,$Subject,$Message,$receiver_id);
		}

		return redirect()->to('/Positions/OrganizationsOfficers/'.$return)->withInput();

	}

	public function OrganizationsOfficers($id = '')
	{
		\ScisSystem::refreshData1();

		$permission_model = new \App\Models\PermissionsModel();

		$session = session();

		$data = [
			'page_title' => 'Clearance Officer Positions | PUPT SCIS',
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
		
		if($permission_model->hasPermission($uID,['view_positions']))
		{
			if($id == '')
			{
				return redirect()->to('Positions');
			}
			else
			{
				$db = \Config\Database::connect();
				$scis_model = new \App\Models\ScisModel($db);
				$pos_model = new \App\Models\PositionsModel();
				$org_model = new \App\Models\StudentOrganizationsModel();
				
				$positions = $pos_model->find($id);
				$position_name = $positions['position_name'];

				$organizations = $org_model->where('deleted',0)->findAll();

				$clearanceOfficers = $scis_model->getRoleUsers(2);
				
				$assigned_officers = $scis_model->getOrganizationOfficersList();

				$data['Organizations'] = $organizations;
				$data['ClearanceOfficers'] = $clearanceOfficers;
				$data['PositionID'] = $id;
				$data['PositionName'] = $position_name;
				$data['AssignedOfficers'] = $assigned_officers;

				$data['AddClearanceFieldOfficers'] = ($permission_model->hasPermission($uID,'add_clearance_officer_positions')) ? TRUE : FALSE ;
				$data['EditClearanceFieldOfficers'] = ($permission_model->hasPermission($uID,'edit_clearance_officer_positions')) ? TRUE : FALSE ;

				return view('admin/assign_org_officers', $data);
			}			
		}
		else
		{
			return view('site/no_permission', $data);
		}
	}
}