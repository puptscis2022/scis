<?php

namespace App\Controllers;
use App\Libraries\Hash;
use App\Libraries\Reports;

use TCPDF;

class ClearancePeriods extends BaseController
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
		
		if($permission_model->hasPermission($uID,['view_clearance_periods']))
		{
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

			$data['periods'] = $periods;

			$data['AddClearancePeriods'] = ($permission_model->hasPermission($uID,'add_clearance_periods')) ? TRUE : FALSE ;


			return view('admin/clearance_periods' ,$data);
		}
		else
		{
			return view('site/no_permission' ,$data);
		}
	}

	public function Records($pID = "")
	{
		$session = session();
		
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

	public function Initiate()
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
		
		if($permission_model->hasPermission($uID,['add_clearance_periods']))
		{
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

			if($passedErrors = $session->get('err_messages'))
			{
				foreach($passedErrors as $pE)
				{
					array_push($errors,$pE);
				}
			}

			$session->setFlashdata('err_messages',$errors);

			$data['eligibilityCheck'] = $errors;
			$data['ScYears'] = $scYears;
			$data['ClearanceTypes'] = $clearanceTypes;

			return view('admin/initiate_clearance', $data);
		}
		else
		{
			return view('site/no_permission', $data);
		}
	}

	public function InitiateClearance()
	{
		$session = session();
		
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
			return redirect()->to('/ClearancePeriods/Initiate')->withInput();	
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
			$preRequisites_model = new \App\Models\ClearanceFieldPrerequisitesModel();
			$currentForms = $cForm_model->where('clearance_period_data_id', $currentPeriodID)->findAll();

			$clearanceField_model = new \App\Models\ClearanceFieldsModel();
			$clearanceFields = $clearanceField_model->where('deleted',0)->findAll();

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
					if($field['clearance_type_id'] == 1 || $field['clearance_type_id'] == 3)
					{
						$entryID = array();
						
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
								$entry_data_found = FALSE;
								$data = array();

								if($field['field_name'] == $officer->field_name && $field['field_name'] == 'Student Organization' && $officer->org_activeness = 1)
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

										$entry_data_found = TRUE;
									}
								}
								else if($field['field_name'] == $officer->field_name && $field['field_name'] != 'Student Organization')
								{
									$data = [
										'clearance_form_id' => $clearanceFormID, 
										'clearance_field_id' =>$field['id'], 
										'clearance_officer_id' =>$officer->clearance_officer_id,
									];

									$entry_data_found = TRUE;
								}

								if($entry_data_found)
								{
									//Inputting Deficiencies for Black Listed Student

									//Check if The students is black listed with deficiencies
									$blackList_condition1 = [
										'student_id' => $studentID,
										'clearance_field_id' => $officer->position_id,
										'deleted'	=> 0,
									];
									//Check if there is blacklist for all student
									$blackList_condition2 = [
										'student_id' => 0,
										'clearance_field_id' => $officer->position_id,
										'deleted'	=> 0,
									];

									$listInfo1 = $blackList_model->where($blackList_condition1)->findAll();
									$listInfo2 = $blackList_model->where($blackList_condition2)->findAll();

									if(!empty($listInfo1) || !empty($listInfo2) )
									{
										$data['clearance_field_status'] = "0";
										$clearanceEntry_model->insert($data);
										$entryID = $clearanceEntry_model->getInsertID();

										foreach($listInfo1 as $info)
										{
											$deficiencyData = [
												'clearance_entry_id' => $entryID,
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
										}

										foreach($listInfo2 as $info)
										{
											$deficiencyData = [
												'clearance_entry_id' => $entryID,
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
										}							
									}
									else
									{	
										$preReq_condition = [
											'clearance_field_id' => $officer->position_id,
											'deleted' => 0,
										];

										$positionPreReq = $preRequisites_model->where($preReq_condition)->findAll();

										$entry_clearad = TRUE;

										foreach($positionPreReq as $posPreReq)
										{
											$blackList_condition1['clearance_field_id'] = $posPreReq['requisite_clearance_field_id'];
											$blackList_condition2['clearance_field_id'] = $posPreReq['requisite_clearance_field_id'];

											$bListInfo1 = $blackList_model->where($blackList_condition1)->findAll();
											$bListInfo2 = $blackList_model->where($blackList_condition2)->findAll();
												
											if(!empty($bListInfo1) || !empty($bListInfo2))
											{
												$entry_clearad = FALSE;
											}
										}

										if($field['field_name'] != "Director's Office" && $entry_clearad)
										{
											$data['clearance_field_status'] = "1";
										}

										$clearanceEntry_model->insert($data);
									}
								}
							} // End of for loop for officers
						}
					}					
				}
			}//End of Clearance Entries Creation

			//clear Black List
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
			$Message = "Attention! A new clearance period has been initiated for the ".$sem." Semester of School Year ".$school_year['school_year'].". The scheduled clearance will be on ".$initiatedClearanceStart." until ".$initiatedClearanceDue.".";
			$receiver_id = 0;

			\ScisSystem::CreateNotification($sender_id,$Subject,$Message,$receiver_id);

			$session->setFlashdata("success_messages",["Clearance for ".$sem." Semester of ".$school_year['school_year']." has been successfully initiated"]);
				return redirect()->to('/test_dashboard')->withInput();
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

					//Log Activity
					$log_user = $session->get('user_id');
					$log_message = 'Extended Clearance Period from '.$currentDueDate.' to '.$newDueDate;

					\ScisSystem::logActivity($log_user,$log_message);

					//Create Notification
					$sender_id = $session->get('user_id');
					$Subject = "Clearance Period Extension";
					$Message = "Attention! The due date for completing the current clearance has be extended from ".$currentDueDate." to ".$newDueDate;
					$receiver_id = 0;

					\ScisSystem::CreateNotification($sender_id,$Subject,$Message,$receiver_id);
				}
			}

			return redirect()->back()->withInput();
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}
}