<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
	public function __construct(){
		helper('ScisSystem');
	}

	//Admin
	public function index()
	{
		$session = session();
		if($session->get('logged_in'))
		{
			if($session->get('roles')[0]['name'] == "Administrator" || $session->get('roles')[0]['name'] == "Super Administrator")
			{
				$db = \Config\Database::connect();
				$scis_model = new \App\Models\ScisModel($db);
				$user_model = new \App\Models\UsersModel();

				$user_data = $user_model->find($session->get('user_id'));
				$name = $user_data['first_name']." ".$user_data['middle_name']." ".$user_data['last_name']." ".$user_data['suffix_name']; 

				$cPeriod_model = new \App\Models\ExistingClearancePeriodsModel();
				$currentPeriod = $cPeriod_model->where('deleted',0)->orderBy('id','DESC')->first();

				date_default_timezone_set('Asia/Manila');
				$today = date("Y-m-d");

				$ongoingClearance = FALSE;

				if(!empty($currentPeriod))
				{
					if($currentPeriod['start_date'] <= $today && $currentPeriod['end_date'] >= $today)
					{
						$ongoingClearance = TRUE;
					}
				}

				$clearanceData = [
					'current_period' => $currentPeriod['id'],
				];

				$data = [
					'admin_access' => TRUE,
					'user_role' => 'administrator',
					'user_name' => $name,
					'user_field' => $user_data,
					'clearance_data' => $clearanceData,
					'ongoingClearance' => $ongoingClearance,
				];

				if($session->get('roles')[0]['name'] == "Super Administrator")
				{
					$data['superAdmin_access'] = TRUE;
				}
				$session->set($data);

				$data = $this->Admin();

				if($session->get("superAdmin_access"))
				{
					return redirect()->to("/UserManagement");
				}
				else
				{
					return view('admin/dashboard', $data);
				}
			}
			else if($session->get('roles')[0]['name'] == "Clearance Officer")
			{

				$user_model = new \App\Models\UsersModel();
				$user_data = $user_model->find($session->get('user_id'));
				$name = $user_data['first_name']." ".$user_data['middle_name']." ".$user_data['last_name']." ".$user_data['suffix_name']; 

				$cPeriod_model = new \App\Models\ExistingClearancePeriodsModel();
				$currentPeriod = $cPeriod_model->where('deleted',0)->orderBy('id','DESC')->first();

				date_default_timezone_set('Asia/Manila');
				$today = date("Y-m-d");

				$ongoingClearance = FALSE;

				if(!empty($currentPeriod))
				{
					if($currentPeriod['start_date'] <= $today && $currentPeriod['end_date'] >= $today)
					{
						$ongoingClearance = TRUE;
					}
				}

				$user_co_id = $user_data['id'];

				$data = [
					'co_access' => TRUE,
					'user_role' => 'clearanceofficer',
					'user_field' => $user_data,
					'user_co_id' => $user_data['id'],
					'user_name' => $name,
					'ongoingClearance' => $ongoingClearance,
				];
				$session->set($data);

				$data = $this->CO();

				return view('clearance_officer/dashboard', $data);
			}
			else if($session->get('roles')[0]['name'] == "Student")
			{
				$db = \Config\Database::connect();
				$scis_model = new \App\Models\ScisModel($db);

				$user_model = new \App\Models\UsersModel();
				$student_model = new \App\Models\StudentsModel();

				$user_data = $user_model->find($session->get('user_id'));
				$student_data = $student_model->where('user_id',$session->get('user_id'))->first();
				$name = $user_data['first_name']." ".$user_data['middle_name']." ".$user_data['last_name']." ".$user_data['suffix_name']; 

				//Get user Notifications
				$notifications = \ScisSystem::getUserNotifications();

				$num_of_notif = 0;
				foreach($notifications as $notif)
				{
					$num_of_notif++;
				}

				$user_notifications = [
					'total_notif' => $num_of_notif,
					'notifications' => $notifications,
				];

				//Get Clearance Periods
				$clearance_periods = $scis_model->getStudentForms($student_data['id']);
				$periods = array();
				$count = 0;
				foreach($clearance_periods as $cperiod)
				{
					$title = ($cperiod->semester == 1) ? "1st Sem" : (($cperiod->semester == 2) ? "2nd Sem" : "Summer" );
					$title = $title." SY ".$cperiod->year;
					
					$periods[$count++] = [
						'id' => $cperiod->id,
						'title' => $title,
					];
				}

				$cPeriod_model = new \App\Models\ExistingClearancePeriodsModel();
				$currentPeriod = $cPeriod_model->where('deleted',0)->orderBy('id','DESC')->first();

				date_default_timezone_set('Asia/Manila');
				$today = date("Y-m-d");

				$ongoingClearance = FALSE;

				if(!empty($currentPeriod))
				{
					if($currentPeriod['start_date'] <= $today && $currentPeriod['end_date'] >= $today)
					{
						$ongoingClearance = TRUE;
					}
				}

				$data = [
					'student_access' => TRUE,
					'user_role' => 'student',
					'user_student_number' => $student_data['student_number'], 
					'user_notifications' => $user_notifications,
					'student_id' => $student_data['id'],
					'clearance_periods' => $periods,
					'user_name' => $name,
					'ongoingClearance' => $ongoingClearance,
				];
				$session->set($data);

				$data = $this->Student();

				return view('student/dashboard',$data);
			}
			else if($session->get('roles')[0]['name'] == "Registrar")
			{
				$user_model = new \App\Models\UsersModel();
				$user_data = $user_model->find($session->get('user_id'));
				$name = $user_data['first_name']." ".$user_data['middle_name']." ".$user_data['last_name']." ".$user_data['suffix_name']; 

				$user_co_id = $user_data['id'];

				$cPeriod_model = new \App\Models\ExistingClearancePeriodsModel();
				$currentPeriod = $cPeriod_model->where('deleted',0)->orderBy('id','DESC')->first();

				date_default_timezone_set('Asia/Manila');
				$today = date("Y-m-d");

				$ongoingClearance = FALSE;

				if(!empty($currentPeriod))
				{
					if($currentPeriod['start_date'] <= $today && $currentPeriod['end_date'] >= $today)
					{
						$ongoingClearance = TRUE;
					}
				}

				$clearanceData = [
					'current_period' => $currentPeriod['id'],
				];

				$data = [
					'registrar_access' => TRUE,
					'user_role' => 'registrar',
					'user_field' => $user_data,
					'user_co_id' => $user_data['id'],
					'clearance_data' => $clearanceData,
					'user_name' => $name,
					'ongoingClearance' => $ongoingClearance,
				];
				$session->set($data);

				$data = $this->registrar();

				return view('registrar/dashboard', $data);
			}
			else if($session->get('roles')[0]['name'] == "Director")
			{
				$user_model = new \App\Models\UsersModel();
				$cFields_model = new \App\Models\ClearanceFieldsModel();

				$user_data = $user_model->find($session->get('user_id'));
				$name = $user_data['first_name']." ".$user_data['middle_name']." ".$user_data['last_name']." ".$user_data['suffix_name']; 

				$user_co_id = $user_data['id'];

				$cPeriod_model = new \App\Models\ExistingClearancePeriodsModel();
				$currentPeriod = $cPeriod_model->where('deleted',0)->orderBy('id','DESC')->first();

				date_default_timezone_set('Asia/Manila');
				$today = date("Y-m-d");

				$ongoingClearance = FALSE;

				if(!empty($currentPeriod))
				{
					if($currentPeriod['start_date'] <= $today && $currentPeriod['end_date'] >= $today)
					{
						$ongoingClearance = TRUE;
					}
				}

				$field_condition = [
					'field_name' => "Director's Office",
					'deleted' => 0,
				];
				$directorOffice = $cFields_model->where($field_condition)->first();

				$clearanceData = [
					'current_period' => $currentPeriod['id'],
				];

				$data = [
					'director_access' => TRUE,
					'user_role' => 'director',
					'user_field' => $user_data,
					'user_co_id' => $user_data['id'],
					'clearance_data' => $clearanceData,
					'user_name' => $name,
					'DirectorOfficeID' => $directorOffice['id'],
					'ongoingClearance' => $ongoingClearance,
				];
				$session->set($data);

				$data = $this->registrar();

				return view('director/dashboard', $data);
			}
			// else
			// {
			// 	$session->destroy();
			// 	return redirect()->back()->to('/');
			// }
		}  

		else
		{
			return redirect()->back()->to('/');
		} 

	}

	public function Admin()
	{
		$session = session();
		if($session->get('admin_access'))
		{
			\ScisSystem::refreshData1();

			$db = \Config\Database::connect();
			$scis_model = new \App\Models\ScisModel($db);

			$student_model = new \App\Models\StudentsModel();
			$enrolled = $student_model->where('deleted',0)->countAllResults();

			$regular_conditions = [
				'student_type_id' => 1,
				'deleted' => 0,
			];
			$regulars = $student_model->where($regular_conditions)->countAllResults();

			$irregular_conditions = [
				'student_type_id' => 2,
				'deleted' => 0,
			];
			$irregulars = $student_model->where($irregular_conditions)->countAllResults();

			$clearancePeriod_model = new \App\Models\ExistingClearancePeriodsModel();
			$period = $clearancePeriod_model->where('deleted',0)->orderBy('id',"DESC")->first();
			$period_id = (!empty($period)) ? $period['id'] : "";

			date_default_timezone_set('Asia/Manila');
			$today = date("Y-m-d");

			$period_message = "";

			$school_year = "";
			$clearanceType = "";
			$sem = "";
			$dueDate = "";
			// $cleared = "";
			// $uncleared = "";
			// $countSub = "";
			$coCFieldData = array();
			$coCFieldCount = 0;

			$incomingSched = false; //for checking if there is already a schedule for next clearance period

			$ongoing_clearance = FALSE;
			if(!empty($period))
			{
				if($period['start_date'] <= $today && $period['end_date'] >= $today)
				{
					$ongoing_clearance = TRUE;
				} 
				else if($period['start_date'] > $today)
				{
					$period_message = "But there is a scheduled Clearance that will begin on ".$period['start_date'];
					$incomingSched = true;
				} 
				else if($period['end_date'] < $today)
				{
					$period_message = "The last clearance period ended on ".$period['end_date'];
				}


				$sc_year_model = new \App\Models\ScYearsModel();
				$scYear = $sc_year_model->find($period['sc_year_id']);
				$school_year = $scYear['school_year'];

				$clearanceType = ($period['semester'] == 0) ? "Graduation" : "Semestral";

				$sem = ($period['semester'] == 1) ? "1st Semester" : (($period['semester'] == 2) ? "2nd Semester" : "Summer");

				$dueDate = $period['end_date'];

				$clearanceForm_model = new \App\Models\ClearanceFormsModel();

				//Start of Checking Cleared Student by Forms ===========================
				// $condition1 = ['clearance_period_data_id' => $period_id, 'clearance_status' => 1];
				// $cleared = $clearanceForm_model->where($condition1)->countAllResults();
				// if(empty($cleared))
				// {
				// 	$cleared = 0;
				// }

				// $condition2 = ['clearance_period_data_id' => $period_id, 'clearance_status' => 0];
				// $uncleared = $clearanceForm_model->where($condition2)->countAllResults();
				// if(empty($uncleared))
				// {
				// 	$uncleared = 0;
				// }
				//End of Checking Cleared Student by Forms ===========================

				$user_fields = $session->get('user_co_fields');
				$cFields_model = new \App\Models\ClearanceFieldsModel();

				//Check number of clear, unclear, and submitted requirements in every assign clearance field
				foreach($user_fields as $field)
				{
					$fieldData = $cFields_model->find($field['field_id']);

					$coCFieldData[$coCFieldCount]['field_id'] = $field['field_id'];

					$coCFieldData[$coCFieldCount]['field_name'] = $fieldData['field_name'];

					//Start of Checking Cleared and Uncleared Student by Entries==========================
					$cleared = 0;
					$uncleared = 0;
					$Entries =  $scis_model->getClearanceEntriesForCO($period_id,$field['field_id'],"all","all","all");
					$cleared = 0;
					$uncleared =0;
					foreach($Entries as $ent)
					{
						if($ent->status == 1)
						{
							$cleared++;
						}
						else if($ent->status == 0)
						{
							$uncleared++;
						}
					}
					$coCFieldData[$coCFieldCount]['clearedCount'] = $cleared;
					$coCFieldData[$coCFieldCount]['unclearedCount'] = $uncleared;

					//End of Checking Cleared Student by Entries==========================

					// $submissions = $scis_model->getSubmissions($period_id,$session->get('user_co_id'));
					$submissions = $scis_model->getSubmissionsForCField($field['field_id'],$period_id,'all','all',0,'all');
					$countSub = 0;
					foreach($submissions as $sub)
					{
						$countSub++;
					}

					$coCFieldData[$coCFieldCount]['subCount'] = $countSub;

					$coCFieldCount++;
				}
			}

			$data = [
			    'page_title' 	=> 'Admin Dashboard | PUPT SCIS',
			    'user_role'		=> $session->get('user_role'),
			    'activeClearance' => $session->get('ongoingClearance'),
			    'user_title'	=> 'Administrator',
			    'Name'			=> $session->get('user_name'),
			    'profilePic'			=> $session->get('user_pic'),
			    'clearanceData'	=> $session->get('clearance_data'),
			    'user_notifications' => $session->get('user_notifications'),
			    'user_fields' => $session->get('user_co_fields'),
			    'user_gradFields' => $session->get('user_co_gradFields'),
			    'user_subject_resp' => $session->get('user_subject_handled'),	
			    'enrolled'		=> $enrolled,
			    'regulars'		=> $regulars,
			    'irregulars'	=> $irregulars,
			    'clearance_period_status' => $ongoing_clearance,
			    'clearance_message' => $period_message,
			    'incoming_clearance' => $incomingSched,
			    'school_year'	=> $school_year,
			    'clearanceType' => $clearanceType,
			    'semester'		=> $sem,
			    'dueDate'		=> $dueDate,
			    'periodID'		=> $period_id,
			    // 'cleared'		=> $cleared,
			    // 'uncleared'		=> $uncleared,
			    // 'submission'	=> $countSub,
			    'coFields' => $coCFieldData,
			    'coFieldsCount' => $coCFieldCount,
			];

			if( $session->get("superAdmin_access"))
			{
			    $data['user_title']	= 'Super Administrator';
			}

			return $data;
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function CO()
	{        
        $session = session();
		if($session->get('co_access'))
		{
			\ScisSystem::refreshData1();

			$db = \Config\Database::connect();
			$scis_model = new \App\Models\ScisModel($db);

			$student_model = new \App\Models\StudentsModel();
			$enrolled = $student_model->where('deleted',0)->countAllResults();

			$regular_conditions = [
				'student_type_id' => 1,
				'deleted' => 0,
			];
			$regulars = $student_model->where($regular_conditions)->countAllResults();

			$irregular_conditions = [
				'student_type_id' => 2,
				'deleted' => 0,
			];
			$irregulars = $student_model->where($irregular_conditions)->countAllResults();

			$clearancePeriod_model = new \App\Models\ExistingClearancePeriodsModel();
			$period = $clearancePeriod_model->where('deleted',0)->orderBy('id',"DESC")->first();
			$period_id = (!empty($period)) ? $period['id'] : "";

			date_default_timezone_set('Asia/Manila');
			$today = date("Y-m-d");

			$period_message = "";

			$school_year = "";
			$clearanceType = "";
			$sem = "";
			$dueDate = "";
			// $cleared = "";
			// $uncleared = "";
			// $countSub = "";
			$coCFieldData = array();
			$coCFieldCount = 0;

			$ongoing_clearance = FALSE;
			if(!empty($period))
			{
				if($period['start_date'] <= $today && $period['end_date'] >= $today)
				{
					$ongoing_clearance = TRUE;
				} 
				else if($period['start_date'] > $today)
				{
					$period_message = "But there is a scheduled Clearance that will begin on ".$period['start_date'];
				} 
				else if($period['end_date'] < $today)
				{
					$period_message = "The last clearance period ended on ".$period['end_date'];
				}


				$sc_year_model = new \App\Models\ScYearsModel();
				$scYear = $sc_year_model->find($period['sc_year_id']);
				$school_year = $scYear['school_year'];

				$clearanceType = ($period['semester'] == 0) ? "Graduation" : "Semestral";

				$sem = ($period['semester'] == 1) ? "1st Semester" : (($period['semester'] == 2) ? "2nd Semester" : "Summer");

				$dueDate = $period['end_date'];

				$user_fields = $session->get('user_co_fields');
				$cFields_model = new \App\Models\ClearanceFieldsModel();

				//Check number of clear, unclear, and submitted requirements in every assign clearance field
				foreach($user_fields as $field)
				{
					$fieldData = $cFields_model->find($field['field_id']);

					$coCFieldData[$coCFieldCount]['field_id'] =$field['field_id'];

					$coCFieldData[$coCFieldCount]['field_name'] = $fieldData['field_name'];

					//Start of Checking Cleared and Uncleared Student by Entries==========================
					$cleared = 0;
					$uncleared = 0;
					$Entries =  $scis_model->getClearanceEntriesForCO($period_id,$field['field_id'],"all","all","all");
					$cleared = 0;
					$uncleared =0;
					foreach($Entries as $ent)
					{
						if($ent->status == 1)
						{
							$cleared++;
						}
						else if($ent->status == 0)
						{
							$uncleared++;
						}
					}
					$coCFieldData[$coCFieldCount]['clearedCount'] = $cleared;
					$coCFieldData[$coCFieldCount]['unclearedCount'] = $uncleared;

					//End of Checking Cleared Student by Entries==========================

					// $submissions = $scis_model->getSubmissions($period_id,$session->get('user_co_id'));
					$submissions = $scis_model->getSubmissionsForCField($field['field_id'],$period_id,'all','all',0,'all');
					$countSub = 0;
					foreach($submissions as $sub)
					{
						$countSub++;
					}

					$coCFieldData[$coCFieldCount]['subCount'] = $countSub;

					$coCFieldCount++;
				}
			}
			
			$data = [
			    'page_title' 	=> 'Clearance Officer Dashboard | PUPT SCIS',
			    'user_role'		=> $session->get('user_role'),
			    'activeClearance' => $session->get('ongoingClearance'),
			    'user_title'	=> 'Clearance Officer',
			    'Name'			=> $session->get('user_name'),
			    'profilePic'			=> $session->get('user_pic'),
			    'user_notifications' => $session->get('user_notifications'),
			    'user_fields' => $session->get('user_co_fields'),
			    'user_gradFields' => $session->get('user_co_gradFields'),
			    'user_subject_resp' => $session->get('user_subject_handled'),	
			    'enrolled'		=> $enrolled,
			    'regulars'		=> $regulars,
			    'irregulars'	=> $irregulars,
			    'clearance_period_status' => $ongoing_clearance,
			    'clearance_message' => $period_message,
			    'school_year'	=> $school_year,
			    'clearanceType' => $clearanceType,
			    'semester'		=> $sem,
			    'dueDate'		=> $dueDate,
			    // 'cleared'		=> $cleared,
			    // 'uncleared'		=> $uncleared,
			    // 'submission'	=> $countSub,
			    'coFields' => $coCFieldData,
			    'coFieldsCount' => $coCFieldCount,
			];

			return $data;
		}
		else
		{
			return redirect()->back()->to('/');
		}	  
	}

	public function registrar()
	{        
        $session = session();
		if($session->get('registrar_access') || $session->get('director_access'))
		{
			\ScisSystem::refreshData1();

			$db = \Config\Database::connect();
			$scis_model = new \App\Models\ScisModel($db);

			$student_model = new \App\Models\StudentsModel();
			$enrolled = $student_model->where('deleted',0)->countAllResults();

			$regular_conditions = [
				'student_type_id' => 1,
				'deleted' => 0,
			];
			$regulars = $student_model->where($regular_conditions)->countAllResults();

			$irregular_conditions = [
				'student_type_id' => 2,
				'deleted' => 0,
			];
			$irregulars = $student_model->where($irregular_conditions)->countAllResults();

			$clearancePeriod_model = new \App\Models\ExistingClearancePeriodsModel();
			$period = $clearancePeriod_model->where('deleted',0)->orderBy('id',"DESC")->first();
			$period_id = (!empty($period)) ? $period['id'] : "";

			date_default_timezone_set('Asia/Manila');
			$today = date("Y-m-d");

			$period_message = "";

			$school_year = "";
			$clearanceType = "";
			$sem = "";
			$dueDate = "";
			// $cleared = "";
			// $uncleared = "";
			// $countSub = "";
			$coCFieldData = array();
			$coCFieldCount = 0;

			$incomingSched = false; //for checking if there is already a schedule for next clearance period

			$ongoing_clearance = FALSE;
			if(!empty($period))
			{
				if($period['start_date'] <= $today && $period['end_date'] >= $today)
				{
					$ongoing_clearance = TRUE;
				} 
				else if($period['start_date'] > $today)
				{
					$period_message = "But there is a scheduled Clearance that will begin on ".$period['start_date'];
					$incomingSched = true;
				} 
				else if($period['end_date'] < $today)
				{
					$period_message = "The last clearance period ended on ".$period['end_date'];
				}


				$sc_year_model = new \App\Models\ScYearsModel();
				$scYear = $sc_year_model->find($period['sc_year_id']);
				$school_year = $scYear['school_year'];

				$clearanceType = ($period['semester'] == 0) ? "Graduation" : "Semestral";

				$sem = ($period['semester'] == 1) ? "1st Semester" : (($period['semester'] == 2) ? "2nd Semester" : "Summer");

				$dueDate = $period['end_date'];

				$clearanceForm_model = new \App\Models\ClearanceFormsModel();

				// Start of Checking Cleared Student by Forms ===========================
				$cleared = 0;
				$condition1 = ['clearance_period_data_id' => $period_id, 'clearance_status' => 1];
				if($session->get('registrar_access'))
				{
					$cleared = $clearanceForm_model->where($condition1)->countAllResults();
				}

				// $condition2 = ['clearance_period_data_id' => $period_id, 'clearance_status' => 0];
				// $uncleared = $clearanceForm_model->where($condition2)->countAllResults();
				// if(empty($uncleared))
				// {
				// 	$uncleared = 0;
				// }

				$cEntry_model = new \App\Models\ClearanceEntriesModel();

				//get all forms with a status of zero
				$currentClearanceData = $session->get('clearance_data');
				$forms = $scis_model->getFormInfo('all',0,$currentClearanceData['current_period']);

				//check clearance entries of the forms if all of it is cleared - put all students with with a fully cleared entries in student
				$incomplete = 0;
				$completed = 0;
				$done_count = 0;
// $casda = 1;	
				foreach($forms as $f)
				{
					$entries = $cEntry_model->where('clearance_form_id',$f->form_id)->findAll();
					$candidate = true;

					foreach($entries as $ent)
					{
						if(!$session->get('director_access'))
						{
							if($ent['clearance_field_status'] == 0)
							{
								$candidate = false;
								break;
							}
						} 
						else 
						{
							if($ent['clearance_field_status'] == 0 && $ent['clearance_field_id'] != $session->get("DirectorOfficeID"))
							{
								$candidate = false;
								break;
							} 
							else if ($ent['clearance_field_status'] == 1 && $ent['clearance_field_id'] == $session->get("DirectorOfficeID"))
							{
								$candidate = false;
								$done_count++;
								break;
							}
						}
						
					}
					
					// echo $casda++."-".$candidate."<br>";

					if($candidate)
					{
						$completed++;
					}
					else
					{						
						$incomplete++;
					}
				}

				$incomplete -= $done_count; 

				if($session->get('director_access'))
				{
					$forms2 = $scis_model->getFormInfo('all',2,$currentClearanceData['current_period']);
					foreach($forms2 as $f)
					{
						$entry_condition = [
							'clearance_form_id' => $f->form_id,
							'clearance_field_id' => $session->get('DirectorOfficeID'),
						];
						$entryData = $cEntry_model->where($entry_condition)->first();

						if($entryData['clearance_field_status'] == 1)
						{
							$cleared++;
						}
					}	
				}
				
				if(empty($cleared))
				{
					$cleared = 0;
				}
				// End of Checking Cleared Student by Forms ===========================
			
			}

			$data = [
			    'user_role'		=> $session->get('user_role'),
			    'activeClearance' => $session->get('ongoingClearance'),
			    'Name'			=> $session->get('user_name'),
			    'profilePic'			=> $session->get('user_pic'),
			    'clearanceData'	=> $session->get('clearance_data'),
			    'user_notifications' => $session->get('user_notifications'),
			    'user_fields' => $session->get('user_co_fields'),
			    'user_gradFields' => $session->get('user_co_gradFields'),
			    'user_subject_resp' => $session->get('user_subject_handled'),	
			    'enrolled'		=> $enrolled,
			    'regulars'		=> $regulars,
			    'irregulars'	=> $irregulars,
			    'clearance_period_status' => $ongoing_clearance,
			    'clearance_message' => $period_message,
			    'incoming_clearance' => $incomingSched,
			    'school_year'	=> $school_year,
			    'clearanceType' => $clearanceType,
			    'semester'		=> $sem,
			    'dueDate'		=> $dueDate,
			    'periodID'		=> $period_id,
			    'cleared'		=> $cleared,
			    'completed'		=> $completed,
			    'incomplete'	=> $incomplete,
			    'coFields' => $coCFieldData,
			    'coFieldsCount' => $coCFieldCount,
			];

			if($session->get('registrar_access'))
			{
				$data['page_title'] = 'Admin Dashboard | PUPT SCIS';
				$data['user_title'] = 'Registrar';
			}
			else if($session->get('director_access'))
			{
				$data['page_title'] = 'Director Dashboard | PUPT SCIS';
				$data['user_title'] = 'Director';
			}

			return $data;
		}
		else
		{
			return redirect()->back()->to('/');
		}	  
	}

	public function Student()
	{
		$session = session();
		if($session->get('student_access'))
		{
			$db = \Config\Database::connect();
			$scis_model = new \App\Models\ScisModel($db);
			\ScisSystem::refreshData1();

			$clearancePeriod_model = new \App\Models\ExistingClearancePeriodsModel();
			$period = $clearancePeriod_model->where('deleted',0)->orderBy('id',"DESC")->first();
			$period_id = (!empty($period)) ? $period['id'] : "";

			date_default_timezone_set('Asia/Manila');
			$today = date("Y-m-d");

			$period_message = "";
			$ongoing_clearance = FALSE;

			$school_year = "";
			$clearanceType = "";
			$sem = "";
			$dueDate = "";
			$cleared = "";
			$uncleared = "";
			$deficiencies = "";

			if(!empty($period))
			{
				if($period['start_date'] <= $today && $period['end_date'] >= $today)
				{
					$ongoing_clearance = TRUE;
				} 
				else if($period['start_date'] > $today)
				{
					$period_message = "But there is a scheduled Clearance that will begin on ".$period['start_date'];
				} 
				else if($period['end_date'] < $today)
				{
					$period_message = "The last clearance period ended on ".$period['end_date'];
				}

				$sc_year_model = new \App\Models\ScYearsModel();
				$scYear = $sc_year_model->find($period['sc_year_id']);
				$school_year = $scYear['school_year'];

				$clearanceType = ($period['semester'] == 0) ? "Graduation" : "Semestral";

				$sem = ($period['semester'] == 1) ? "1st Semester" : (($period['semester'] == 2) ? "2nd Semester" : "Summer");

				$student_current_Entries = $scis_model->getCurrentStudentEntries($session->get('student_id'),$period_id);

				$cleared = 0;
				$uncleared = 0;
				$deficiencies = 0;
				foreach($student_current_Entries as $ent)
				{
					if($ent->field != "Director's Office")
					{				
						if($ent->status == 1)
						{
							$cleared++;
						}
						else if($ent->status == 0)
						{
							$uncleared++;
						}

						$entryDeficiencies = $scis_model->getCurrentStudentDeficiency($ent->entry_id);
						foreach($entryDeficiencies as $def)
						{
							if($def->def_status!=2)
							{
								$deficiencies++;
							}
						}
					}
				}
				$dueDate = $period['end_date'];
			}

			$data = [
			    'page_title' => 'Student Dashboard | PUPT SCIS',
			    'user_role' => $session->get('user_role'),
			    'user_title' => $session->get('user_student_number'),
			    'Name'			=> $session->get('user_name'),
			    'profilePic'			=> $session->get('user_pic'),
			    "clearance_periods" => $session->get('clearance_periods'),
			    'user_notifications' => $session->get('user_notifications'),
			    'clearance_period_status' => $ongoing_clearance,
			    'clearance_message' => $period_message,
			    'school_year'	=> $school_year,
			    'clearanceType' => $clearanceType,
			    'semester'		=> $sem,
			    'dueDate'		=> $dueDate,
			    'cleared'		=> $cleared,
			    'uncleared'		=> $uncleared,
			    'deficiencies'	=> $deficiencies,
			    'currentClearance' => $period_id,
 			];
 			
			return $data;
		}
		else
		{
			return redirect()->back()->to('/');
		}		
	}
}