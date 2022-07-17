<?php

namespace App\Controllers;

class Test_dashboard extends BaseController
{
	public function __construct(){
		helper('ScisSystem');
	}

	public function index()
	{
		$session = session();
		if($session->get('logged_in'))
		{
			$has_permission = FALSE;

			$permission_model = new \App\Models\PermissionsModel();

			$uID = $session->get('user_id');

			\ScisSystem::refreshData1();

			$data = [
				'page_title' 	=> 'Dashboard | PUPT SCIS',
			    'user_role'		=> $session->get('user_role'),
			    'activeClearance' => $session->get('ongoingClearance'),
			    'user_title'	=> 'Administrator',
			    'Name'			=> $session->get('user_name'),
			    'profilePic'	=> $session->get('user_pic'),
			    'clearanceData'	=> $session->get('clearance_data'),
			    'user_notifications' => $session->get('user_notifications'),
			    'user_fields' => $session->get('user_co_fields'),
			    'user_gradFields' => $session->get('user_co_gradFields'),
			    'user_subject_resp' => $session->get('user_subject_handled'),					    
			];
			
			$data['view_students'] 	= FALSE;
			$data['view_clearancePeriods']  = FALSE;
			$data['view_clearanceForms'] = FALSE;
			$data['view_clearanceEntries'] = FALSE;
			$data['view_submissions'] = FALSE;

			if($permission_model->hasPermission($uID,'view_students'))
			{
				$has_permission = TRUE;
				$students = $this->studentStats();

				$data['enrolled']		= $students["enrolled"];
			    $data['regulars']		= $students["regulars"];
			    $data['irregulars']		= $students["irregulars"];
			    $data['view_students'] 	= TRUE;
			}

			if($permission_model->hasPermission($uID,'view_clearance_periods'))
			{
				$has_permission = TRUE;
				$periodData = $this->clearancePeriod();

				$data['clearance_period_status'] = $periodData['ongoing_clearance'];
			    $data['clearance_message'] = $periodData['period_message'];
			    $data['incoming_clearance'] = $periodData['incomingSched'];
			    $data['school_year'] = $periodData['school_year'];
			    $data['clearanceType'] = $periodData['clearanceType'];
			    $data['semester'] = $periodData['sem'];
			    $data['dueDate'] = $periodData['dueDate'];
			    $data['periodID'] = $periodData['period_id'];
			    $data['view_clearancePeriods']  = TRUE;

			    $data['ExtendClearancePeriod'] = ($permission_model->hasPermission($uID,'edit_clearance_periods')) ? TRUE : FALSE;

			    if($permission_model->hasPermission($uID,'view_clearance_forms') && $periodData['ongoing_clearance'])
			    {
			    	$data['view_clearanceForms'] = TRUE;

			    	if(!$session->get("Director_access"))
			    	{
				    	$formData = $this->clearanceFormsStat($periodData['period_id']);
					    
					    $data['cleared'] = $formData['cleared'];
					    $data['completed'] = $formData['completed'];
					    $data['incomplete'] = $formData['incomplete'];
					} 
					else
					{
						$formData = $this->clearanceFormsStat2($periodData['period_id']);
					    
					    $data['cleared'] = $formData['cleared'];
					    $data['completed'] = $formData['completed'];
					    $data['incomplete'] = $formData['incomplete'];
					}
			    	
			    	if($session->get("Student_access") && !empty($periodData['period_id']))
			    	{
			    		if($permission_model->hasPermission($uID,'view_clearance_entries'))
			    		{
			    			$studentClearanceData = $this->studentClearanceStatus($periodData['period_id']);

				    		$data['view_clearanceEntries'] = TRUE;
					    	$data['cleared'] = $studentClearanceData['cleared'];
					    	$data['uncleared'] = $studentClearanceData['uncleared'];
					    	$data['deficiencies'] = $studentClearanceData['deficiencies'];
					    	$data['formID'] = $studentClearanceData['formID'];
			    		}
			    		
			    	}
			    	
			    }	


			    if($permission_model->hasPermission($uID,'view_clearance_entries') && $periodData['ongoing_clearance'])
			    {
			    	$defData = $this->Deficiencies($periodData['period_id']);

			    	$data['coFields'] = $defData['coCFieldData'];
			    	$data['coFieldsCount'] = $defData['coCFieldCount'];
			    	$data['view_clearanceEntries'] = TRUE;

			    	if($permission_model->hasPermission($uID,'view_submissions'))
				    {	
			    		$data['view_submissions'] = TRUE;
				    }
			    }


			}


			if($has_permission || $session->get("SuperAdministrator_access"))
			{
				if($session->get("SuperAdministrator_access"))
				{
					return redirect()->to("/UserManagement");
				}
				else
				{
					return view('test_dashboard',$data);
				}
			}
			else 
			{
				return view('site/no_permission',$data);
			}			
		}
		else
		{
			return redirect()->back()->to('/');
		}		
	}

	public function studentStats()
	{
		$student_model = new \App\Models\StudentsModel();

		$data = array();

		$data['enrolled'] = $student_model->where('deleted',0)->countAllResults();

		$regular_conditions = [
				'student_type_id' => 1,
				'deleted' => 0,
		];
		$data['regulars'] = $student_model->where($regular_conditions)->countAllResults();

		$irregular_conditions = [
				'student_type_id' => 2,
				'deleted' => 0,
		];
		$data['irregulars'] = $student_model->where($irregular_conditions)->countAllResults();

		return $data;
	}

	public function clearancePeriod()
	{
		$data = array();

		$clearancePeriod_model = new \App\Models\ExistingClearancePeriodsModel();
		$period = $clearancePeriod_model->where('deleted',0)->orderBy('id',"DESC")->first();
		$data['period_id'] = (!empty($period)) ? $period['id'] : "";

		date_default_timezone_set('Asia/Manila');
		$today = date("Y-m-d");

		$data['period_message'] = "";

		$data['school_year'] = "";
		$data['clearanceType'] = "";
		$data['sem'] = "";
		$data['dueDate'] = "";

		$data['incomingSched'] = false; //for checking if there is already a schedule for next clearance period

		$data['ongoing_clearance'] = FALSE;
		if(!empty($period))
		{
			if($period['start_date'] <= $today && $period['end_date'] >= $today)
			{
				$data['ongoing_clearance'] = TRUE;
			} 
			else if($period['start_date'] > $today)
			{
				$data['period_message'] = "But there is a scheduled Clearance that will begin on ".$period['start_date'];
				$data['incomingSched'] = true;
			} 
			else if($period['end_date'] < $today)
			{
				$data['period_message'] = "The last clearance period ended on ".$period['end_date'];
			}


			$sc_year_model = new \App\Models\ScYearsModel();
			$scYear = $sc_year_model->find($period['sc_year_id']);
			$data['school_year'] = $scYear['school_year'];

			$data['clearanceType'] = ($period['semester'] == 0) ? "Graduation" : "Semestral";

			$data['sem'] = ($period['semester'] == 1) ? "1st Semester" : (($period['semester'] == 2) ? "2nd Semester" : "Summer");

			$data['dueDate'] = $period['end_date'];
		}

		return $data;
	}

	public function Deficiencies($period_id)
	{
		$data = array();
		$session = session();

		$db = \Config\Database::connect();
		$scis_model = new \App\Models\ScisModel($db);
		$clearanceForm_model = new \App\Models\ClearanceFormsModel();

		$coCFieldData = array();
		$coCFieldCount = 0;

		$user_fields = session()->get('user_co_fields');
		$cFields_model = new \App\Models\ClearanceFieldsModel();

		//Check number of clear, unclear, and submitted requirements in every assign clearance field
		foreach($user_fields as $field)
		{
			$fieldData = $cFields_model->find($field['field_id']);

			$coCFieldData[$coCFieldCount]['field_id'] = $field['field_id'];

			$coCFieldData[$coCFieldCount]['field_name'] = $fieldData['field_name'];			
			$coCFieldData[$coCFieldCount]['position_id'] = $field['position_id'];
			$coCFieldData[$coCFieldCount]['position_name'] = $field['positions_name'];

			//Start of Checking Cleared and Uncleared  Student by Entries==========================
			$co_org = ($field['field_name'] == "Student Organization") ? $session->get('user_org') : FALSE ;
			$Entries =  $scis_model->getClearanceEntriesForCO($period_id,$field['field_id'],"all","all","all",$field['position_id'],$co_org);
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
			$submissions = $scis_model->getSubmissionsForCField($field['field_id'],$period_id,'all','all',0,'all',$field['position_id'],$co_org);
			$countSub = 0;
			foreach($submissions as $sub)
			{
				$countSub++;
			}

			$coCFieldData[$coCFieldCount]['subCount'] = $countSub;

			$coCFieldCount++;
		}

		$data['coCFieldData'] = $coCFieldData;
		$data['coCFieldCount'] = $coCFieldCount;

		return $data;
	}

	public function clearanceFormsStat($period_id)
	{
		$data = array();

		$db = \Config\Database::connect();
		$scis_model = new \App\Models\ScisModel($db);
		$clearanceForm_model = new \App\Models\ClearanceFormsModel();

		// Start of Checking Cleared Student by Forms ===========================
		$cleared = 0;
		$condition1 = ['clearance_period_data_id' => $period_id, 'clearance_status' => 1];
		
		$cleared = $clearanceForm_model->where($condition1)->countAllResults();

		// $condition2 = ['clearance_period_data_id' => $period_id, 'clearance_status' => 0];
		// $uncleared = $clearanceForm_model->where($condition2)->countAllResults();
		// if(empty($uncleared))
		// {
		// 	$uncleared = 0;
		// }

		$cEntry_model = new \App\Models\ClearanceEntriesModel();

		//get all forms with a status of zero
		$forms = $scis_model->getFormInfo('all',0,$period_id);

		//check clearance entries of the forms if all of it is cleared - put all students with with a fully cleared entries in student
		$incomplete = 0;
		$completed = 0;

		foreach($forms as $f)
		{
			$entries = $cEntry_model->where('clearance_form_id',$f->form_id)->findAll();
			$candidate = true;

			foreach($entries as $ent)
			{
				if($ent['clearance_field_status'] == 0)
				{
					$candidate = false;
					break;
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

		$data = [
			'cleared' => $cleared,
			'completed' => $completed,
			'incomplete' => $incomplete,
		];

		return $data;
	}

	public function clearanceFormsStat2($period_id) // For Director Only
	{
		$session = session();
		$data = array();

		$db = \Config\Database::connect();
		$scis_model = new \App\Models\ScisModel($db);
		$clearanceForm_model = new \App\Models\ClearanceFormsModel();

		// Start of Checking Cleared Student by Forms ===========================
		$cleared = 0;

		$cEntry_model = new \App\Models\ClearanceEntriesModel();

		//get all forms with a status of zero
		$currentClearanceData = $session->get('clearance_data');
		$forms = $scis_model->getFormInfo('all',0,$period_id);

		//check clearance entries of the forms if all of it is cleared - put all students with with a fully cleared entries in student
		$incomplete = 0;
		$completed = 0;
		$done_count = 0;

		foreach($forms as $f)
		{
			$entries = $cEntry_model->where('clearance_form_id',$f->form_id)->findAll();
			$candidate = true;

			foreach($entries as $ent)
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

		$forms2 = $scis_model->getFormInfo('all',2,$period_id);
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

		$data = [
			'cleared' => $cleared,
			'completed' => $completed,
			'incomplete' => $incomplete,
		];

		return $data;
	}

	public function studentClearanceStatus($period_id)
	{
		$data = array();

        if($period_id)
        {
    		$session = session();
    
    		$db = \Config\Database::connect();
    		$scis_model = new \App\Models\ScisModel($db);
    		$cForms_model = new \App\Models\ClearanceFormsModel();
    
    		$cForm_condition = [
    			'student_id' => $session->get('student_id'),
    			'clearance_period_data_id' => $period_id,
    		];
    		
    		$cForm_info = $cForms_model->where($cForm_condition)->first();
    
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
    
    		$data = [
    			'cleared' => $cleared,
    			'uncleared' => $uncleared,
    			'deficiencies' => $deficiencies,
    			'formID' => (!empty($cForm_info['id'])) ? $cForm_info['id'] : FALSE,
    		];
        }

		return $data;
	}
}