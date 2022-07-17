<?php

namespace App\Controllers;

class PreRequisites extends BaseController
{
	public function __construct(){
		helper('ScisSystem');
	}

    public function list($field_pos = "")
    {
    	$id = explode("-",$field_pos);

    	$field_id = $id[0];
    	$pos_id = $id[1];

    	\ScisSystem::refreshData1();

		$permission_model = new \App\Models\PermissionsModel();

		$session = session();

		$data = [
			'page_title' => 'Clearance Field PreRequisites | PUPT SCIS',
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
		
		if($permission_model->hasPermission($uID,['view_clearance_field_prerequisites']))
		{
    		if(!empty($id))
    		{
    			$db = \Config\Database::connect();
				$scis_model = new \App\Models\ScisModel($db);
	    		$preReq_model = new \App\Models\ClearanceFieldPrerequisitesModel();
	    		$cField_model = new \App\Models\ClearanceFieldsModel();
	    		$pos_model = new \App\Models\PositionsModel();

	    		$fieldList = $scis_model->getPositionsList();

	    		$fieldData = $cField_model->find($field_id);
	    		$fieldName = $fieldData['field_name'];

	    		$posData = $pos_model->find($pos_id);
	    		$posName = $posData['position_name'];

	   			$preReq_condition = [
	   				'deleted' => 0,
	  				'clearance_field_id' => $pos_id,
	   			];
	    		$Requisites = $preReq_model->where($preReq_condition)->findAll();

	    		$FieldReq = array();
		   		$Req_count = 0;
	    		foreach($Requisites as $req)
	    		{
	    			$positionData = $pos_model->find($req['requisite_clearance_field_id']);
					$fieldData = $cField_model->find($positionData['clearance_field_id']);

	    			$FieldReq[$Req_count++] = [
	    				'id' 	=> $req['id'],
	    				'field_name' => $fieldData['field_name'],
	    				'pos_name' => $positionData['position_name'],
	    			];
	    		}

		        $data['FieldName'] = $fieldName;
		        $data['PosName'] = $posName;
		        $data['Requisites'] = $FieldReq;
		        $data['Fields'] = $fieldList;
		        $data['currentFieldID'] = $field_id;
		        $data['currentPosID'] = $pos_id;

		        $data['AddPrerequisites'] = ($permission_model->hasPermission($uID,'add_clearance_field_prerequisites')) ? TRUE : FALSE ;
				$data['RemovePrerequisites'] = ($permission_model->hasPermission($uID,'delete_clearance_field_prerequisites')) ? TRUE : FALSE ;

	        	return view("prerequisite",$data);
	        }
	        else
	        {
	        	return view("site/no_permission",$data);
	        }
    	}
    	else
    	{
    		return view("site/no_permission",$data);
    	}
    }

    public function Add()
    {
    	$session = session();
    	
    	$preReq_model = new \App\Models\ClearanceFieldPrerequisitesModel();
    	$cField_model = new \App\Models\ClearanceFieldsModel();
    	$position_model = new \App\Models\PositionsModel();

	    $fieldID = $this->request->getPOST('id');
	    $fields = $this->request->getPOST('cField');

	    foreach($fields as $f)
	    {
	    	$preReq_data = [
	    		'clearance_field_id' => $fieldID,
	    		'requisite_clearance_field_id' => $f,
	    		'deleted' => 0,
	    	];

	    	if(!$preReq_model->where($preReq_data)->first())
	    	{
	    		if($preReq_model->insert($preReq_data) == false)
	    		{
	    			$errors = $preReq_model->errors();
	    			$session->setFlashData('err_messages',$errors);

	    			foreach($errors as $err)
	    			{
	    				echo $err."<br>";
	    			}
	    		}
	    		else
	    		{
	    			$positionDetail = $position_model->find($fieldID);
	    			$cField_data = $cField_model->find($positionDetail['clearance_field_id']);

	    			$positionReqDetail = $position_model->find($f);
	    			$preReq_data = $cField_model->find($positionReqDetail['clearance_field_id']);
	    			$session->setFlashData('success_messages',['Adding Prequisite Successful']);

	    			//Log Activity
					$log_user = $session->get('user_id');
					$log_message = 'Added a Pre-requisite for '.$positionDetail['position_name'].' ['.$cField_data['field_name'].'] : '.$positionReqDetail['position_name']."[".$preReq_data['field_name']."]";

					\ScisSystem::logActivity($log_user,$log_message);
	    		}
	    	}
	    	else
	    	{
	    		$session->setFlashData('err_messages',['Pre Requisite Already Exist']);
	    	}
	    }	    	

	    return redirect()->back()->withInput();
    }

    public function Remove($id)
    {
    	$session = session();
    	
    	$preReq_model = new \App\Models\ClearanceFieldPrerequisitesModel();
    	$cField_model = new \App\Models\ClearanceFieldsModel();
    	$position_model = new \App\Models\PositionsModel();
    		
	    date_default_timezone_set('Asia/Manila');
		$delete_stamp = date('Y-m-d H:i:s', time());

	    $preReq_data = [
	    	'deleted' => 1,
	    	'deleted_date' => $delete_stamp,
	    ];

	    if($preReq_model->update($id,$preReq_data) == false)
	    {
	    	$errors = $preReq_model->errors();
	    	$session->setFlashData('err_messages',$errors);

	    	foreach($errors as $err)
	    	{
	    		echo $err."<br>";
	    	}
	    }
	    else
	    {
	    	$removedPreReq_data = $preReq_model->find($id);

	    	$positionDetail = $position_model->find($removedPreReq_data['clearance_field_id']);
	    	$cField_data = $cField_model->find($positionDetail['clearance_field_id']);

	    	$positionReqDetail = $position_model->find($removedPreReq_data['requisite_clearance_field_id']);
	    	$preReq_data = $cField_model->find($positionReqDetail['clearance_field_id']);

	    	$session->setFlashData('success_messages',['Successfully Removed PreRequisite']);

	    	//Log Activity
			$log_user = $session->get('user_id');
			$log_message = 'Removed a Pre-requisite for '.$positionDetail['position_name'].' ['.$cField_data['field_name'].'] : '.$positionReqDetail['position_name']."[".$preReq_data['field_name']."]";

			\ScisSystem::logActivity($log_user,$log_message);
	    }

	    return redirect()->back()->withInput();
    }

}