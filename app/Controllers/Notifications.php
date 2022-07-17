<?php

namespace App\Controllers;

class Notifications extends BaseController
{
	public function __construct(){
		helper('ScisSystem');
	}

	//Notifications
	public function index()
	{
		$session = session();
		if($session->get('logged_in'))
		{
			\ScisSystem::refreshData1();			

			$Notification_list = \ScisSystem::getUserNotifications();

			$data = [
			    'page_title' 	=> 'Notifications | PUPT SCIS',
			    'user_role'		=> $session->get('user_role'),
			    'activeClearance' => $session->get('ongoingClearance'),
			    'user_notifications' => $session->get('user_notifications'),	    
			    'Name'			=> $session->get('user_name'),
			    'profilePic'			=> $session->get('user_pic'),
			    'Notifications'	=> $Notification_list,
			    'user_fields' => $session->get('user_co_fields'),
			    'user_gradFields' => $session->get('user_co_gradFields'),
			    'user_subject_resp' => $session->get('user_subject_handled'),
			    'clearanceData' => $session->get('clearance_data'),
			];

			if($session->get('admin_access'))
			{
				$data['user_title'] = "Administrator";				
			    $data['user_fields'] = $session->get('user_co_fields');
			    $data['clearanceData'] = $session->get('clearance_data');
			    
			    if( $session->get("superAdmin_access"))
				{
				    $data['user_title']	= 'Super Administrator';
				}
			}
			else if($session->get('co_access'))
			{
				$data['user_title'] = "Clearance Officer";							
			    $data['user_fields'] = $session->get('user_co_fields');
			}
			else if($session->get('registrar_access'))
			{
				$data['user_title'] = "Registrar";							
			    $data['user_fields'] = $session->get('user_co_fields');			    
			    $data['clearanceData'] = $session->get('clearance_data');
			}
			else if($session->get('student_access'))
			{
				$data['user_title'] = $session->get('user_student_number');
				$data['clearance_periods'] = $session->get('clearance_periods');
			}

			return view('notification', $data);
		}
		else
		{
			return redirect()->back()->to('/');
		}
	}
}