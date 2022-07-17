<?php

namespace App\Controllers;
use App\Libraries\Hash;
use App\Libraries\Reports;

use TCPDF;

//Manage Students' Deficiencies 
class Deficiencies extends BaseController
{
	public function __construct(){
		helper('ScisSystem');
	}

	public function index()
	{
		return redirect()->to('/Deficiencies/List');
	}

	public function List($id,$stat = "") //$id = fieldID + positionID + clearanceType
	{
		$field_pos_type = explode("-",$id);
		$field_id = $field_pos_type[0];
		$pos_id = $field_pos_type[1];

        $gradClearance = FALSE ;
		$clearanceType = (!empty($field_pos_type[2])) ? $field_pos_type[2] : FALSE ;
	    if($clearanceType)
	    {
	        $gradClearance = ($clearanceType == 1) ? TRUE : FALSE ;
	    }
		

		\ScisSystem::refreshData1();

		$permission_model = new \App\Models\PermissionsModel();

		$session = session();

		$data = [
			'page_title' => 'Manage Deficiencies | PUPT SCIS',
			'Name'			=> $session->get('user_name'),
		    'profilePic'			=> $session->get('user_pic'),
		    'clearanceData'			=> $session->get('clearance_data'),
		    'activeClearance' => $session->get('ongoingClearance'),
		    'user_title'	=> $session->get('title'),
		    'user_notifications' => $session->get('user_notifications'),
		    'user_fields' => $session->get('user_co_fields'),
			    'user_gradFields' => $session->get('user_co_gradFields'),
			    'user_subject_resp' => $session->get('user_subject_handled'),
			    'gradClearance' => $gradClearance,
		];

		$uID = $session->get('user_id');
		
		if($permission_model->hasPermission($uID,['view_deficiencies']))
		{
			$data['TaggingDeficiencies'] = ($permission_model->hasPermission($uID,'add_deficiencies')) ? TRUE : FALSE ;
			$data['ClearStudents'] = ($permission_model->hasPermission($uID,'edit_clearance_entries') && $permission_model->hasPermission($uID,'edit_deficiencies')) ? TRUE : FALSE ;

			\ScisSystem::refreshData1();

			$errors = array();
			$array_count = 0;

			$user_data = $session->get('user_field');
			$id = $session->get('user_id');

			//get User specified Entries ===============================
			$filterCourse = "all";
			$filterYear = "all";
			$filterStatus ="all";
			$filterExist = false;
			$clearFilter = false;

			//condition for the link in the dashboard
			if($stat == 'cleared')
			{
				$filterStatus = 1;
				$filterExist = true;
			} 
			else if($stat == 'uncleared')
			{
				$filterStatus = '0';
				$filterExist = true;
			}

			if($this->request->getGET('clearFilter') == "Clear Filter")
			{
				$clearFilter = true;
				// echo "Filter Cleared";
			}
			else
			{
				//filter for students
				if($courseFilter = $this->request->getGET('course'))
				{
					if($courseFilter != "all")
					{
						$filterCourse = $courseFilter;
						// echo $filterCourse."<br>";
					}
					$filterExist = true;
				}

				//filter for Year
				if($yearFilter = $this->request->getGET('year'))
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
				if($statFilter = $this->request->getGET('status'))
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

			if($defField = $session->get("defField"))
			{
				if($defField == $field_id)
				{
					if($session->get("defFilter") && !$filterExist && !$clearFilter)
					{
						$filters = $session->get("defFilter");
						$filterCourse = $filters['course'];
						$filterYear = $filters['year'];
						$filterStatus = $filters['status'];
					}			
					else
					{
						$filters = [
							'course' => $filterCourse,
							'year' => $filterYear,
							'status' => $filterStatus,
						];
						$session->set("defFilter",$filters);
					} 	
				}
				else
				{
					$filters = [
						'course' => $filterCourse,
						'year' => $filterYear,
						'status' => $filterStatus,
					];
					$session->set("defFilter",$filters);

					$session->set("defField",$field_id);
				}
			}
			else
			{
				$session->set("defField",$field_id);
			}	
			//End of getting Specified entries ===========================	

			$cEntries_model = new \App\Models\ClearanceEntriesModel();
			$cF_model = new \App\Models\ClearanceFieldsModel();
			$field = $cF_model->find($field_id);
			$field_name = $field['field_name'];
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


			$doField_condition = [
				'deleted' => 0,
				'field_name' => "Director's Office",
			];
			$DirectorOfficeID = $cF_model->select('id')->where($doField_condition)->first();

			//Dropdowns Selections
			//Requirements
			$req_model = new \App\Models\RequirementsModel();
			$req_list = $req_model->where('deleted',0)->findAll();

			$requirements = array();
			$array_count = 0;
			foreach($req_list as $req)
			{
				if($req['clearance_field_id'] == $field_id)
				{
					$requirements[$array_count++] = [
						'id' => $req['id'],
						'name' => $req['requirement_name'],
					];
				}
			}

			//Courses
			$course_model = new \App\Models\CoursesModel();
			$courses = $course_model->where('deleted',0)->findAll();

			if($gradClearance)
			{
				$db = \Config\Database::connect();
				$scis_model = new \App\Models\ScisModel($db);

				$clearanceEntry_list = $scis_model->getGradClearanceEntriesForCO($field_id,$filterCourse,$filterStatus,$pos_id,$co_org);
						
				$Entries = array();
				$array_count = 0;
				foreach($clearanceEntry_list as $entry)
				{
					$entry_id = $entry->entry_id;
					$approvedClearance = FALSE;

					$def_condition = [
						'deleted' => 0,
						'clearance_entry_id' => $entry_id,
					];
					$deficiencies = $scis_model->getEntryDeficiencies($entry_id);

					$dField_condition = [
						'deleted' => 0,
						'clearance_form_id' => $entry->form_id,
						'clearance_field_id' => $DirectorOfficeID['id'],
						'clearance_field_status' => 1,
					];

					if($cEntries_model->where($dField_condition)->first() || $entry->form_status == 1)
					{
						$approvedClearance = TRUE;
					}

					$Entries[$array_count++] = [
						'form_id' => $entry->form_id,
						'student_number' => $entry->student_number,
						'student_id' => $entry->student_id,
						'student_name' => $entry->student_name, 
						'course' => $entry->course, 
						'year' => $entry->year, 
						'entry_id' => $entry->entry_id,
						'clearance_field_id' => $entry->clearance_field_id,
						'deficiencies' => $deficiencies,
						'status' => $entry->status,
						'approvedClearance' => $approvedClearance,
					];
				}

				$data['gradClearance'] = TRUE;
				$data['Office'] = $field_name;
				$data['Position'] = $position_name;
			   	$data['field_id'] = $field_id;
			   	$data['pos_id'] = $pos_id;
				$data['errors'] = $errors;
				$data['Entries'] = $Entries;
				$data['clearance_check'] = true;
				$data['Requirements'] = $requirements;
				$data['courses'] = $courses;
				$data['CurrentPeriod'] = FALSE;
				$data['co_ID'] = $id;
				$data['courseFil'] = $filterCourse;
				$data['yearFil'] = $filterYear;
				$data['statusFil'] = $filterStatus;

				if($session->get('admin_access'))
				{				
				    $data['user_title']	= 'Administrator';
			   		$data['clearanceData'] = $session->get('clearance_data');    
				}
				else if($session->get('co_access'))
				{
					$data['user_title']	= 'Clearance Officer';
				}

				return view('manage_deficiency',$data);
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

						$clearanceEntry_list = $scis_model->getClearanceEntriesForCO($currentPeriod['id'],$field_id,$filterCourse,$filterYear,$filterStatus,$pos_id,$co_org);
						
						$Entries = array();
						$array_count = 0;
						foreach($clearanceEntry_list as $entry)
						{
							$entry_id = $entry->entry_id;
							$approvedClearance = FALSE;

							$def_condition = [
								'deleted' => 0,
								'clearance_entry_id' => $entry_id,
							];
							$deficiencies = $scis_model->getEntryDeficiencies($entry_id);

							$dField_condition = [
								'deleted' => 0,
								'clearance_form_id' => $entry->form_id,
								'clearance_field_id' => $DirectorOfficeID['id'],
								'clearance_field_status' => 1,
							];

							if($cEntries_model->where($dField_condition)->first() || $entry->form_status == 1)
							{
								$approvedClearance = TRUE;
							}

							$Entries[$array_count++] = [
								'form_id' => $entry->form_id,
								'student_number' => $entry->student_number,
								'student_id' => $entry->student_id,
								'student_name' => $entry->student_name, 
								'course' => $entry->course, 
								'year' => $entry->year, 
								'entry_id' => $entry->entry_id,
								'clearance_field_id' => $entry->clearance_field_id,
								'deficiencies' => $deficiencies,
								'status' => $entry->status,
								'approvedClearance' => $approvedClearance,
							];
						}

				    	$data['Office'] = $field_name;
				    	$data['Position'] = $position_name;
				    	$data['field_id'] = $field_id;
				    	$data['pos_id'] = $pos_id;
						$data['errors'] = $errors;
						$data['Entries'] = $Entries;
						$data['clearance_check'] = true;
						$data['Requirements'] = $requirements;
						$data['courses'] = $courses;
						$data['CurrentPeriod'] = $currentPeriod['id'];
						$data['co_ID'] = $id;
						$data['courseFil'] = $filterCourse;
						$data['yearFil'] = $filterYear;
						$data['statusFil'] = $filterStatus;

						if($session->get('admin_access'))
						{				
						    $data['user_title']	= 'Administrator';
				    		$data['clearanceData'] = $session->get('clearance_data');    
						}
						else if($session->get('co_access'))
						{
							$data['user_title']	= 'Clearance Officer';
						}

						return view('manage_deficiency',$data);
					}
				}
				else
				{
					$errors[$array_count++] = "There is no Existing Clearance Period at the moment";
				}
			}

			
			$data['Office'] = $field_name;
			$data['Position'] = $position_name;
			$data['field_id'] = $field_id;
			$data['errors'] = $errors;
			$data['Entries'] = array();
			$data['clearance_check'] = false;
			$data['Requirements'] = $requirements;
			$data['courses'] = $courses;
			$data['CurrentPeriod'] = "";
			$data['co_ID'] = $id;

			return view('manage_deficiency',$data);
		}
		else
		{
			return view('site/no_permission',$data);
		}
	}

	public function MultipleTagging()
	{
		$session = session();
		
		// for alert messages
		$studentsTags = []; //successTags
		$studentsCount = 0;
		$failedTags = []; //Failed Tags
		$failStudentCount = 0;

		//For modify stamping
		date_default_timezone_set('Asia/Manila');
		$modidy_stamp = date('Y-m-d H:i:s', time());

		$Students = $this->request->getPOST('SelectedStudent');
		$Deficiencies =  $this->request->getPOST('SelectedDeficiency');

		$clearCheck = false; //check if the multi tag has clear in it
		$alreadyClearCheck = false;
			 
		//Models
		$deficiencies_model = new \App\Models\DeficienciesModel();
		$cEntries_model = new \App\Models\ClearanceEntriesModel();
		$req_model = new \App\Models\RequirementsModel();
		$sub_model = new \App\Models\SubmissionsModel();
		$students_model = new \App\Models\StudentsModel();
		$cForm_model = new \App\Models\ClearanceFormsModel();
		$cField_model = new \App\Models\ClearanceFieldsModel();
		$user_model = new \App\Models\UsersModel();

		$db = \Config\Database::connect();
		$scis_model = new \App\Models\ScisModel($db);

		$doField_condition = [
			'deleted' => 0,
			'field_name' => "Director's Office",
		];
		$DirectorOfficeID = $cField_model->select('id')->where($doField_condition)->first();

		$alreadyApprovedError = array();

		foreach($Students as $stud)
		{
			//get Student's Info
			$entry = $cEntries_model->find($stud);
			$clearanceForm = $cForm_model->find($entry['clearance_form_id']);
			$student = $students_model->find($clearanceForm['student_id']);
			$student_info = $user_model->find($student['user_id']);
			$student_name = $student_info['first_name']." ".$student_info['middle_name']." ".$student_info['last_name']." ".$student_info['suffix_name'];

			//get current clearance field data
			$current_field = $cField_model->find($entry['clearance_field_id']);
			$current_field_name = $current_field['field_name'];

			$studDeficiency_count = 0; //count of each student's deficiency / for alert messages				
			$failTagsCount = 0;

			$taggingFailed = false;
			$taggingSuccess = false;

			$studentDeficiency_list = $scis_model->getEntryDeficiencies($stud);

			$dField_condition = [
				'deleted' => 0,
				'clearance_form_id' => $clearanceForm['id'],
				'clearance_field_id' => $DirectorOfficeID['id'],
				'clearance_field_status' => 1,
			];

			if($cEntries_model->where($dField_condition)->first() || $clearanceForm['clearance_status'] == 1)
			{
				array_push($alreadyApprovedError,"Fail to tag ".$student_name.". The Student's Clearance was already signed by the Director/Registrar");
			}
			else
			{
				//For Notification
				$sender_id = $session->get('user_id');
				$Subject = "Deficiency Tagged";
				$Message = "Dear Student,<br> ";
				$receiver_id = $student['user_id'];
				$tagged_def = "";
			
				foreach ($Deficiencies as $def) {
					if($def == "clear")
					{
						$checkClearStatusData = [
							'id' => $stud,
							'clearance_field_status' => 1,
							'deleted' => 0,
						];
						if($cEntries_model->where($checkClearStatusData)->first())
						{
							$alreadyClearCheck = true;
							$taggingFailed = true;	

							$failedTags[$failStudentCount]["tag"][$failTagsCount++] = "Clear";						
						}
						else
						{
							$clearCheck = true;

							$data = [
								'clearance_field_status' => 1, 
								'modified' => $modidy_stamp, 
							];

							$cEntries_model->update($stud,$data);

							$existingDeficiencies = $deficiencies_model->where('clearance_entry_id',$stud)->findAll();

							foreach($existingDeficiencies as $eDef )
							{
								$def_id = $eDef['id'];
								$data = [
									'status' => 2, 
									'modified' => $modidy_stamp, 
								];

								$deficiencies_model->update($def_id,$data);
							}

							$studentsTags[$studentsCount]["tag"][$studDeficiency_count++] = "Clear";
							$taggingSuccess = true;

							$Message = $Message."&emsp; You have been cleared in <b>".$current_field_name."</b>";
						}

						break;
					}
					else
					{
						$requirement = $req_model->find($def);
						$req_name = $requirement['requirement_name'];

						$check_exist = 0; //check if deficiency tag already exist
						foreach($studentDeficiency_list as $studDef)
						{
							if($stud == $studDef->entry_id && $def == $studDef->req_id)
							{
								$check_exist = 1;

								$taggingFailed = true;
								$failedTags[$failStudentCount]['tag'][$failTagsCount++] = $req_name;
							}
						}

						if($check_exist == 0)
						{
							$data = [
								'clearance_entry_id' => $stud,
								'requirement_id' => $def
							];

							if($deficiencies_model->insert($data) != false)
							{
								$data = [
									'clearance_field_status' => 0, 
									'modified' => $modidy_stamp, 
								];

								$cEntries_model->update($stud,$data);

								$def_id = $deficiencies_model->getInsertID();

								$req = $req_model->where('id',$def)->first();

								if($req['submission_type'] == 1)
								{
									$sub_data = [
										'deficiency_id' => $def_id,
									];

									$sub_model->insert($sub_data);
								}

								$studentsTags[$studentsCount]["tag"][$studDeficiency_count++] = $req_name;
								$taggingSuccess = true;

								$tagged_def = $tagged_def."<li>".$req_name."</li>";
							}
						}
					}
				}

				if($taggingFailed)
				{
					$failedTags[$failStudentCount]['student'] = $student_name;
					$failStudentCount++;
				}
					
				if($taggingSuccess)
				{
					$studentsTags[$studentsCount]["student"] = $student_name;
					$studentsCount++;

					if($tagged_def != "")
					{
						$Message = $Message."&emsp;You have been tagged with the following deficiencies in ".$current_field_name.":<b><ol>".$tagged_def."</ol></b>";
					}

					\ScisSystem::CreateNotification($sender_id,$Subject,$Message,$receiver_id);		
				}
			}		
		}

		//Log Activity
		$log_user = $session->get('user_id');
		$log_message = 'Tagged Multiple deficiencies for a number of students';

		\ScisSystem::logActivity($log_user,$log_message);

		$successMessages = array();
		$successCount = 0;
		$clear_message = "";

		$totalClearTaggedStud = 0;

		foreach($studentsTags as $studTags)
		{
			if($studTags['tag'][0] == "Clear")
			{
				$totalClearTaggedStud++;
			}
		}
		$taggedStudCount = 0;
			
		foreach($studentsTags as $studTags)
		{
			if($clearCheck && $studTags['tag'][0] == "Clear")
			{
				$taggedStudCount++;
				$clear_message = $clear_message.$studTags['student'];
				if($taggedStudCount < $totalClearTaggedStud-1 )
				{
					$clear_message = $clear_message.", ";
				} 
				else if($taggedStudCount == $totalClearTaggedStud-1 )
				{
					$clear_message = $clear_message.", and ";
				}
			}
			else if(!empty($studTags['tag']))
			{
				$successMessages[$successCount] = $studTags['student']." Successfully Tagged with the following deficiencies: ";

				$totalTags = count($studTags['tag']);
				$tagCount = 0;
				foreach($studTags['tag'] as $tag)
				{
					$tagCount++;
					$successMessages[$successCount] = $successMessages[$successCount]." ".$tag;
					if($tagCount < $totalTags-1 )
					{
						$successMessages[$successCount] = $successMessages[$successCount].", ";
					} 
					else if($tagCount == $totalTags-1 )
					{
						$successMessages[$successCount] = $successMessages[$successCount].", and ";
					}
				}		

				$successMessages[$successCount] = $successMessages[$successCount].".";			
			}

			$successCount++;
		}

		$errors = array();
		$errorCount = 0;

		if($clearCheck)
		{
			$successMessages = [$clear_message." has been cleared"];
		}

		$alreadyClear_message = "";

		$totalAlreadyClearedTags = 0;

		foreach($failedTags as $fail)
		{
			if($fail['tag'][0] == "Clear")
			{
				$totalAlreadyClearedTags++;
			}
		}

		$failTagCount = 0;

		foreach($failedTags as $fail)
		{

			if($alreadyClearCheck && $fail['tag'][0] == "Clear")
			{
				$failTagCount++;
				$alreadyClear_message = $alreadyClear_message.$fail['student'];

				if($failTagCount < $totalAlreadyClearedTags-1 )
				{
					$alreadyClear_message = $alreadyClear_message.", ";
				} 
				else if($failTagCount == $totalAlreadyClearedTags-1 )
				{
					$alreadyClear_message = $alreadyClear_message.", and ";
				}
			}
			else if(!empty($fail['tag']))
			{
				$errors[$errorCount] = $fail['student']." was already tagged with ";

				$totalFailedTags = count($fail['tag']);
				$failedTagCount = 0;
				foreach($fail['tag'] as $tagFails)
				{
					$failedTagCount++;
					$errors[$errorCount] = $errors[$errorCount].$tagFails;
					if($failedTagCount < $totalFailedTags-1)
					{
						$errors[$errorCount] = $errors[$errorCount].", ";
					}
					else if($failedTagCount == $totalFailedTags-1)
					{
						$errors[$errorCount] = $errors[$errorCount].", and ";
					}
				} 

				$errors[$errorCount] = $errors[$errorCount].".";
			}

			$errorCount++;
		}

		if($alreadyClearCheck)
		{
			$errors[$errorCount++] = $alreadyClear_message." was already cleared";
		}

		foreach($alreadyApprovedError as $apError)
		{
			array_push($errors, $apError);
		}
			
		$session->setFlashdata('success_messages',$successMessages);
		$session->setFlashdata('err_messages',$errors);

		return redirect()->back()->withInput();
	}

	public function SingleTagging()
	{
		$session = session();
		
		$entID = $this->request->getPOST('entID');
		$Deficiencies =  $this->request->getPOST('SelectedDeficiency');

		//For modify stamping
		date_default_timezone_set('Asia/Manila');
		$modidy_stamp = date('Y-m-d H:i:s', time());

		$failTag = array(); //failed to tag 
		$failTagCount = 0;
		$tagged = array();
		$taggedCount = 0;

		//Models
		$db = \Config\Database::connect();
		$scis_model = new \App\Models\ScisModel($db);

		$deficiencies_model = new \App\Models\DeficienciesModel();
		$req_model = new \App\Models\RequirementsModel();			
		$sub_model = new \App\Models\SubmissionsModel();
		$cEntries_model = new \App\Models\ClearanceEntriesModel();
		$cForms_model = new \App\Models\ClearanceFormsModel();
		$students_model = new \App\Models\StudentsModel();
		$cField_model = new \App\Models\ClearanceFieldsModel();
		$user_model = new \App\Models\UsersModel();

		$cEntry = $cEntries_model->find($entID);

		$cField = $cField_model->find($cEntry['clearance_field_id']);

		$cForm =  $cForms_model->find($cEntry['clearance_form_id']);

		$student = $students_model->find($cForm['student_id']);

		$student_info = $user_model->find($student['user_id']);
		$student_name = $student_info['first_name']." ".$student_info['middle_name']." ".$student_info['last_name']." ".$student_info['suffix_name'];

		$studentDeficiency_list = $scis_model->getEntryDeficiencies($entID);

		//For Notification
		$sender_id = $session->get('user_id');
		$Subject = "Deficiency Tagged";
		$Message = "Dear Student,<br>";
		$receiver_id = $student['user_id'];

		$tagged_def = "";

		foreach($Deficiencies as $def)
		{
			$check_exist = 0; //check if deficiency tag already exist
			$tagged_deficiency = $req_model->find($def); //Tagged Deficiency Info

			foreach($studentDeficiency_list as $studDef)
			{
				if($entID == $studDef->entry_id && $def == $studDef->req_id)
				{
					$check_exist = 1;

					$failTag[$failTagCount++] = $tagged_deficiency['requirement_name'];
				}
			}

			if($check_exist == 0)
			{
				$data = [
					'clearance_entry_id' => $entID,
					'requirement_id' => $def
				];

				if($deficiencies_model->insert($data) != false)
				{
					$entData = [
						'clearance_field_status' => 0, 
						'modified' => $modidy_stamp, 
					];

					$cEntries_model->update($entID,$entData);

					$def_id = $deficiencies_model->getInsertID();

					$req = $req_model->where('id',$def)->first();

					if($req['submission_type'] == 1)
					{
						$data = [
							'deficiency_id' => $def_id,
						];
							$sub_model->insert($data);
					}

					$tagged[$taggedCount++] = $tagged_deficiency['requirement_name'];

					$tagged_def = $tagged_def."<li>".$tagged_deficiency['requirement_name']."</li>";
				}
			}
		}

		$error_message = "";

		if($failTagCount != 0)
		{
			$count = 1;
			foreach($failTag as $fail)
			{
				$error_message = $error_message."<b>".$fail."</b>";
				if($count < $failTagCount-1)
				{
					$error_message = $error_message.", ";
				}
				else if($count == $failTagCount-1)
				{
					$error_message = $error_message.", and ";
				}
				$count++;
			}

			$error_message = $error_message." already Exist for ".$student_name.". The system prevented the tagging";

			$error = [$error_message];

			$session->setFlashdata('err_messages',$error);
		}

		$success_message = "";
		
		if($taggedCount != 0)
		{
			$count = 1;
			foreach($tagged as $success)
			{
				$success_message = $success_message."<b>".$success."</b>";
				if($count < $taggedCount-1)
				{
					$success_message = $success_message.", ";
				}
				else if($count == $taggedCount-1)
				{
					$success_message = $success_message.", and ";
				}
				$count++;
			}

			$success_message = $success_message." was successfully tagged to ".$student_name;

			$success = [$success_message];

			$session->setFlashdata('success_messages',$success);

			//Finalize message for notification
			$Message = $Message."&emsp;You have been tagged with the following deficiencies in ".$cField['field_name'].":<b><ol>".$tagged_def."</ol></b>";

			\ScisSystem::CreateNotification($sender_id,$Subject,$Message,$receiver_id);		
		}

		//Log Activity
		$log_user = $session->get('user_id');
		$log_message = 'Tagged Deficiencies to a Student';

		\ScisSystem::logActivity($log_user,$log_message);

		return redirect()->back()->withInput();
	}

	public function StudentDeficiencies($ent_stud = '')
	{
		\ScisSystem::refreshData1();

		$permission_model = new \App\Models\PermissionsModel();

		$session = session();

		$data = [
			'page_title' => 'Student Deficiencies | PUPT SCIS',
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
		
		if($permission_model->hasPermission($uID,['view_deficiencies']))
		{
			if($ent_stud == '')
			{
				return redirect()->back();
			}
			else
			{
				$id = explode("-",$ent_stud);

				$entry_id = $id[0];
				$student_id = $id[1];
				$field_id = $id[2];
				$period_id = (!empty($id[3])) ? $id[3] : $session->get('clearance_data');

				$db = \Config\Database::connect();
				$scis_model = new \App\Models\ScisModel($db);
				$Deficiency_list = $scis_model->getEntryDeficiencies($entry_id);
				$cEntries_model = new \App\Models\ClearanceEntriesModel();

				$coFields = $session->get('user_co_fields');
				$coPos = "";
				foreach($coFields as $cf)
				{
					if($cf['field_id'] == $field_id)
					{
						$coPos = $cf['position_id'];
					}
				}

				$preReqData = $scis_model->getPreRequisites($coPos);			

				$prCount = 0;
				if($preReqData)
				{
					foreach($preReqData as $PR) {
                       	$preReq_Entry = $scis_model->getCurrentStudentEntries($student_id,$period_id,$PR->position_id);
						$preReqData[$prCount++]->status = (!empty($preReq_Entry->status)) ? $preReq_Entry->status : 0;
					}
					
				}

				//Requirements
				$req_model = new \App\Models\RequirementsModel();
				$req_list = $req_model->where('deleted',0)->findAll();

				$requirements = array();
				$array_count = 0;
				foreach($req_list as $req)
				{
					if($req['clearance_field_id'] == $field_id)
					{
						$requirements[$array_count++] = [
							'id' => $req['id'],
							'name' => $req['requirement_name'],
						];
					}
				}

				$student_info = $scis_model->getStudentInfo($student_id);
				$form_info = $scis_model->getEntryFormInfo($entry_id);

				$dField_condition = [
					'deleted' => 0,
					'clearance_form_id' => $form_info->form_id,
					'clearance_field_id' => $session->get('DirectorOfficeID'),
					'clearance_field_status' => 1,
				];

				$data['approvedClearance'] = ($cEntries_model->where($dField_condition)->first() || $form_info->status == 1) ? TRUE : FALSE;

				$cF_model = new \App\Models\ClearanceFieldsModel();
				$field = $cF_model->find($field_id);
				$field_name = $field['field_name'];

			    $data['field_id'] = $field_id;
			    $data['Office'] = $field_name;
				$data['Deficiencies'] = $Deficiency_list;
				$data['student_info'] = $student_info;
				//$data['period'] = $period_id;
				$data['magic'] = $ent_stud;
				$data['preRequisites'] = $preReqData;
				$data['entryID'] = $entry_id;
				$data['Requirements'] = $requirements;

				$data['EditDeficiencies'] = ($permission_model->hasPermission($uID,'edit_deficiencies')) ? TRUE : FALSE;
				$data['RemoveDeficiencies'] = ($permission_model->hasPermission($uID,'delete_deficiencies')) ? TRUE : FALSE;
				$data['TagDeficiencies'] =  ($permission_model->hasPermission($uID,'add_deficiencies')) ? TRUE : FALSE;

				return view('view_student_deficiency',$data);
			}
		}
		else
		{
			return view('site/no_permission',$data);
		}
	}

	public function ClearDeficiency($id = '')
	{
		$session = session();
		
		if($id == '')
		{
			return redirect()->back();
		}
		else
		{
			$def_ent_stud = explode("-",$id);

			$def_id = $def_ent_stud[0];
			$student_id = $def_ent_stud[2];
			$ent_stud_id = $def_ent_stud[1]."-".$def_ent_stud[2]."-".$def_ent_stud[3];

			$deficiency_model = new \App\Models\DeficienciesModel();
			$entry_model = new \App\Models\ClearanceEntriesModel();
			$req_model = new \App\Models\RequirementsModel();
			$students_model = new \App\Models\StudentsModel();
			$cFields_model = new \App\Models\ClearanceFieldsModel();
			$user_model = new \App\Models\UsersModel();

			$deficiency = $deficiency_model->find($def_id);

			$requirement = $req_model->find($deficiency['requirement_id']);

			$message = array();
			$messageCount = 0;

			date_default_timezone_set('Asia/Manila');
			$modify_stamp = date('Y-m-d H:i:s', time());

			$data = [
				'status' => 2,
				'modified' => $modify_stamp,
			];

			if($deficiency_model->update($def_id,$data))
			{
				$message[$messageCount++] = $requirement['requirement_name']." Successfully Cleared";

				//get Student name
				$student = $students_model->find($student_id);

				$student_info = $user_model->find($student['user_id']);
				$student_name = $student_info['first_name']." ".$student_info['middle_name']." ".$student_info['last_name']." ".$student_info['suffix_name'];

				//get Field Name
				$entry = $entry_model->find($def_ent_stud[1]);
				$field = $cFields_model->find($entry['clearance_field_id']);
				$fieldName = $field['field_name'];

				//Create Notification
				$sender_id = $session->get('user_id');
				$Subject = "Clearance Status Update";
				$Message = "Dear Student, You have successfully cleared <b>".$requirement['requirement_name']."</b> in <b>".$fieldName."</b>";
				$receiver_id = $student['user_id'];

				\ScisSystem::CreateNotification($sender_id,$Subject,$Message,$receiver_id);

				//Check if all Deficiency is Cleared
				$db = \Config\Database::connect();
				$scis_model = new \App\Models\ScisModel($db);
				$Deficiency_list = $scis_model->getEntryDeficiencies($def_ent_stud[1]);

				$clear_check = TRUE;
				foreach($Deficiency_list as $def)
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

					$entry_model->update($def_ent_stud[1],$data);

					$message[$messageCount++] = "All Deficiency has been cleared. ".$student_name." has cleared ".$fieldName;

					//Create Notification
					$Message = "Dear Student, You have successfully cleared all your accountabilities in <b>".$fieldName."</b>";

					\ScisSystem::CreateNotification($sender_id,$Subject,$Message,$receiver_id);
				}

				//Log Activity
				$log_user = $session->get('user_id');
				$log_message = 'Cleared a Student\'s Deficiency';

				\ScisSystem::logActivity($log_user,$log_message);

				$session->setFlashdata('success_messages',$message);
			}	

			return redirect()->to('/Deficiencies/StudentDeficiencies/'.$ent_stud_id)->withInput();				
		}
		
		
	}

	public function UnclearDeficiency($id = '')
	{
		$session = session();
		
		if($id == '')
		{
			return redirect()->back();
		}
		else
		{
			$def_ent_stud = explode("-",$id);

			$def_id = $def_ent_stud[0];
			$student_id = $def_ent_stud[2];
			$ent_stud_id = $def_ent_stud[1]."-".$def_ent_stud[2]."-".$def_ent_stud[3];

			//Models
			$deficiency_model = new \App\Models\DeficienciesModel();
			$entry_model = new \App\Models\ClearanceEntriesModel();
			$req_model = new \App\Models\RequirementsModel();
			$students_model = new \App\Models\StudentsModel();
			$cFields_model = new \App\Models\ClearanceFieldsModel();
			$user_model = new \App\Models\UsersModel();

			$deficiency = $deficiency_model->find($def_id);

			$requirement = $req_model->find($deficiency['requirement_id']);

			$message = array();
			$messageCount = 0;

			date_default_timezone_set('Asia/Manila');
			$modify_stamp = date('Y-m-d H:i:s', time());

			$data = [
				'status' => 0,
				'modified' => $modify_stamp,
			];

			if($deficiency_model->update($def_id,$data) != false)
			{
				$message[$messageCount++] = $requirement['requirement_name']." Successfully Uncleared";

				//get Student name
				$student = $students_model->find($student_id);

				$student_info = $user_model->find($student['user_id']);
				$student_name = $student_info['first_name']." ".$student_info['middle_name']." ".$student_info['last_name']." ".$student_info['suffix_name']; 

				//get Field Name
				$entry = $entry_model->find($def_ent_stud[1]);
				$field = $cFields_model->find($entry['clearance_field_id']);
				$fieldName = $field['field_name'];

				//Create Notification
				$sender_id = $session->get('user_id');
				$Subject = "Clearance Status Update";
				$Message = "Dear Student, Your Deficiency, <b>".$requirement['requirement_name']."</b> in <b>".$fieldName."</b>, has been uncleared. Please settle it accordingly";
				$receiver_id = $student['user_id'];

				\ScisSystem::CreateNotification($sender_id,$Subject,$Message,$receiver_id);

				//Ensure Clearance Entry is not yet cleared
				$entry = $entry_model->find($def_ent_stud[1]);

				if($entry['clearance_field_status'] != 0)
				{
					$data = [
						'clearance_field_status' => 0,
						'modified' => $modify_stamp,
					];

					if($entry_model->update($def_ent_stud[1],$data) != false)
					{
						$message[$messageCount++] = $student_name." became uncleared in ".$fieldName;
					}
				}

				//Log Activity
				$log_user = $session->get('user_id');
				$log_message = 'Uncleared a Student\'s Deficiency';

				\ScisSystem::logActivity($log_user,$log_message);

				$session->setFlashdata('success_messages',$message);

				//Create Notification
				$Message = "Dear Student, Due to the revocation of your cleared status on <b>".$requirement['requirement_name']."</b>. Your cleared status on <b>".$fieldName."</b> was also revoked. Please settle it accordingly";

				\ScisSystem::CreateNotification($sender_id,$Subject,$Message,$receiver_id);
			}

			return redirect()->to('/Deficiencies/StudentDeficiencies/'.$ent_stud_id)->withInput();
		}
	}

	public function RemoveDeficiency($id = '')
	{
		$session = session();
		
		if($id == '')
		{
			return redirect()->back();
		}
		else
		{
			$def_ent_stud = explode("-",$id);

			$def_id = $def_ent_stud[0];
			$ent_stud_id = $def_ent_stud[1]."-".$def_ent_stud[2]."-".$def_ent_stud[3];

			$deficiency_model = new \App\Models\DeficienciesModel();
			$req_model = new \App\Models\RequirementsModel();
			$entry_model = new \App\Models\ClearanceEntriesModel();
			$cFields_model = new \App\Models\ClearanceFieldsModel();
			$students_model = new \App\Models\StudentsModel();
			$user_model = new \App\Models\UsersModel();

			$deficiency = $deficiency_model->find($def_id);

			$requirement = $req_model->find($deficiency['requirement_id']);

			//get Field Name
			$entry = $entry_model->find($def_ent_stud[1]);
			$field = $cFields_model->find($entry['clearance_field_id']);
			$fieldName = $field['field_name'];

			//get Student name
			$student = $students_model->find($def_ent_stud[2]);

			$student_info = $user_model->find($student['user_id']);
			$student_name = $student_info['first_name']." ".$student_info['middle_name']." ".$student_info['last_name']." ".$student_info['suffix_name']; 

			$message = array();
			$messageCount = 0;

			date_default_timezone_set('Asia/Manila');
			$modify_stamp = $delete_stamp = date('Y-m-d H:i:s', time());

			$data = [
				'deleted' => 1,
				'deleted_date' => $delete_stamp,
			];

			if($deficiency_model->update($def_id,$data) != false)
			{
				$message[$messageCount++] = $requirement['requirement_name']." Successfully Removed";

				//Log Activity
				$log_user = $session->get('user_id');
				$log_message = 'Remove a Student\'s Deficiency';

				\ScisSystem::logActivity($log_user,$log_message);

				$session->setFlashdata('success_messages',$message);

				//Create Notification
				$sender_id = $session->get('user_id');
				$Subject = "Clearance Status Update";
				$Message = "Dear Student, Your Deficiency, <b>".$requirement['requirement_name']."</b> in <b>".$fieldName."</b>, has been removed";
				$receiver_id = $student['user_id'];

				\ScisSystem::CreateNotification($sender_id,$Subject,$Message,$receiver_id);

				//Check if all Deficiency is Cleared
				$db = \Config\Database::connect();
				$scis_model = new \App\Models\ScisModel($db);
				$Deficiency_list = $scis_model->getEntryDeficiencies($def_ent_stud[1]);

				$clear_check = TRUE;
				foreach($Deficiency_list as $def)
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

					$entry_model->update($def_ent_stud[1],$data);

					$message[$messageCount++] = "All Deficiency has been cleared. ".$student_name." has cleared ".$fieldName;

					//Create Notification
					$Message = "Dear Student, You have successfully cleared all your accountabilities in <b>".$fieldName."</b>";

					\ScisSystem::CreateNotification($sender_id,$Subject,$Message,$receiver_id);
				}
			}				

			return redirect()->to('/Deficiencies/StudentDeficiencies/'.$ent_stud_id)->withInput();
		}
	}

	//for individual clear button
	public function clearAllDeficiency($entryID)
	{
		$session = session();
		//Models
		$def_model = new \App\Models\DeficienciesModel();
		$cEntry_model = new \App\Models\ClearanceEntriesModel();
		$cForm_model = new \App\Models\ClearanceFormsModel();
		$student_model = new \App\Models\StudentsModel();
		$cField_model = new \App\Models\ClearanceFieldsModel();
		$user_model = new \App\Models\UsersModel();

		$entryCurrentData = $cEntry_model->find($entryID);
		$formData = $cForm_model->find($entryCurrentData['clearance_form_id']);
		$studentData = $student_model->find($formData['student_id']);

		$student_info = $user_model->find($studentData['user_id']);
		$name = $student_info['first_name']." ".$student_info['middle_name']." ".$student_info['last_name']." ".$student_info['suffix_name'];

		$cFieldData = $cField_model->find($entryCurrentData['clearance_field_id']);

		$Def_builder = $def_model->builder();

		date_default_timezone_set('Asia/Manila');
		$modify_stamp = date('Y-m-d H:i:s', time());

		$defData = [
			'status' => 2,
			'modified' => $modify_stamp,
		];
		$Def_builder->set($defData)->where('clearance_entry_id',$entryID)->update();

		$entData = [
			'clearance_field_status' => 1,
			'modified' => $modify_stamp,
		];
		
		$cEntry_model->update($entryID,$entData);

		session()->setFlashData('success_messages',[$name.' Successfully Cleared']);

		//Log Activity
		$log_user = $session->get('user_id');
		$log_message = 'Cleared A User : '.$name;

		\ScisSystem::logActivity($log_user,$log_message);

		//Create Notification
		$sender_id = $session->get('user_id');
		$Subject = "Clearance Update";
		$Message = "Dear Student, &emsp; You have been cleared in <b>".$cFieldData['field_name']."</b>";
		$receiver_id = $studentData['user_id'];

		\ScisSystem::CreateNotification($sender_id,$Subject,$Message,$receiver_id);

		return redirect()->back()->withInput();
	}
}