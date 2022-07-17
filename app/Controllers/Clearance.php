<?php

namespace App\Controllers;
use App\Libraries\Hash;
use App\Libraries\Reports;

use TCPDF;

class Clearance extends BaseController
{
	public function __construct(){
		helper('ScisSystem');
	}

	public function index()
	{
		return redirect()->to('/');
	}

	public function Form($id)
	{
		\ScisSystem::refreshData1();

		$permission_model = new \App\Models\PermissionsModel();

		$session = session();

		$data = [
			'page_title' => 'Clearance Form | PUPT SCIS',
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
		
		if($permission_model->hasPermission($uID,['view_clearance_forms']) && $session->get('Student_access'))
		{
			$db = \Config\Database::connect();
			$scis_model = new \App\Models\ScisModel($db);
			$cField_model = new \App\Models\ClearanceFieldsModel();
			$cEntry_model = new \App\Models\ClearanceEntriesModel();
			$cForm_model = new \App\Models\ClearanceFormsModel();
			$pos_model = new \App\Models\PositionsModel();

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
					'id' => $id."-".$entry->position_id."-".$entry->entry_id,
					'clearance_field' =>  $entry->field,
					'clearance_officer' =>  $entry->officer_name,
					//'deficiencies' => $student_deficiencies,
					'status' => $entry->status,
					'pos'	=> $entry->position,
				];
				
			}

			$dField_condition = [
				'deleted' => 0,
				'clearance_form_id' => $formID,
				'clearance_field_id' => $session->get('DirectorOfficeID'),
			];

			$doEntryInfo = $cEntry_model->where($dField_condition)->first();
			$doPositionInfo = $pos_model->where('clearance_field_id',$session->get('DirectorOfficeID'))->first();

			$data['Entries'] = $student_entries;
			$data['formID'] = $formID;
			$data['director_sign'] = $directors_approval;
			$data['registrar_sign'] = $registrars_approval;
			$data['doEntryID'] = $id."-".$doPositionInfo['id']."-".$doEntryInfo['id'];

			$data['Requirements'] = ($permission_model->hasPermission($uID,'view_deficiencies')) ? TRUE : FALSE ;	
			
			return view('student/clearance_form', $data);
		}
		else
		{
			return view('site/no_permission',$data);
		}
	}

	public function Requirements($id)
	{
		\ScisSystem::refreshData1();

		$permission_model = new \App\Models\PermissionsModel();

		$session = session();

		$data = [
			'page_title' => 'Requirements | PUPT SCIS',
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
		
		if($permission_model->hasPermission($uID,['view_deficiencies']) && $session->get('Student_access'))
		{
			$mess = explode("-" ,$id);
			$pID = $mess[0];
			$posID = $mess[1];
			if(isset($mess[2]))
			{
				$entID = $mess[2];
			}

			$db = \Config\Database::connect();
			$scis_model = new \App\Models\ScisModel($db);
			$position_model = new \App\Models\PositionsModel();

			$preReqData = $scis_model->getPreRequisites($posID);

			$student_id = $session->get('student_id');
			//$period_id = $session->get('clearance_data');

			$prCount = 0;
			if($preReqData)
			{
				foreach ($preReqData as $PR) {

					$preReq_Entry = $scis_model->getCurrentStudentEntries($student_id,$pID,$PR->position_id);
					$preReqData[$prCount++]->status = (!empty($preReq_Entry->status)) ? $preReq_Entry->status : 0;
				}
				
			}

			$student_id = $session->get('student_id');

			$cField = "";

			$position_data = $position_model->find($posID);
			$position_name = $position_data['position_name'];

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

				$data['pID'] = $pID;
				$data['fieldName'] = $cField;
				$data['positionName'] = $position_name;
				$data['entry_deficiencies'] = $entry_deficiencies;
				$data['preRequisites'] = $preReqData;

				$data['SubmitRequirements'] = ($permission_model->hasPermission($uID,'add_submissions')) ? TRUE : FALSE ;
				$data['ViewSubmissions'] = ($permission_model->hasPermission($uID,'view_submissions')) ? TRUE : FALSE ;

				return view('student/requirements',$data);
			}

			return redirect()->back();
		}		
		else
		{
			return view('site/no_permission',$data);
		}
	}

	//Finalization Stage / Clearance Completion Stage / Registrar
	public function FormsList($period ="", $statFilter = 0)
	{
		\ScisSystem::refreshData1();

		$permission_model = new \App\Models\PermissionsModel();

		$session = session();

		$data = [
			'page_title' => 'Clearance Forms | PUPT SCIS',
			'Name'			=> $session->get('user_name'),
		    'profilePic'			=> $session->get('user_pic'),
		    'clearanceData'			=> $session->get('clearance_data'),
		    'activeClearance' => $session->get('ongoingClearance'),
		    'user_title'	=> $session->get('title'),
		    'user_notifications' => $session->get('user_notifications'),
		    'user_fields' => $session->get('user_co_fields'),
			'user_gradFields' => $session->get('user_co_gradFields'),
			'user_subject_resp' => $session->get('user_subject_handled'),
			'gradClearance' => ($period == "Graduation") ? TRUE : FALSE,
		];

		$uID = $session->get('user_id');

		$gradClearance = ($period == "Graduation") ? TRUE : FALSE;
		
		if($permission_model->hasPermission($uID,['view_clearance_forms']) && (!$session->get('Student_access') || $session->get('ClearanceOfficer_access')))
		{
			if($session->get('Director_access'))
			{
				$directorField = $session->get('user_co_fields');
				$data['directorInfo'] = $directorField[0]['field_id']."-".$directorField[0]['position_id'];
			}

			$data['ManageDeficiencies'] = ($permission_model->hasPermission($uID,['view_deficiencies']) && $session->get('Director_access'));

			if($this->request->getGET('status'))
			{
				$statFilter = $this->request->getGET('status');
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
					if($gradClearance)
					{
						$forms = $scis_model->getGradFormInfo('all',0);
					}
					else
					{
						$forms = $scis_model->getFormInfo('all',0,$period);
					}
					

					$count = 1;
					//check clearance entries of the forms if all of it is cleared - put all students with with a fully cleared entries in student
					foreach($forms as $f)
					{
						$entries = $cEntry_model->where('clearance_form_id',$f->form_id)->findAll();
						$candidate = true;

						if($session->get('Director_access'))
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

						if(empty($entries))
						{
							$candidate = false;
						}
						else
						{
							foreach($entries as $ent)
							{		
								if($session->get('Director_access'))
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
						}

						if($gradClearance)
						{
							$respectiveProfessors_model = new \App\Models\RespectiveProfessorsModel();

							$respProf = $respectiveProfessors_model->getList2($f->form_id);

							foreach($respProf as $resp)
							{	
								if($resp->status == 0)
								{
									$candidate = false;
									break;
								}																							
							}

						}					
						
						
						if($candidate)
						{
							$students[$studCount] = $f;
							if($session->get('Director_access'))
							{
								$students[$studCount]->entry_id = $entID2;
								$students[$studCount]->entry_info = $entID2."-".$f->studID."-".$session->get('DirectorOfficeID');
							}							
							$studCount++;
						}
												
					}
				} 
				else if ($statFilter == 1)
				{
					//get all forms with a status of 1
					if($gradClearance)
					{
						if($session->get('Director_access'))
						{
							$forms = $scis_model->getGradFormInfo('all',2);
						}
						else
						{
							$forms = $scis_model->getGradFormInfo('all',1);
						}
					}
					else
					{
						if($session->get('Director_access'))
						{
							$forms = $scis_model->getFormInfo('all',2,$period);
						}
						else
						{
							$forms = $scis_model->getFormInfo('all',1,$period);
						}
					}

					foreach($forms as $f)
					{
						if($session->get('Director_access'))
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
					if($gradClearance)
					{
						$forms = $scis_model->getGradFormInfo('all',0);
					}
					else
					{
						$forms = $scis_model->getFormInfo('all',0,$period);
					}

					//check clearance entries of the forms if has any uncleared entries - put all students with with uncleared entries in students
					foreach($forms as $f)
					{
						$entries = $cEntry_model->where('clearance_form_id',$f->form_id)->findAll();
						$candidate = true;

						if(empty($entries))
						{	
							$candidate = false;
						}
						else
						{
							foreach($entries as $ent)
							{
								if($session->get('Director_access'))
								{
									if($ent['clearance_field_id'] != $session->get('DirectorOfficeID'))
									{
										//echo $ent['clearance_field_id']."-";
										if($ent['clearance_field_status'] == 0)
										{
										    $entID2 = $ent['id'];
											$candidate = false;
											break;
										}
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
						}

						if($gradClearance)
						{
							$respectiveProfessors_model = new \App\Models\RespectiveProfessorsModel();

							$respProf = $respectiveProfessors_model->getList2($f->form_id);

							foreach($respProf as $resp)
							{	
								if($resp->status == 0)
								{
									$candidate = false;
									break;
								}																							
							}

						}

						if(!$candidate)
						{
							$students[$studCount] = $f;
							if($session->get('Director_access'))
							{
								$students[$studCount]->entry_id = $entID2;
								$students[$studCount]->entry_info = $entID2."-".$f->studID."-".$session->get('DirectorOfficeID');
							}							
							$studCount++;
						}
					}
				}
			}

			$session->setFlashdata('statFil',$statFilter);

			$data['profilePic'] = $session->get('user_pic');
		    $data['clearanceData'] = $session->get('clearance_data');
		    $data['student_list'] = $students;
		    $data['period'] = $period;

			return view('admin/view_completed_clearance',$data);					
		}
		else
		{
			return view('site/no_permission',$data);
		}
	}

	//Approve / Sign Clearance Completion
	public function SignCompletion($form)
	{
		$session = session();
		
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

	public function StudentStatus($form_period) //For viewing in completion of clearance
	{
		\ScisSystem::refreshData1();

		$permission_model = new \App\Models\PermissionsModel();

		$session = session();

		$data = [
			'page_title' => 'Student\'s Form | PUPT SCIS',
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
		
		if($permission_model->hasPermission($uID,['view_clearance_forms']) && (!$session->get('Student_access') || $session->get('ClearanceOfficer_access')))
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
			$student_subjects = FALSE;
			$array_count = 0;

			$studForm = $cForm_model->find($formID);

			$data['gradClearance'] = $gradClearance = ($studForm['clearance_type_id'] == 2) ? TRUE : FALSE;

			$formData = array();
			if($gradClearance)
			{
				$formData = $scis_model->getGradFormInfo($formID,2);
			}
			else
			{
				$formData = $scis_model->getFormInfo($formID,2);
			}

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

			if($gradClearance)
			{
				$student_subjects = array();
				$array_count = 0;

				$respectiveProfessors_model = new \App\Models\RespectiveProfessorsModel();

				$respProf = $respectiveProfessors_model->getList2($formID);

				foreach($respProf as $resp)
				{	
					$student_subjects[$array_count++] = [
						'id' => $resp->id,
						'subject' => $resp->sub_code." | ".$resp->sub_name,
						'professor' => $resp->professor_name,
						'status' => $resp->status,
					];																							
				}
			}

			$data['Entries'] = $student_entries;
			$data['Subjects'] = $student_subjects;
		    $data['formID'] = $formID;
		    $data['formData'] = $formData;
		    $data['periodH'] = $periodH;
		    $data['director_sign'] = $directors_approval;
		    $data['registrar_sign'] = $registrars_approval;
			
			return view('clearance_form2', $data);
		}
		else
		{
			return view('site/no_permission', $data);
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
}