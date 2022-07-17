<?php

namespace App\Controllers;
use App\Libraries\Hash;
use CodeIgniter\I18n\Time;

class Home extends BaseController
{
    public function __construct(){
        helper('ScisSystem');
    }

    public function index()
    {
        $testMode_status = FALSE;
        
        $session = session();
        if($session->get('logged_in'))
        {
            return redirect()->to("test_dashboard");
        }
        else if($testMode_status && !$session->get('developer_access'))
        {
            echo "This site is currently under Maintenance";
            echo "<br><br>If you are one of the Developers? Enter the password to gain access.<br><br>";
            echo "<form class='developer-login' method='POST' action=".base_url('home/getDeveloperAccess')." >
                            <div class='input-group mb-4'>
                                <span class='input-group-text'><i class='fas fa-lock' style='color: #616161;'></i></span>
                                <input type='password' class='form-control' id='password' placeholder='Password' aria-label='Password' name='password' autocomplete='off' aria-describedby='togglePassword' required>
                                <span class='input-group-text'><i class='far fa-eye' id='togglePassword' style='color: #616161;'></i></span>
                            </div>

                            <div class='row mb-4'>
                                <div class='col text-center'>
                                    <input class='btn btn-primary btn-block px-4 text-center' type='submit' name='login' value='Get Access'>
                                </div>
                            </div>
                        </form>";
            echo "<br>";
            if($session->get('denied'))
            {
                echo "<br>";
                echo "Access Denied";
            }
            
        }
        else
        {
            $data = [
                'page_title' => 'Login | PUPT SCIS',
            ];
            return view('site/login', $data);
        }

    }
    
    public function getDeveloperAccess()
    {
       $password = $this->request->getPOST('password');
        if($password)
        {
            if($password === "AyawKoNa")
            {
                session()->set('developer_access',TRUE);
                return redirect()->to(base_url('/'));
            }
            else
            {
                session()->setFlashdata('denied',TRUE);
                return redirect()->back();
            }
        }
        else
        {
            session()->setFlashdata('denied',TRUE);
            return redirect()->back();
        }
    }

    public function login()
    {
        $session = session();
        if($session->get('logged_in'))
        {
            return redirect()->to("test_dashboard");
        }
        else
        {
            $username = $this->request->getPOST('username');
            $password = $this->request->getPOST('password');

            //Models
            $user_model = new \App\Models\UsersModel();
            $userRoles_model = new \App\Models\UserRolesModel();
            $rolePermissions_model = new \App\Models\RolePermissionsModel();
            $userPermissions_model = new \App\Models\UserPermissionsModel();
            $Permissions_model = new \App\Models\PermissionsModel();
            $roles_model = new \App\Models\RolesModel();

            $user_condition = [
                'username' => $username,
                'deleted'	=> 0,
            ];
            $user_info = $user_model->where($user_condition)->first();

            if($user_info){
                $check_pass = Hash::check_pass($password, $user_info["password"]);
                // echo Hash::encrypt_pass($password);

                if(!$check_pass){
                    $session->setFlashdata('err_messages',["Incorrect Password"]);
                    return redirect()->back()->withInput();
                }else{

                    $uID = $user_info['id'];

                    $uRoles_condition = [
                        'user_id' => $uID,
                        'deleted' => 0,
                    ];

                    $roles = $userRoles_model->where($uRoles_condition)->findAll();

                    $user_roles = array();
                    $role_count = 0;
                    $user_permissions = array();
                    $permission_count = 0;

                    //Get Permissions of User
                    foreach($roles as $role)
                    {
                        //Get role info
                        $role_info = $roles_model->find($role['role_id']);
                        $user_roles[$role_count++] = [
                            'id' => $role['id'],
                            'name' => $role_info['role'],
                        ];

                        if($role_info['role'] == "Administrator")
                        {
                            $session->set('admin_access',TRUE);
                        }
                        else
                        {
                            $Access = str_replace(' ','',$role_info['role']);
                            $Access .= "_access";
                            $session->set($Access,TRUE);
                        }
                    }

                    $user_data = [
                        'user_id' => $uID,
                        'roles' => $user_roles,
                        'logged_in' => TRUE,
                    ];

                    $session->set($user_data);

                    $log = new \App\Models\ActivityLogsModel();
                    $log_data = [
                        'user_id' => $user_info['id'],
                        'logged_activity' => 'Logged In',
                    ];

                    if($log->insert($log_data) == false)
                    {
                        echo 'error';
                    }

                    return redirect()->to('/test_dashboard');
                }
            }else{

                $session->setFlashdata('err_messages',["Username Doesn't Exist"]);
                return redirect()->back()->withInput();
            }
        }
    }

    public function register()
    {
        $session = session();
        if($session->get('logged_in'))
        {
            return redirect()->to("test_dashboard");
        }
        else
        {
            $inputtedData = [
                'userName' => FALSE,
                'role' => FALSE,
                'lastName' => FALSE,
                'firstName' => FALSE,
                'middleName' => FALSE,
                'suffixName' => FALSE,
                'contactno' => FALSE,
                'email' => FALSE,
                'studentno' => FALSE,
                'course' => FALSE,
                'yearLevel' => FALSE,
                //'section' => FALSE,
                'studType' => FALSE,
            ];
            if(empty($session->get('inputtedData')))
            {
                 $session->setFlashdata('inputtedData', $inputtedData);
            }
        
            //Get Dropdown Items

            //Roles
            $roles_model = new \App\Models\RolesModel();
            $roles_list = $roles_model->where('deleted',0)->findAll();

            //Courses
            $course_model = new \App\Models\CoursesModel();
            $course_list = $course_model->where('deleted',0)->findAll();

            //Student Types
            $sTypes_model = new \App\Models\StudentTypesModel();
            $sTypes = $sTypes_model->where('deleted',0)->findAll();


            $data = [
                'page_title' => 'Register | PUPT SCIS',
                'roles' => $roles_list,
                'courses' => $course_list,
                'sTypes' =>  $sTypes,
            ];
            return view('site/register', $data);
        }
    }

    public function submitRegistration()
    {

        $session = session();
        $reg_model = new \App\Models\RegistrationsModel();
        $db = \Config\Database::connect();
        $scis_model = new \App\Models\ScisModel($db);
        $role_model = new \App\Models\RolesModel();

        //forReturning Inputted data if failed
        $inputtedData = [
            'userName' => $this->request->getPOST('userName'),
            'role' => $this->request->getPOST('role'),
            'lastName' => $this->request->getPOST('lastName'),
            'firstName' => $this->request->getPOST('firstName'),
            'middleName' => $this->request->getPOST('middleName'),
            'suffixName' => $this->request->getPOST('suffixName'),
            'contactno' => $this->request->getPOST('contactno'),
            'email' => $this->request->getPOST('email'),
            'studentno' => $this->request->getPOST('studentno'),
            'course' => $this->request->getPOST('course'),
            'yearLevel' => $this->request->getPOST('yearLevel'),
            //'section' => $this->request->getPOST('section'),
            'studType' => $this->request->getPOST('studType'),
        ];
        $session->setFlashdata('inputtedData', $inputtedData);

        $password = $this->request->getPOST('password');
        $conPass = $this->request->getPOST('confirmpass');
        $role = $this->request->getPOST('role');

        if (empty($middle_name = $this->request->getPOST('middleName'))) {
            $middle_name = " ";
        }
        if (empty($suffix_name = $this->request->getPOST('suffixName'))) {
            $suffix_name = '';
        }

        if ($password === $conPass) {

            $password = (!empty($password)) ? Hash::encrypt_pass($password) : "";

            $registerData = [
                'username' => $this->request->getPOST('userName'),
                'password' => $password,
                'role_id' => $role,
                'last_name' => $this->request->getPOST('lastName'),
                'first_name' => $this->request->getPOST('firstName'),
                'middle_name' => $middle_name,
                'suffix_name' => $suffix_name,
                'contact_no' => $this->request->getPOST('contactno'),
                'email' => $this->request->getPOST('email'),
                'student_number' => $this->request->getPOST('studentno'),
                'course_id' => $this->request->getPOST('course'),
                'year_level' => $this->request->getPOST('yearLevel'),
                //'section' => $this->request->getPOST('section'),
                'student_type_id' => $this->request->getPOST('studType'),
            ];

            if ($role == 3) {
                $validationRules = [
                    "username" => [
                        'rules' => 'required|alpha_numeric_punct|is_unique[users.username,id,{id}]',
                        'errors' => [
                            'required' => 'Username is Required',
                            'alpha_numeric' => 'Username must only contain alphanumeric characters',
                            'is_unique' => 'Username Already Exist'
                        ],
                    ],
                    "password" => [
                        'rules' => 'required|min_length[6]',
                        'errors' => [
                            'required' => 'Password is Required',
                            'min_length[6]' => 'Password must have a minimum of 6 characters'
                        ],
                    ],
                    "role_id" => [
                        'rules' => 'required|integer',
                        'errors' => [
                            'required' => "User's Role is Required",
                            'integer' => 'Invalid Role'
                        ],
                    ],
                    "last_name" => [
                        'rules' => 'required|alpha_space',
                        'errors' => [
                            'required' => "User's Last Name is Required",
                            'alpha_space' => 'Last Name must only contain alphabet'
                        ],
                    ],
                    "first_name" => [
                        'rules' => 'required|alpha_space',
                        'errors' => [
                            'required' => "User's First Name is Required",
                            'alpha_space' => 'First Name must only contain alphabet'
                        ],
                    ],
                    "middle_name" => [
                        'rules' => 'alpha_space',
                        'errors' => [
                            'alpha_space' => 'Middle Name must only contain alphabet'
                        ],
                    ],
                    "suffix_name" => [
                        'rules' => 'regex_match[/^[a-zA-Z.]*$/]',
                        'errors' => [
                            'regex_match[/^[a-zA-Z.]*$/]' => 'Suffix Name must only contain alphabet and period'
                        ],
                    ],
                    "email" => [
                        'rules' => 'required|valid_email|is_unique[users.email]',
                        'errors' => [
                            'required' => "User's Email is Required",
                            'valid_email' => 'Invalid Email',
                            'is_unique' => "Email Already Exist",
                        ],
                    ],
                    "contact_no" => [
                        'rules' => 'integer|exact_length[11]',
                        'errors' => [
                            'integer' => 'Contact Number must only contain numeric values',
                            'exact_length[11]' => 'Invalid Contact Number'
                        ],
                    ],
                    "student_number" => [
                        'rules' => 'required|alpha_dash|exact_length[15]|is_unique[students.student_number]',
                        'errors' => [
                            'required' => 'Student Number is Required',
                            'alpha_dash' => 'Student Number is must only contain alphanumeric characters and dashes',
                            'exact_length[15]' => 'Invalid Student Number',
                            'is_unique' => 'Student Number Already Exist'
                        ],
                    ],
                    "year_level" => [
                        'rules' => 'required|integer',
                        'errors' => [
                            'required' => 'Year Level is Required',
                            'integer' => 'Invalid Year Level'
                        ],
                    ],
                    "course_id" => [
                        'rules' => 'required|integer',
                        'errors' => [
                            'required' => 'Course is Required',
                            'integer' => 'Invalid Course'
                        ],
                    ],
                    "student_type_id" => [
                        'rules' => 'required|integer',
                        'errors' => [
                            'required' => 'Student Type is Required',
                            'integer' => 'Invalid Student Type'
                        ],
                    ],
                ];
                $reg_model->setValidationRules($validationRules);
            }

            if ($reg_model->insert($registerData) == false) {
                $errors = $reg_model->errors();
                $session->setFlashdata('err_messages', $errors);


                return redirect()->back()->withInput();
            } else {
                $session->setFlashdata('success_register', ["Successfully Registered"]);

                $registeredName = $this->request->getPOST('firstName') . " " . $this->request->getPOST('middleName') . " " . $this->request->getPOST('lastName');

                //Create Notification
                $sender_id = 0;
                $Subject = "Registration";
                $Message = "New Registration: " . $registeredName;

                $superAdmin_condition = [
                    'role' => "Super Administrator",
                    'deleted' => 0,
                ];
                $superAdmin = $role_model->where($superAdmin_condition)->first();

                $saUsers = $scis_model->getRoleUsers($superAdmin['id']);

                foreach ($saUsers as $sa) {
                    \ScisSystem::CreateNotification($sender_id, $Subject, $Message, $sa->id);
                }


                return redirect()->back()->to('/')->withInput();
            }
        } else {
            $session->setFlashdata('err_messages', ['Password Doesn\'t match']);

            return redirect()->back()->withInput();
        }

    }

    public function logout()
    {
        $session = session();
        $developer_access = ($session->get('developer_access')) ? TRUE : FALSE;
        $log = new \App\Models\ActivityLogsModel();
        $log_data = [
            'user_id' => $session->get('user_id'),
            'logged_activity' => 'Logged Out',
        ];

        if($log->insert($log_data) == false)
        {
            echo 'error';
        }

        $session->destroy();
        
        if($developer_access)
        {
            session()->setFlashdata('developer_access',TRUE);
        }
        
        return redirect()->to('/');
    }

    public function forgot_password()
    {
        $session = session();
        if($session->get('logged_in'))
        {
            return redirect()->to("test_dashboard");
        }
        else
        {
            $data = [
                'page_title' => 'Forgot Password | PUPT SCIS',
            ];
            return view('site/forgot_password', $data);
        }
    }

    public function request_password_reset()
    {
        $session = session();
        if($session->get('logged_in'))
        {
            return redirect()->to("test_dashboard");
        }
        else
        {
            $email = $this->request->getPOST('email');

            $user_model = new \App\Models\UsersModel();
            $resetCodes_model = new \App\Models\ResetCodesModel();

            $user_id = 0;
            $user_exist = false;
            $log_data = [];

            //Check Email Existense
            if($user_model->where('email', $email)->first())
            {
                $user_data = $user_model->where('email', $email)->first();
                $user_id = $user_data['id'];

                //check if there are any request within 1 hour
                $codeConditions = [
                    'deleted' => 0,
                    'user_id' => $user_id
                ];
                $existingCodes = $resetCodes_model->where($codeConditions)->findAll();

                $validRequest = true;

                date_default_timezone_set('Asia/Manila');
                $current_time = date('Y-m-d H:i:s', time());
                $now = strtotime($current_time);

                //Check active requests Existence
                if($existingCodes)
                {
                    foreach($existingCodes as $oldReq)
                    {
                        if($oldReq['user_id'] == $user_id)
                        {
                            $Req_time = strtotime($oldReq['created']);
                            if(($time_diff = $now - $Req_time) < 3600)
                            {
                                $remaining = 3600 - $time_diff;
                                $minutes = abs(floor($remaining/60));
                                $seconds = abs(floor($remaining % 60));

                                $validRequest = false;

                                $errors = "Request has already been sent to email. Another Request can be sent after ".$minutes." minutes and ".$seconds." seconds";
                                $session->setFlashdata('err_messages',[$errors]);

                                return redirect()->back()->withInput();
                            }
                            else if(($now - $Req_time) >= 3600)
                            {

                                $data = [
                                    'deleted' => 1,
                                    'deleted_date' => $delete_stamp,
                                ];

                                $resetCodes_model->update($oldReq['id'],$data);
                            }
                        }
                    }
                }

                //Create request if there are no current active requests
                if($validRequest)
                {

                    $reset_code = uniqid();
                    $reset_data = [
                        "user_id" => $user_id,
                        "code" => $reset_code,
                    ];

                    if($resetCodes_model->insert($reset_data) == false)
                    {
                        $errors = $resetCodes_model->errors();
                        $session->setFlashdata('err_messages',$errors);

                        return redirect()->back()->withInput();
                    }



                    //Send Email
                    $recipient = $email;
                    $subject = 'Requested Reset Link';
                    $sender = [
                        'email' => 'noreply@scis.puptaguigcs.net',
                        'name' => 'PUPT SCIS'
                    ];
                    $message = '
                                    <p style="text-align: center;">
                                        We have sent you this email in response to your request to reset your password on your SCIS account. To reset your password, click the link below. 
                                    </p> 
                                  
                          
                                   <a href="'.base_url().'/home/reset_password/'.$reset_code.'" target="_blank" 
                                   style="box-sizing: border-box;display: inline-block;font-family:Lato,sans-serif;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;color: #800000; 
                                   background-color: #e4c062; border-radius: 1px;-webkit-border-radius: 1px; -moz-border-radius: 1px; width:auto; max-width:100%; 
                                   overflow-wrap: break-word; word-break: break-word; word-wrap:break-word; mso-border-alt: none;margin:10px;">
                                       <span style="display:block;padding:15px 40px;line-height:120%;">
                                            <span style="font-size: 18px; line-height: 21.6px;">Reset Password</span>
                                       </span>
                                    </a>
                                    <br><br><hr> 
                                    <p style="text-align: center; color: gray;"><i>
                                        If you did not request a password reset link, 
                                        you can safely ignore this email and your password will not be changed. </i>
                                    </p>
                                      ';

                    if(\ScisSystem::sendEmail($recipient,$subject,$message,$sender)){
                        $session->setFlashdata('success_email',["Reset Link Successfully Sent"]);
                        return redirect()->back()->to('/home')->withInput();
                    }
                    else{
                        //echo " You have requested to reset you SCIS account Password. To reset your password click <a href=\"reset_password/".$code."\">here</a>";
                        return redirect()->back()->to('/home/reset_password/'.$reset_code);
                    }

                    // Log Activity
                    $log = new \App\Models\ActivityLogsModel();
                    $log_data = [
                        'user_id' => $user_id,
                        'logged_activity' => 'Requested for a password reset link',
                    ];

                    if($log->insert($log_data) == false)
                    {
                        echo 'error';
                    }

                    // return redirect()->back()->to('/home/reset_password');
                }
            }
            else
            {
                $session->setFlashdata('err_messages',["Email Doesn't Exist"]);
                return redirect()->back()->withInput();
            }
        }
    }

    public function reset_password($code = "")
    {
        $session = session();

        $resetCodes_model = new \App\Models\ResetCodesModel();

        if($session->get('logged_in'))
        {
            return redirect()->to("test_dashboard");
        }
        else
        {
            if($code == "")
            {
                $session->setFlashdata('err_messages',['Invalid Reset Link']);
                return redirect()->to("/home")->withInput();
            }
            else
            {
                $existingCodes = $resetCodes_model->where("deleted",0)->findAll();
                foreach($existingCodes as $request)
                {
                    if($request['code'] == $code)
                    {
                        $data = [
                            'page_title' => 'Reset Password | PUPT SCIS',
                            'reset_code' => $code,
                        ];
                        return view('site/reset_password', $data);
                    }
                }

                $session->setFlashdata('err_messages',["Invalid Reset Link"]);
                return redirect()->to("/home");
            }
        }
    }

    public function reset($code = "") // Note Session for reset ID must be replace with link | no one can go here without emailed link
    {
        $resetCodes_model = new \App\Models\ResetCodesModel();
        $session = session();
        if(empty($code))
        {
            $session->setFlashdata('err_messages',['Invalid Link']);
            return redirect()->to("/home")->withInput();
        }
        else if(empty($resetCodes_model->where("code",$code)->first()))
        {
            $session->setFlashdata('err_messages',['Invalid Link']);
            return redirect()->to("/home")->withInput();
        }
        else
        {
            if($session->get('logged_in'))
            {
                return redirect()->to("/test_dashboard");
            }
            else
            {

                $user_model = new \App\Models\UsersModel();

                $validation = $this->validate([
                    'pass'=> 'required',
                    'confirm_pass'=> 'matches[pass]',
                ]);

                if($validation && $code){
                    $reset_request = $resetCodes_model->where("code",$code)->first();
                    $reset_id = $reset_request['id'];
                    $id = $reset_request['user_id'];
                    $password = Hash::encrypt_pass($this->request->getPOST('pass'));

                    date_default_timezone_set('Asia/Manila');
                    $modified_stamp = date('Y-m-d H:i:s', time());

                    $data = [
                        'password' => $password,
                        'modified' => $modified_stamp
                    ];

                    echo $data['password'];

                    $user_model->update($id, $data);

                    $reset_data = [
                        'deleted' => 1,
                        'deleted_date' => $modified_stamp
                    ];
                    $resetCodes_model->update($reset_id,$reset_data);

                    $log = new \App\Models\ActivityLogsModel();
                    $log_data = [
                        'user_id' => $id,
                        'logged_activity' => 'Password Reset',
                    ];

                    if($log->insert($log_data) == false)
                    {
                        echo 'error';
                    }
                    $session->setFlashdata('success_messages',["Password Successfully Reset"]);
                    return redirect()->back()->to('/home')->withInput();
                }
                else
                {
                    $session->setFlashdata('err_messages',["Password Doesn't Match"]);
                    return redirect()->back()->withInput();
                }
            }
        }
    }


}
