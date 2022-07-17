<?php

namespace App\Controllers;
use App\Libraries\Hash;
use App\Libraries\Reports;

use TCPDF;

class GenerateReports extends BaseController
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
			'page_title' => 'Generate Reports | PUPT SCIS',
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
		
		if($permission_model->hasPermission($uID,['view_clearance_entries','view_clearance_forms']))
		{
			$db = \Config\Database::connect();
			$scis_model = new \App\Models\ScisModel($db);
			$course_model = new \App\Models\CoursesModel();
			$scYear_model = new \App\Models\ScYearsModel();

			//Dropdown Data
			//Clearance Period
			$cPeriod_list = $scis_model->getClearancePeriods();
			
			//Course
			$course_list = $course_model->where('deleted',0)->orderBy('course_name','ASC')->findAll();

			//SchoolYears
			$scYears = $scYear_model->where('deleted',0)->findAll();

            $data['periods'] = $cPeriod_list;
            $data['courses'] = $course_list;
            $data['scYears'] = $scYears;

            $data['ViewClearanceEntries'] = ($permission_model->hasPermission($uID,'view_clearance_entries')) ? TRUE : FALSE ;
            $data['ViewClearanceForms'] = ($permission_model->hasPermission($uID,'view_clearance_forms')) ? TRUE : FALSE ;

		    return view('generate_report', $data);
		}
		else
		{
		    return view('site/no_permission', $data);
		}
	}

	public function Generate()
	{
		$session = session();
		
		//Models
		$db = \Config\Database::connect();
		$scis_model = new \App\Models\ScisModel($db);
		$cField_model = new \App\Models\ClearanceFieldsModel();
		$cEntries_model = new \App\Models\ClearanceEntriesModel();

		$report = new Reports();
		$report_type = '';

		if($reportType = $this->request->getPost('selectedReport'))
		{
			$period = $this->request->getPOST('clearancePeriod');
			$field = $this->request->getPOST('clearanceField');
			$status = $this->request->getPOST('clearanceStatus');
			$course = $this->request->getPOST('Course');
			$level = $this->request->getPOST('YearLevel');
			$scYear= $this->request->getPOST('scYear');

			if($reportType == 'clearanceFieldStatus')
			{
				$report_type = 'Field Status Report';
				$reportData = $scis_model->clearanceStatusReport($period, $field, $status, $course, $level);

				//echo "&emsp;|&emsp;Name&emsp;|&emsp;Course&emsp;|&emsp;Year&emsp;|&emsp;Status&emsp;|";

				// foreach($reportData as $data)
				// {
				// 	echo "<br>&emsp;|&emsp;".$data->student_name."&emsp;|&emsp;".$data->course_name."&emsp;|&emsp;".$data->level."&emsp;|&emsp;".$data->status."&emsp;|";
				// }

				$periodData = $scis_model->getPeriodInfo($period);
				$fieldData = $cField_model->find($field);
				$field_name = $fieldData['field_name'];

				if($reportData)
				{
					//Log Activity
					$log_user = $session->get('user_id');
					$log_message = 'Generated a Report : '.$report_type;

					\ScisSystem::logActivity($log_user,$log_message);
					
					$report->ClearanceFieldStatus($reportData,$periodData,$field_name);
				}
				else
				{
					//$session->setFlashdata('err_messages',['No Data to report']);
					//return redirect()->back()->withInput();
					echo "No Data to report";
				}				
			}
			else if($reportType == 'clearanceFormsStatus')
			{
				$report_type = 'Form Status Report';
				$reportData = $scis_model->clearanceFormsReport($period, $status, $course, $level);

				$periodData = $scis_model->getPeriodInfo($period);

				$period = $periodData->year.", ".$periodData->sem;
				echo "Clearance Period : ".$period."<br><br>";

				if($reportData)
				{
					//Log Activity
					$log_user = $session->get('user_id');
					$log_message = 'Generated a Report : '.$report_type;

					\ScisSystem::logActivity($log_user,$log_message);

					$report->ClearanceFormsStatus($reportData,$periodData);
				}
				else
				{
					//$session->setFlashdata('err_messages',['No Data to report']);
					//return redirect()->back()->withInput();
					echo "No Data to report";
				}	
			}
			else if($reportType == 'clearanceForms')
			{
				$report_type = 'Clearance Forms';
				echo "ClearanceForms<br>================<br><br>";

				//get forms
				$forms = $scis_model->clearanceFormsReport($period, $status, $course, $level);
				$reportData = array();
				$reportCount = 0;

				//Director's Office Field
				$doField_condition = [
					'deleted' => 0,
					'field_name' => "Director's Office",
				];
				$DirectorOfficeID = $cField_model->select('id')->where($doField_condition)->first();


				foreach($forms as $f)
				{
				    //Director's Office Entry
    				$doEntry_condition = [
    					'clearance_form_id' => $f->fID,
    					'clearance_field_id' => $DirectorOfficeID['id'],
    					'clearance_field_status' => 1,
    				];
    				
					//Form Details
					$formDet = $scis_model->getFormInfo($f->fID,2);
					$formDet->director_sign = ($cEntries_model->where($doEntry_condition)->first()) ? TRUE : FALSE;

					//Entries
					$entries = $scis_model->getCurrentStudentEntries($formDet->studID,$formDet->period);
					$ClearanceFieldStatuses = array();
					$array_count = 0;

					foreach($entries as $ent)
					{
						if($ent->field_id != $DirectorOfficeID['id'])
						{
							$ClearanceFieldStatuses[$array_count]['field'] = $ent->field;
							$ClearanceFieldStatuses[$array_count]['officer'] = $ent->officer_name;
							$ClearanceFieldStatuses[$array_count]['entry_status'] = $ent->status;

							//Deficiencies of an Entry
							$deficiencies = $scis_model->getCurrentStudentDeficiency($ent->entry_id);

							$ClearanceFieldStatuses[$array_count]['deficiencies'] = $deficiencies;
							$array_count++;
						}						
					}

					$form_data = [
						'form' => $formDet,
						'clearance_field' => $ClearanceFieldStatuses,
					];

					$reportData[$reportCount++] = $form_data;
				}

				if($reportData)
				{
					//Log Activity
					$log_user = $session->get('user_id');
					$log_message = 'Generated a Report : '.$report_type;

					\ScisSystem::logActivity($log_user,$log_message);

                    $report->ClearanceForms($reportData);
     //                foreach($reportData as $report)
					// {
					// 	echo "Student Name: ".$report['form']->student_name."<br>";
					// 	echo "Student Number: ".$report['form']->student_number."<br>";
					// 	echo "Course: ".$report['form']->course_code."<br>";
					// 	echo "Year and Section: ".$report['form']->year."-1<br>";
					// 	echo "Student Type: ".$report['form']->studType."<br>";
					// 	echo "Contact No.: ".$report['form']->contact_no."<br>";
					// 	echo "School Year: ".$report['form']->sc_year."<br>";
					// 	echo "Clerance Type: ".$report['form']->clearanceType."<br>";

					// 	echo "Form Status / Registrar Remark: ";
					// 	echo ($report['form']->status == 1) ? "Cleared" : "Not Cleared";

					// 	echo "<br>Statuses<br>";
					// 	$count = 1;
					// 	foreach($report['clearance_field'] as $field)
					// 	{
					// 		echo $count++." | ".$field['field']." | ".$field['officer']." | ";
					// 		echo ($field['entry_status'] == 0) ? "Not Cleared" : "Cleared";
					// 		echo " | ";
					// 		if($field['deficiencies'] && $field['entry_status'] == 0)
					// 		{
					// 			echo "Deficiencies: ";
					// 			foreach($field['deficiencies'] as $def)
					// 			{
					// 				echo $def->req_name."[";
					// 				echo ($def->def_status == 0 || $def->def_status == 1) ? "Uncleared" : "Cleared";
					// 				echo "] -";
					// 			}
					// 		}

					// 			echo "<br>";
					// 	}
					// 	echo "<br>=========================<br><br>";
					// }
				}
				else
				{
					//$session->setFlashdata('err_messages',['No Data to report']);
					//return redirect()->back()->withInput();
					echo "No Data to report";
				}
			}
			else if($reportType == 'graduationClearances')
			{
				$report_type = 'Graduation Clearance Forms';
				echo "GradClearanceForms<br>================<br><br>";

				//get forms
				$forms = $scis_model->gradClearanceFormsReport($scYear, $status, $course);
				$reportData = array();
				$reportCount = 0;

				foreach($forms as $f)
				{
					//Director's Office Field
					$doField_condition = [
						'deleted' => 0,
						'field_name' => "Director's Office",
					];
					$DirectorOfficeID = $cField_model->select('id')->where($doField_condition)->first();

					//Director's Office Entry
					$doEntry_condition = [
						'clearance_form_id' => $f->fID,
						'clearance_field_id' => $DirectorOfficeID,
						'clearance_field_status' => 1,
					];

					//Form Details
					$formDet = $scis_model->getGradFormInfo($f->fID,2);
					$formDet->director_sign = ($cEntries_model->where($doEntry_condition)->first()) ? TRUE : FALSE;

					//Entries
					$entries = $scis_model->getCurrentStudentEntries($formDet->studID,0);
					$ClearanceFieldStatuses = array();
					$array_count = 0;

					$respProf_model = new \App\Models\RespectiveProfessorsModel();
            		$resProf = $respProf_model->getList($formDet->grad_form_id);

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

					$form_data = [
						'form' => $formDet,
						'clearance_field' => $ClearanceFieldStatuses,
						'respective_professors' => $resProf,
					];

					$reportData[$reportCount++] = $form_data;
				}

				if($reportData)
				{
					//Log Activity
					$log_user = $session->get('user_id');
					$log_message = 'Generated a Report : '.$report_type;

					\ScisSystem::logActivity($log_user,$log_message);

                    $report->ClearanceForms($reportData,TRUE);
     //                foreach($reportData as $report)
					// {
					// 	echo "Student Name: ".$report['form']->student_name."<br>";
					// 	echo "Student Number: ".$report['form']->student_number."<br>";
					// 	echo "Course: ".$report['form']->course_code."<br>";
					// 	echo "Year and Section: ".$report['form']->year."-1<br>";
					// 	echo "Student Type: ".$report['form']->studType."<br>";
					// 	echo "Contact No.: ".$report['form']->contact_no."<br>";
					// 	echo "School Year: ".$report['form']->sc_year."<br>";
					// 	echo "Clerance Type: ".$report['form']->clearanceType."<br>";

					// 	echo "Form Status / Registrar Remark: ";
					// 	echo ($report['form']->status == 1) ? "Cleared" : "Not Cleared";

					// 	echo "<br>=================================<br>";
					// 	echo "Respective Professors<br>";
					// 	$count = 1;
					// 	foreach($resProf as $prof)
					// 	{
					// 		echo $count++." | ".$prof->professor_name." | ".$prof->sub_code."-".$prof->sub_name." | ";
					// 		echo ($prof->status == 0) ? "Not Cleared" : "Cleared";
							

					// 		echo "<br>";
					// 	}

					// 	echo "<br>Statuses<br>";
					// 	$count = 1;
					// 	foreach($report['clearance_field'] as $field)
					// 	{
					// 		echo $count++." | ".$field['field']." | ".$field['officer']." | ";
					// 		echo ($field['entry_status'] == 0) ? "Not Cleared" : "Cleared";
					// 		echo " | ";
					// 		if($field['deficiencies'] && $field['entry_status'] == 0)
					// 		{
					// 			echo "Deficiencies: ";
					// 			foreach($field['deficiencies'] as $def)
					// 			{
					// 				echo $def->req_name."[";
					// 				echo ($def->def_status == 0 || $def->def_status == 1) ? "Uncleared" : "Cleared";
					// 				echo "] -";
					// 			}
					// 		}

					// 			echo "<br>";
					// 	}
					// 	echo "<br>=========================<br><br>";
					// }
				}
				else
				{
					//$session->setFlashdata('err_messages',['No Data to report']);
					//return redirect()->back()->withInput();
					echo "No Data to report";
				}
			}
		}
	}

	public function FormReport()
	{	
		$session = session();
		
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

	public function GradFormReport()
	{	
		$session = session();

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
			$form = $scis_model->getGradFormInfo($formID,2);
			echo $form->director_sign = ($cEntries_model->where($doEntry_condition)->first()) ? TRUE : FALSE;

			//Entries
			$entries = $scis_model->getCurrentStudentEntries($form->studID,0);
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

			$respProf_model = new \App\Models\RespectiveProfessorsModel();
            $resProf = $respProf_model->getList($form->grad_form_id);

			$data = [
				'form' => $form,
				'clearance_field' => $ClearanceFieldStatuses,
				'respective_professors' => $resProf,
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

			echo "<br>==============Respective Professors ===================<br>";
			$count = 1;
			foreach($resProf as $prof)
			{
				echo $count++." | ".$prof->professor_name." | ".$prof->sub_code."-".$prof->sub_name." | ";
				echo ($prof->status == 0) ? "Not Cleared" : "Cleared";
				

				echo "<br>";
			}

			echo "<br>================Clearance Fields=================<br>";
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
			$report->ClearanceForm($data,TRUE);
		}
		else
		{
			return redirect()->back();
		}
	}
}