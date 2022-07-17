<?php

namespace App\Controllers;

class RoleManagement extends BaseController
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
			'page_title' => 'Roles | PUPT SCIS',
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

		if($permission_model->hasPermission($uID,'view_roles'))
		{
			$roles_model = new \App\Models\RolesModel();
			$rolesPermissions = new \App\Models\RolePermissionsModel();

			$permission_list = array();
			$permission_list = $permission_model->where('deleted',0)->findAll();

			$data['permissions'] = $permission_list;

			$RoleData = array();
			$RoleCount = 0;

			$roles = $roles_model->where('deleted',0)->findAll();

			foreach($roles as $r)
			{
				$RoleData[$RoleCount]['role_id'] = $r['id'];
				$RoleData[$RoleCount]['role_name'] = $r['role'];

				$rPerm_condition = [
					'role_id' => $r['id'],
					'deleted' => 0,
				];
				$rPerm = $rolesPermissions->where($rPerm_condition)->findAll();

				$RoleData[$RoleCount]['permissions'] = array();	
				$permIDs = array();			

				foreach($rPerm as $rP)
				{
					$perm = $permission_model->where('id',$rP['permission_id'])->first();

					$permissionData = [
						'perm_id' => $perm['id'],
						'perm_name' => $perm['permission'],
					];

					array_push($RoleData[$RoleCount]['permissions'],$permissionData);
					array_push($permIDs,$perm['id']);
				}

				$RoleData[$RoleCount]['permIDs'] = $permIDs;	

				$RoleCount++;
			}

		    $data['RolesData'] = $RoleData;
		    
		    $data['AddRoles'] = ($permission_model->hasPermission($uID,'add_roles')) ? TRUE : FALSE ;
			$data['EditRoles'] = ($permission_model->hasPermission($uID,'edit_roles')) ? TRUE : FALSE ;
			$data['DeleteRoles'] = ($permission_model->hasPermission($uID,'delete_roles')) ? TRUE : FALSE ;

		    return view('admin/roles', $data);
		}
		else
		{
			return view('site/no_permission', $data);
		}
			
		
		
	}

	public function newRole()
	{
		$session = session();
		
		$role_model = new \App\Models\RolesModel();
		$rolePermissions_model = new \App\Models\RolePermissionsModel();

		$newRole = $this->request->getPOST('role');
		$rolePermissions = $this->request->getPOST('permissions');

		$newRoleData = [
			'role' => $newRole,
		];

		$validationRules  = [	
		   	"role"	=> [
				'rules' => "required|alpha_numeric_punct|is_unique[roles.role]",
				'errors' => [
					'required' 		=> 'Role Name is Required',
					'alpha_numeric_punct'	=> 'Role Name must only contain alphanumeric characters',
					'is_unique' 	=> 'Role Already Exist'
				],
			],
		];

		$role_model->setValidationRules($validationRules);

		if($role_model->insert($newRoleData) == false)
		{
			$errors = $role_model->errors();
			$session->setFlashdata('err_messages',$errors);
		}
		else
		{
			$roleID = $role_model->getInsertID();

			foreach($rolePermissions as $rP)
			{
				$rolePermission_data = [
					'role_id' => $roleID,
					'permission_id' => $rP,
				];

		   		$rolePermissions_model->insert($rolePermission_data);
		   	}


		   	$session->setFlashdata('success_messages',[$newRole. " Successfully Added"]);
		}

		return redirect()->to("/RoleManagement")->withInput();	
	}

	public function editRole()
	{
		$session = session();
		
		$role_model = new \App\Models\RolesModel();

		$role_id = $this->request->getPOST('roleID');
		$newRoleName = $this->request->getPOST('role');

		$oldRoleData = $role_model->find($role_id);

		date_default_timezone_set('Asia/Manila');
		$modify_stamp = date('Y-m-d H:i:s', time());

		if($newRoleName != $oldRoleData['role'])
		{
			$newRoleData = [
				'role' => $newRoleName,
				'modified_data' => $modify_stamp,
			];

			$validationRules  = [	
				"role"	=> [
					'rules' => "required|alpha_numeric_punct|is_unique[roles.role,id,".$role_id."]",
					'errors' => [
						'required' 		=> 'Role Name is Required',
		   				'alpha_numeric_punct'	=> 'Role Name must only contain alphanumeric characters',
		   				'is_unique' 	=> 'Role Already Exist'
		   			],
		   		],
		   	];

		   	$role_model->setValidationRules($validationRules);

		   	if($role_model->update($role_id,$newRoleData) == false)
		   	{
		   		$errors = $role_model->errors();
				$session->setFlashdata('err_messages',$errors);
		   	}
		   	else
			{
			   	$session->setFlashdata('success_messages',[$newRoleName. " Successfully Edited"]);
			}
		}
		else
		{
			$session->setFlashdata('err_messages',['No Changes']);
		}

		return redirect()->to("/RoleManagement")->withInput();	
	}

	public function deleteRole($id)
	{
		$session = session();
		
		$role_model = new \App\Models\RolesModel();
		$rolePermissions_model = new \App\Models\RolePermissionsModel();

		$RoleData = $role_model->find($id);

		date_default_timezone_set('Asia/Manila');
		$delete_stamp = date('Y-m-d H:i:s', time());

		$data = [
			'deleted' => 1,
			'deleted_date' => $delete_stamp,
		];

		if($role_model->update($id,$data) == false)
		{
			$errors = $role_model->errors();
			$session->setFlashdata('err_messages',$errors);
		}
		else
		{
		  	$session->setFlashdata('success_messages',[$RoleData['role']. " Successfully Deleted"]);
		}

		return redirect()->to("/RoleManagement")->withInput();	
	}

}