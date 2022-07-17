<?php

namespace App\Controllers;
use CodeIgniter\I18n\Time;

class GraduationClearance extends BaseController
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
            'page_title' => 'Graduation Clearance | PUPT SCIS',
            'Name'          => $session->get('user_name'),
            'profilePic'            => $session->get('user_pic'),
            'clearanceData'         => $session->get('clearance_data'),
            'activeClearance' => $session->get('ongoingClearance'),
            'user_title'    => $session->get('title'),
            'user_notifications' => $session->get('user_notifications'),
            'user_fields' => $session->get('user_co_fields'),
			    'user_gradFields' => $session->get('user_co_gradFields'),
			    'user_subject_resp' => $session->get('user_subject_handled'),   
        ];

        $uID = $session->get('user_id');
        
        if($permission_model->hasPermission($uID,['add_graduation_clearances','view_graduation_clearances']))
        {
        	$db = \Config\Database::connect();
			$scis_model = new \App\Models\ScisModel($db);
			$student_model = new \App\Models\StudentsModel();
			$course_model = new \App\Models\CoursesModel();

            if($session->get('Student_access'))
            {
                $studID = $session->get('student_id');
                $studentData = $student_model->find($studID);

                $studCourse = $course_model->find($studentData['course_id']);

                $eligibility = false;
                if($studentData['year_level'] == $studCourse['max_year_level'] || $studentData['year_level'] == 0)
                {
                    $eligibility = true;
                }

                $gradForm = $scis_model->getGradForm($studID);
                if($gradForm)
                {
                    $gradForm_model = new \App\Models\GraduationClearancesModel();
                    $respProf_model = new \App\Models\RespectiveProfessorsModel();
                    $cEntries_model = new \App\Models\ClearanceEntriesModel();

                    $gradForm->data = $gradForm_model->getForm($gradForm->id);
                    $gradForm->resProf = $respProf_model->getList($gradForm->id);
                    $gradForm->cEntries = $cEntries_model->getList($gradForm->clearance_form_id);
                }                                

                $data['form'] = $gradForm;
                $data['eligible'] = $eligibility;

                return view('student/graduation_clearance', $data);    
            }
            else
            {
                return redirect()->to('/GraduationClearance/Applications');
            }
        	
        }
        else
        {
           return view('site/no_permission', $data);
        }
    }

    public function Applications($status = 0)
    {
        \ScisSystem::refreshData1();

        $permission_model = new \App\Models\PermissionsModel();

        $session = session();

        $data = [
            'page_title' => 'Graduation Clearance Applications | PUPT SCIS',
            'Name'          => $session->get('user_name'),
            'profilePic'            => $session->get('user_pic'),
            'clearanceData'         => $session->get('clearance_data'),
            'activeClearance' => $session->get('ongoingClearance'),
            'user_title'    => $session->get('title'),
            'user_notifications' => $session->get('user_notifications'),
            'user_fields' => $session->get('user_co_fields'),
			    'user_gradFields' => $session->get('user_co_gradFields'),
			    'user_subject_resp' => $session->get('user_subject_handled'),   
        ];

        $uID = $session->get('user_id');
        
        if($permission_model->hasPermission($uID,['view_graduation_clearances']))
        {
            if($session->get('Student_access'))
            {
                return redirect()->to('/GraduationClearance');
            }
            else
            {
                $gradForm_model = new \App\Models\GraduationClearancesModel();
                $gradForms_condition = [
                    "deleted" => 0,
                    "status" => $status,
                ];

                $gradForms_list = $gradForm_model->getList($status);

                $data['forms'] = $gradForms_list;

                return view('admin/grad_clearance_application_list', $data);
                /**
                echo "Graduation Forms<br>";
                $count = 1;
                foreach($gradForms_list as $form)
                {
                    echo $count++." - ".$form->grad_form_id." - ".$form->clearance_form_id." - ".$form->student_name." - ".$form->application_status." - ".$form->course;
                    echo "\t\t<a class='btn btn-success btn-sm' href='".base_url('GraduationClearance/ViewApplication/'.$form->grad_form_id)."' >View</a>";
                    echo "|\t <a class='btn btn-success btn-sm' href='".base_url('GraduationClearance/Approve/'.$form->grad_form_id)."' >Approve</a>";
                    echo "|\t <a class='btn btn-success btn-sm' href='".base_url('GraduationClearance/Decline/'.$form->grad_form_id)."' >Decline</a>";
                    echo "<br>";
                }
                 **/
            }
        }
        else
        {
           return view('site/no_permission', $data);
        }
    }

    public function ViewApplication($id = "")
    {
        \ScisSystem::refreshData1();

        $permission_model = new \App\Models\PermissionsModel();

        $session = session();

        $data = [
            'page_title' => 'Graduation Clearance Application | PUPT SCIS',
            'Name'          => $session->get('user_name'),
            'profilePic'            => $session->get('user_pic'),
            'clearanceData'         => $session->get('clearance_data'),
            'activeClearance' => $session->get('ongoingClearance'),
            'user_title'    => $session->get('title'),
            'user_notifications' => $session->get('user_notifications'),
            'user_fields' => $session->get('user_co_fields'),
                'user_gradFields' => $session->get('user_co_gradFields'),
                'user_subject_resp' => $session->get('user_subject_handled'),   
        ];

        $uID = $session->get('user_id');
        
        if(TRUE) //need to add permission
        {
            if(!empty($id))
            {
                $gradForm_model = new \App\Models\GraduationClearancesModel();
                $respProf_model = new \App\Models\RespectiveProfessorsModel();
                $formData = $gradForm_model->getForm($id);

                $data['forms'] = $formData;


                /**
                echo "Student Name : ".$formData->student_name."<br>";
                echo "Student Number : ".$formData->student_number."<br>";
                echo "Course-Year : ".$formData->course." ".$formData->year_level."-1"."<br>";
                echo "Student Type : ".$formData->student_type."<br>";
                echo "Contact Number : ".$formData->contact."<br>";
                echo "Address : ".$formData->address."<br>";
                echo "Date of Birth : ".$formData->dob."<br>";
                echo "Gender : ==== <br>";
                echo "Major : ".($formData->major) ? $formData->major : "None" ."<br>";
                echo "Admitted in PUP : ".$formData->admitted_year." | ";
                echo ($formData->admitted_term == "1") ? '1st Sem' : '2nd Sem';
                echo "<br>";
                echo "Elementary School : ".$formData->elem." | Year Graduated : ".$formData->elem_year."<br>";
                echo "High School : ".$formData->hs." | Year Graduated : ".$formData->hs_year."<br>";
                **/
                $respectiveProf = $respProf_model->getList($formData->grad_form_id);
                $data['respectiveProf'] =  $respectiveProf;
                return view('admin/view_graduation_clearance', $data);
                /**
                echo "<br>+++++++++++++++++<br>";
                echo "No. - Subject Code - Subject Detail - Professor - Days - Time<br>";
                $subject_count = 1;
                foreach($respectiveProf as $prof)
                {
                    echo $subject_count++." - ".$prof->sub_code." - ".$prof->sub_name." - ".$prof->professor_name." - ".$prof->days." - ".$prof->time;
                    echo "<br>";
                }

                echo "<br>+++++++++++++++++<br>";
                echo "<br> COC : <a href='".base_url('uploads/certificate_of_candicacy/'.$formData->coc)."'>View COC</a><br>";
                 **/
            }
            else
            {
                return redirect()->to('/GraduationClearance/Applications');
            }
        }
        else
        {
           return view('site/no_permission', $data);
        }
    }

    public function Approve($id = "")
    {
        $session = session();
        if(!empty($id))
        {
            $gradForm_model = new \App\Models\GraduationClearancesModel();
            $formData = $gradForm_model->getForm($id);

            //echo $formData->student_name." has been Approved";

            //Modifiy graduation application status from pending to approved
            date_default_timezone_set('Asia/Manila');
            $time_stamp = $modify_stamp = date('Y-m-d H:i:s', time());

            $data = [
                'status' => 1,
                'modified' => $modify_stamp,
            ];

            $gradForm_model->update($id,$data);

            //create clearance entries for clearance form ========================================
            $clearanceField_model = new \App\Models\ClearanceFieldsModel();
            $clearanceFields = $clearanceField_model->where('deleted',0)->findAll();

            $db = \Config\Database::connect();
            $scis_model = new \App\Models\ScisModel($db);
            $officer_list = $scis_model->initiatingCleranceEntriesData();

            $clearanceEntry_model = new \App\Models\ClearanceEntriesModel();

            $clearanceFormID = $formData->clearance_form_id;
            $studentID = $formData->student_id;

            foreach($clearanceFields as $field)
            {
                if($field['clearance_type_id'] == 2 || $field['clearance_type_id'] == 3)
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

                            if($field['field_name'] == $officer->field_name)
                            {
                                if($field['field_name'] == 'Student Organization' && $officer->org_activeness = 1)
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
                                else if($field['field_name'] != 'Student Organization')
                                {
                                    $data = [
                                        'clearance_form_id' => $clearanceFormID, 
                                        'clearance_field_id' =>$field['id'], 
                                        'clearance_officer_id' =>$officer->clearance_officer_id,
                                    ];

                                    $entry_data_found = TRUE;
                                }
                            }                            

                            if($entry_data_found)
                            {
                                $data['clearance_field_status'] = "0";
                                $clearanceEntry_model->insert($data);
                            }
                        } // End of for loop for officers
                    }
                }                   
            }            

            // End of Creating Clearance Entries =======================================

            //Create Notification
            $sender_id = 0;
            $Subject = "Graduation Clearance Application";
            $Message = "Your Graduation Clearance Application has been Approved. You may now monitor your Graduation Clearance Status.";
            \ScisSystem::CreateNotification($sender_id,$Subject,$Message,$formData->student_id);

            //Send Email
            // $sender_email = [
            //     'email' => 'scis.puptaguig@gmail.com',
            //     'name' => 'PUPT Student Clearance Information System'
            // ];
            // \ScisSystem::sendEmail($formData->student_id,$Subject,$Message,$sender_email)

            // Log Activity
            $log = new \App\Models\ActivityLogsModel();
            $log_data = [
                'user_id' => $session->get('user_id'),
                'logged_activity' => 'Graduation Application of '.$formData->student_name." has been Approved",
            ];

            if($log->insert($log_data) == false)
            {
                echo 'error';
            }

            $session->setFlashdata('success_messages',[$formData->student_name."'s Graduation Clearance Application Successfuly Approved"]);

            return redirect()->back()->withInput();
        }
        else
        {
            return redirect()->to('/GraduationClearance/Applications');
        }
    }

    public function Decline($id = "")
    {
        $session = session();
        if(!empty($id))
        {
            $gradForm_model = new \App\Models\GraduationClearancesModel();
            $formData = $gradForm_model->getForm($id);

            $reason = $this->request->getPOST('reason');

            //echo $formData->student_name." has been Declined";

            //Modifiy graduation application status from pending to rejected
            date_default_timezone_set('Asia/Manila');
            $modify_stamp = date('Y-m-d H:i:s', time());

            $data = [
                'note' => $reason,
                'status' => 2,
                'modified' => $modify_stamp,
            ];

            $gradForm_model->update($id,$data);

            //inform student and or COs = notification and email
            //Create Notification
            $sender_id = 0;
            $Subject = "Graduation Clearance Application";
            $Message = "Your Graduation Clearance Application has been Rejected. <br> Reason : <b>".$reason."</b>";
            \ScisSystem::CreateNotification($sender_id,$Subject,$Message,$formData->student_id);

            //Send Email
            // $sender_email = [
            //     'email' => 'scis.puptaguig@gmail.com',
            //     'name' => 'PUPT Student Clearance Information System'
            // ];
            // \ScisSystem::sendEmail($formData->student_id,$Subject,$Message,$sender_email)

            // Log Activity
            $log = new \App\Models\ActivityLogsModel();
            $log_data = [
                'user_id' => $session->get('user_id'),
                'logged_activity' => 'Graduation Application of '.$formData->student_name." has been Declined",
            ];

            if($log->insert($log_data) == false)
            {
                echo 'error';
            }

            $session->setFlashdata('success_messages',[$formData->student_name."'s Graduation Clearance Application Successfuly Declined"]);

            return redirect()->back()->withInput();
        }
        else
        {
            return redirect()->to('/GraduationClearance/Applications');
        }
    }

	public function Apply()
    {
    	$permission_model = new \App\Models\PermissionsModel();

        $session = session();

        $data = [
            'page_title' => 'Graduation Clearance | PUPT SCIS',
            'Name'          => $session->get('user_name'),
            'profilePic'            => $session->get('user_pic'),
            'clearanceData'         => $session->get('clearance_data'),
            'activeClearance' => $session->get('ongoingClearance'),
            'user_title'    => $session->get('title'),
            'user_notifications' => $session->get('user_notifications'),
            'user_fields' => $session->get('user_co_fields'),
			    'user_gradFields' => $session->get('user_co_gradFields'),
			    'user_subject_resp' => $session->get('user_subject_handled'),   
        ];

        $uID = $session->get('user_id');
        
        if($permission_model->hasPermission($uID,['add_graduation_clearances']))
        {
        	\ScisSystem::refreshData1();

        	$student_model = new \App\Models\StudentsModel();
        	$scYear_model = new \App\Models\ScYearsModel();
            $subject_model = new \App\Models\SubjectsModel();
            $db = \Config\Database::connect();
            $scis_model = new \App\Models\ScisModel($db);
            $role_model = new \App\Models\RolesModel();

            $studID = $session->get('student_id');
        	$studentData = $student_model->find($studID);

            $majors = $scis_model->getMajorsList($studentData['course_id']);

            $subjects = $subject_model->where('deleted',0)->findAll();

        	$sc_years = $scYear_model->where('deleted',0)->findAll();

            $profRole_condition = [
                'role' => 'Professor',
                'deleted' => 0,
            ];
            $professorRole = $role_model->where($profRole_condition)->first();

            $prof_list = $scis_model->getRoleUsers($professorRole['id']);

        	$data['majors'] = $majors;
            $data['scYears'] = $sc_years;
            $data['subjects'] = $subjects;
            $data['professors'] = $prof_list;

            return view('student/graduation_clearance_application', $data);
        }
        else
        {
        	return view('site/no_permission', $data);
        }
    }

    public function submitApplication()
    {
    	$session = session();
       
        $cForm_model = new \App\Models\ClearanceFormsModel();
        $gradForm_model = new \App\Models\GraduationClearancesModel();
        $respectiveProfessors_model = new \App\Models\RespectiveProfessorsModel();

        $cFormData = [
        	'student_id' => $session->get('student_id'),
        	'clearance_period_data_id' => 0,
        	'clearance_type_id' => 2,
        	'clearance_status' => 0
        ];
        
        if($cForm_model->insert($cFormData) == false)
        {
        	$errors = $cForm_model->errors();
			$session->setFlashdata('err_messages',$errors);
            echo "cFormFail";
        }
        else
        {
            $certificate_of_candidacy = $this->request->getFile('coc');
            $fileName = "";         

            if($certificate_of_candidacy)
            {
                if ($certificate_of_candidacy->isValid() && ! $certificate_of_candidacy->hasMoved())
                {
                    $fileName = $certificate_of_candidacy->getRandomName();
                }
            }

     		$gradFormData = [
        		'form_id' => $cForm_model->getInsertID(),
        		'address' => $this->request->getPOST("completeAddress"),
        		'gender' => $this->request->getPOST("gender"),
        		'major_id' => ($this->request->getPOST("major")) ? $this->request->getPOST("major") : 0,
        		'admitted_scyear' => $this->request->getPOST("schoolYearAdmitted"),
                'graduation_school_year_id' => $this->request->getPOST("schoolYearGraduation"),
        		'admitted_sem' => $this->request->getPOST("semAdmitted"),
        		'date_of_birth' => $this->request->getPOST("dateOfBirth"),
        		'elementary' => $this->request->getPOST("elem"),
        		'elementary_graduated_year' => $this->request->getPOST("elemYearGrad"),
        		'highschool' => $this->request->getPOST("hs"),
        		'highschool_graduated_year' => $this->request->getPOST("hsYearGrad"),
        		'status' => 0,
        	];

            $subjectsExist = FALSE;
            $rP_count = 0;
            while($this->request->getPOST("subject_".$rP_count) != NULL) 
            {
                if($this->request->getPOST("subject_".$rP_count) != "Select Subject" || $this->request->getPOST("subject_".$rP_count) != "")
                {
                    $subjectsExist = TRUE;
                }
                $rP_count++;
            }

            if(!empty($fileName) && $subjectsExist)
            {
                $gradFormData['certificate_of_candidacy'] = $fileName;

                if($gradForm_model->insert($gradFormData) == false)
                {
                    $errors = $gradForm_model->errors();
                    $session->setFlashdata('err_messages',$errors);
                    foreach($errors as $err)
                    {
                        echo $err."<br>";
                    }
                    $cForm_model->delete($cForm_model->getInsertID());

                    echo "gFormFail";
                }
                else
                {
                    $certificate_of_candidacy->move('uploads/certificate_of_candicacy', $fileName);

                    $createdGradFormID = $gradForm_model->getInsertID();

                    $rP_count = 0;
                    while($this->request->getPOST("subject_".$rP_count) != NULL) 
                    {
                        if($this->request->getPOST("subject_".$rP_count) != "Select Subject")
                        {
                            echo "<br>";
                            echo $subject = $this->request->getPOST("subject_".$rP_count);
                            echo "<br>";
                            echo $professor = $this->request->getPOST("prof_".$rP_count);
                            echo "<br>";
                            echo $days = $this->request->getPOST("days_".$rP_count);
                            echo "<br>";
                            echo $time = $this->request->getPOST("time_".$rP_count);

                            $respectiveProfessorData = [
                                "graduation_clearance_id" => $createdGradFormID,
                                "professor_id" => $professor,
                                "subject_id" => $subject,
                                "days" => $days,
                                "time" => $time,
                            ];

                            if($respectiveProfessors_model->insert($respectiveProfessorData) == false)
                            {
                                $errors = $respectiveProfessors_model->errors();
                                $session->setFlashdata('err_messages',$errors);
                                foreach($errors as $err)
                                {
                                    echo $err."<br>";
                                }
                                $cForm_model->delete($cForm_model->getInsertID());
                                $gradForm_model->delete($createdGradFormID);
                                $session->remove('success_messages');
                                echo "profFail";
                                break;                                
                            }
                            else
                            {                                
                                $session->setFlashdata('success_messages',['Successfuly Submitted Graduation Application']);
                            }
                        }
                        else
                        {
                            // echo "Skipped! -- Invalid Input";
                        }
                        $rP_count++;
                    }

                    //Log Activity
                    $log_user = $session->get('user_id');
                    $log_message = 'Applied For Graduation Application';

                    \ScisSystem::logActivity($log_user,$log_message);
                }
            }
            else
            {
                echo "no COC or subjects";
                $cForm_model->delete($cForm_model->getInsertID());

                echo (empty($fileName)) ? "no file" : "" ;  
                echo (empty($subjects)) ? "no subject" : "" ;
            }
      	}


        	
        return redirect()->to('/GraduationClearance')->withInput();


        // echo "Student Name : ===<br>";
        //     echo "Student Number : ===<br>";
        //     echo "Course-Year : ===<br>";
        //     echo "Student Type : ===<br>";
        //     echo "Contact Number : ===<br>";
        //     echo "Address : ".$this->request->getPOST("completeAddress")."<br>";
        //     echo "Date of Birth : ".$this->request->getPOST("dateOfBirth")."<br>";
        //     echo "Gender : ==== <br>";
        //     echo "Major : ".($this->request->getPOST("major")) ? $this->request->getPOST("major") : "None"."<br>";
        //     echo "Admitted in PUP : ".$this->request->getPOST("schoolYearAdmitted")." | ";
        //     echo ($this->request->getPOST("semAdmitted") == "1") ? '1st Sem' : '2nd Sem';
        //     echo "<br>";
        //     echo "Elementary School : ".$this->request->getPOST("elem")." | Year Graduated : ".$this->request->getPOST("elemYearGrad")."<br>";
        //     echo "High School : ".$this->request->getPOST("hs")." | Year Graduated : ".$this->request->getPOST("hsYearGrad")."<br>";

        //     $subjects = $this->request->getPOST("subjects");
        //     $professors = $this->request->getPOST("prof");
        //     $days = $this->request->getPOST("days");
        //     $time = $this->request->getPOST("time");

        //     echo "<br>+++++++++++++++++<br>";
        //     echo "No. - Prof - Subject Detail - Days - Time<br>";
        //     // $subject_count = 1;
        //     // $sub_count = 0;
        //     // foreach($subjects as $sub)
        //     // {
        //     //     echo $subject_count++." - ";
        //     //     echo $professors[$sub_count]." - ";
        //     //     echo $subjects[$sub_count]." - ";
        //     //     echo $days[$sub_count]." - ";
        //     //     echo $time[$sub_count]." - ";
        //     //     echo "<br>";
        //     // }

        //     $rP_count = 0;
        //     while($this->request->getPOST("subject_".$rP_count) != NULL) 
        //     {
        //         echo $rP_count." - ";
        //         if($this->request->getPOST("subject_".$rP_count) != "Select Subject")
        //         {
        //             $subject = $this->request->getPOST("subject_".$rP_count);
        //             $professor = $this->request->getPOST("prof_".$rP_count);
        //             $days = $this->request->getPOST("days_".$rP_count);
        //             $time = $this->request->getPOST("time_".$rP_count);

        //             echo $professor." - ";
        //             echo $subject." - ";
        //             echo $days." - ";
        //             echo $time." - ";
        //         }
        //         else
        //         {
        //             echo "Skipped! -- Invalid Input";
        //         }
        //         echo "<br>";
        //         $rP_count++;
        //     }
    }

    
}