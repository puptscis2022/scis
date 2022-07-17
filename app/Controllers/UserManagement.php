<?php

namespace App\Controllers;
use App\Libraries\Hash;
use App\Libraries\Reports;

use TCPDF;

class UserManagement extends BaseController
{
	public function __construct(){
		helper('ScisSystem');
	}

	public function index()
	{
		return redirect()->to('UserManagement/UsersList');
	}

	public function UsersList()
	{
		\ScisSystem::refreshData1();

		$permission_model = new \App\Models\PermissionsModel();

		$session = session();

		$data = [
			'page_title' => 'Users List | PUPT SCIS',
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
		
		if($permission_model->hasPermission($uID,['view_users','add_users']))
		{
			$db = \Config\Database::connect();
			$scis_model = new \App\Models\ScisModel($db);
			$users = $scis_model->getUsersList();
			$roles_model = new \App\Models\RolesModel();

			$user_permissions = array();
			$users_list = array();
			$user_count = 0;

			foreach($users as $u)
			{
				$user_roles = $scis_model->getUserRoles($u->id);

				$users_list[$user_count] = $u;

				$uRole = array();
				$role_count = 0;

				foreach($user_roles as $role)
				{
					$uRole[$role_count++] = $role->name;
				}

				$users_list[$user_count]->roles = $uRole;
				$user_count++;
			}

			$data['AddUsers'] = ($permission_model->hasPermission($uID,'add_users')) ? TRUE : FALSE ;
			$data['EditUsers'] = ($permission_model->hasPermission($uID,'edit_users')) ? TRUE : FALSE ;
			$data['DeleteUsers'] = ($permission_model->hasPermission($uID,'delete_users')) ? TRUE : FALSE ;


			$data['users'] = $users_list;
            $data['roles'] = $roles_model->where('deleted',0)->findAll();

			return view('admin/users_list', $data);
		}
		else
		{
			return view('site/no_permission',$data);
		}
	}

	public function AddUser()
	{
		\ScisSystem::refreshData1();

		$permission_model = new \App\Models\PermissionsModel();

		$session = session();

		$data = [
			'page_title' => 'Add User | PUPT SCIS',
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

		if($permission_model->hasPermission($uID,'add_users'))
		{
			$role_model = new \App\Models\RolesModel();
			$roles = $role_model->where('deleted',0)->findAll();

			$course_model = new \App\Models\CoursesModel();
			$courses = $course_model->where('deleted',0)->findAll();

			$studType_model = new \App\Models\StudentTypesModel();
			$studentTypes = $studType_model->where('deleted',0)->findAll();

			$data['roles'] = $roles;
			$data['courses'] = $courses;
			$data['studentTypes'] = $studentTypes;
		
			return view('admin/add_user', $data);
		}
		else
		{
			return view('site/no_permission',$data);
		}
	}

	public function NewUserSave()
	{
		$session = session();

		$user_model = new \App\Models\UsersModel();
		$student_model = new \App\Models\StudentsModel();
		$userRoles_model = new \App\Models\UserRolesModel();

		$password = Hash::encrypt_pass($this->request->getPOST('pass'));
		$new_user_roles = [$this->request->getPOST('userRole')];

		$uName = $this->request->getPOST('userName');

		$new_user_data = [
			'username' => $uName, 
			'password' => $password,
			'last_name' => $this->request->getPOST('lastName'),
			'first_name' => $this->request->getPOST('firstName'),
			'middle_name' => $this->request->getPOST('middleName')." ",
			'suffix_name' => $this->request->getPOST('suffixName')." ",
			'email' => $this->request->getPOST('email'),
			'contact_no' => $this->request->getPOST('contact'),
		];

		$user_model->skipValidation(false);

		if($user_model->insert($new_user_data) == false)
		{
			$errors = $user_model->errors();
			$session->setFlashdata('err_messages',$errors);

			return redirect()->to("/UserManagement/AddUser")->withInput();							
		}
		else
		{
			$new_user_id =  $user_model->getInsertID();

			foreach($new_user_roles as $role)
			{
				$new_user_roles_data = [
					'user_id' => $new_user_id,
					'role_id' => $role,
				];

				if($userRoles_model->insert($new_user_roles_data) == false)
				{
					$errors = $user_model->errors();
					$session->setFlashdata('err_messages',$errors);

					return redirect()->to("/UserManagement/AddUser")->withInput();	
				}
			}

			if($new_user_roles[0] == 3)
			{
				$errors = array();
				$array_count = 0;
				$invalid = false;					

				$new_user_studNo = $this->request->getPOST('studentNo');
					
				$new_student_data = [
					'user_id' => $new_user_id,
					'student_number' => $new_user_studNo,
					'year_level' => $this->request->getPOST('year'),
					'course_id' => $this->request->getPOST('course'),
					'student_type_id' => $this->request->getPOST('studentType'),
				];
						
				$student_model->skipValidation(false);

				if($student_model->insert($new_student_data) == false)
				{
					$errors = $student_model->errors();
					$session->setFlashdata('err_messages',$errors);

					$user_model->delete($new_user_id);

					return redirect()->to("/UserManagement/AddUser")->withInput();							
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
							$new_student_id = $student_model->getInsertID();
							\ScisSystem::createClearanceForm($new_student_id);
						}
					}
				}
			} 
		}

		$addedName = $this->request->getPOST('firstName')." ".$this->request->getPOST('middleName')." ".$this->request->getPOST('lastName')." ".$this->request->getPOST('suffixName');

		//Log Activity
		$log_user = $session->get('user_id');
		$log_message = 'Added a user : '.$addedName;

		\ScisSystem::logActivity($log_user,$log_message);

		$session->setFlashdata('success_messages',[$addedName." Was Successfully Added"]);

		return redirect()->to('/UserManagement/UsersList');
	}

	public function uploadUsers()
	{

		//Models
		$users_model = new \App\Models\UsersModel();
		$students_model = new \App\Models\StudentsModel();
		$course_model = new \App\Models\CoursesModel();
		$studentType_model = new \App\Models\StudentTypesModel();
		$userRoles_model = new \App\Models\UserRolesModel();

		//for reports of success and failed inputs on uploaded file
		// Name of users, errors
		$successMessages = array();
		$errorMessages = array();

		$role = $this->request->getPost('role'); //selected role for uploaded users

		$input = $this->validate([
			'file' => 'uploaded[file]|max_size[file,1024]|ext_in[file,csv],'
			]);
		
		if($newUsersInfo = $this->request->getFile('newUsers')) {
			if ($newUsersInfo->isValid() && ! $newUsersInfo->hasMoved()) {
				
				// Get random file name
				$newName = $newUsersInfo->getRandomName();
				
				// Store file
				$newUsersInfo->move('uploads/UsersUploaded', $newName);
				
				// Reading file
				$fileUploaded = fopen("uploads/UsersUploaded/".$newName,"r");
				$i = 0; //csv rows count
				$j = 0; //inputted/valid rows count
				$numberOfFields = ($role == 3 ) ? 10 : 6; // Total number of fields
				$importUserData_arr = array();
				$importStudentData_arr = array();
				
				while (($filedata = fgetcsv($fileUploaded,1000,",")) !== FALSE) {
					
					$noIssue = TRUE;
					$CompleteData = TRUE;

					$thisUserData = [
						'input_no' => $i - 3,
						'name' => $filedata[0]." ".$filedata[1]." ".$filedata[2],
						'messages' => array(),
					];

					//Skip head Rows
					if($i > 1)
					{ 
						for($dataCount = 0; $dataCount < $numberOfFields; $dataCount++)
						{
							if(empty($filedata[$dataCount]) && ($dataCount != 2 && $dataCount != 3))
							{
								$CompleteData = FALSE;
								$noIssue = FALSE;
								
								array_push($thisUserData['messages'],"Missing / Incomplete User Data");
							}							
						}

						if($CompleteData)
						{
							if($role == 3) //if the uploaded file is students
							{
								//get student data
								
								//Student Number Similarity
								$studentNumber_condition = [
									'student_number' => $filedata[6],
								];
								if($students_model->where($studentNumber_condition)->first())
								{
									$noIssue = FALSE;
									array_push($thisUserData['messages'],"Student Number Already Exist");
								}
								else
								{
									$importUserData_arr[$j]['username'] = $importStudentData_arr[$j]['student_number'] = $filedata[6];
								}


								$importStudentData_arr[$j]['year_level'] = $filedata[7];
								
								//Check Student Course Code and change data to its to id
								$course_condition = [
									'abbreviation' => $filedata[8],
									'deleted' => 0,
								];
								if($course_data = $course_model->where($course_condition)->first())
								{
									$importStudentData_arr[$j]['course_id'] = $course_data['id'];
								}
								else
								{
									$noIssue = FALSE;
									array_push($thisUserData['messages'],"Invalid Course");
								}

								//Check Student Type and Change its id
								$studentType_condition = [
									'type' => $filedata[9],
									'deleted' => 0,
								];
								if($studentType_data = $studentType_model->where($studentType_condition)->first())
								{
									$importStudentData_arr[$j]['student_type_id'] = $studentType_data['id'];
								}
								else
								{
									$noIssue = FALSE;
									array_push($thisUserData['messages'],"Invalid Student type");
								}
							}
							else
							{
								$notUniqueUsername = TRUE;
								$newUsername = "";

								while($notUniqueUsername)
								{
									//Create Random Username for non student users
									$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
								    $charLength = strlen($characters);
								    $newUsername = '';
								    for ($charCount = 0; $charCount < 6; $charCount++) {
								        $newUsername .= $characters[rand(0, $charLength - 1)];
								    }
									$newUsername = "user_".$newUsername;

									//check if username already exist in the db
									$username_condition = [
										'username' => $newUsername,
									];
									if(!$users_model->where($username_condition)->first())
									{
										$notUniqueUsername = FALSE;
									}
								}							

								$importUserData_arr[$j]['username'] = $newUsername;
							}

							$importUserData_arr[$j]['input_no'] = $i - 3;

							//Generate Random Password
								$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
								$charLength = strlen($characters);
								$newPassword = '';
								for ($charCount = 0; $charCount < 8; $charCount++) {
								    $newPassword .= $characters[rand(0, $charLength - 1)];
								}
								
							//Hash Password and store in DB
								$importUserData_arr[$j]['password'] = Hash::encrypt_pass($newPassword);
								$importUserData_arr[$j]['unhashed_password'] = $newPassword;

							//get user data
							$importUserData_arr[$j]['last_name'] = $filedata[0];
							$importUserData_arr[$j]['first_name'] = $filedata[1];
							$importUserData_arr[$j]['middle_name'] = $filedata[2];
							$importUserData_arr[$j]['suffix'] = $filedata[3];

							//check email Uniqueness
							if(!$users_model->where('email',$filedata[4])->first())
							{
								$importUserData_arr[$j]['email'] = $filedata[4];
							}
							else
							{
								$noIssue = FALSE;
								array_push($thisUserData['messages'],"Email Alread Exist");
							}

							//check contact no uniqueness
							if(!$users_model->where('contact_no',$filedata[5])->first())
							{
								$importUserData_arr[$j]['contact_no'] = $filedata[5];
							}
							else
							{
								$noIssue = FALSE;
								array_push($thisUserData['messages'],"Contact No Already Exist");
							}
							
						}

						if($noIssue)
						{
							$j++;
						}
						else
						{
							array_pop($importUserData_arr);
							if($role == 3)
							{
								array_pop($importStudentData_arr);
							}
							array_push($errorMessages,$thisUserData);
						}
					}
					$i++;					
				}
				fclose($fileUploaded);
				// Insert data
				
				$j = 0;
				foreach($importUserData_arr as $row)
				{
					$successUpload = TRUE;

					$thisUserData = [
						'input_no' => $row['input_no'],
						'name' => $row['first_name']." ".$row['middle_name']." ".$row['last_name'],
						'messages' => array(),
					];

					if($users_model->insert($row) == false)
					{
						$errors = $users_model->errors();
						
						$thisUserData['messages'] = $errors;
						array_push($errorMessages,$thisUserData);	

						$successUpload = FALSE;				
					}
					else
					{
						$new_user_id =  $users_model->getInsertID();

						$new_user_role_data = [
							'user_id' => $new_user_id,
							'role_id' => $role,
						];

						if($userRoles_model->insert($new_user_role_data) == false)
						{
							$errors = $userRoles_model->errors();

							$thisUserData = [
								'input_no' => $row['input_no'],
								'name' => $row['first_name']." ".$row['middle_name']." ".$row['last_name'],
								'messages' => $errors,
							];
							array_push($errorMessages,$thisUserData);
						}

						if($role == 3)
						{
							$errors = array();
							$invalid = false;					

							$newStudent_data = $importStudentData_arr[$j];
							$newStudent_data['user_id'] = $new_user_id;
																
							$students_model->skipValidation(false);

							if($students_model->insert($newStudent_data) == false)
							{
								$errors = $students_model->errors();
						
								$thisUserData['messages'] = $errors;
								array_push($errorMessages,$thisUserData);

								$users_model->delete($new_user_id);

								$successUpload = FALSE;			
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
										$new_student_id = $students_model->getInsertID();
										\ScisSystem::createClearanceForm($new_student_id);
									}
								}
							}
						} 
					}
					
					if($successUpload)
					{
						array_push($successMessages,$thisUserData);

						//email the uploaded user  their login credentials
						echo "<br>|||||||||||||||||||||||||||||||||||||||<br>";
						echo "Good Day ".$row['first_name']." ".$row['middle_name']." ".$row['last_name'].", your SCIS account has been created. Please use the following credentials for using your account.";
						echo "<br> Username: ".$row['username'];
						echo "<br> Password: ".$row['unhashed_password']."<br>";

					}
					$j++;
				}

				$count = 1;
				echo "<br><br> ====================== <br>";
				echo "<br> ERRORS <br>";
				foreach($errorMessages as $err)
				{
					echo $count++." - ".$err['input_no']." - ".$err['name'];
					echo "<br> +++++++++++++++ <br>";
					foreach($err['messages'] as $mess)
					{
						echo $mess."<br>";
					}
					echo "+++++++++++++++ <br><br>";
				}

				$count = 1;
				echo "<br><br> ====================== <br>";
				echo "<br> Successfully Uploaded <br>";
				foreach($successMessages as $succ)
				{
					echo $count++." - ".$succ['input_no']." - ".$succ['name']."<br>";
				}

				// // Set Session
				// session()->setFlashdata('message', $count.' Record inserted successfully!');
				// session()->setFlashdata('alert-class', 'alert-success');

			}
			else{
				// // Set Session
				// session()->setFlashdata('message', 'File not imported.');
				// session()->setFlashdata('alert-class', 'alert-danger');
				// echo "error1";
			}
		}
		else{
			// // Set Session
			// session()->setFlashdata('message', 'File not imported.');
			// session()->setFlashdata('alert-class', 'alert-danger');
			// echo "error2";
		}
		

		// return redirect()->route('/'); 
	}

	//Manage Registrations ===========================
	public function verifyUsers()
	{
		\ScisSystem::refreshData1();

		$permission_model = new \App\Models\PermissionsModel();

		$session = session();

		$data = [
			'page_title' => 'Verify Registrations | PUPT SCIS',
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

		if($permission_model->hasPermission($uID,'view_registrations'))
		{
			$db = \Config\Database::connect();
			$scis_model = new \App\Models\ScisModel($db);

			$registrations = $scis_model->getRegistrationRequests();

			$data['req_reg'] = $registrations;

			$data['AddUsers'] = ($permission_model->hasPermission($uID,'add_users')) ? TRUE : FALSE ;
			$data['DeleteRegistrations'] = ($permission_model->hasPermission($uID,'delete_registrations')) ? TRUE : FALSE ;
			
			return view('admin/verify_user',$data);
		}
		else
		{
			return view('site/no_permissions',$data);
		}
	}

	public function viewRegistration($id)
	{
		\ScisSystem::refreshData1();

		$permission_model = new \App\Models\PermissionsModel();

		$session = session();

		$data = [
			'page_title' => 'Verify Registration | PUPT SCIS',
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

		if($permission_model->hasPermission($uID,'view_registrations'))
		{			
			$db = \Config\Database::connect();
			$scis_model = new \App\Models\ScisModel($db);

			$registration = $scis_model->getRegistration($id);

			$data['reg'] = true;
			$data['role_id'] = $registration->role_id;
			$data['role'] = $registration->role;
			$data['username'] = $registration->username;
			$data['lastName'] = $registration->last_name;
			$data['firstName'] = $registration->first_name;
			$data['middleName'] = $registration->middle_name;
			$data['suffixName'] = $registration->suffix_name;
			$data['email'] = $registration->email;
			$data['contactNo'] = $registration->contact_no;
			$data['course'] = $registration->course;
			$data['studentNumber'] = $registration->student_number;
			$data['type'] = $registration->type;
			
			$data['isStudent'] = ($registration->role == "Student") ? TRUE : FALSE;

			$year = $registration->year_level;
            $year_level;

            if($year == 0)
            {
                $year_level = "Graduate";
            }
            else if($year == 1)
            {
                $year_level = "1st Year";
            }
            else if($year == 2)
            {
                $year_level = "2nd Year";
            }
            else if($year == 3)
            {
                $year_level = "3rd Year";
            }
            else
            {
                $year_level = $year."th Year";
            }
			$data['year'] = $year_level;
			
			return view('admin/view_user',$data);
		}
		else
		{
			return view('site/no_permission',$data);
		}
	}

	public function approveRegistration($id = "")
	{
		$session = session();
		
		$reg_model = new \App\Models\RegistrationsModel();
		$users_model = new \App\Models\UsersModel();
		$students_model = new \App\Models\StudentsModel();
		$userRole_model = new \App\Models\UserRolesModel();

		$transferSuccess = false; //verify if transfer of data succeed

		//Get Registration Data
		$reg_data = $reg_model->find($id);

		//Copy Registration Data to users and students/clearance officers 
		$newUser_data = [
			'username' => $reg_data['username'],
			'password' => $reg_data['password'],
			'last_name' => $reg_data['last_name'],
			'first_name' => $reg_data['first_name'],
			'middle_name' => $reg_data['middle_name'],
			'suffix_name' => $reg_data['suffix_name'],
			'email' => $reg_data['email'],
			'contact_no' => $reg_data['contact_no'],
		];

		if($users_model->insert($newUser_data) == false)
		{
			$errors = $users_model->errors();
			$session->setFlashdata('err_messages',$errors);
		}
		else
		{
			$new_user_id =  $users_model->getInsertID();

			$transferSuccess = true;

			$new_user_roles = [$reg_data['role_id']];
			foreach($new_user_roles as $role)
			{
				$new_user_role_data = [
					'user_id' => $new_user_id,
					'role_id' => $role,
				];

				if($userRole_model->insert($new_user_role_data))
				{
					$errors = $userRole_model->errors();
					$session->setFlashdata('err_messages',$errors);
				}
			}

			if($reg_data['role_id'] == 3)
			{
				$newUserInfo_data = [
					'user_id' => $new_user_id,
					'student_number' => $reg_data['student_number'],
					'course_id' => $reg_data['course_id'],
					'year_level' => $reg_data['year_level'],
					'student_type_id' => $reg_data['student_type_id'],
				];

				if($students_model->insert($newUserInfo_data) == false)
				{
					$errors = $student_model->errors();
					$session->setFlashdata('err_messages',$errors);

					$users_model->delete($new_user_id);	

					$transferSuccess = false;					
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
							$new_student_id = $students_model->getInsertID();
							\ScisSystem::createClearanceForm($new_student_id);
						}
					}

				}
			} 

			//Delete Registration Request
			if($transferSuccess)
			{
				date_default_timezone_set('Asia/Manila');
				$deleted_stamp = date('Y-m-d H:i:s', time());

				$regUpdate_data = [
					'status' => 1,
					'deleted' => 1,
					'deleted_date' => $deleted_stamp, 
				];

				$reg_model->update($id,$regUpdate_data);

                //Send Email
                $recipient = $reg_data['email'];
                $subject = 'Account Registration Approved';
                $sender = [
                    'email' => 'scis.puptaguig@gmail.com',
                    'name' => 'PUPT Student Clearance Information System'
                ];
                $message = '
                                    <p style="text-align: center;">
                                        Congratulations, '.$reg_data['first_name'].' '.$reg_data['last_name'].'! Your account registration for Student Clearance Information System (SCIS) has been approved. You may now log into your account.
                                    </p> 
                                  
                          
                                   <a href="'.base_url().'" target="_blank" 
                                   style="box-sizing: border-box;display: inline-block;font-family:Lato,sans-serif;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;color: #800000; 
                                   background-color: #e4c062; border-radius: 1px;-webkit-border-radius: 1px; -moz-border-radius: 1px; width:auto; max-width:100%; 
                                   overflow-wrap: break-word; word-break: break-word; word-wrap:break-word; mso-border-alt: none;margin:10px;">
                                       <span style="display:block;padding:15px 40px;line-height:120%;">
                                            <span style="font-size: 18px; line-height: 21.6px;">Login</span>
                                       </span>
                                    </a>
                                    <br><br>
                                      ';

                if(\ScisSystem::sendEmail($recipient,$subject,$message,$sender)){
                    $session->setFlashdata('success_messages',['Sucessfully Approved Request']);
                }
                $session->setFlashdata('success_messages',['Sucessfully Approved Request']);

                //Log Activity
				$log_user = $session->get('user_id');
				$log_message = 'Approve a Registration : '.$reg_data['first_name'].' '.$reg_data['last_name'];

				\ScisSystem::logActivity($log_user,$log_message);

				$session->setFlashdata('success_messages',[$reg_data['first_name']." ".$reg_data['last_name']."'s Registration Successfully Approved"]);
			}
		}
	
		return redirect()->to('/UserManagement/verifyUsers')->withInput();
	}

	public function rejectRegistration($id = "")
	{
		$session = session();
		
		if(!empty($id))
		{
			$reg_model = new \App\Models\RegistrationsModel();

			date_default_timezone_set('Asia/Manila');
			$deleted_stamp = date('Y-m-d H:i:s', time());

			$reg_data = [
				'status' => 2,
				'deleted' => 1,
				'deleted_date' => $deleted_stamp, 
			];

			$reg_model->update($id,$reg_data);

			// echo "rejected";
			$session->setFlashdata('success_messages',['Sucessfully Rejected Request']);

			$reg_data = $reg_model->find($id);

			//Log Activity
			$log_user = $session->get('user_id');
			$log_message = 'Rejected a Registration : '.$reg_data['first_name'].' '.$reg_data['last_name'];

			\ScisSystem::logActivity($log_user,$log_message);
		}
			
		return redirect()->to('/UserManagement/verifyUsers')->withInput();
	}

	public function ViewUser($id='')
	{
		\ScisSystem::refreshData1();

		$permission_model = new \App\Models\PermissionsModel();

		$session = session();

		$data = [
			'page_title' => 'View User | PUPT SCIS',
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

		if($permission_model->hasPermission($uID,'view_users'))
		{
			if($id != '')
			{			
				$user_model = new \App\Models\UsersModel();
				$db = \Config\Database::connect();
				$scis_model = new \App\Models\ScisModel($db);

				$user_data = $user_model->where('id',$id)->first();
				$user_roles = $scis_model->getUserRoles($id);


				$role_id = $user_roles[0]->role_id;
				$user_role = $user_roles[0]->name;

				
				$data['role_id'] = $role_id;
				$data['role'] = $user_role;
				$data['username'] = $user_data['username'];
				$data['lastName'] = $user_data['last_name'];
				$data['firstName'] =$user_data['first_name'];
				$data['middleName'] =$user_data['middle_name'];
				$data['suffixName'] =$user_data['suffix_name'];
				$data['email'] =$user_data['email'];
				$data['contactNo'] =$user_data['contact_no'];
				
				$data['reg'] = FALSE;
				$data['isStudent'] = FALSE;

				if ($role_id == 3) 
				{
					$student_model = new \App\Models\StudentsModel();
					$user_data2 = $student_model->where('user_id', $id)->first();

					$course_model = new \App\Models\CoursesModel();
					$course = $course_model->where('id', $user_data2['course_id'])->first();

					$studentType_model = new \App\Models\StudentTypesModel();
					$studentType = $studentType_model->where('id', $user_data2['student_type_id'])->first();

					$year = $user_data2['year_level'];
					$year_levels;
					if($year == 0)
					{
						$year_level = "Graduate";
					}
					else if($year == 1)
					{
						$year_level = "1st Year";
					}
					else if($year == 2)
					{
						$year_level = "2nd Year";
					}
					else if($year == 3)
					{
						$year_level = "3rd Year";
					}
					else
					{
						$year_level = $year."th Year";
					}

					$data['studentNumber'] = $user_data2['student_number'];
					$data['year'] = $year_level;
					$data['course'] = $course['course_name'];
					$data['type'] = $studentType['type'];
				    $data['isStudent'] = TRUE;
				}
			
				return view('admin/view_user', $data);
			}
			else{
				return redirect()->to('UsersList');
			}
		}
		else
		{
			return view('site/no_permission', $data);
		}
	}

	public function DeleteUser($id='')
	{
		$session = session();
		
		if($id != '')
		{			
			$user_model = new \App\Models\UsersModel();
			$db = \Config\Database::connect();
			$scis_model = new \App\Models\ScisModel($db);

			$user_roles = $scis_model->getUserRoles($id);
			$role = $user_roles[0]->role_id;

			date_default_timezone_set('Asia/Manila');
			$delete_stamp = date('Y-m-d H:i:s', time());

			$user_data = $user_model->find($id);

			$data = [
				'deleted' => 1,
				'deleted_date' => $delete_stamp,
			];
			
			if($user_model->update($id,$data) != FALSE)
			{

				$deletedName = $user_data['first_name']." ".$user_data['middle_name']." ".$user_data['last_name']." ".$user_data['suffix_name'];

				if($role == 3)
				{
					$student_model = new \App\Models\StudentsModel();
					$student_info = $student_model->where('user_id',$id)->first();
					$student_id = $student_info['id'];
				
					$student_model->update($student_id,$data);
				}

				//Log Activity
				$log_user = $session->get('user_id');
				$log_message = 'Deleted a user : '.$deletedName;

				\ScisSystem::logActivity($log_user,$log_message);
				$session->setFlashData('success_messages',[$deletedName.' successfully deleted']);
			}

		}
		
		return redirect()->to('/UserManagement/UsersList');
	}

	public function EditUser($id='')
	{
		\ScisSystem::refreshData1();

		$permission_model = new \App\Models\PermissionsModel();

		$session = session();

		$data = [
			'page_title' => 'Edit User | PUPT SCIS',
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

		if($permission_model->hasPermission($uID,'edit_users'))
		{
			if($id != '')
			{			
				$user_model = new \App\Models\UsersModel();
				$db = \Config\Database::connect();
				$scis_model = new \App\Models\ScisModel($db);
				$role_model = new \App\Models\RolesModel();

				$user_roles = $scis_model->getUserRoles($id);
				$role = $user_roles[0];

				$user_data = $user_model->where('id',$id)->first();

				$roles = $role_model->where('deleted', 0)->findAll();

				$year_levels = [ 
					['id' => 1, 'level' => '1st Year'], 
					['id' => 2, 'level' => '2nd Year'], 
					['id' => 3, 'level' => '3rd Year'], 
					['id' => 4, 'level' => '4th Year'], 
					['id' => 5, 'level' => '5th Year'], 
					['id' => 0, 'level' => 'Graduate'], 
				];

				$course_model = new \App\Models\CoursesModel();
				$courses = $course_model->where('deleted', 0)->findAll();

				$studentType_model = new \App\Models\StudentTypesModel();
				$studentTypes = $studentType_model->where('deleted', 0)->findAll();

				
				$data['id'] = $id;
				$data['role_id'] = $role->role_id;
				$data['role'] = $role->name;
				$data['roles'] = $roles;
				$data['username'] = $user_data['username'];
				$data['lastName'] = $user_data['last_name'];
				$data['firstName'] =$user_data['first_name'];
				$data['middleName'] =$user_data['middle_name'];
				$data['suffixName'] =$user_data['suffix_name'];
				$data['email'] =$user_data['email'];
				$data['contactNo'] =$user_data['contact_no'];
				$data['year_levels'] = $year_levels;
				$data['courses'] = $courses;
				$data['types'] = $studentTypes;
				
				$data['studentNumber'] = "";
				$data['year_id'] = "";
				$data['level'] = "";
				$data['course_id'] = "";
				$data['course'] = "";
				$data['type_id'] = "";
				$data['type'] = "";
				$data['isStudent'] = FALSE;

				if ($role->role_id == 3) 
				{
					$student_model = new \App\Models\StudentsModel();
					$user_data2 = $student_model->where('user_id', $id)->first();

					$course = $course_model->where('id', $user_data2['course_id'])->first();

					$studentType = $studentType_model->where('id', $user_data2['student_type_id'])->first();

					$year = $user_data2['year_level'];
					$year_level = "";
					if($year == 0)
					{
						$year_level = "Graduate";
					}
					else if($year == 1)
					{
						$year_level = "1st Year";
					}
					else if($year == 2)
					{
						$year_level = "2nd Year";
					}
					else if($year == 3)
					{
						$year_level = "3rd Year";
					}
					else
					{
						$year_level = $year."th Year";
					}

					$data['studentNumber'] = $user_data2['student_number'];
					$data['year_id'] = $year;
					$data['level'] = $year_level;
					$data['course_id'] = $user_data2['course_id'];
					$data['course'] = $course['course_name'];
					$data['type_id'] = $user_data2['student_type_id'];
					$data['type'] = $studentType['type'];
					$data['isStudent'] = TRUE;
				}
		
				return view('admin/edit_user', $data);
			}
			else
			{
				$session->set('err_messages',['Invalid User']);
				return redirect()->to('UsersList');
			}
		}
		else
		{
			return view('site/no_permission', $data);
		}
	}

	public function EditUserSave($id = '')
	{
		$session = session();
		
		if($id != '')
		{
			$valid = true;
			$errors = array();

			$user_model = new \App\Models\UsersModel();
			$student_model = new \App\Models\StudentsModel();

			$uName = $this->request->getPOST('userName');
			$new_user_email = $this->request->getPOST('email');
			$new_user_studNo = $this->request->getPOST('studentNo');

			$edit_user_role = $this->request->getPOST('userRole');
				
			date_default_timezone_set('Asia/Manila');
			$modify_stamp = date('Y-m-d H:i:s', time());

			$new_data = [
				'username' => $uName, 
				//'password' => $password,
				'last_name' => $this->request->getPOST('lastName'),
				'first_name' => $this->request->getPOST('firstName'),
				'middle_name' => $this->request->getPOST('middleName')." ",
				'suffix_name' => $this->request->getPOST('suffixName')." ",
				'email' => $new_user_email,
				'contact_no' => $this->request->getPOST('contact'),
				'modified' => $modify_stamp,
			];

			$validationRules  = [	
	    		"username"	=> [
	    						'rules' => "required|alpha_numeric_punct|is_unique[users.username,id,".$id."]",
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
	    						'rules' => "required|valid_email|is_unique[users.email,id,".$id."]",
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

	    	$user_model->setValidationRules($validationRules);

			$user_model->skipValidation(false);

			if($user_model->update($id,$new_data) == false)
			{
				$errors = $user_model->errors();
				$session->setFlashdata('err_messages',$errors);	

				return redirect()->back()->withInput();			
			}
			else
			{
				if($edit_user_role == 3)
				{
					$student_data = [
						'student_number' => $new_user_studNo,
						'year_level' => $this->request->getPOST('year'),
						'course_id' => $this->request->getPOST('course'),
						'student_type_id' => $this->request->getPOST('studentType'),
						'modified' => $modify_stamp,
					];

					$student = $student_model->where('user_id',$id)->first();
					$student_id = $student['id'];

					$validationRules2 = [
						"student_number"	=> [
							'rules' => "required|alpha_dash|exact_length[15]|is_unique[students.student_number,id,".$student_id."]",
							'errors' => [
								'required' 	=> 'Student Number is Required',
    							'alpha_dash'=> 'Student Number is must only contain alphanumeric characters and dashes',
    							'exact_length[15]' => 'Invalid Student Number',
    							'is_unique' => 'Student Number already Exist',
    						],
						],
    					"year_level"		=> [
    						'rules' => 'required|integer',
    						'errors' => [
    							'required' => 'Year Level is Required',
    							'integer'  => 'Invalid Year Level',
   							],
    					],
    					"course_id"			=> [
    						'rules' => 'required|integer',
    						'errors' => [
    							'required' => 'Course is Required',
    							'integer'  => 'Invalid Course',
    						],
    					],
    					"student_type_id"	=> [
    						'rules' => 'required|integer',
    						'errors' => [
    							'required' => 'Student Type is Required',
    							'integer'  => 'Invalid Student Type',
    						],
    					],
					];

					$student_model->setValidationRules($validationRules2);

					$student_model->skipValidation(false);

					if($student_id)
					{
						echo '1';
						if($student_model->update($student_id,$student_data) == false)
						{
							echo '1.1';
							$errors = $student_model->errors();
							$session->setFlashdata('err_messages',$errors);

							return redirect()->back()->withInput();							
						}
					}
					else
					{
						echo '2';

						if($student_model->insert($student_data) == false)
						{
							echo '2.1';
							$errors = $student_model->errors();
							$session->setFlashdata('err_messages',$errors);

							return redirect()->back()->withInput();							
						}
					}
				}

				$editedName = $this->request->getPOST('firstName')." ".$this->request->getPOST('middleName')." ".$this->request->getPOST('lastName')." ".$this->request->getPOST('suffixName');

				//Log Activity
				$log_user = $session->get('user_id');
				$log_message = 'Edited a user : '.$editedName;

				\ScisSystem::logActivity($log_user,$log_message);

				$session->setFlashdata('success_messages',[$editedName."'s Information Successfully Edited"]);

				return redirect()->to('/UserManagement/ViewUser/'.$id)->withInput();
			}
		}
		else
		{
			return redirect()->to('/UserManagement/UsersList'.$id)->withInput();
		}
	}
}