<?php

namespace App\Controllers;
use App\Libraries\Hash;
use App\Libraries\Reports;

use TCPDF;

class Courses extends BaseController
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
			'page_title' => 'Courses | PUPT SCIS',
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
		
		if($permission_model->hasPermission($uID,['view_courses']))
		{
			$db = \Config\Database::connect();
			$scis_model = new \App\Models\ScisModel($db);
			$course_list = $scis_model->getCoursesList();

			$org_model = new \App\Models\StudentOrganizationsModel();
			$organizations = $org_model->where('deleted',0)->findAll();
			
			$data['Courses'] = $course_list;
			$data['Organizations'] = $organizations;

			$data['AddCourses'] = ($permission_model->hasPermission($uID,'add_courses')) ? TRUE : FALSE ;
			$data['EditCourses'] = ($permission_model->hasPermission($uID,'edit_courses')) ? TRUE : FALSE ;
			$data['DeleteCourses'] = ($permission_model->hasPermission($uID,'delete_courses')) ? TRUE : FALSE ;

			return view('admin/manage_courses', $data);
		}
		else
		{
			return view('site/no_permission', $data);
		}
	}

	public function newCourse()
	{
		$session = session();
		
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
			$data["max_year_level"] = $this->request->getPOST('maxYear');

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

		return redirect()->to('/Courses')->withInput();
	}

	public function editCourse()
	{
		$session = session();
		
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
			$data["max_year_level"] = $this->request->getPOST('maxYear');
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

		return redirect()->to('/Courses')->withInput();

	}

	public function deleteCourse($id = '')
	{
		$session = session();
		
		if($id != '')
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
		}

		return redirect()->to('/Courses')->withInput();
	}
}