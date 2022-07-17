<?php

namespace App\Controllers;
use App\Libraries\Hash;
use App\Libraries\Reports;

use TCPDF;

class RespectiveProfessors extends BaseController
{
	public function __construct(){
		helper('ScisSystem');
	}

	public function index()
	{
		return redirect()->to('List');
	}

	public function List($id) //id = Subject ID
	{
		$session = session();
		if($session->get('logged_in'))
		{
			\ScisSystem::refreshData1();

			$uID = $session->get('user_id');
			$data = array();
			
			$user_model = new \App\Models\UsersModel();

			$user_data = $user_model->find($uID);

			$data = [
				'page_title' 	=> 'Subject Management | PUPT SCIS',
			    'user_role'		=> $session->get('user_role'),
			    'activeClearance' => $session->get('ongoingClearance'),
			    'Name'			=> $session->get('user_name'),
			    'profilePic'			=> $session->get('user_pic'),
			    'user_notifications' => $session->get('user_notifications'),
			    'user_fields' => $session->get('user_co_fields'),
			    'user_gradFields' => $session->get('user_co_gradFields'),
			    'user_subject_resp' => $session->get('user_subject_handled'),
			];

			if ($session->get('Professor_access'))
			{
				$rProfessor_model = new \App\Models\RespectiveProfessorsModel();
				$subject_model = new \App\Models\SubjectsModel();

				$SubjectDetail = $subject_model->find($id);
				$EntryList = $rProfessor_model->getStudentEntryList($uID);

				$rProfList = array(); // Array for Porfessors need to sign
				$rProfList_count = 0;
				$counter = 1;
				foreach($EntryList as $EList)
				{
					if($EList->sub_id == $id)
					{
						$rProfList[$rProfList_count++] = [
							"sub_id" => $EList->sub_id,
							"sub_name" => $EList->subject,
							"student_name" => $EList->student_name,
                            "student_number" => $EList->student_number,
							"course" => $EList->course,
							"status" => $EList->status,
							"id" => $EList->entry_id, //for Buttons (clear/tag/view)
						];
					}
				}

                //PErmissions = NOTE: must be replace with true permissions data from db
                $data['TaggingDeficiencies'] = TRUE;
                $data['ClearStudents'] = TRUE;

				//Data for Views
				$data['subject_detail'] = $SubjectDetail;
				$data['rProf_entries'] = $rProfList;

				//Sample Output
				// echo "<br>+++++++++++++++++++++++++++++++++++++++++++++++<br>";
				// echo "Subject : ".$SubjectDetail['code']." | ".$SubjectDetail['subject']."<br>";
				// echo "<br>=====ENTRIES====<br>";

				// $ent_count = 1;
				// if($rProfList)
				// {
				// 	foreach($rProfList as $ent)
				// 	{					
				// 		echo $ent_count++." - ";
				// 		echo $ent['student_name']." - ";
				// 		echo $ent['course']." - ";
				// 		echo $ent['status']." - ";
				// 		echo "<a href='../Clear/".$ent['id']."'>Clear</a>"; 
				// 		echo "| <a href='../TagDef/".$ent['id']."'>Tag Deficiency</a>";
				// 		echo "| <a href='../View/".$ent['id']."-".$id."'>View</a>";
				// 		echo "<br>";
				// 		echo "<br>";					
				// 	}
				// }
				// else
				// {
				// 	echo "No data Available";
				// }
				// echo "<br><a href='/pupt_scis'>BACK</a>";
			}

			//View
			return view('manage_grad_deficiency', $data);
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function Clear($id = FALSE, $multiClear = FALSE) //$id = entryID
	{
		$session = session();
		if($session->get('logged_in') && $session->get('Professor_access'))
		{
			if($id)
			{
				$rProfessor_model = new \App\Models\RespectiveProfessorsModel();

				date_default_timezone_set('Asia/Manila');
				$modify_stamp = date('Y-m-d H:i:s', time());

				$data = [
					'signature' => 1,
					'modified' => $modify_stamp,
				];

				if($rProfessor_model->update($id,$data) != FALSE)
				{
					$messages = ['Cleared Successfully'];
					$session->setFlashdata('success_messages',$messages);
				}
				else
				{
					$errors = $rProfessor_model->errors();
					$session->setFlashdata('err_messages',$errors);
				}
			}
			else
			{
				$errors = ['Invalid ID'];
				$session->setFlashdata('err_messages',$errors);
			}		

            if(!$multiClear)
            {
                return redirect()->back()->withInput();
            }
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function TagDef($multiTag = FALSE, $id = FALSE, $def = FALSE, $submitable = FALSE, $note = "") //$id = respective prof entry ID, $defInput = inputted Def (if typed)
	{
		$session = session();
		if($session->get('logged_in') && $session->get('Professor_access')) //must add restriction base on permission
		{
		    if($this->request->getPOST('entID'))
		    {
		        $id = $this->request->getPOST('entID');
		    }
		    
		    if($this->request->getPOST('Deficiency'))
		    {
		        $def = $this->request->getPOST('Deficiency');
		    }
		    
		    if($this->request->getPOST('Note'))
		    {
		        $note = $this->request->getPOST('Note');
		    }
		
			if($id && $def)
			{
				$sDeficiency_model = new \App\Models\SubjectDeficienciesModel();

				date_default_timezone_set('Asia/Manila');
				$modify_stamp = date('Y-m-d H:i:s', time());

				$data = [
					'respective_professor_id' => $id,
					'subject_requirement' => $def,
					'note' => $note,
				];

				if($sDeficiency_model->insert($data) != FALSE)
				{
					$messages = ['Tagged Successfully'];
					$session->setFlashdata('success_messages',$messages);

					if($this->request->getPOST('submittable') || $submitable)
					{
						$sDefID = $sDeficiency_model->getInsertID();

						$sSubmissions_model = new \App\Models\SubjectSubmissionsModel();

						$sSub_data = [
							'subject_deficiency_id' => $sDefID,
						];

						$sSubmissions_model->insert($sSub_data);
					}
				}
				else
				{
					$errors = $sDeficiency_model->errors();
					$session->setFlashdata('err_messages',$errors);
					echo $errors;
				}
			}
			else
			{
				$errors = ['Invalid ID'];
				$session->setFlashdata('err_messages',$errors);
			}		

            if(!$multiTag)
            {
                return redirect()->back()->withInput();
            }
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}
	
	public function MultiTagging()
	{
	    $ids = $this->request->getPOST('SelectedStudent');
	    $def = $this->request->getPOST('Deficiency');
	    $note = $this->request->getPOST('Note');
	    $submittable = (!empty($this->request->getPOST('submittable'))) ? TRUE : FALSE;
	    
	    foreach($ids as $id)
	    {
	        $this->TagDef(TRUE, $id, $def, $submittable, $note);
	    }
	    
	    return redirect()->back()->withInput();
	}
	
	public function MultiClearing()
	{
	    $ids = $this->request->getPOST('SelectedStudent');
	    
	    foreach($ids as $id)
	    {
	        $this->Clear($id, TRUE);
	    }
	    
	    return redirect()->back()->withInput();
	}

	public function View($id = FALSE) //$id = entryID-subjectID
	{
		$session = session();
		if($session->get('logged_in'))
		{
			\ScisSystem::refreshData1();

			$uID = $session->get('user_id');
			$data = array();
			
			$user_model = new \App\Models\UsersModel();

			$user_data = $user_model->find($uID);

			$data = [
				'page_title' 	=> 'Subject Management | PUPT SCIS',
			    'user_role'		=> $session->get('user_role'),
			    'activeClearance' => $session->get('ongoingClearance'),
			    'Name'			=> $session->get('user_name'),
			    'profilePic'			=> $session->get('user_pic'),
			    'user_notifications' => $session->get('user_notifications'),
			    'user_fields' => $session->get('user_co_fields'),
			    'user_gradFields' => $session->get('user_co_gradFields'),
			    'user_subject_resp' => $session->get('user_subject_handled'),
			];

			if($session->get('Professor_access'))
			{
				if($id)
				{
					$ID = explode("-",$id);
					$entryID = $ID[0];
					$subID = $ID[1];
					$sDeficiency_model = new \App\Models\SubjectDeficienciesModel();
					$rProfessor_model = new \App\Models\RespectiveProfessorsModel();
					$subject_model = new \App\Models\SubjectsModel();
					$sSubmissions_model = new \App\Models\SubjectSubmissionsModel();

					$list_condition = [
						'respective_professor_id' => $entryID,
						'deleted' => 0,
					];

					$student_info = $rProfessor_model->getStudentInfo($entryID);

					$list = $sDeficiency_model->where($list_condition)->findAll();

					$subjectDetail = $subject_model->find($subID);

					$listed_count = 0;
					foreach($list as $l)
					{
						$submission_condition = [
							'subject_deficiency_id' => $l['id'],
							'deleted' => 0
						];
						if($submission_detail = $sSubmissions_model->where($submission_condition)->first())
						{
							if($submission_detail["file_path"])
							{
								$list[$listed_count]['submission'] = $submission_detail['file_path'];
							}
						}

						
						$listed_count++;
					}
					
					$data['entID'] = $entryID;
					$data['list'] = $list;
					$data['studentInfo'] = $student_info;
					$data['subName'] = $subjectDetail['code']." | ".$subjectDetail['subject'];
				}
				else
				{
					$errors = ['Invalid ID'];
					$session->setFlashdata('err_messages',$errors);
				}		

				//Sample Output
				// echo "ENTRY ID: ".$entryID."<br>";
				// echo "<a href='../TagDef/".$id."'>AddDef</a>";
				// echo "<br>==============================<br>";
				// if($data['list'])
				// {
				// 	echo "No. | Requirement | Note | Status | Actions<br>";
				// 	$defCount = 1;
				// 	foreach($data['list'] as $row)
				// 	{
				// 		echo $defCount++." | ";
				// 		echo $row['subject_requirement']." | ";
				// 		echo ($row['note']) ? $row['note'] : "NONE";
				// 		echo " | ";
				// 		echo $row['status']." | ";
				// 		echo "- <a href='../ClearReq/".$row['id']."'>Clear</a>";
				// 		echo "- <a href='../DeleteReq/".$row['id']."'>Delete</a>";
				// 		echo "<br>";
				// 	}
				// }
				// else
				// {
				// 	echo "No data Available";
				// }
				// echo "<br>";
				// echo "<a href='../List/".$subID."'>BACK</a>";
			}

			//View
			return view('view_grad_deficiency', $data);
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function ClearReq($id = FALSE) //$id = subject deficiency
	{
		$session = session();
		if($session->get('logged_in') && $session->get('Professor_access'))
		{
			if($id)
			{
				$sDeficiency_model = new \App\Models\SubjectDeficienciesModel();

				date_default_timezone_set('Asia/Manila');
				$modify_stamp = date('Y-m-d H:i:s', time());

				$data = [
					'status' => 1,
					'modified' => $modify_stamp,
				];

				if($sDeficiency_model->update($id,$data) != FALSE)
				{
					$messages = ['Cleared Successfully'];
					$session->setFlashdata('success_messages',$messages);
				}
				else
				{
					$errors = $sDeficiency_model->errors();
					$session->setFlashdata('err_messages',$errors);
				}
			}
			else
			{
				$errors = ['Invalid ID'];
				$session->setFlashdata('err_messages',$errors);
			}		

			return redirect()->back()->withInput();
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function UnclearReq($id = FALSE) //$id = subject deficiency
	{
		$session = session();
		if($session->get('logged_in') && $session->get('Professor_access'))
		{
			if($id)
			{
				$sDeficiency_model = new \App\Models\SubjectDeficienciesModel();

				date_default_timezone_set('Asia/Manila');
				$modify_stamp = date('Y-m-d H:i:s', time());

				$data = [
					'status' => 0,
					'modified' => $modify_stamp,
				];

				if($sDeficiency_model->update($id,$data) != FALSE)
				{
					$messages = ['Cleared Successfully'];
					$session->setFlashdata('success_messages',$messages);
				}
				else
				{
					$errors = $sDeficiency_model->errors();
					$session->setFlashdata('err_messages',$errors);
				}
			}
			else
			{
				$errors = ['Invalid ID'];
				$session->setFlashdata('err_messages',$errors);
			}		

			return redirect()->back()->withInput();
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function DeleteReq($id = FALSE) //$id = subject deficiency
	{
		$session = session();
		if($session->get('logged_in') && $session->get('Professor_access'))
		{
			if($id)
			{
				$sDeficiency_model = new \App\Models\SubjectDeficienciesModel();

				date_default_timezone_set('Asia/Manila');
				$modify_stamp = date('Y-m-d H:i:s', time());

				$data = [
					'deleted' => 1,
					'deleted_date' => $modify_stamp,
				];

				if($sDeficiency_model->update($id,$data) != FALSE)
				{
					$messages = ['Cleared Successfully'];
					$session->setFlashdata('success_messages',$messages);
				}
				else
				{
					$errors = $sDeficiency_model->errors();
					$session->setFlashdata('err_messages',$errors);
				}
			}
			else
			{
				$errors = ['Invalid ID'];
				$session->setFlashdata('err_messages',$errors);
			}		

			return redirect()->back()->withInput();
		}
		else
		{
			return redirect()->back()->to('/');
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
			$entID = $id;

			$db = \Config\Database::connect();
			$scis_model = new \App\Models\ScisModel($db);
			$rProfessor_model = new \App\Models\RespectiveProfessorsModel();

			$student_id = $session->get('student_id');			

			$entry_deficiencies = $rProfessor_model->getDeficiencies($entID);
			$entry_info = $rProfessor_model->getEntryInfo($entID);

			$cField = $entry_info->sub_code." | ".$entry_info->sub_name;

			$sSubmissions_model = new \App\Models\SubjectSubmissionsModel();

			$array_count = 0;

			foreach($entry_deficiencies as $ent)
			{
				$sub_condition = [
					'subject_deficiency_id' => $ent->id,
					'deleted' => 0,
				];
				$def_submission = $sSubmissions_model->where($sub_condition)->first();

				if(!empty($def_submission))
				{   
					$entry_deficiencies[$array_count]->sub_type = 1;
					$entry_deficiencies[$array_count]->submission = $def_submission['file_path'];
				}

				$array_count++;
			}

			$data['fieldName'] = $cField;
			$data['entry_deficiencies'] = $entry_deficiencies;
			
			$data['SubmitRequirements'] = ($permission_model->hasPermission($uID,'add_submissions')) ? TRUE : FALSE ;
			$data['ViewSubmissions'] = ($permission_model->hasPermission($uID,'view_submissions')) ? TRUE : FALSE ;

			return view('student/subject_requirements',$data);
		}		
		else
		{
			return view('site/no_permission',$data);
		}
	}

}