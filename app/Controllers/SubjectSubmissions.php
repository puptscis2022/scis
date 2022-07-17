<?php

namespace App\Controllers;
use App\Libraries\Hash;
use App\Libraries\Reports;

class SubjectSubmissions extends BaseController
{
	public function __construct(){
		helper('ScisSystem');
	}

	public function index()
	{
		return redirect()->back()->to('/');
	}

	//Manage Submitted Requirements Submissions=====================
	public function List($id = "") //$id = fieldID + positionID + clearanceType
	{
		$field_pos_type = explode("-",$id);
		$field_id = $field_pos_type[0];
		$pos_id = $field_pos_type[1];

		$clearanceType = ($field_pos_type[2]) ? $field_pos_type[2] : FALSE ;
		$gradClearance = ($clearanceType == 1) ? TRUE : FALSE ;

		\ScisSystem::refreshData1();

		$permission_model = new \App\Models\PermissionsModel();

		$session = session();

		$data = [
			'page_title' => 'Submissions | PUPT SCIS',
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
		
		if($permission_model->hasPermission($uID,['view_submissions']) && (!$session->get('Student_access') || $session->get('ClearanceOfficer_access')))
		{
			$errors = array();
			$array_count = 0;

			$user_data = $session->get('user_field');
			$id = $user_data['id'];

			//get User specified Entries ===============================
			$filterCourse = "all";
			$filterYear = "all";
			$filterStatus = 0;
			$filterRequirement = "all";
			$filterExist = false;
			$clearFilter = false;

			if($this->request->getPOST('clearFilter') == "Clear Filter")
			{
				$clearFilter = true;
				// echo "Filter Cleared";
			}
			else
			{
				//filter for course
				if($courseFilter= $this->request->getPOST('course'))
				{
					if($courseFilter != "all")
					{
						$filterCourse = $courseFilter;
						// echo $filterCourse."<br>";
					}
					$filterExist = true;
				}

				//filter for Requirement
				if($reqFilter = $this->request->getPOST('req'))
				{
					if($reqFilter != "all")
					{
						$filterRequirement = $reqFilter;
						// echo $filterRequirement."<br>";
					}
					$filterExist = true;
				}

				//filter for Year
				if($yearFilter= $this->request->getPOST('year'))
				{
					if($yearFilter != "all")
					{
						if($yearFilter == "x")
						{
							$yearFilter = "0";
						}
						$filterYear = $yearFilter;
						// echo $filterYear."<br>";
					}
					$filterExist = true;
				}

				//filter for status
				if($statFilter= $this->request->getPOST('status'))
				{
					if($statFilter != "all")
					{
						if($statFilter == "x")
						{
							$statFilter = "0";
						}
						$filterStatus = $statFilter;
						// echo $filterStatus;
					}
					$filterExist = true;
				}	
			}	

			if($defField = $session->get("defField2"))
			{
				if($defField == $field_id)
				{
					if($session->get("defFilter2") && !$filterExist && !$clearFilter)
					{
						$filters = $session->get("defFilter2");
						$filterCourse = $filters['course'];
						$filterYear = $filters['year'];
						$filterStatus = $filters['status'];
						$filterRequirement = $filters['req'];
					}			
					else
					{
						$filters = [
							'course' => $filterCourse,
							'year' => $filterYear,
							'status' => $filterStatus,
							'req' => $filterRequirement,
						];
						$session->set("defFilter2",$filters);
					} 	
				}
				else
				{
					$filters = [
						'course' => $filterCourse,
						'year' => $filterYear,
						'status' => $filterStatus,
						'req' => $filterRequirement,
					];
					$session->set("defFilter2",$filters);

					$session->set("defField2",$field_id);
				}
			}
			else
			{
				$session->set("defField2",$field_id);
			}	
			//End of getting Specified entries ===========================	

			$cF_model = new \App\Models\ClearanceFieldsModel();
			$field = $cF_model->find($field_id);
			$field_name = "";
			if(isset($field))
			{
				$field_name = $field['field_name'];
			}

			//Dropdowns Selections
			//Requirements
			$req_model = new \App\Models\RequirementsModel();
			$req_condition = [
				'clearance_field_id' => $field_id,
				'deleted' => 0,
			];
			$requirements_list = $req_model->where($req_condition)->findAll();

			$requirements = array();
			$reqCount = 0;

			foreach($requirements_list as $reqL)
			{
				if($reqL['submission_type'] == '1')
				{
					$requirements[$reqCount]['id'] = $reqL['id'];
					$requirements[$reqCount]['name'] = $reqL['requirement_name'];
					$reqCount++;
				}
			}

			

			//Courses
			$course_model = new \App\Models\CoursesModel();
			$courses = $course_model->where('deleted',0)->findAll();

			$data['Office'] = $field_name;
			$data['field_id'] = $field_id;
			$data['clearance_check'] = false;
			$data['Requirements']	= $requirements;
			$data['courses'] = $courses;

			$position_name = "";
			$co_org = FALSE;
			$user_fields = $session->get('user_co_fields');
			foreach($user_fields as $uf)
			{
				if($uf['field_id'] == $field_id && $uf['position_id'] == $pos_id)
				{
					$position_name = $uf['positions_name'];

					if($uf['field_name'] == "Student Organization")
					{
						$co_org = $session->get('user_org');
					}
				}
			}

			$data['Position'] = $position_name;

			$data['ApproveReject'] = ($permission_model->hasPermission($uID,'edit_submissions')) ? TRUE : FALSE ;

			if($gradClearance)
			{
				$db = \Config\Database::connect();
				$scis_model = new \App\Models\ScisModel($db);
				$submissions_list = $scis_model->getSubmissionsForGradCField($field_id,$filterCourse,$filterStatus,$filterRequirement,$pos_id,$co_org);
						
				$data['gradClearance'] = TRUE;
				$data['clearance_check'] = true;
				$data['Submissions'] = $submissions_list;
				$data['courseFil'] = $filterCourse;
				$data['yearFil'] = $filterYear;
				$data['statusFil'] = $filterStatus;
				$data['reqFil'] = $filterRequirement;
					
				$session->setFlashdata('err_messages',$errors);
			}
			else
			{
				//Get Current CLearance Period Data
				$cPeriod_model = new \App\Models\ExistingClearancePeriodsModel();
				$currentPeriod = $cPeriod_model->where('deleted',0)->orderBy('id','DESC')->first();

				date_default_timezone_set('Asia/Manila');
				$today = date("Y-m-d");
				
				if(!empty($currentPeriod))
				{
					if($today < $currentPeriod['start_date'])
					{
						$errors[$array_count++] = "There is no current Clearance Period, the next one will start on ".$currentPeriod['start_date'];
					}
					else if($today > $currentPeriod['end_date'])
					{
						$errors[$array_count++] = "There is no current Clearance Period, the last one was on ".$currentPeriod['end_date'];
					}
					else
					{

						$db = \Config\Database::connect();
						$scis_model = new \App\Models\ScisModel($db);
						$submissions_list = $scis_model->getSubmissionsForCField($field_id,$currentPeriod['id'],$filterCourse,$filterYear,$filterStatus,$filterRequirement,$pos_id,$co_org);
						
						$data['clearance_check'] = true;
						$data['Submissions'] = $submissions_list;
						$data['courseFil'] = $filterCourse;
						$data['yearFil'] = $filterYear;
						$data['statusFil'] = $filterStatus;
						$data['reqFil'] = $filterRequirement;
						
						$session->setFlashdata('err_messages',$errors);
					}
				}
				else
				{
					$errors[$array_count++] = "There is no Existing Clearance Period at the moment";
				}
			}

			$session->setFlashdata('err_messages',$errors);

			return view('submissions',$data);
		}
		else
		{
			return view('site/no_permission',$data);
		}
	}

	public function approveSubmission($def_sub)
	{
		$session = session();
		
		$id = explode("-", $def_sub);
		$defID = $id[0];
		$subID = $id[1];

		$db = \Config\Database::connect();

		$deficiency_model = new \App\Models\DeficienciesModel();
		$submissions_model = new \App\Models\SubmissionsModel();
		$scis_model = new \App\Models\ScisModel($db);
		$req_model = new \App\Models\RequirementsModel();
		$cEntries_model = new \App\Models\ClearanceEntriesModel();
		$cField_model = new \App\Models\ClearanceFieldsModel();

		//get requirement name
		$def_info = $deficiency_model->find($defID);
		$req_info = $req_model->find($def_info['requirement_id']);

		//get Field Info
		$cEntry_info = $cEntries_model->find($def_info['clearance_entry_id']);
		$field_info = $cField_model->find($cEntry_info['clearance_field_id']);

		//get student info
		$student = $scis_model->getStudentInfo1($defID);
		$receiver_id = $student[0]->user_id;

		//update deficieny status from 0 to 2
		date_default_timezone_set('Asia/Manila');
		$modify_stamp = date('Y-m-d H:i:s', time());

		$data = [
			'status' => 2,
			'modified' => $modify_stamp
		];

		$deficiency_model->update($defID,$data);

		//ensure that submitted requirement is not deleted
		date_default_timezone_set('Asia/Manila');
		$modify_stamp = date('Y-m-d H:i:s', time());

		//ensure other submission is deleted
		$similar_sub = $submissions_model->where('deficiency_id',$defID)->findAll();

		foreach($similar_sub as $sim)
		{
			$deleteData = [
				'deleted' => 1,
				'deleted_date' => $modify_stamp
			];

			$submissions_model->update($sim['id'],$deleteData);
		}

		$data2 = [
			'deleted' => 0,
			'deleted_date' => "",
			'modified' => $modify_stamp,
		];

		$submissions_model->update($subID,$data2);

		//Create Notification
		$sender_id = $session->get('user_id');
		$Subject = "Submission Accepted";
		$Message = "Dear Student, Your Submission on <b>".$req_info['requirement_name']."</b> in <b>".$field_info['field_name']."</b>, has been approved";

		\ScisSystem::CreateNotification($sender_id,$Subject,$Message,$receiver_id);

		$session->setFlashdata('success_messages',["Requirement Approved Successfully"]);

		//Log Activity
		$log_user = $session->get('user_id');
		$log_message = 'Approved a Submission: '.$req_info['requirement_name'].'['.$field_info['field_name'].'] of '.$student->student_name;

		\ScisSystem::logActivity($log_user,$log_message);

		//Check if all Deficiency is Cleared
		$EDeficiency_list = $scis_model->getEntryDeficiencies($def_info['clearance_entry_id']);

		$clear_check = TRUE;
		foreach($EDeficiency_list as $def)
		{
			if($def->def_status != 2)
			{
				$clear_check = FALSE;
			}
		}

		if($clear_check)
		{
			$data = [
				'clearance_field_status' => 1,
				'modified' => $modify_stamp,
			];

			$cEntries_model->update($def_info['clearance_entry_id'],$data);		

			//Create Notification
			$Message = "Dear Student, You have successfully cleared all your accountabilities in <b>".$fieldName."</b>";
			
			\ScisSystem::CreateNotification($sender_id,$Subject,$Message,$receiver_id);
		}			

		return redirect()->to("/Submissions/List/".$cEntry_info['clearance_field_id']);
	}

	public function rejectSubmission($id)
	{
		$session = session();
		
		$db = \Config\Database::connect();

		$reason = $this->request->getPOST('reason');

		$deficiency_model = new \App\Models\SubjectDeficienciesModel();
		$submissions_model = new \App\Models\SubjectSubmissionsModel();
		$scis_model = new \App\Models\ScisModel($db);

		//get requirement name
		$def_info = $deficiency_model->find($id);

		//Submissions Detail
		$submissionDetail = $submissions_model->submissionDetail2($id);

		//set Student for notification recieving id
		$receiver_id = $submissionDetail->student_user_id;

		//update deficieny status from 0 or 2 to 1
		date_default_timezone_set('Asia/Manila');
		$modify_stamp = date('Y-m-d H:i:s', time());

		$data = [
			'note' => $reason,
			'status' => 2,
			'modified' => $modify_stamp
		];

		$deficiency_model->update($id,$data);

		//delete and create new submission entry
		$data2 = [
			'deleted' => 1,
			'deleted_date' => $modify_stamp
		];

		$submissions_model->update($submissionDetail->submission_id,$data2);

		$newSub = [
			'subject_deficiency_id' => $id,
		];
		$submissions_model->insert($newSub);

		//Create Notification
		$sender_id = $session->get('user_id');
		$Subject = "Submission Rejected";
		echo $Message = "Dear Student, Your Submission on <b>".$submissionDetail->requirement."</b> in <b>".$submissionDetail->subject."</b>, has been Rejected. Kindly resubmit correct requirement";

		\ScisSystem::CreateNotification($sender_id,$Subject,$Message,$receiver_id);

		//Log Activity
		$log_user = $session->get('user_id');
		$log_message = 'Rejected a Submission: '.$submissionDetail->requirement.'['.$submissionDetail->subject.'] of '.$submissionDetail->student_name;

		\ScisSystem::logActivity($log_user,$log_message);

		$session->setFlashdata('success_messages',["Requirement Rejected Successfully"]);

		return redirect()->back()->withInput();
	}

	public function submitRequirement()
	{
		$session = session();
		
		$submissions_model = new \App\Models\SubjectSubmissionsModel();
		$deficiency_model = new \App\Models\SubjectDeficienciesModel();
		$users_model = new \App\Models\UsersModel();

		$defID = $this->request->getPOST('defID');
		$fileUploaded = $this->request->getFile('fileReq');
		$extension =  $fileUploaded->guessExtension();

		if ($fileUploaded->isValid() && ! $fileUploaded->hasMoved())
		{			
			//make random new name for the uploaded file
			$newName = $fileUploaded->getRandomName();

			//upload file to upload directory
			$fileUploaded->move('uploads/SubjectSubmissions', $newName);

			//get Assigned submission query
			$submission_condition = [
				'subject_deficiency_id' => $defID,
				'deleted' => 0,
			];

			$assignedSubQuery = $submissions_model->where($submission_condition)->first();
			date_default_timezone_set('Asia/Manila');
			$modify_stamp = date('Y-m-d H:i:s', time());

			$data = [
				'file_path' => $newName, //file name talaga dapat ahahah
				'modified' => $modify_stamp,
			];

			$submissions_model->update($assignedSubQuery['id'],$data);

			//Revert submission to pending
			$defData = [
				'status' => 0, 
				'modified' => $modify_stamp,
			];

			$deficiency_model->update($defID,$defData);
					
			$session->setFlashdata('success_messages',["Requirement Submitted Successfully. Kindly wait for its approval"]);
			$session->remove('error_message');

			//Log Activity
			$submissionDetail = $submissions_model->submissionDetail($assignedSubQuery['id']);

			$log_user = $session->get('user_id');
			echo $log_message = 'Submitted a Requirement : '.$submissionDetail->requirement;

			\ScisSystem::logActivity($log_user,$log_message);

			//Create Notification for Assigned User
			$sender_id = $session->get('user_id');
			$Subject = "Requirement Submission";
			$Message = "Good Day ".$submissionDetail->professor_assigned_name.", I have Submitted my ".$submissionDetail->requirement.".";
			$receiver_id = $submissionDetail->professor_id;

			\ScisSystem::CreateNotification($sender_id,$Subject,$Message,$receiver_id);		

			echo "Success";
		}
		else
		{
			echo "ERROR";
			$session->setFlashdata('error_messages',["Failed to Submit Requirement. Try Again"]);
		}		

		return redirect()->back()->withInput();
	}
}