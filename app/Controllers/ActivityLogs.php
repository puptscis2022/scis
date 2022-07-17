<?php

namespace App\Controllers;

class ActivityLogs extends BaseController
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
			'page_title' => 'Clearance Fields | PUPT SCIS',
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
		
		if($permission_model->hasPermission($uID,['view_activity_logs']))
		{
			$logs_model = new \App\Models\ActivityLogsModel();
			$activityLogs = $logs_model->where('deleted',0)->orderBy('id','DESC')->findAll();

			$db = \Config\Database::connect();
			$scis_model = new \App\Models\ScisModel($db);
			$users_list = $scis_model->getUsersList();

			$logs = array();
			$array_count = 0;
			foreach($activityLogs as $activity)
			{
				foreach($users_list as $user)
				{
					if($activity['user_id'] == $user->id)
					{
						$logs[$array_count++] = [
							'name' => $user->name,
							'activity' => $activity['logged_activity'],
							'time_stamp' => $activity['created'],
						];
					}
				}
			}

			$data['logs'] = $logs;
			
			return view('admin/activity_logs', $data);
		}
		else
		{
			return view('site/no_permission', $data);
		}
	}
}