<?php

namespace App\Controllers;
use App\Libraries\Hash;
use App\Libraries\Reports;

use TCPDF;

class ProfileManagement extends BaseController
{
	public function __construct(){
		helper('ScisSystem');
	}

	//User Profile Management ================================
	public function index()
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
				'page_title' 	=> 'Profile | PUPT SCIS',
			    'user_role'		=> $session->get('user_role'),
			    'activeClearance' => $session->get('ongoingClearance'),
		    'clearanceData'			=> $session->get('clearance_data'),
			    'Name'			=> $session->get('user_name'),
			    'profilePic'			=> $session->get('user_pic'),
			    'user_notifications' => $session->get('user_notifications'),
			    'user_fields' => $session->get('user_co_fields'),
			    'user_gradFields' => $session->get('user_co_gradFields'),
			    'user_subject_resp' => $session->get('user_subject_handled'),
			];

            $data['isStudent'] = FALSE;
			if ($session->get('Student_access'))
			{
				$student_model = new \App\Models\StudentsModel();
				$course_model = new \App\Models\CoursesModel();			
				$studType_model = new \App\Models\StudentTypesModel();

				$user_data2 = $student_model->where('user_id', $uID)->first();

				$course = $course_model->find($user_data2['course_id']);
				$type = $studType_model->find($user_data2['student_type_id']);

				$level = "";
				$year = $user_data2['year_level'];
				if($year == 0)
				{
					$level = "Graduate";
				}
				else if($year == 1)
				{
					$level = "1st Year";
				}
				else if($year == 2)
				{
					$level = "2nd Year";
				}
				else if($year == 3)
				{
					$level = "3rd Year";
				}
				else
				{
					$level = $year."th Year";
				}

				$user_data['courseName'] = $course['course_name'];
				$user_data['studTypeName'] = $type['type'];
				$user_data['level'] = $level;
				$user_data['student_number'] = $data['user_title']	= $user_data2['student_number'];
				$data['isStudent'] = TRUE;
			}

			$data['userData'] = $user_data;

			return view('view_profile', $data);
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function editProfilePage()
	{
		$session = session();
		if($session->get('logged_in') )
		{
			\ScisSystem::refreshData1();			


			$uID = $session->get('user_id');
			
			$user_model = new \App\Models\UsersModel();
			$course_model = new \App\Models\CoursesModel();
			$studType_model = new \App\Models\StudentTypesModel();

			$courses = $course_model->where("deleted", 0)->findAll();
			$studTypes = $studType_model->where("deleted", 0)->findAll();

			$user_data = $user_model->find($uID);

            $year_level = [
                ['id' => 1, 'level' => '1st Year'],
                ['id' => 2, 'level' => '2nd Year'],
                ['id' => 3, 'level' => '3rd Year'],
                ['id' => 4, 'level' => '4th Year'],
                ['id' => 5, 'level' => '5th Year'],
                ['id' => 0, 'level' => 'Graduate'],
            ];


			$data = [
				'page_title' 	=> 'Edit Profile | PUPT SCIS',
			    'user_role'		=> $session->get('user_role'),
			    'activeClearance' => $session->get('ongoingClearance'),
		    'clearanceData'			=> $session->get('clearance_data'),
			    'Name'			=> $session->get('user_name'),
			    'profilePic'			=> $session->get('user_pic'),
			    'user_notifications' => $session->get('user_notifications'),
			    'user_fields' => $session->get('user_co_fields'),
			    'user_gradFields' => $session->get('user_co_gradFields'),
			    'user_subject_resp' => $session->get('user_subject_handled'),
			    'userData' => $user_data,
			    'courses' => $courses,
			    'studTypes' => $studTypes,
                'year_level' => $year_level,

            ];

            $data['isStudent'] = FALSE;
			if($session->get('admin_access'))
			{				
			    $data['user_title']	= 'Administrator';
			   	$data['clearanceData'] = $session->get('clearance_data'); 

			   	if( $session->get("superAdmin_access"))
				{
				    $data['user_title']	= 'Super Administrator';
				}   
			}
			else if($session->get('co_access'))
			{
				$data['user_title']	= 'Clearance Officer';
			}
			else if($session->get('registrar_access'))
			{
				$data['user_title']	= 'Registrar';
			   	$data['clearanceData'] = $session->get('clearance_data'); 
			}
			else if($session->get('director_access'))
			{
				$data['user_title']	= 'Director';
			   	$data['clearanceData'] = $session->get('clearance_data'); 
			}		
			else if ($session->get('Student_access'))
			{
                $student_model = new \App\Models\StudentsModel();

                $user_data2 = $student_model->where('user_id', $uID)->first();

                $user_course = $course_model->find($user_data2['course_id']);
                $user_type = $studType_model->find($user_data2['student_type_id']);

				$level = "";
				$year = $user_data2['year_level'];
				if($year == 0)
				{
					$level = "Graduate";
				}
				else if($year == 1)
				{
					$level = "1st Year";
				}
				else if($year == 2)
				{
					$level = "2nd Year";
				}
				else if($year == 3)
				{
					$level = "3rd Year";
				}
				else
				{
					$level = $year."th Year";
				}

				$user_data['courseName'] = $user_course['course_name'];
                $user_data['courseId'] = $user_data2['course_id'];
                $user_data['studTypeName'] = $user_type['type'];
                $user_data['studTypeId'] = $user_type['id'];
                $user_data['level'] = $level;
                $user_data['yearId'] = $year;
				$user_data['student_number'] = $data['user_title'] = $user_data2['student_number'];
				$data['isStudent'] = TRUE;
			}

			$data['userData'] = $user_data;

			return view('edit_profile',$data);
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function editProfileSave()
	{
		$session = session();
		if($session->get('logged_in'))
		{
			$uID = $session->get('user_id');

			$user_model = new \App\Models\UsersModel();
			$student_model = new \App\Models\StudentsModel();

			$user_currentData = $user_model->find($uID);
			$student_currentData = $student_model->where("user_id",$uID)->first();

			date_default_timezone_set('Asia/Manila');
			$modify_stamp = date('Y-m-d H:i:s', time());

			$user_type = "";

			$middleName = (!empty($this->request->getPOST('MiddleName'))) ? $this->request->getPOST('MiddleName') : " ";
			$suffixName = (!empty($this->request->getPOST('Suffix'))) ? $this->request->getPOST('Suffix') : " ";

			$valid = TRUE;
			$fileUploaded = $this->request->getFile('profilePic');
			$fileName = "";			

			if($fileUploaded)
			{
				if ($fileUploaded->isValid() && ! $fileUploaded->hasMoved())
				{
					$fileName = $fileUploaded->getRandomName();
				}
			}

			$user_data = [
				'username' => $this->request->getPOST('userName'),
				'last_name' => $this->request->getPOST('LastName'),
				'first_name' => $this->request->getPOST('FirstName'),
				'middle_name' => $middleName,
				'suffix_name' => $suffixName,
				'email' => $this->request->getPOST('EmailAddress'),
				'contact_no' => $this->request->getPOST('ContactNumber'),
				'modified' => $modify_stamp,
			];

			$student_validationRules  = [	
				"student_number"	=> [
					'rules' => "required|alpha_dash|exact_length[15]",
					'errors' => [
						'required' 	=> 'Student Number is Required',
	    				'alpha_dash'=> 'Student Number is must only contain alphanumeric characters and dashes',
	    				'exact_length[15]' => 'Invalid Student Number',
					],
				],
				"year_level"	=> [
					'rules' => "required|integer",
					'errors' => [
						'required' => 'Year Level is Required',
	    				'integer'  => 'Invalid Year Level'
					],
				],
				"course_id"	=> [
					'rules' => "required|integer",
					'errors' => [
						'required' => 'Course is Required',
	    				'integer'  => 'Invalid Course'
					],
				],
				"student_type_id"	=> [
					'rules' => "required|integer",
					'errors' => [
						'required' => 'Student Type is Required',
	    				'integer'  => 'Invalid Student Type'
					],
				],			
			];
			$student_model->setValidationRules($student_validationRules);
			$student_model->skipValidation(false);

			if($user_currentData['last_name'] != $user_data['last_name'] 
				|| $user_currentData['first_name'] != $user_data['first_name'] 
				|| $user_currentData['middle_name'] != $user_data['middle_name'] 
				|| $user_currentData['suffix_name'] != $user_data['suffix_name'] 
				|| $user_currentData['email'] != $user_data['email'] 
				|| $user_currentData['contact_no'] != $user_data['contact_no'] 
				|| $user_currentData['username'] != $user_data['username'] || !empty($fileName))
			{
				$validationRules  = [	
		    		"username"	=> [
		    						'rules' => "required|alpha_numeric_punct|is_unique[users.username,id,".$uID."]",
		    						'errors' => [
		    							'required' 		=> 'Username is Required',
		    							'alpha_numeric_punct'	=> 'Username must only contain alphanumeric characters',
		    							'is_unique' 	=> 'Username Already Exist'
		    						],
		    					],
		    		"password"	=> [
		    						'rules' => 'required|min_length[6]',
		    						'errors' => [
		    							'required'		=> 'Password is Required',
		    							'min_length[6]'	=> 'Password must have a minimum of 6 characters'
		    						],
		    					],
		    		"last_name" 		=> [
		    						'rules' => 'required|alpha_space',
		    						'errors' => [
		    							'required'	=> "User's Last Name is Required",
		    							'alpha_space'		=> 'Last Name must only contain alphabet'
		    						],
		    					],
		    		"first_name"		=> [
		    						'rules' => 'required|alpha_space',
		    						'errors' => [
		    							'required'	=> "User's First Name is Required",
		    							'alpha_space'		=> 'First Name must only contain alphabet'
		    						],
		    					],
		    		"middle_name"		=> [
		    						'rules' => 'alpha_space',
		    						'errors' => [
		    							'alpha_space'	=> 'Middle Name must only contain alphabet', 
		    						],
		    					],
		    		"suffix_name"		=> [
		    							'rules' => 'alpha_numeric_space',
		    						'errors' => [
		    							'alpha_numeric_space'	=> 'Suffix Name must only contain alphabet', 
		    						],
		    					],
		    		"email"				=> [
		    						'rules' => "required|valid_email|is_unique[users.email,id,".$uID."]",
		    						'errors' => [
		    							'required'		=> "User's Email is Required",
		    							'valid_email'	=> 'Invalid Email',
		    							'is_unique' => 'Email Already Exist',
		    						],
		    					],
		    		"contact_no"		=> [
		    						'rules' => 'integer|exact_length[11]',
		    						'errors' => [
		    							'integer' => 'Contact Number must only contain numeric values',
		    							'exact_length[11]' =>'Invalid Contact Number',
		    						],
		    					],
		    	];

		    	if(!empty($fileName))
				{
					$user_data['profile_picture'] = $fileName;
				}								

		    	$user_model->setValidationRules($validationRules);

				$user_model->skipValidation(false);

				if($user_model->update($uID,$user_data) == false)
				{
					$errors = $user_model->errors();
					$session->setFlashdata('err_messages',$errors);

					return redirect()->back()->withInput();
				}					
				else
				{
					if(!empty($fileName))
					{
						$fileUploaded->move('uploads/profiles', $fileName);
					}

					if(!empty($student_currentData))
					{
						echo $student_id = $student_currentData['id'];
						$student_data = [
							"student_number" => $this->request->getPOST('StudentNumber'), 
							"year_level" => $this->request->getPOST('year'), 
							"course_id" => $this->request->getPOST('Course'), 
							"student_type_id" => $this->request->getPOST('StudentType'),
						];

						if($student_currentData['student_number'] != $student_data['student_number'] 
							|| $student_currentData['year_level'] != $student_data['year_level'] 
							|| $student_currentData['course_id'] != $student_data['course_id'] 
							|| $student_currentData['student_type_id'] != $student_data['student_type_id'])
						{
							if($student_model->update($student_id,$student_data) == false)
							{
								$errors = $student_model->errors();
								$session->setFlashdata('err_messages',$errors);

								return redirect()->back()->withInput();
							}					
						}
					}
					
					//Log Activity
					$log_user = $session->get('user_id');
					$log_message = "Edited their profile info";

					\ScisSystem::logActivity($log_user,$log_message);

					$session->setFlashdata('success_messages',['User Information Successfully Edited']);
				}
			}
			else
			{
				if(!empty($student_currentData))
				{
					$student_id = $student_currentData['id'];
					$student_data = [
						"student_number" => $this->request->getPOST('StudentNumber'), 
						"year_level" => $this->request->getPOST('year'), 
						"course_id" => $this->request->getPOST('Course'), 
						"student_type_id" => $this->request->getPOST('StudentType'),
					];

					if($student_currentData['student_number'] != $student_data['student_number'] 
						|| $student_currentData['year_level'] != $student_data['year_level'] 
						|| $student_currentData['course_id'] != $student_data['course_id'] 
						|| $student_currentData['student_type_id'] != $student_data['student_type_id'])
					{
						if($student_model->update($student_id,$student_data) == false)
						{
							$errors = $student_model->errors();
							$session->setFlashdata('err_messages',$errors);

							return redirect()->back()->withInput();
						}					
						else
						{
							//Log Activity
							$log_user = $session->get('user_id');
							$log_message = "Edited their profile info";

							\ScisSystem::logActivity($log_user,$log_message);

							$session->setFlashdata('success_messages',['User Information Successfully Edited']);
						}
					}
					else
					{
						$session->setFlashdata('err_messages',['Inputted data has no difference from old data']);
					}
				}
				else
				{
					$session->setFlashdata('err_messages',['Inputted data has no difference from old data']);
				}				
			}

			return redirect()->to("/ProfileManagement")->withInput();
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function changePassPage()
	{
		$session = session();
		if($session->get('logged_in'))
		{
			\ScisSystem::refreshData1();			

			$data = [
				'page_title' 	=> 'Profile | PUPT SCIS',
			    'user_role'		=> $session->get('user_role'),
			    'activeClearance' => $session->get('ongoingClearance'),
			    'Name'			=> $session->get('user_name'),
			    'profilePic'			=> $session->get('user_pic'),
			    'clearanceData'			=> $session->get('clearance_data'),
			    'user_notifications' => $session->get('user_notifications'),
			    'user_fields' => $session->get('user_co_fields'),
			];

			if($session->get('admin_access'))
			{				
			    $data['user_title']	= 'Administrator';
			   	$data['clearanceData'] = $session->get('clearance_data'); 
			    $data['user_fields'] = $session->get('user_co_fields');
			}
			else if($session->get('ClearanceOfficer_access'))
			{
				$data['user_title']	= 'Clearance Officer';
			    $data['user_fields'] = $session->get('user_co_fields');
			}
			else if($session->get('Registrar_access'))
			{
				$data['user_title']	= 'Registrar';
			   	$data['clearanceData'] = $session->get('clearance_data'); 
			}
			else if($session->get('Director_access'))
			{
				$data['user_title']	= 'Director';
			   	$data['clearanceData'] = $session->get('clearance_data'); 
			}	
			else if($session->get('Student_access'))
			{
				$data['user_title']	= $session->get('user_student_number');
			    $data["clearance_periods"] = $session->get('clearance_periods');
			}

			return view('change_password', $data);
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

	public function changePassSave()
	{
		$session = session();
		if($session->get('logged_in'))
		{
			$uID = $session->get('user_id');

			$user_model = new \App\Models\UsersModel();

			$user_currentLoginData = $user_model->find($uID);

			$old_password = $this->request->getPOST("oldPass");
			$new_password1 = $this->request->getPOST("newPass");
			$new_password2 = $this->request->getPOST("confirmNewPass");

			if($old_password == "" && $new_password1 == "" && $new_password2 == "")
			{
				$session->setFlashdata('err_messages',["All fields are required"]);
				return redirect()->back()->withInput();
			}
			else
			{
				$check_pass = Hash::check_pass($old_password, $user_currentLoginData['password']);

				if($check_pass)
				{
					if($new_password1 === $new_password2)
					{
						if($new_password1 === $old_password)
						{
							$session->setFlashdata('err_messages',['Inputted password has no difference from old password']);
							return redirect()->back()->withInput();
						}
						else
						{
							$newPassword = Hash::encrypt_pass($new_password1);

							date_default_timezone_set('Asia/Manila');
							$modified_stamp = date('Y-m-d H:i:s', time());

							$data = [
								'password' => $newPassword,
								'modified' => $modified_stamp
							];

							$user_model->update($uID, $data);

							//Log Activity
							$log_user = $session->get('user_id');
							$log_message = 'Change Password';

							\ScisSystem::logActivity($log_user,$log_message);

							$session->setFlashdata('success_messages',["Password Changed Successfully"]);

							if($session->get('SuperAdministrator_access'))
							{
								return redirect()->to("/UserManagement/UsersList")->withInput();
							}
							else
							{
								return redirect()->to("/test_dashboard")->withInput();
							}							
						}
					}
					else
					{
						$session->setFlashdata('err_messages',["New Passwords Doesn't Match"]);
						return redirect()->back()->withInput();
					}
				}
				else
				{
					$session->setFlashdata('err_messages',["Incorrect Old/Current Password"]);
					return redirect()->back()->withInput();
				}
			}
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}

}