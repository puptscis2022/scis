<?php

namespace App\Controllers;
use App\Libraries\Hash;
use App\Libraries\Reports;

use TCPDF;

class Maintenancex extends BaseController
{
	public function __construct(){
		helper('ScisSystem');
	}

	public function index()
	{
		return redirect()->back()->to('/');
	}

	//Maintenance|ClearanceFields ============================================================
	public function ClearanceFields()
	{
		$session = session();
		if($session->get('admin_access'))
		{
			\ScisSystem::refreshData1();

			$db = \Config\Database::connect();
			$scis_model = new \App\Models\ScisModel($db);
			$cField_list = $scis_model->getClearanceFieldsList();

			$CT_model = new \App\Models\ClearanceTypesModel();
			$clearanceTypes = $CT_model->where('deleted',0)->findAll();
			
			$data = [
				'page_title' 		=> 'Clearance Fields | PUPT SCIS',
			   	'user_role'		=> $session->get('user_role'),
			    'activeClearance' => $session->get('ongoingClearance'),
			    'user_title'	=> 'Administrator',
			    'user_notifications' => $session->get('user_notifications'),
			    'user_fields' => $session->get('user_co_fields'),
			    'user_gradFields' => $session->get('user_co_gradFields'),
			    'user_subject_resp' => $session->get('user_subject_handled'),			    
				'Name'			=> $session->get('user_name'),
			    'profilePic'			=> $session->get('user_pic'),
				'user' 				=> $session->get('user'),
				'ClearanceFields' 	=> $cField_list,
				'ClearanceTypes'	=> $clearanceTypes,
			];

			if( $session->get("superAdmin_access"))
			{
			    $data['user_title']	= 'Super Administrator';
			}

			return view('admin/manage_clearance_fields', $data);
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function newCField()
	{
		$session = session();
		if($session->get('admin_access'))
		{
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

			return redirect()->back()->withInput();
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function editCField()
	{
		$session = session();
		if($session->get('admin_access'))
		{
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

			return redirect()->back()->withInput();
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function deleteCField($id = '')
	{
		$session = session();
		if($session->get('admin_access'))
		{
			if($id == '')
			{
				return redirect()->back()->to('ClearanceFields');
			}
			else
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

				return redirect()->back()->withInput();
			}
			
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	//Maintenance|Positions============================================================
	public function Positions()
	{
		$session = session();
		if($session->get('admin_access'))
		{
			\ScisSystem::refreshData1();

			$db = \Config\Database::connect();
			$scis_model = new \App\Models\ScisModel($db);
			$position_list = $scis_model->getPositionsList();

			$organizationOfficers = $scis_model->getOrganizationOfficersList();

			$organization_model = new \App\Models\StudentOrganizationsModel();
			$organizations =  $organization_model->where('deleted',0)->findAll();

			$CF_model = new \App\Models\ClearanceFieldsModel();
			$clearanceFields = $CF_model->where('deleted',0)->findAll();

			$clearanceOfficers = $scis_model->getRoleUsers(2);

			$data = [
				'page_title' 		=> 'Manage Positions | PUPT SCIS',
			   	'user_role'		=> $session->get('user_role'),
			    'activeClearance' => $session->get('ongoingClearance'),
			    'user_title'	=> 'Administrator',
			    'user_notifications' => $session->get('user_notifications'),
			    'user_fields' => $session->get('user_co_fields'),
			    'user_gradFields' => $session->get('user_co_gradFields'),
			    'user_subject_resp' => $session->get('user_subject_handled'),			    			   	
				'Name'			=> $session->get('user_name'),
			    'profilePic'			=> $session->get('user_pic'),
				'user' 				=> $session->get('user'),
				'Positions' 	=> $position_list,
				'ClearanceFields'	=> $clearanceFields,
				'organizations' => $organizations,
				'orgOfficers' => $organizationOfficers,
				'ClearanceOfficers' => $clearanceOfficers,
			];

			if( $session->get("superAdmin_access"))
			{
			    $data['user_title']	= 'Super Administrator';
			}

			return view('admin/manage_positions', $data);
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function newPosition()
	{
		$session = session();
		if($session->get('admin_access'))
		{
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

			return redirect()->back()->withInput();
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function editPosition()
	{
		$session = session();
		if($session->get('admin_access'))
		{
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

			return redirect()->back()->withInput();
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function deletePosition($id = '')
	{
		$session = session();
		if($session->get('admin_access'))
		{
			if($id == '')
			{
				return redirect()->back()->to('Positions');
			}
			else
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

				return redirect()->back()->withInput();
			}
			
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}
	//Maintenance | Assigning Position======================
	public function assignOfficerToPosition()
	{
		$session = session();
		if($session->get('admin_access'))
		{
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

				$session->setFlashdata('success_messages',["<b>".$co_name."</b> Successfully Assigned as ".$position['position_name']]);
			}

			return redirect()->back()->withInput();
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function assignOfficerToOrganizationPosition()
	{
		$session = session();
		if($session->get('admin_access'))
		{
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

				$session->setFlashdata('success_messages',["<b>".$co_name."</b> Successfully Assigned as ".$position['position_name']." of ".$organization['organization_name']]);
			}	
			return redirect()->back()->withInput();
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function editPositionOfficer()
	{
		$session = session();
		if($session->get('admin_access'))
		{
			$coPositions_model = new \App\Models\ClearanceOfficerPositionsModel();
			$id = $this->request->getPOST('coPosID');
			$coPos = $coPositions_model->find($id);

			$users_model = new \App\Models\UsersModel();
			$Positions_model = new \App\Models\PositionsModel();

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

				$session->setFlashdata('success_messages',["<b>".$co_name."</b> Successfully Assigned as the new ".$position['position_name']]);
			}

			return redirect()->back()->withInput();
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function editOrganizationPositionOfficer()
	{
		$session = session();
		if($session->get('admin_access'))
		{
			$coPositions_model = new \App\Models\ClearanceOfficerPositionsModel();
			$id = $this->request->getPOST('coPosID');
			$id2 = $this->request->getPOST('coOrgID');

			$coPos = $coPositions_model->find($id);

			$users_model = new \App\Models\UsersModel();
			$Positions_model = new \App\Models\PositionsModel();

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
			}

			

			return redirect()->back()->withInput();
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function OrganizationsOfficers($id = '')
	{
		$session = session();
		if($session->get('admin_access'))
		{
			\ScisSystem::refreshData1();

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

				$data = [
					'page_title' 		=> 'Manage Positions | PUPT SCIS',
				   	'user_role'		=> $session->get('user_role'),
			    'activeClearance' => $session->get('ongoingClearance'),
			    	'user_title'	=> 'Administrator',
			    	'user_notifications' => $session->get('user_notifications'),
			    'user_fields' => $session->get('user_co_fields'),
			    'user_gradFields' => $session->get('user_co_gradFields'),
			    'user_subject_resp' => $session->get('user_subject_handled'),			    
					'Name'			=> $session->get('user_name'),
			    'profilePic'			=> $session->get('user_pic'),
					'user' 				=> $session->get('user'),
					'Organizations' => $organizations,
					'ClearanceOfficers' => $clearanceOfficers,
					'PositionID' => $id,
					'PositionName' => $position_name,
					'AssignedOfficers' => $assigned_officers,
				];

				if( $session->get("superAdmin_access"))
				{
				    $data['user_title']	= 'Super Administrator';
				}

				return view('admin/assign_org_officers', $data);


			}
			

			// return redirect()->back()->withInput();
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	//Maintenance|Requirements========================================
	public function Requirements()
	{
		$session = session();
		if($session->get('admin_access') || $session->get('co_access') )
		{
			\ScisSystem::refreshData1();

			$db = \Config\Database::connect();
			$scis_model = new \App\Models\ScisModel($db);
			$req_list = $scis_model->getRequirementsList();

			$user_co_fields = $session->get('user_co_fields');

			//Getting Fields Requirements base on the user's field
			$co_req_list = array();
			$array_count = 0;
			foreach($user_co_fields as $row)
			{
				foreach($req_list as $row2)
				{
					if($row['field_id'] == $row2->field_id){
						$co_req_list[$array_count++] = $row2; 
					}
				}
			}

			$CF_model = new \App\Models\ClearanceFieldsModel();
			$clearanceFields = $CF_model->where('deleted',0)->findAll();
			
			$user_fields = array();
			$array_count = 0;
			foreach($user_co_fields as $row) //Getting Administrator Field dropdown
			{
				$data = [
					'id' 	=> $row['field_id'],
					'name' 	=> $row['field_name'],
				];
				$user_fields[$array_count] = $data;
				$array_count += 1;
			}

			$fileType_model = new \App\Models\FileTypesModel();
			$fileTypes = $fileType_model->where('deleted',0)->findAll();

			$data = [
				'page_title' 		=> 'Requirements | PUPT SCIS',
			   	'user_role'		=> $session->get('user_role'),
			    'activeClearance' => $session->get('ongoingClearance'),
			    'user_notifications' => $session->get('user_notifications'),
			    'user_fields' => $session->get('user_co_fields'),
			    'user_gradFields' => $session->get('user_co_gradFields'),
			    'user_subject_resp' => $session->get('user_subject_handled'),			    
				'Name'			=> $session->get('user_name'),
			    'profilePic'			=> $session->get('user_pic'),
				'user' 				=> $session->get('user'),
				'Requirements' 		=> $co_req_list,
				'userFields'		=> $user_fields,
				'FileTypes'			=> $fileTypes,
			];

			if($session->get('admin_access'))
			{				
			    $data['user_title']	= 'Administrator';
			}
			else if($session->get('co_access'))
			{
				$data['user_title']	= 'Clearance Officer';
			}

			return view('manage_requirements', $data);
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function newRequirement()
	{
		$session = session();
		if($session->get('admin_access') || $session->get('co_access') )
		{
			$req_model = new \App\Models\RequirementsModel();

			$addedReq = $this->request->getPOST('reqName');
			$cFieldID = $this->request->getPOST('reqFieldID');

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

			return redirect()->back()->withInput();
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function editRequirement()
	{
		$session = session();
		if($session->get('admin_access') || $session->get('co_access'))
		{
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

			return redirect()->back()->withInput();
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function deleteRequirement($id = '')
	{
		$session = session();
		if($session->get('admin_access') || $session->get('co_access') )
		{
			if($id == '')
			{
				return redirect()->back()->to('Requirements');
			}
			else
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

				return redirect()->back()->withInput();
			}
			
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	//Maintenance|Organizations====================================================
	public function Organizations()
	{
		$session = session();
		if($session->get('admin_access'))
		{
			\ScisSystem::refreshData1();

			$db = \Config\Database::connect();
			$scis_model = new \App\Models\ScisModel($db);
			$organization_list = $scis_model->getOrganizationsList();

			$OT_model = new \App\Models\OrganizationTypesModel();
			$organizationTypes = $OT_model->where('deleted',0)->findAll();
			
			$data = [
				'page_title' 		=> 'Organizations | PUPT SCIS',
			   	'user_role'		=> $session->get('user_role'),
			    'activeClearance' => $session->get('ongoingClearance'),
			    'user_title'	=> 'Administrator',
			    'user_notifications' => $session->get('user_notifications'),
			    'user_fields' => $session->get('user_co_fields'),
			    'user_gradFields' => $session->get('user_co_gradFields'),
			    'user_subject_resp' => $session->get('user_subject_handled'),			    
				'Name'			=> $session->get('user_name'),
			    'profilePic'			=> $session->get('user_pic'),
				'user' 				=> $session->get('user'),
				'Organizations' 	=> $organization_list,
				'OrganizationTypes'	=> $organizationTypes,
			];

			if( $session->get("superAdmin_access"))
			{
			    $data['user_title']	= 'Super Administrator';
			}

			return view('admin/manage_organizations', $data);
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function newOrganization()
	{
		$session = session();
		if($session->get('admin_access'))
		{
			$org_model = new \App\Models\StudentOrganizationsModel();

			$addedOrg = $this->request->getPOST('orgName');

			$data = [
				'organization_name' => $addedOrg, 
				'organization_type_id' => $this->request->getPOST('orgType'), 
				'deleted' => 0,
			];

			$check_org_exist = $org_model->where($data)->first();

			$error = array();
			if(!empty($check_org_exist))
			{
				$session->setFlashdata('err_messages',["Organization Already Exist"]);
			}
			else
			{
				$org_model->skipValidation(false);
				if($org_model->insert($data) == false)
				{
					$errors = $org_model->errors();
					$session->setFlashdata('err_messages',$errors);
								
				}
				else
				{
					$session->setFlashdata('success_messages',["<b>".$addedOrg."</b> Successfully Added"]);

					//Log Activity
					$log_user = $session->get('user_id');
					$log_message = 'Added an Organization : '.$this->request->getPOST('orgName');

					\ScisSystem::logActivity($log_user,$log_message);
				}
			}

			return redirect()->back()->withInput();
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function editOrganization()
	{
		$session = session();
		if($session->get('admin_access'))
		{
			$org_model = new \App\Models\StudentOrganizationsModel();
			$id = $this->request->getPOST('orgID');

			$editedOrg = $this->request->getPOST('orgName');

			date_default_timezone_set('Asia/Manila');
			$modify_stamp = date('Y-m-d H:i:s', time());

			$data = [
				'organization_name' => $editedOrg, 
				'organization_type_id' => $this->request->getPOST('orgTypeID'),
				'deleted' => 0,
			];

			$check_org_exist = $org_model->where($data)->first();

			$error = array();
			if(!empty($check_org_exist))
			{
				$session->setFlashdata('err_messages',["Organization Already Exist"]);
			}
			else
			{
				$data["modified"] = $modify_stamp;
				$org_model->skipValidation(false);
				if($org_model->update($id,$data) == false)
				{
					$errors = $org_model->errors();
					$session->setFlashdata('err_messages',$errors);
								
				}
				else
				{
					$session->setFlashdata('success_messages',["<b>".$editedOrg."</b> Successfully Edited"]);

					//Log Activity
					$log_user = $session->get('user_id');
					$log_message = 'Edited an Organization : '.$this->request->getPOST('orgName');

					\ScisSystem::logActivity($log_user,$log_message);
				}	
			}

			return redirect()->back()->withInput();
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function deleteOrganization($id = '')
	{
		$session = session();
		if($session->get('admin_access'))
		{
			if($id == '')
			{
				return redirect()->back()->to('Organizations');
			}
			else
			{
				$org_model = new \App\Models\StudentOrganizationsModel();

				date_default_timezone_set('Asia/Manila');
				$delete_stamp = date('Y-m-d H:i:s', time());

				$data = [
					'deleted' => 1,
					'deleted_date' => $delete_stamp,
				];
			
				$org_model->update($id,$data);
				$deleted_organization = $org_model->where('id',$id)->first();

				$course_model = new \App\Models\CoursesModel();
				$course_builder = $course_model->builder();
				$course_builder->set($data)->where('student_organization_id',$id)->update();

				$SOO_model = new \App\Models\StudentOrganizationOfficersModel();
				$SOO_builder = $SOO_model->builder();
				$SOO_builder->set($data)->where('student_organization_id',$id)->update();

				$session->setFlashdata('success_messages',["<b>".$deleted_organization['organization_name']."</b> Successfully Deleted"]);

				//Log Activity
				$log_user = $session->get('user_id');
				$log_message = 'Deleted an Organization : '.$deleted_organization['organization_name'];

				\ScisSystem::logActivity($log_user,$log_message);

				return redirect()->back()->withInput();
			}
			
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	//Maintenance|Courses====================================================
	public function Courses()
	{
		$session = session();
		if($session->get('admin_access'))
		{
			\ScisSystem::refreshData1();

			$db = \Config\Database::connect();
			$scis_model = new \App\Models\ScisModel($db);
			$course_list = $scis_model->getCoursesList();

			$org_model = new \App\Models\StudentOrganizationsModel();
			$organizations = $org_model->where('deleted',0)->findAll();
			
			$data = [
				'page_title' 		=> 'Courses | PUPT SCIS',
			   	'user_role'		=> $session->get('user_role'),
			    'activeClearance' => $session->get('ongoingClearance'),
			    'user_title'	=> 'Administrator',
			    'user_notifications' => $session->get('user_notifications'),
			    'user_fields' => $session->get('user_co_fields'),
			    'user_gradFields' => $session->get('user_co_gradFields'),
			    'user_subject_resp' => $session->get('user_subject_handled'),			    
				'Name'			=> $session->get('user_name'),
			    'profilePic'			=> $session->get('user_pic'),
				'user' 				=> $session->get('user'),
				'Courses' 	=> $course_list,
				'Organizations'	=> $organizations,
			];

			if( $session->get("superAdmin_access"))
			{
			    $data['user_title']	= 'Super Administrator';
			}

			return view('admin/manage_courses', $data);
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function newCourse()
	{
		$session = session();
		if($session->get('admin_access'))
		{
			$course_model = new \App\Models\CoursesModel();

			$addedCourse = $this->request->getPOST('courseName');

			$data = [
				'course_name' => $addedCourse, 
				'deleted' => 0,
			];

			$check_course_exist = $course_model->where($data)->first();

			$error = array();
			if(!empty($check_course_exist))
			{
				$session->setFlashdata('err_messages',["Course Already Exist"]);
			}
			else
			{
				$data["student_organization_id"] = $this->request->getPOST('orgID');
				$data["abbreviation"] = $this->request->getPOST('courseCode');

				$course_model->skipValidation(false);
				if($course_model->insert($data) == false)
				{
					$errors = $course_model->errors();
					$session->setFlashdata('err_messages',$errors);
				}
				else
				{
					$session->setFlashdata('success_messages',["<b>".$addedCourse."</b> Successfully Added"]);

					//Log Activity
					$log_user = $session->get('user_id');
					$log_message = 'Added a Course : '.$this->request->getPOST('courseName');

					\ScisSystem::logActivity($log_user,$log_message);
				}				
			}

			return redirect()->back()->withInput();
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function editCourse()
	{
		$session = session();
		if($session->get('admin_access'))
		{
			$course_model = new \App\Models\CoursesModel();
			$id = $this->request->getPOST('courseID');

			$editedCourse = $this->request->getPOST('courseName');

			date_default_timezone_set('Asia/Manila');
			$modify_stamp = date('Y-m-d H:i:s', time());

			$data = [
				'course_name' => $editedCourse,
				'deleted' => 0,
			];

			$check_course_exist = $course_model->where($data)->first();

			$error = array();
			if(!empty($check_course_exist) && $check_course_exist['id'] != $id)
			{
				$session->setFlashdata('err_messages',["Course Already Exist"]);
			}
			else
			{
				$data["student_organization_id"] = $this->request->getPOST('orgID');
				$data["abbreviation"] = $this->request->getPOST('courseCode');
				$data['modified'] = $modify_stamp;
				$course_model->skipValidation(false);
				if($course_model->update($id,$data) == false)
				{
					$errors = $course_model->errors();
					$session->setFlashdata('err_messages',$errors);
								
				}
				else
				{
					$session->setFlashdata('success_messages',["<b>".$editedCourse."</b> Successfully Edited"]);

					//Log Activity
					$log_user = $session->get('user_id');
					$log_message = 'Edited a Course : '.$editedCourse;

					\ScisSystem::logActivity($log_user,$log_message);
				}
			}

			return redirect()->back()->withInput();
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function deleteCourse($id = '')
	{
		$session = session();
		if($session->get('admin_access'))
		{
			if($id == '')
			{
				return redirect()->back()->to('Courses');
			}
			else
			{
				$course_model = new \App\Models\CoursesModel();

				date_default_timezone_set('Asia/Manila');
				$delete_stamp = date('Y-m-d H:i:s', time());

				$data = [
					'deleted' => 1,
					'deleted_date' => $delete_stamp,
				];
			
				$deleted_course = $course_model->where('id',$id)->first();
				$course_model->update($id,$data);

				$session->setFlashdata('success_messages',["<b>".$deleted_course['course_name']."</b> Successfully Deleted"]);

				//Log Activity
				$log_user = $session->get('user_id');
				$log_message = 'Deleted a Course : '.$deleted_course['course_name'];

				\ScisSystem::logActivity($log_user,$log_message);

				return redirect()->back()->withInput();
			}
			
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

}