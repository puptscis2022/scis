<?php 

	class ScisSystem 
	{
		function __construct(){
			helper('ScisSystem');
		}
		//Sending Email | Must be converted to email messaging code
		public static function sendEmail($Recipient,$Subject,$Message,$Sender = []){
			$email = \Config\Services::email();

			if(!empty($Sender))
			{
				$email->setFrom($Sender['email'], $Sender['name']);
			}
			else
			{
				$email->setFrom('noreply@scis.puptaguigcs.net', 'PUPT SCIS');
			}
			
			$email->setTo($Recipient);
			//$email->setBCC('them@their-example.com'); //For Multiple emails that hides an individiual from others

			$email->setSubject($Subject);

			$MSG = '
			        <table style="font-family:Lato,sans-serif;" width="620" align="center" border="0" cellspacing="0" cellpadding="0">
                                    <tr style="background-color: #800000;">
                                        <td align="left" style="padding: 20px; display: flex;align-items: center; justify-content: center;">
                                          <img src="https://www.pngkey.com/png/full/52-528919_the-pup-logo-polytechnic-university-of-the-philippines.png" alt="" width="70" height="70">
                                          <span style="margin-left: 20px; margin-top: auto; margin-bottom:auto;"> 
                                            <h5 style="font-family: Trajan Pro, serif; font-size: 0.8rem; color: white; margin:0;font-weight: 300;">POLYTECHNIC UNIVERSITY OF THE PHILIPPINES - TAGUIG BRANCH</h5>
                                            <h2 style="margin: 0; font-family: Roboto, sans-serif; color: white;font-size: 1.4rem;">Student Clearance Information System</h2>
                                          </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center">
                                            <h1 style="color: #800000; padding-top:20px;">'.$Subject.'</h1>
                                        </td>
                                    </tr>
                                    
                                    <tr style="padding: 10px; background-color: #fff;font-family:Lato,sans-serif;">
                                        <td align="center" style="padding: 20px;">
                                            '.$Message.'
                                        </td>
                                    </tr>
                                            
                                    
                                    <tr style="background-color: #800000;">
                                        <td  align="center" style="padding: 30px; background-color: #800000; color: #fff;">
                                          <small style="text-align: center;font-family:Lato,sans-serif;"> © 2021 PUPT SCIS</small>
                                        </td>
                                    </tr>
                                </table>
			
			       ';
			$email->setMessage($MSG);

			if($email->send())
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}

		//Refresh Page Data
		public static function refreshData1()
		{
			$session = session();

			$db = \Config\Database::connect();
			$scis_model = new \App\Models\ScisModel($db);
			$student_model = new \App\Models\StudentsModel();
			$cFields_model = new \App\Models\ClearanceFieldsModel();
			$permission_model = new \App\Models\PermissionsModel();

			$user_id = $session->get('user_id');

			$coPosition_model = new \App\Models\ClearanceOfficerPositionsModel();
			$userPosition_condition = [
				'clearance_officer_id' => $user_id,
				'deleted' => 0,
			];
			$user_positions = array();

			if(($session->get('admin_access') || $session->get('registrar_access') ) && !$session->get('ClearanceOfficer_access') )
			{
				//$copositionlist = $scis_model->getPositionsList("Student Organization");
				$copositionlist = $scis_model->getPositionsList();
				$position_count = 0;
				foreach($copositionlist as $pos)
				{	
					if($pos->field != "Director's Office" || $session->get('Director_access'))
					{
						$user_positions[$position_count++]['position_id'] = $pos->id;
					}
				}
			}
			else
			{
				$user_positions = $coPosition_model->select('position_id')->where($userPosition_condition)->findAll();
			}

			$position_model = new \App\Models\PositionsModel();
			$positions = $position_model->where('deleted',0)->findAll();

			//Get Clearance Officer's Clearance Fields
			$clearanceField_model = new \App\Models\ClearanceFieldsModel();
			$user_co_fields = array(); //for Semestral Clearance Field Handled
			$user_co_fields2 = array();	//for Graduation Clearance Field Handled
			$array_count = 0;
			$gradArray_count = 0;

			$co_org = FALSE;
			foreach($user_positions as $row)
			{
				foreach($positions as $row2)
				{
					if($row['position_id'] == $row2['id'])
					{
						$clearanceFieldID = $row2['clearance_field_id'];

						$clearanceField = $clearanceField_model->find($clearanceFieldID);

						if($clearanceField['clearance_type_id'] == 1 || $clearanceField['clearance_type_id'] == 3)
						{
							$user_co_fields[$array_count++] = [
								"position_id" =>  $row2['id'],
								"positions_name" => $row2['position_name'],
								"field_id" => $clearanceFieldID,
								"field_name" => $clearanceField['field_name'],
							];
						}

						if($clearanceField['clearance_type_id'] == 2 || $clearanceField['clearance_type_id'] == 3)
						{
							$user_co_fields2[$gradArray_count++] = [
								"position_id" =>  $row2['id'],
								"positions_name" => $row2['position_name'],
								"field_id" => $clearanceFieldID,
								"field_name" => $clearanceField['field_name'],
							];
						}

                        
						if($clearanceField['field_name'] == "Student Organization" && session()->get('ClearanceOfficer_access'))
						{
							$coOrg_model = new \App\Models\StudentOrganizationOfficersModel();
							$coOrg_data = $coOrg_model->where('clearance_officer_id',$session->get('user_id'))->first();
							$co_org = $coOrg_data['student_organization_id'];
						}
					}
				}
			}
			$subject_handled = FALSE;
			if($session->get('Professor_access'))
			{
				$rProfessor_model = new \App\Models\RespectiveProfessorsModel();
				$EntryList = $rProfessor_model->getStudentEntryList($user_id);

				$subject_handled = array();
				$subject_count = 0;
				$counter = 1;
				foreach($EntryList as $EList)
				{
					$newDiv = TRUE;
					foreach($subject_handled as $ExistingDiv)
					{
						if($EList->sub_id == $ExistingDiv['sub_id'])
						{
							$newDiv = FALSE;
						}
					}

					if($newDiv)
					{
						$subject_handled[$subject_count++] = [
							"sub_id" => $EList->sub_id,
							"sub_name" => $EList->subject,
						];
					}
				}
			}
			

			//GET Notifications
			$notifications = \ScisSystem::getUserNotifications();

			$num_of_notif = 0;
			foreach($notifications as $notif)
			{
				if($notif['read_status'] == 0)
					$num_of_notif++;
			}

			$user_notifications = [
				'total_notif' => $num_of_notif,
				'notifications' => $notifications,
			];

			//Update Name
			$user_model = new \App\Models\UsersModel();
			$user_data = $user_model->find($session->get('user_id'));
			$name = $user_data['first_name']." ".$user_data['middle_name']." ".$user_data['last_name']." ".$user_data['suffix_name'];
			$pfp = ($user_data['profile_picture']) ? "uploads/profiles/".$user_data['profile_picture'] : "assets/img/default-profile.png";

			//Update Clearance Period Activeness
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
					$currentPeriod = $currentPeriod['id'];
				}
			}
			else
			{
				$currentPeriod = 0;
			}

			$user_title = "Title";

			$studentID = FALSE;
			$stud_periods = array();
			$count = 0;
			if($session->get('Student_access'))
			{
				$studentInfo = $student_model->where('user_id',$user_id)->first();

				$user_title = $studentInfo['student_number'];
				$studentID = $studentInfo['id'];

				//Get Clearance Periods
				$clearance_periods = $scis_model->getStudentForms($studentID);				
				foreach($clearance_periods as $cperiod)
				{
					$title = ($cperiod->semester == 1) ? "1st Sem" : (($cperiod->semester == 2) ? "2nd Sem" : "Summer" );
					$title = $title." SY ".$cperiod->year;
					
					$stud_periods[$count++] = [
						'id' => $cperiod->id,
						'title' => $title,
					];
				}
			} 
			else 
			{
				$user_roles = $session->get('roles');
				$user_title = $user_roles[0]['name'];
			}

			//Director's Office
			$field_condition = [
				'field_name' => "Director's Office",
				'deleted' => 0,
			];
			$directorOffice = $cFields_model->where($field_condition)->first();


			$data = [
				'user_notifications' => $user_notifications,
				'user_co_fields' => $user_co_fields,
				'user_co_gradFields' => $user_co_fields2,
				'user_name' => $name,
				'user_pic' => $pfp,
				'ongoingClearance' => $ongoingClearance,
				'clearance_data' => $currentPeriod,
				'title' => $user_title,
				'student_id' => $studentID,
				'DirectorOfficeID' => $directorOffice['id'],
				'clearance_periods' => $stud_periods,
				'user_org' => $co_org,
				'user_subject_handled' => $subject_handled,
			];
	
			$session->set($data);
			\ScisSystem::menuAccess($user_id);
		}

		//Set Allowed Menu Buttons
		public static function menuAccess($uID)
		{
			$session = session();
			$menuPermissions = array();
			$permission_model = new \App\Models\PermissionsModel();

			if ($permission_model->hasPermission($uID,['view_users','add_users','view_registrations','view_roles']))
			{
				$menuPermissions['UserManagement'] = TRUE;

				$menuPermissions['UsersList'] = ($permission_model->hasPermission($uID,['view_users','add_users'])) ? TRUE : FALSE;
				$menuPermissions['VerifyUsers'] = ($permission_model->hasPermission($uID,'view_registrations')) ? TRUE : FALSE;
				$menuPermissions['RolesAndPermissions'] = ($permission_model->hasPermission($uID,'view_roles') && FALSE) ? TRUE : FALSE;
			}
			else
			{
				$menuPermissions['UserManagement'] = FALSE;
			}

			if($permission_model->hasPermission($uID,['view_clearance_fields','view_positions','view_requirements','view_student_organizations','view_courses','view_clearance_field_prerequisites','view_subjects','view_majors']))
			{
				$menuPermissions['Maintenance'] = TRUE;

				$menuPermissions['ClearanceFields'] = ($permission_model->hasPermission($uID,'view_clearance_fields')) ? TRUE : FALSE;
				$menuPermissions['Positions'] = ($permission_model->hasPermission($uID,'view_positions')) ? TRUE : FALSE;
				$menuPermissions['Organizations'] = ($permission_model->hasPermission($uID,'view_student_organizations')) ? TRUE : FALSE;
				$menuPermissions['Courses'] = ($permission_model->hasPermission($uID,'view_courses')) ? TRUE : FALSE;				
				$menuPermissions['Requirements'] = ($permission_model->hasPermission($uID,'view_requirements')) ? TRUE : FALSE;
				$menuPermissions['Prerequisites'] = ($permission_model->hasPermission($uID,'view_clearance_field_prerequisites')) ? TRUE : FALSE;
				$menuPermissions['Subjects'] = ($permission_model->hasPermission($uID,'view_subjects')) ? TRUE : FALSE;
				$menuPermissions['Majors'] = ($permission_model->hasPermission($uID,'view_majors')) ? TRUE : FALSE;
			}
			else
			{
				$menuPermissions['Maintenance'] = FALSE;
			}

			$menuPermissions['StudentClearanceRecords'] = ($permission_model->hasPermission($uID,'view_clearance_forms') && $session->get('Student_access')) ? TRUE : FALSE;

			$menuPermissions['ActivityLogs'] = ($permission_model->hasPermission($uID,'view_activity_logs')) ? TRUE : FALSE;

			$menuPermissions['InitiateClearancePeriod'] = ($permission_model->hasPermission($uID,'add_clearance_periods')) ? TRUE : FALSE;

			$menuPermissions['BlackList'] = ($permission_model->hasPermission($uID,'view_black_list')) ? TRUE : FALSE;

			$menuPermissions['ManageDeficiencies'] = (($permission_model->hasPermission($uID,'view_clearance_entries')) && (!$session->get('Student_access') || ($session->get('ClearanceOfficer_access') || $session->get('Professor_access')))) ? TRUE : FALSE;

			$menuPermissions['Submissions'] = ($permission_model->hasPermission($uID,'view_submissions') && (!$session->get('Student_access') || $session->get('ClearanceOfficer_access'))) ? TRUE : FALSE;

			$menuPermissions['ClearanceFinalization'] = ($permission_model->hasPermission($uID,'view_clearance_forms') && (!$session->get("Student_access") || $session->get("ClearanceOfficer_access")) ) ? TRUE : FALSE;

			$menuPermissions['GraduationApplications'] = ($permission_model->hasPermission($uID,'view_graduation_clearances') || $session->get('Registrar_access')) ? TRUE : FALSE;
			$menuPermissions['GraduationSubjectDeficiencies'] = (session()->get('user_subject_handled')) ? TRUE : FALSE;
			$menuPermissions['GraduationClearance'] = ($menuPermissions['GraduationApplications'] || $menuPermissions['GraduationSubjectDeficiencies'] || $menuPermissions['ManageDeficiencies'] || $menuPermissions['Submissions']) ? TRUE : FALSE;
			
			$menuPermissions['GenerateReports'] = ((!$session->get('Student_access') || $session->get('ClearanceOfficer_access')) && ($permission_model->hasPermission($uID,'view_clearance_forms') || $permission_model->hasPermission($uID,'view_clearance_entries'))) ? TRUE : FALSE;

			$menuPermissions['ClearanceHistory'] = ($permission_model->hasPermission($uID,'view_clearance_periods') && $permission_model->hasPermission($uID,'view_clearance_forms')) ? TRUE : FALSE;

			session()->set('menu',$menuPermissions);
		}

		//Log Activity
		public static function logActivity($user,$activity)
		{
			$log = new \App\Models\ActivityLogsModel();
			$log_data = [
				'user_id' => $user,
				'logged_activity' => $activity,
			];

			if($log->insert($log_data) == FALSE)
			{
				echo "ERROR Logging";
			}		
		}

		//Create Notification
		public static function CreateNotification($send_id, $Subject, $message, $receiver_id)
		{
			$notification_model = new \App\Models\NotificationsModel();

			$msg = $Subject." | ".$message;

			$data = [
				"recipient_user_id" =>$receiver_id, 
				"message" 			=>$msg, 
				"sender_user_id" 	=>$send_id,
			];

			$notification_model->insert($data);
		}

		//get User Notification
		public static function getUserNotifications()
		{
			$session = session();

			$db = \Config\Database::connect();
				$scis_model = new \App\Models\ScisModel($db);

				$Notifications = $scis_model->getUserNotifications($session->get('user_id'));

				$users_list = $scis_model->getUsersList();

				$Notification_list = array();
				$array_count = 0;
				foreach($Notifications as $notif)
				{
					$mess = explode("|" ,$notif->message);
					$subject = $mess[0];
					$message = $mess[1];

					date_default_timezone_set('Asia/Manila');
					$current_time = date('Y-m-d H:i:s', time());

					$now = strtotime($current_time);
					$notif_created = strtotime($notif->created);

					$time_diff = $now - $notif_created;
					$days = abs(floor($time_diff/86400));
					$hours = abs(floor(($time_diff-($days * 86400))/3600));
					$minutes = abs(floor(($time_diff-($days * 86400)-($hours * 3600))/60));
					$seconds = abs(floor(($time_diff-($days * 86400)-($hours * 3600))%60));
					
					$ago = "";

					if($days != 0)
					{
						$ago = ($days > 1) ? $days." Days ago" : $days." Day ago";
					}
					else if($hours != 0)
					{
						$ago = ($hours > 1) ? $hours." Hours ago" : $hours." Hour ago";
					}
					else if($minutes != 0)
					{
						$ago = ($minutes > 1) ? $minutes." Minutes ago" : $minutes." Minute ago";
					}
					else if($seconds != 0)
					{
						$ago = ($seconds > 1) ? $seconds." Seconds ago" : $seconds." Second ago";
					}

					foreach($users_list as $user)
					{
						if($notif->sender_user_id == $user->id)
						{
						    $sender_role = $scis_model->getUserRoles($user->id); 
						    
							$Notification_list[$array_count++] = [
								'id' => $notif->id,
								'sender_name' => $user->name,
								'sender_role' => $sender_role[0]->name,
								'subject' => $subject,
								'message' => $message,
								'ago' => $ago,
								'created' => $notif->created,
								'read_status' => $notif->read_status,
							];
						}
					}

					if($notif->sender_user_id == 0)
					{
						$Notification_list[$array_count++] = [
							'id' => $notif->id,
							'sender_name' => "SCIS",
							'sender_role' => "System",
							'subject' => $subject,
							'message' => $message,
							'ago' => $ago,
							'created' => $notif->created,
							'read_status' => $notif->read_status,
						];
						$isSenderUser = true;
					}
				}

			return $Notification_list;
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
?>