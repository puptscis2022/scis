<?php

namespace App\Controllers;
use App\Libraries\Hash;
use App\Libraries\Reports;

use TCPDF;

class ClearanceManagementx extends BaseController
{
	public function __construct(){
		helper('ScisSystem');
	}

	public function index()
	{
		return redirect()->back()->to('/');
	}

	public function InitiateClearancePage()
	{
		$session = session();
		if($session->get('admin_access'))
		{
			\ScisSystem::refreshData1();

			$errors = array();
			$error_count = 0;

			$db = \Config\Database::connect();
			$scis_model = new \App\Models\ScisModel($db);

			$positions = $scis_model->getPositionsList();
			foreach($positions as $row)
			{
				if($row->co_id == null && $row->field != 'Student Organization')
				{
					$errors[$error_count++] = "There is no Officer assigned as ".$row->name." for ".$row->field;
				}
			}

			$org_model = new \App\Models\StudentOrganizationsModel();
			$organizations = $org_model->where('deleted',0)->findAll();
			$numOfOrg = $org_model->where('deleted',0)->countAllResults();
			$orgPositions = $scis_model->getOrganizationOfficersList();
			foreach($positions as $row1)
			{
				if($row1->field == 'Student Organization')
				{
					$orgOfficers = 0;
					foreach($organizations as $row2)
					{	
						
						foreach($orgPositions as $row3)
						{
							if($row3->pos_name == $row1->name && $row3->org_name == $row2['organization_name'])
							{
								$orgOfficers++;
							}
						}						
					}
					if($orgOfficers < $numOfOrg)
					{
						$errors[$error_count++] = "There is an empty position(s) for ".$row1->name." in Student Organizations";
					}
				}
			}

			$cFields_model = new \App\Models\ClearanceFieldsModel();
			$clearanceFields = $cFields_model->where('deleted',0)->findAll();

			foreach($clearanceFields as $field)
			{
				$position_for_field_exist = 0;
				foreach($positions as $pos)
				{					
					if($pos->field_id == $field['id'] || $field['field_name'] == "Director's Office")
					{
						$position_for_field_exist = 1;
					}
				}

				if($position_for_field_exist == 0)
				{
					$errors[$error_count++] = "There is no position for ".$field['field_name'];
				}
			}

			$cPeriod_model = new \App\Models\ExistingClearancePeriodsModel();
			$currentPeriod = $cPeriod_model->where('deleted',0)->orderBy('id',"DESC")->findAll();

			date_default_timezone_set('Asia/Manila');
			$today = date("Y-m-d");
			
			if(!empty($currentPeriod))
			{
				if($today <= $currentPeriod[0]['end_date'])
				{
					$errors[$error_count++] = "There is still an ongoing Clearance Period until ".$currentPeriod[0]['end_date'];
				}
			}

			//Dropdown Menus
			$scYear_model = new \App\Models\ScYearsModel();
			$scYears = $scYear_model->where('deleted',0)->findAll();

			$cTypes_model = new \App\Models\ClearanceTypesModel();
			$clearanceTypes = $cTypes_model->where('deleted',0)->findAll();

			$session->setFlashdata('err_messages',$errors);

			$data = [
			    'page_title' 	=> 'Initiate Clearance | PUPT SCIS',
			    'user_role'		=> $session->get('user_role'),
			    'activeClearance' => $session->get('ongoingClearance'),
			    'user_title'	=> 'Administrator',
			    'user_notifications' => $session->get('user_notifications'),
			    'user_fields' => $session->get('user_co_fields'),
			    'user_gradFields' => $session->get('user_co_gradFields'),
			    'user_subject_resp' => $session->get('user_subject_handled'),			    
			    'Name'			=> $session->get('user_name'),
			    'profilePic'			=> $session->get('user_pic'),
			    'clearanceData'			=> $session->get('clearance_data'),
			    'eligibilityCheck' => $errors,
			    'ScYears' => $scYears,
			    'ClearanceTypes' => $clearanceTypes,
			];

			return view('admin/initiate_clearance', $data);
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function InitiateClearance()
	{
		$session = session();
		if($session->get('admin_access'))
		{
			$initiatedClearanceType = $this->request->getPOST('clearanceType');
			$initiatedClearanceScYear = $this->request->getPOST('schoolYear');
			$initiatedClearanceSem = $this->request->getPOST('semester');
			$initiatedClearanceStart = $this->request->getPOST('clearanceStartDate');
			$initiatedClearanceDue = $this->request->getPOST('clearanceDueDate');

			$clearancePeriods_model = new \App\Models\ExistingClearancePeriodsModel();
			$existingPeriods = $clearancePeriods_model->where('deleted',0)->orderBy('id','DESC')->findALL();

			$errors = array();
			$array_count = 0;
			foreach($existingPeriods as $cPeriod)
			{
				if($cPeriod['sc_year_id'] == $initiatedClearanceScYear && $cPeriod['semester'] == $initiatedClearanceSem)
				{
					$errors[$array_count++] = "Clearance Period Already Exist";
					$existence_check = 1;
					break;
				}
			}

			if($initiatedClearanceStart == $initiatedClearanceDue)
			{
				$errors[$array_count++] = "Clearance Due Date must not be set on the same day of the Start Date";
			}

			if($initiatedClearanceStart > $initiatedClearanceDue)
			{
				$errors[$array_count++] = "Clearance Due Date must not be set before the day of the Start Date";
			}

			date_default_timezone_set('Asia/Manila');
			$today = date("Y-m-d");

			if($today > $initiatedClearanceStart)
			{
				$errors[$array_count++] = "Clearance Start Date must not be set before today";
			}
			
			if(!empty($errors))
			{
				$session->setFlashdata('err_messages',$errors);
				return redirect()->back()->withInput();	
			}
			else
			{
				$cPeriod_data = [
					'sc_year_id' => $initiatedClearanceScYear, 
					'semester' => $initiatedClearanceSem, 
					'start_date' => $initiatedClearanceStart,
					'end_date' => $initiatedClearanceDue,
				];

				$clearancePeriods_model->insert($cPeriod_data);

				$student_model = new \App\Models\StudentsModel();
				$students = $student_model->where('deleted',0)->findAll();

			//Creating Clearance Forms ==============================================
				//Create Semestral Clearance Form
				$currentPeriodID = $clearancePeriods_model->getInsertID();
				$cForm_model = new \App\Models\ClearanceFormsModel();

				if($initiatedClearanceType == 1 || $initiatedClearanceType == 3) 
				{
					echo "Semestral Clearance Initiated <br>";
					foreach($students as $stud)
					{
						if($stud['year_level'] > 0)
						{
							$form_data = [
								'student_id' => $stud['id'],
								'clearance_period_data_id' => $currentPeriodID,
								'clearance_type_id' => 1,
							];

							$cForm_model->insert($form_data);
						}
					}
				}
				
				//Create Graduation Clearance Form
				// if($initiatedClearanceType == 2 || $initiatedClearanceType == 3) 
				// {
				// 	echo "Graduation Clearance Initiated";
				// 	foreach($students as $stud)
				// 	{
				// 		if($stud['year_level'] == 0)
				// 		{
				// 			$form_data = [
				// 				'student_id' => $stud['id'],
				// 				'clearance_period_data_id' => $currentPeriodID,
				// 				'clearance_type_id' => 2,
				// 			];

				// 			$cForm_model->insert($form_data);
				// 		}
				// 	}
				// }
				//End of Creating Clearance Form

				//Creating Clearance Entries ========================================
				$blackList_model = new \App\Models\BlackListModel();
				$deficiency_model = new \App\Models\DeficienciesModel();
				$req_model = new \App\Models\RequirementsModel();
				$sub_model = new \App\Models\SubmissionsModel();
				$currentForms = $cForm_model->where('clearance_period_data_id', $currentPeriodID)->findAll();

				$clearanceField_model = new \App\Models\ClearanceFieldsModel();
				$cField_condition = [
					'deleted' => 0,
					'clearance_type_id' => 1,
				];
				$clearanceFields = $clearanceField_model->where($cField_condition)->findAll();

				$db = \Config\Database::connect();
				$scis_model = new \App\Models\ScisModel($db);
				$officer_list = $scis_model->initiatingCleranceEntriesData();

				$clearanceEntry_model = new \App\Models\ClearanceEntriesModel();

				date_default_timezone_set('Asia/Manila');
				$time_stamp = date('Y-m-d H:i:s', time());

				foreach($currentForms as $form)
				{
					$clearanceFormID = $form['id'];
					$clearanceTypeID = $form['clearance_type_id'];
					$studentID = $form['student_id'];

					foreach($clearanceFields as $field)
					{
						$entryID = array();
						echo $field['field_name']."-FieldName<br>";

						if($field['field_name'] == "Director's Office")
						{
							$director = $scis_model->getRoleUsers(5);

							$directorID = ($director) ? $director[0]->id : 0 ;

							$data = [
								'clearance_form_id' => $clearanceFormID, 
								'clearance_field_id' =>$field['id'], 
								'clearance_officer_id' =>$directorID,
							];

							$clearanceEntry_model->insert($data);
							array_push($entryID,$clearanceEntry_model->getInsertID());
						}
						else
						{
							foreach($officer_list as $officer)
							{
								if($field['field_name'] == $officer->field_name && $field['field_name'] == 'Student Organization' && $field['clearance_type_id'] == $clearanceTypeID)
								{
									
									$student_info = $scis_model->getStudentOrg($studentID);

									$student_org_id = $student_info->org_id;

										
									if($officer->field_name == 'Student Organization' && $officer->org_id == $student_org_id)
									{
										$data = [
											'clearance_form_id' => $clearanceFormID, 
											'clearance_field_id' =>$field['id'], 
											'clearance_officer_id' =>$officer->clearance_officer_id,
										];

										$clearanceEntry_model->insert($data);
										array_push($entryID,$clearanceEntry_model->getInsertID());
									}
										
								}
								else if($field['field_name'] == $officer->field_name && $field['field_name'] != 'Student Organization' && $field['clearance_type_id'] == $clearanceTypeID)
								{
									$data = [
										'clearance_form_id' => $clearanceFormID, 
										'clearance_field_id' =>$field['id'], 
										'clearance_officer_id' =>$officer->clearance_officer_id,
									];

									$clearanceEntry_model->insert($data);
									array_push($entryID,$clearanceEntry_model->getInsertID());
								}								
							}
						}					

						//Check if The students is black listed with deficiencies
						$blackList_condition1 = [
							'student_id' => $studentID,
							'clearance_field_id' => $field['id'],
							'deleted'	=> 0,
						];
						//Check if there is blacklist for all student
						$blackList_condition2 = [
							'student_id' => 0,
							'clearance_field_id' => $field['id'],
							'deleted'	=> 0,
						];

						foreach($entryID as $ID)
						{
							if($listInfo = $blackList_model->where($blackList_condition1)->findAll())
							{
								foreach($listInfo as $info)
								{
									$deficiencyData = [
										'clearance_entry_id' => $ID,
										'requirement_id' => $info['requirement_id'],
										'deleted' => 0,
									];

									if(empty($deficiency_model->where($deficiencyData)->first()))
									{
										$deficiency_model->insert($deficiencyData);

										$req_data = $req_model->find($info['requirement_id']);

										if($req_data['submission_type'] == 1)
										{
											$subData = [
												'deficiency_id' => $deficiency_model->getInsertID(),
											];

											$sub_model->insert($subData);
										}
									}

									$deletedBL_data = [
										'deleted' => 1,
										'deleted_date' => $time_stamp,
									];

									$blackList_model->update($info['id'],$deletedBL_data);
								}							
							}
							else if($listInfo = $blackList_model->where($blackList_condition2)->findAll())
							{
								foreach($listInfo as $info)
								{
									$deficiencyData = [
										'clearance_entry_id' => $ID,
										'requirement_id' => $info['requirement_id'],
										'deleted' => 0,
									];

									$req_data = $req_model->find($info['requirement_id']);

									if(empty($deficiency_model->where($deficiencyData)->first()))
									{
										$deficiency_model->insert($deficiencyData);

										if($req_data['submission_type'] == 1)
										{
											$subData = [
												'deficiency_id' => $deficiency_model->getInsertID(),
											];

											$sub_model->insert($subData);
										}
									}
								}	
							}
							else
							{
								if($field['field_name'] != "Director's Office")
								{
									$ClearEntry = [
										'clearance_field_status' => 1,
									];
									$clearanceEntry_model->update($ID,$ClearEntry);
								}							
							}
						}
					}
				}//End of Clearance Entries Creation

				$deletedBL_data = [
					'deleted' => 1,
					'deleted_date' => $time_stamp,
				];

				$remaining_blacklist = $blackList_model->where('deleted',0)->findAll();

				foreach($remaining_blacklist as $rem)
				{
					$blackList_model->update($rem['id'],$deletedBL_data);
				}				

				$sc_year_model = new \App\Models\ScYearsModel();
				$school_year = $sc_year_model->find($initiatedClearanceScYear);

				if($initiatedClearanceSem == 1)
				{
					$sem = '1st';
				} 
				else if($initiatedClearanceSem == 2)
				{
					$sem = '2nd';
				}
				else
				{
					$sem = "Summer";
				}

				//Log Activity
				$log_user = $session->get('user_id');
				$log_message = 'Initiated New Clearance Period';

				\ScisSystem::logActivity($log_user,$log_message);

				//Create Notification
				$sender_id = $session->get('user_id');
				$Subject = "Clearance Initiation";
				$Message = "Attention! A new Clearance Period has been initiated for ".$sem."Semester of School Year ".$school_year['school_year'].". Scheduled Clearance will be on ".$initiatedClearanceStart." until ".$initiatedClearanceDue;
				$receiver_id = 0;

				\ScisSystem::CreateNotification($sender_id,$Subject,$Message,$receiver_id);

				$session->setFlashdata("success_messages",["Clearance for ".$sem." Semester of ".$school_year['school_year']." has been successfully initiated"]);
				return redirect()->to('/Dashboard')->withInput();
			}
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function extendClearancePeriod()
	{	$session = session();
		if($session->get('admin_access'))
		{
			$cPeriod_model = new \App\Models\ExistingClearancePeriodsModel();


			$pID = $this->request->getPOST('pID'); //Period ID
			$currentDueDate = $this->request->getPOST('currentDueDate');
			$newDueDate = $this->request->getPOST('clearanceDueDate');

			date_default_timezone_set('Asia/Manila');
			$modify_stamp = date('Y-m-d H:i:s', time());

			if($newDueDate < $currentDueDate)
			{
				$session->setFlashdata('err_messages',['Extended Due Date must not be earlier than the current Due Date']);
			}
			else
			{
				$data = [
					'end_date' => $newDueDate,
					'modified' => $modify_stamp,
				];

				if($cPeriod_model->update($pID,$data) == false)
				{
					$errors = $cPeriod_model->errors();
					$session->setFlashdata('err_messages',$errors);
				}
				else
				{
					$session->setFlashdata('success_messages',['Due Date is extended from '.$currentDueDate.' to '.$newDueDate]);
				}
			}

			return redirect()->back()->withInput();
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	//Manage Students' Deficiencies 
	public function Deficiencies($field_id,$stat = "")
	{
		$session = session();
		if($session->get('admin_access') || $session->get('co_access'))
		{
			\ScisSystem::refreshData1();

			$errors = array();
			$array_count = 0;

			$user_data = $session->get('user_field');
			$id = $user_data['id'];

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

			if($this->request->getPOST('clearFilter') == "Clear Filter")
			{
				$clearFilter = true;
				// echo "Filter Cleared";
			}
			else
			{
				//filter for students
				if($courseFilter= $this->request->getPOST('course'))
				{
					if($courseFilter != "all")
					{
						$filterCourse = $courseFilter;
						// echo $filterCourse."<br>";
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
					$clearanceEntry_list = $scis_model->getClearanceEntriesForCO($currentPeriod['id'],$field_id,$filterCourse,$filterYear,$filterStatus);
					
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

					$data = [
					    'page_title' 	=> 'Students Deficiencies | PUPT SCIS',
					    'user_role'		=> $session->get('user_role'),
			    'activeClearance' => $session->get('ongoingClearance'),
				    	'user_notifications' => $session->get('user_notifications'),
					    'Name'			=> $session->get('user_name'),
			    'profilePic'			=> $session->get('user_pic'),
			    		'user_fields' => $session->get('user_co_fields'),
			    'user_gradFields' => $session->get('user_co_gradFields'),
			    'user_subject_resp' => $session->get('user_subject_handled'),
			    		'Office' => $field_name,
			    		'field_id' => $field_id,
					    'errors'		=> $errors,
					    'Entries'		=> $Entries,
					    'clearance_check' => true,
					    'Requirements'	=> $requirements,
					    'courses' => $courses,
					    'CurrentPeriod' => $currentPeriod['id'],
					    'co_ID'			=> $id,
						'courseFil' => $filterCourse,
						'yearFil' => $filterYear,
						'statusFil' => $filterStatus,
					];

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

			$data = [
			    'page_title' 	=> 'Students Deficiencies | PUPT SCIS',
			    'user_role'		=> $session->get('user_role'),
			    'activeClearance' => $session->get('ongoingClearance'),
			    'user_notifications' => $session->get('user_notifications'),
			    'Name'			=> $session->get('user_name'),
			    'profilePic'			=> $session->get('user_pic'),
			    'user_fields' => $session->get('user_co_fields'),
			    'user_gradFields' => $session->get('user_co_gradFields'),
			    'user_subject_resp' => $session->get('user_subject_handled'),
			    'Office' => $field_name,
			    'field_id' => $field_id,
			    'errors'		=> $errors,
			    'Entries'		=> array(),
			    'clearance_check' => false,
			    'Requirements'	=> $requirements,
				'courses' => $courses,
				'CurrentPeriod' => "",
				'co_ID'			=> $id,
			];

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
			return redirect()->back()->to('/');
		}
	}

	public function MultipleTagging()
	{
		$session = session();
		if($session->get('admin_access') || $session->get('co_access'))
		{
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

									$tagged_def = $tagged_def."<br>&emsp;&emsp;-".$req_name;
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
							$Message = $Message."&emsp;You have been tagged with the following deficiencies in ".$current_field_name." :<b>".$tagged_def."</b>";
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
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function SingleTagging()
	{
		$session = session();
		if($session->get('admin_access')  || $session->get('co_access'))
		{
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

						$tagged_def = $tagged_def."<br>&emsp;&emsp;-".$tagged_deficiency['requirement_name'];
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
				$Message = $Message."&emsp;You have been tagged with the following deficiencies in ".$cField['field_name']." :<b>".$tagged_def."</b>";

				\ScisSystem::CreateNotification($sender_id,$Subject,$Message,$receiver_id);		
			}

			//Log Activity
			$log_user = $session->get('user_id');
			$log_message = 'Tagged Deficiencies to a Student';

			\ScisSystem::logActivity($log_user,$log_message);

			return redirect()->back()->withInput();
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function ManageStudentDeficiencies($ent_stud = '')
	{
		$session = session();
		if($session->get('admin_access') || $session->get('co_access'))
		{
			\ScisSystem::refreshData1();

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
				$period_id = '';
				if($id[3]){ $period_id = $id[3]; }

				$db = \Config\Database::connect();
				$scis_model = new \App\Models\ScisModel($db);
				$Deficiency_list = $scis_model->getEntryDeficiencies($entry_id);

				$student_info = $scis_model->getStudentInfo($student_id);

				$cF_model = new \App\Models\ClearanceFieldsModel();
				$field = $cF_model->find($field_id);
				$field_name = $field['field_name'];

				$data = [
				    'page_title' 	=> 'Manage Deficiencies | PUPT SCIS',
				    'user_role'		=> $session->get('user_role'),
			    'activeClearance' => $session->get('ongoingClearance'),
			    	'user_notifications' => $session->get('user_notifications'),
			    	'user_fields' => $session->get('user_co_fields'),
			    'user_gradFields' => $session->get('user_co_gradFields'),
			    'user_subject_resp' => $session->get('user_subject_handled'),
			    	'field_id' => $field_id,			    
				    'Name'			=> $session->get('user_name'),
			    'profilePic'			=> $session->get('user_pic'),
			    	'Office'	=> $field_name,
				    'Deficiencies' 	=> $Deficiency_list,
				    'student_info' 	=> $student_info,
				    'period'	=> $period_id,
				    'magic'			=> $ent_stud,
				];

				if($session->get('admin_access'))
				{				
				    $data['user_title']	= 'Administrator';
			    	$data['clearanceData'] = $session->get('clearance_data');    
				}
				else if($session->get('co_access'))
				{
					$data['user_title']	= 'Clearance Officer';
				}

				return view('view_student_deficiency',$data);
			}
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function ClearDeficiency($id = '')
	{
		$session = session();
		if($session->get('admin_access') || $session->get('co_access'))
		{
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

				return redirect()->to('/ClearanceManagement/ManageStudentDeficiencies/'.$ent_stud_id)->withInput();
			}
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function UnclearDeficiency($id = '')
	{
		$session = session();
		if($session->get('admin_access') || $session->get('co_access'))
		{
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
				return redirect()->to('/ClearanceManagement/ManageStudentDeficiencies/'.$ent_stud_id)->withInput();
			}
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function RemoveDeficiency($id = '')
	{
		$session = session();
		if($session->get('admin_access') || $session->get('co_access'))
		{
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
				$delete_stamp = date('Y-m-d H:i:s', time());

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
				}				

				return redirect()->to('/ClearanceManagement/ManageStudentDeficiencies/'.$ent_stud_id)->withInput();
			}
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	//Printable Reports
	public function CreateReportPage()
	{
		$session = session();
		if($session->get('admin_access') || $session->get('co_access') || $session->get('registrar_access'))
		{
			$db = \Config\Database::connect();
			$scis_model = new \App\Models\ScisModel($db);
			$course_model = new \App\Models\CoursesModel();

			//Dropdown Data
			//Clearance Period
			$cPeriod_list = $scis_model->getClearancePeriods();
			
			//Course
			$course_list = $course_model->where('deleted',0)->orderBy('course_name','ASC')->findAll();

            $data = [
                'page_title' 	=> 'Generate Reports | PUPT SCIS',
                'user_role'		=> $session->get('user_role'),
			    'activeClearance' => $session->get('ongoingClearance'),
                'Name'			=> $session->get('user_name'),
			    'profilePic'			=> $session->get('user_pic'),
                'user_notifications' => $session->get('user_notifications'),
                'user_fields' => $session->get('user_co_fields'),
			    'user_gradFields' => $session->get('user_co_gradFields'),
			    'user_subject_resp' => $session->get('user_subject_handled'),
                'periods' => $cPeriod_list,
                'courses' => $course_list,
            ];

            if($session->get('admin_access'))
			{
				$data['user_title'] = "Administrator";
			    $data['clearanceData'] = $session->get('clearance_data');
			}
			else if($session->get('co_access'))
			{
				$data['user_title'] = "Clearance Officer";
			}
			else if($session->get('registrar_access'))
			{
				$data['user_title'] = "Registrar";		    
			    $data['clearanceData'] = $session->get('clearance_data');
			}

		    return view('generate_report', $data);
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function GenerateReport()
	{
		$session = session();
		if($session->get('admin_access') || $session->get('registrar_access') || $session->get('co_access'))
		{
			//Models
			$db = \Config\Database::connect();
			$scis_model = new \App\Models\ScisModel($db);
			$cField_model = new \App\Models\ClearanceFieldsModel();

			$report = new Reports();

			if($reportType = $this->request->getPost('selectedReport'))
			{
				$period = $this->request->getPOST('clearancePeriod');
				$field = $this->request->getPOST('clearanceField');
				$status = $this->request->getPOST('clearanceStatus');
				$course = $this->request->getPOST('Course');
				$level = $this->request->getPOST('YearLevel');

				if($reportType == 'clearanceFieldStatus')
				{

					$reportData = $scis_model->clearanceStatusReport($period, $field, $status, $course, $level);

					//echo "&emsp;|&emsp;Name&emsp;|&emsp;Course&emsp;|&emsp;Year&emsp;|&emsp;Status&emsp;|";

					// foreach($reportData as $data)
					// {
					// 	echo "<br>&emsp;|&emsp;".$data->student_name."&emsp;|&emsp;".$data->course_name."&emsp;|&emsp;".$data->level."&emsp;|&emsp;".$data->status."&emsp;|";
					// }

					$periodData = $scis_model->getPeriodInfo($period);
					$fieldData = $cField_model->find($field);
					$field_name = $fieldData['field_name'];

					$report->ClearanceFieldStatus($reportData,$periodData,$field_name);
				}
				else if($reportType == 'clearanceFormsStatus')
				{
					$reportData = $scis_model->clearanceFormsReport($period, $status, $course, $level);

					$periodData = $scis_model->getPeriodInfo($period);

					$period = $periodData->year.", ".$periodData->sem;
					echo "Clearance Period : ".$period."<br><br>";

					echo "&emsp;|&emsp;Name&emsp;|&emsp;Course&emsp;|&emsp;Year&emsp;|&emsp;Form Status&emsp;|";

					foreach($reportData as $data)
					{
					 	echo "<br>&emsp;|&emsp;".$data->student_name."&emsp;|&emsp;".$data->course_name."&emsp;|&emsp;".$data->level."&emsp;|&emsp;".$data->status."&emsp;|";
					}

					$report->ClearanceFormsStatus($reportData,$periodData);
				}
			}
            
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	//Finalization Stage / Clearance Completion Stage / Registrar
	public function ClearanceCompletion($period ="", $statFilter = 0)
	{
		$session = session();
		if($session->get('admin_access') || $session->get('registrar_access') || $session->get('director_access'))
		{

			if($this->request->getPOST('status'))
			{
				$statFilter = $this->request->getPOST('status');
			}

			if(empty($period))
			{
				$students = array();
				$errors = ['No Current Clearance Period'];
				$session->setFlashdata('err_messages',$errors);
			}
			else
			{
				//Models
				$db = \Config\Database::connect();
				$scis_model = new \App\Models\ScisModel($db);
				$cEntry_model = new \App\Models\ClearanceEntriesModel();

				//$statFilter = 0; // 0 = Candidate for completion, 1 = Completed or already signed, 2 = Not yet

				$students = array(); 	//Student infos
				$studCount = 0;		   //number of students counted

				if($statFilter == 0)
				{				
					//get all forms with a status of zero
					$forms = $scis_model->getFormInfo('all',0,$period);

					//check clearance entries of the forms if all of it is cleared - put all students with with a fully cleared entries in student
					foreach($forms as $f)
					{
						$entries = $cEntry_model->where('clearance_form_id',$f->form_id)->findAll();
						$candidate = true;

						if($session->get('director_access'))
						{
							$entry_condition = [
								'clearance_form_id' => $f->form_id,
								'clearance_field_id' => $session->get('DirectorOfficeID'),
							];
							$do_entry = $cEntry_model->where($entry_condition)->first();
							$entID2 = $do_entry['id'];
							if($do_entry['clearance_field_status'] == 1)
							{
								$candidate = false;
							}
						}

						
						foreach($entries as $ent)
						{		

							if($session->get('director_access'))
							{
								if($ent['clearance_field_status'] == 0 && $ent['id'] != $entID2)
								{
									$candidate = false;
									break;
								}
							}
							else
							{
								if($ent['clearance_field_status'] == 0)
								{
									$candidate = false;
									break;
								}
							}
														
						}
						
						if($candidate)
						{
							$students[$studCount] = $f;
							if($session->get('director_access'))
							{
								$students[$studCount]->entry_id = $entID2;
							}							
							$studCount++;
						}
												
					}
				} 
				else if ($statFilter == 1)
				{
					//get all forms with a status of 1
					if($session->get('director_access'))
					{
						$forms = $scis_model->getFormInfo('all',2,$period);
					}
					else
					{
						$forms = $scis_model->getFormInfo('all',1,$period);
					}

					foreach($forms as $f)
					{
						if($session->get('director_access'))
						{
							$entry_condition = [
								'clearance_form_id' => $f->form_id,
								'clearance_field_id' => $session->get('DirectorOfficeID'),
							];
							$entries = $cEntry_model->where($entry_condition)->findAll();

							foreach($entries as $ent)
							{
								if($ent['clearance_field_status'] == 1)
								{
									$students[$studCount++] = $f;
								}
							}
						}
						else
						{
							$students[$studCount++] = $f;
						}
					}

				}
				else if ($statFilter == 2)
				{
					//get all forms with a status of zero
					$forms = $scis_model->getFormInfo('all',0,$period);

					//check clearance entries of the forms if has any uncleared entries - put all students with with uncleared entries in students
					foreach($forms as $f)
					{
						$entries = $cEntry_model->where('clearance_form_id',$f->form_id)->findAll();
						$candidate = true;

						foreach($entries as $ent)
						{
							if($ent['clearance_field_id'] != $session->get('DirectorOfficeID'))
							{
								if($ent['clearance_field_status'] == 0)
								{
									$candidate = false;
									break;
								}
							}							
						}

						if(!$candidate)
						{
							$students[$studCount++] = $f;
						}
					}
				}
			}

			$session->setFlashdata('statFil',$statFilter);

			$data = [
			    'page_title' 	=> 'Completed Clearance | PUPT SCIS',
			    'user_role'		=> $session->get('user_role'),
			    'activeClearance' => $session->get('ongoingClearance'),
			    'user_notifications' => $session->get('user_notifications'),
			    'user_fields' => $session->get('user_co_fields'),
			    'user_gradFields' => $session->get('user_co_gradFields'),
			    'user_subject_resp' => $session->get('user_subject_handled'),			    
			    'Name'			=> $session->get('user_name'),
			    'profilePic'			=> $session->get('user_pic'),
			    'clearanceData'			=> $session->get('clearance_data'),
			    'student_list'	=> $students,
			    'period' => $period,
			];

			if($session->get('admin_access'))
			{
				$data['user_title'] = "Administrator";
			}
			else if($session->get('registrar_access'))
			{
				$data['user_title'] = "Registrar";
			}

			return view('admin/view_completed_clearance',$data);					
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	//Approve / Sign Clearance Completion
	public function ClearForm($form)
	{
		$session = session();
		if($session->get('admin_access') || $session->get('registrar_access'))
		{
			$cForm_model = new \App\Models\ClearanceFormsModel();

			date_default_timezone_set('Asia/Manila');
			$modify_stamp = date('Y-m-d H:i:s', time());

			$data = [
				'clearance_status' => 1,
				'modified' => $modify_stamp,
			];

			if($cForm_model->update($form,$data) == false)
			{
				$errors = $user_model->errors();
				$session->setFlashdata('err_message',$errors);
			}
			else
			{
				$session->setFlashdata('success_messages',['Successfully moved to completed']);	
			}

			return redirect()->back()->withInput();
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function createClearanceForm($studentID, $type = "") //Create Forms and Entries
	{
		//Models
		$student_model = new \App\Models\StudentsModel();
		$cPeriod_model = new \App\Models\ExistingClearancePeriodsModel();
		$form_model = new \App\Models\ClearanceFormsModel();
		$cField_model = new \App\Models\ClearanceFieldsModel();
		$db = \Config\Database::connect();
		$scis_model = new \App\Models\ScisModel($db);
		$cEntry_model = new \App\Models\ClearanceEntriesModel();

		//Retrived Student Data
		$student_data = $student_model->find($studentID);

		// -check if student is graduating // if type is not specified
		if($type == "Graduation")
		{
			//Set Form for Clearance
		}
		else
		{
			//Get Current CLearance Period Data
			$currentPeriod = $cPeriod_model->where('deleted',0)->orderBy('id','DESC')->first();

			//Get Clearance Fields
			$cField_condition = [
				'deleted' => 0,
				'clearance_type_id' => 1,
			];
			$cFields = $cField_model->where($cField_condition)->findAll();

			// -create form
			$formData = [
				'student_id' => $student_data['id'],
				'clearance_period_data_id' => $currentPeriod['id'],
				'clearance_type_id' => 1,
			];

			$form_model->insert($formData);

			$formID = $form_model->getInsertID();

			$officer_list = $scis_model->initiatingCleranceEntriesData();

			// -create entries
			foreach($cFields as $field)
			{
				foreach($officer_list as $officer)
				{
					if($field['field_name'] == $officer->field_name && $field['field_name'] == 'Student Organization')
					{
								
						$student_info = $scis_model->getStudentOrg($studentID);

						$student_org_id = $student_info->org_id;

									
						if($officer->field_name == 'Student Organization' && $officer->org_id == $student_org_id)
						{
							$entryData = [
								'clearance_form_id' => $formID,
								'clearance_field_id' => $field['id'],
								'clearance_officer_id' => $officer->clearance_officer_id,
							];

							$cEntry_model->insert($entryData);

						}
									
					}
					else if($field['field_name'] == $officer->field_name && $field['field_name'] != 'Student Organization')
					{							
						$entryData = [
							'clearance_form_id' => $formID,
							'clearance_field_id' => $field['id'],
							'clearance_officer_id' => $officer->clearance_officer_id,
						];

						$cEntry_model->insert($entryData);
					}
				}
			}
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

	public function ClearanceForm($id)
	{
		$session = session();
		if($session->get('student_access'))
		{
			$db = \Config\Database::connect();
			$scis_model = new \App\Models\ScisModel($db);
			$cField_model = new \App\Models\ClearanceFieldsModel();
			$cEntry_model = new \App\Models\ClearanceEntriesModel();
			$cForm_model = new \App\Models\ClearanceFormsModel();

			$directorOffice = $cField_model->where('field_name',"Director's Office")->first();

			$student_id = $session->get('student_id');

			$entry_list = $scis_model->getCurrentStudentEntries($student_id,$id);

			$formID = $entry_list[0]->form_id;

			$student_entries = array();
			$array_count = 0;

			$studForm = $cForm_model->find($formID);

			$doEntry_condition = [
				'clearance_form_id' => $formID,
				'clearance_field_id' => $directorOffice['id'],
			];
			$doEntryData = $cEntry_model->where($doEntry_condition)->first();

			$directors_approval = ($doEntryData["clearance_field_status"] == 1) ? TRUE : FALSE;
			$registrars_approval = ($studForm["clearance_status"] == 1) ? TRUE : FALSE;

			foreach($entry_list as $entry)
			{
				// $deficiencies = $scis_model->getCurrentStudentDeficiency($entry->entry_id);

				// $student_deficiencies = array();
				// $array_count2 = 0;

				// foreach($deficiencies as $def)
				// {
				// 	$def_status = ($def->status == 2) ? "Cleared" : "Pending";   

				// 	$student_deficiencies[$array_count2++] = [
				// 		'id' => $def->id,
				// 		'req_name' => $def->req_name,
				// 		'status' => $def_status,
				// 	];
				// }

				$student_entries[$array_count++] = [
					'id' => $id."-".$entry->entry_id,
					'clearance_field' =>  $entry->field,
					'clearance_officer' =>  $entry->officer_name,
					//'deficiencies' => $student_deficiencies,
					'status' => $entry->status,
					'pos'	=> $entry->position,
				];
				
			}

			$data = [
			    'page_title' 	=> 'Clearance | PUPT SCIS',
			    'user_role'		=> $session->get('user_role'),
			    'activeClearance' => $session->get('ongoingClearance'),
			    'user_title'	=> $session->get('user_student_number'),
			    "clearance_periods" => $session->get('clearance_periods'),
			    'user_notifications' => $session->get('user_notifications'),
			    'Name'			=> $session->get('user_name'),
			    'profilePic'			=> $session->get('user_pic'),
			    'Entries'		=> $student_entries,
			    'formID'		=> $formID,
			    'director_sign'	=> $directors_approval,
			    'registrar_sign'	=> $registrars_approval,
			];
			
			return view('student/clearance_form', $data);
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function StudentClearanceStatus($form_period) //For viewing in completion of clearance
	{
		$session = session();
		if($session->get('admin_access') || $session->get('registrar_access') || $session->get('director_access'))
		{
			$db = \Config\Database::connect();
			$scis_model = new \App\Models\ScisModel($db);
			$cField_model = new \App\Models\ClearanceFieldsModel();
			$cForm_model = new \App\Models\ClearanceFormsModel();
			$cEntry_model = new \App\Models\ClearanceEntriesModel();

			$dataPass = explode("-", $form_period);
			$id = $dataPass[0];
			$periodH = (!empty($dataPass[1])) ? $dataPass[1] : FALSE;

			$directorOffice = $cField_model->where('field_name',"Director's Office")->first();

			$entry_list = $scis_model->getFormEntries($id);

			$formID = $entry_list[0]->form_id;

			$student_entries = array();
			$array_count = 0;

			$studForm = $cForm_model->find($formID);
			$formData = $scis_model->getFormInfo($formID,2);

			$doEntry_condition = [
				'clearance_form_id' => $formID,
				'clearance_field_id' => $directorOffice['id'],
			];
			$doEntryData = $cEntry_model->where($doEntry_condition)->first();

			$directors_approval = ($doEntryData["clearance_field_status"] == 1) ? TRUE : FALSE;
			$registrars_approval = ($studForm["clearance_status"] == 1) ? TRUE : FALSE;

			foreach($entry_list as $entry)
			{
				// $deficiencies = $scis_model->getCurrentStudentDeficiency($entry->entry_id);

				// $student_deficiencies = array();
				// $array_count2 = 0;

				// foreach($deficiencies as $def)
				// {
				// 	$def_status = ($def->status == 2) ? "Cleared" : "Pending";   

				// 	$student_deficiencies[$array_count2++] = [
				// 		'id' => $def->id,
				// 		'req_name' => $def->req_name,
				// 		'status' => $def_status,
				// 	];
				// }


				$student_entries[$array_count++] = [
					'id' => $id."-".$entry->entry_id,
					'clearance_field' =>  $entry->field,
					'clearance_officer' =>  $entry->officer_name,
					//'deficiencies' => $student_deficiencies,
					'status' => $entry->status,
					'pos' =>	$entry->position,
				];	
			}

			$data = [
			    'page_title' 	=> 'Clearance | PUPT SCIS',
			    'user_role'		=> $session->get('user_role'),
			    'activeClearance' => $session->get('ongoingClearance'),
			    "clearance_periods" => $session->get('clearance_periods'),
			    'user_notifications' => $session->get('user_notifications'),
			    'Name'			=> $session->get('user_name'),
			    'profilePic'			=> $session->get('user_pic'),
			    'clearanceData'	=> $session->get('clearance_data'),
			    'Entries'		=> $student_entries,
			    'formID'		=> $formID,
			    'formData'			=> $formData,
			    'periodH'		=> $periodH,
			    'director_sign'	=> $directors_approval,
			    'registrar_sign'	=> $registrars_approval,
			];

			if($session->get('registrar_access'))
			{
				$data['user_title']	= 'Registrar'; 
			}
			else if($session->get('director_access'))
			{
				$data['user_title']	= 'Director'; 
			}
			else if($session->get('admin_access'))
			{
				$data['user_title']	= 'Administrator';
				$data['user_fields'] = $session->get('user_co_fields');
			}	
			
			return view('clearance_form2', $data);
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function viewRequirements($id)
	{
		$session = session();
		if($session->get('student_access'))
		{
			$mess = explode("-" ,$id);
			$pID = $mess[0];
			if(isset($mess[1]))
			{
				$entID = $mess[1];
			}

			$db = \Config\Database::connect();
			$scis_model = new \App\Models\ScisModel($db);

			$student_id = $session->get('student_id');

			$cField = "";

			$entry_list = $scis_model->getCurrentStudentEntries($student_id,$pID);

			$ent_check = false;

			foreach($entry_list as $ent)
			{
				if($entID == $ent->entry_id)
				{
					$ent_check = true;
					$cField = $ent->field;
					break;
				} 
			}

			if($ent_check)
			{
				$entry_deficiencies = $scis_model->getCurrentStudentDeficiency($entID);
				$fileType_model = new \App\Models\FileTypesModel();
				$file_types = $fileType_model->where('deleted',0)->findAll();

				$submissions_model = new \App\Models\SubmissionsModel();

				$array_count = 0;

				foreach($entry_deficiencies as $ent)
				{
					if($ent->sub_type == 1)
					{
						foreach($file_types as $type)
						{
							if($ent->file_type_id == $type['id'])
							{
								$entry_deficiencies[$array_count]->file_type_name = $type['type'];
								$entry_deficiencies[$array_count]->file_type_desc = $type['description'];
							}
						}

						$sub_condition = [
							'deficiency_id' => $ent->id,
							'deleted' => 0,
						];
						$def_submission = $submissions_model->where($sub_condition)->first();

						$entry_deficiencies[$array_count]->submission = $def_submission['file_path'];
					}

					$array_count++;
				}

				$data = [
					'page_title' 	=> 'Clearance | PUPT SCIS',
				    'user_role'		=> $session->get('user_role'),
			    'activeClearance' => $session->get('ongoingClearance'),
				    'user_title'	=> $session->get('user_student_number'),
				    "clearance_periods" => $session->get('clearance_periods'),
				    'user_notifications' => $session->get('user_notifications'),
				    'Name'			=> $session->get('user_name'),
			    'profilePic'			=> $session->get('user_pic'),
				    'pID' 			=> $pID,
				    'fieldName'		=> $cField,
				    'entry_deficiencies' => $entry_deficiencies,
				];

				return view('student/requirements',$data);
			}


			return redirect()->back()->to('/');
			
		}		
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function FormReport()
	{	
		$session = session();
		if($session->get('student_access') || $session->get('admin_access') || $session->get('registrar_access'))
		{
			if(!empty($this->request->getPOST('form_id')))
			{
				$formID = $this->request->getPOST('form_id');

				$db = \Config\Database::connect();
				$scis_model = new \App\Models\ScisModel($db);
				$cField_model = new \App\Models\ClearanceFieldsModel();
				$cEntries_model = new \App\Models\ClearanceEntriesModel();

				//Director's Office Field
				$doField_condition = [
					'deleted' => 0,
					'field_name' => "Director's Office",
				];
				$DirectorOfficeID = $cField_model->select('id')->where($doField_condition)->first();

				//Director's Office Entry
				$doEntry_condition = [
					'clearance_form_id' => $formID,
					'clearance_field_id' => $DirectorOfficeID,
					'clearance_field_status' => 1,
				];

				//Form Details
				$form = $scis_model->getFormInfo($formID,2);
				echo $form->director_sign = ($cEntries_model->where($doEntry_condition)->first()) ? TRUE : FALSE;

				//Entries
				$entries = $scis_model->getCurrentStudentEntries($form->studID,$form->period);
				$ClearanceFieldStatuses = array();
				$array_count = 0;

				foreach($entries as $ent)
				{
					$ClearanceFieldStatuses[$array_count]['field'] = $ent->field;
					$ClearanceFieldStatuses[$array_count]['officer'] = $ent->officer_name;
					$ClearanceFieldStatuses[$array_count]['entry_status'] = $ent->status;

					//Deficiencies of an Entry
					$deficiencies = $scis_model->getCurrentStudentDeficiency($ent->entry_id);

					$ClearanceFieldStatuses[$array_count]['deficiencies'] = $deficiencies;
					$array_count++;
				}

				$data = [
					'form' => $form,
					'clearance_field' => $ClearanceFieldStatuses,
				];

				echo "Student Name: ".$form->student_name."<br>";
				echo "Student Number: ".$form->student_number."<br>";
				echo "Course: ".$form->course_code."<br>";
				echo "Year and Section: ".$form->year."-1<br>";
				echo "Student Type: ".$form->studType."<br>";
				echo "Contact No.: ".$form->contact_no."<br>";
				echo "School Year: ".$form->sc_year."<br>";
				echo "Clerance Type: ".$form->clearanceType."<br>";

				echo "Form Status / Registrar Remark: ";
				echo ($form->status == 1) ? "Cleared" : "Not Cleared";

				echo "<br>=================================<br>";
				$count = 1;
				foreach($ClearanceFieldStatuses as $field)
				{
					echo $count++." | ".$field['field']." | ".$field['officer']." | ";
					echo ($field['entry_status'] == 0) ? "Not Cleared" : "Cleared";
					echo " | ";
					if($field['deficiencies'] && $field['entry_status'] == 0)
					{
						echo "Deficiencies: ";
						foreach($field['deficiencies'] as $def)
						{
							echo $def->req_name."[";
							echo ($def->def_status == 0 || $def->def_status == 1) ? "Uncleared" : "Cleared";
							echo "] -";
						}
					}

					echo "<br>";
				}

				$report = new Reports();
				$report->ClearanceForm($data);
			}
			else
			{
				return redirect()->back();
			}
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	//Clearance Periods Records
	public function Periods()
	{
		$session = session();
		if($session->get('admin_access') || $session->get('registrar_access'))
		{
			\ScisSystem::refreshData1();

			$cPeriod_model = new \App\Models\ExistingClearancePeriodsModel();
			$scYear_model = new \App\Models\ScYearsModel();

			$periods = $cPeriod_model->where('deleted',0)->orderBy('id',"DESC")->findAll();

			date_default_timezone_set('Asia/Manila');
			$today = date("Y-m-d");

			$count = 0;
			foreach($periods as $p)
			{
				$SchoolYear = $scYear_model->find($p['sc_year_id']);
				$periods[$count]['scYear'] = $SchoolYear['school_year'];

				if($today < $p['start_date'])
				{
					$periods[$count]['status'] = 2;
				}
				else if($today > $p['end_date'])
				{
					$periods[$count]['status'] = 1;
				}
				else
				{
					$periods[$count]['status'] = 0;
				}

				$count++;
			}

			$data = [
				'page_title' 	=> 'Completed Clearance | PUPT SCIS',
			    'user_role'		=> $session->get('user_role'),
			    'activeClearance' => $session->get('ongoingClearance'),
			    'user_notifications' => $session->get('user_notifications'),
			    'user_fields' => $session->get('user_co_fields'),
			    'user_gradFields' => $session->get('user_co_gradFields'),
			    'user_subject_resp' => $session->get('user_subject_handled'),			    
			    'Name'			=> $session->get('user_name'),
			    'profilePic'			=> $session->get('user_pic'),
			    'clearanceData'			=> $session->get('clearance_data'),
			    'periods'	=> $periods,
			];

			if($session->get('admin_access'))
			{
				$data['user_title'] = "Administrator";
			}
			else if($session->get('registrar_access'))
			{
				$data['user_title'] = "Registrar";
			}

			return view('admin/clearance_periods' ,$data);
		}
		else
		{
			return redirect()->to('/');
		}
	}

	public function PeriodRecords($pID = "")
	{
		$session = session();
		if($session->get('admin_access'))
		{
			\ScisSystem::refreshData1();

			$cForm_model = new \App\Models\ClearanceFormsModel();
			$db = \Config\Database::connect();
			$scis_model = new \App\Models\ScisModel($db);

			$coID = $session->get('user_co_id');

			$cEntries = [];
			$count = 0;

			$co_field = $session->get('user_co_fields');

			$cPeriod_model = new \App\Models\ExistingClearancePeriodsModel();
			$selectedPeriodData = $cPeriod_model->find($pID);

			$sc_year_model = new \App\Models\ScYearsModel();
			$scYear = $sc_year_model->find($selectedPeriodData['sc_year_id']);
			$periodData['scyear'] = $scYear['school_year'];

			$periodData['clearanceType'] = ($selectedPeriodData['semester'] == 0) ? "Graduation" : "Semestral";

			$periodData['sem'] = ($selectedPeriodData['semester'] == 1) ? "1st Semester" : (($selectedPeriodData['semester'] == 2) ? "2nd Semester" : "Summer");

			// foreach($co_field as $field)
			// {
			// 	$Entry_list = $scis_model->getClearanceEntriesForCO($pID,$field['field_id'],"all","all","all");
			// 	$cEntries[$count]['id'] = $field['field_id'];
			// 	$cEntries[$count]['entries'] = $Entry_list;
			// 	$count++;				
			// }

			$studList = $scis_model->getFormInfo('all',2,$pID);

			$data = [
				'page_title' 	=> 'Completed Clearance | PUPT SCIS',
			    'user_role'		=> $session->get('user_role'),
			    'activeClearance' => $session->get('ongoingClearance'),
			    'user_title'	=> 'Administrator',
			    'user_notifications' => $session->get('user_notifications'),
			    'user_fields' => $session->get('user_co_fields'),
			    'user_gradFields' => $session->get('user_co_gradFields'),
			    'user_subject_resp' => $session->get('user_subject_handled'),			    
			    'Name'			=> $session->get('user_name'),
			    'profilePic'			=> $session->get('user_pic'),
			    'clearanceData'			=> $session->get('clearance_data'),
			    'Entries'	=> $cEntries,
			    'student_list' => $studList,
			    'period' => $pID,
			    'periodData' => $periodData,
			];

			return view('admin/view_clearance_period_records' ,$data);
		}
		else
		{
			return redirect()->to('/');
		}
	}
}