<?php

namespace App\Controllers;
use App\Libraries\Hash;
use App\Libraries\Reports;

use TCPDF;

class Organizations extends BaseController
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
			'page_title' => 'Student Organizations | PUPT SCIS',
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
		
		if($permission_model->hasPermission($uID,['view_student_organizations']))
		{
			$db = \Config\Database::connect();
			$scis_model = new \App\Models\ScisModel($db);
			$organization_list = $scis_model->getOrganizationsList();

			$OT_model = new \App\Models\OrganizationTypesModel();
			$organizationTypes = $OT_model->where('deleted',0)->findAll();
			
			$data['Organizations'] = $organization_list;
			$data['OrganizationTypes'] = $organizationTypes;

			$data['AddStudentOrganizations'] = ($permission_model->hasPermission($uID,'add_student_organizations')) ? TRUE : FALSE ;
			$data['EditStudentOrganizations'] = ($permission_model->hasPermission($uID,'edit_student_organizations')) ? TRUE : FALSE ;
			$data['DeleteStudentOrganizations'] = ($permission_model->hasPermission($uID,'delete_student_organizations')) ? TRUE : FALSE ;

			return view('admin/manage_organizations', $data);
		}
		else
		{
			return view('site/no_permission', $data);
		}
		
	}

	public function newOrganization()
	{
		$session = session();
		
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

		return redirect()->to('/Organizations')->withInput();
	}

	public function editOrganization()
	{
		$session = session();
		
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

		return redirect()->to('/Organizations')->withInput();
	}

	public function deleteOrganization($id = '')
	{
		$session = session();
		
		if($id != '')
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
		}

		return redirect()->to('/Organizations')->withInput();
	}
}