<?php

namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;

class ScisModel
{
    protected $db;

    public function __construct(ConnectionInterface &$db)
    {
        $this->db = &$db;
    }

    public function getUsersList()
    {
    	$query = $this->db->query("SELECT id, username, CONCAT(last_name,', ',first_name,' ',middle_name,' ',suffix_name) as 'name' 
                FROM users 
                WHERE deleted = 0;");
                  
        return $query->getResult();
    }

    public function getUserRoles($id)
    {
        $query = $this->db->query("SELECT a.id,a.role_id as 'role_id', b.role as 'name' 
                FROM user_roles a 
                INNER JOIN roles b on b.id = a.role_id
                WHERE a.deleted = 0 AND a.user_id = '".$id."';");
                  
        return $query->getResult();
    }

    public function getRoleUsers($role)
    {
        $query = $this->db->query("SELECT b.id, CONCAT(b.last_name,', ',b.first_name,' ',b.middle_name,' ',b.suffix_name) as 'name'
            FROM user_roles a
            LEFT JOIN users b on b.id = a.user_id
            WHERE a.deleted = 0 AND b.deleted = 0 AND a.role_id = '".$role."';");
                  
        return $query->getResult();
    }

    public function getRegistrationRequests($status = "")
    {
        $string = "SELECT a.id, CONCAT(a.first_name,' ',a.middle_name,' ',a.last_name,' ',a.suffix_name) as 'name',a.role_id, b.role, a.email,a.contact_no,a.student_number,a.year_level,a.course_id,c.course_name,a.student_type_id,d.type
            FROM registrations a
            INNER JOIN roles b on b.id = a.role_id
            LEFT JOIN courses c on c.id = a.course_id
            LEFT JOIN student_types d on d.id = a.student_type_id";

        if($status == "" || $status = '0')
        {
            $string = $string." WHERE a.deleted = 0 AND a.status = 0";
        }
        else if($status == "1")
        {
            $string = $string." WHERE a.deleted = 1 AND a.status = 1";
        }
        else if($status == "2")
        {
            $string = $string." WHERE a.deleted = 1 AND a.status = 2";
        }

        $query = $this->db->query($string);
                  
        return $query->getResult();
    }

    public function getRegistration($id)
    {
        $query = $this->db->query("SELECT a.*, b.role as 'role', c.course_name as 'course', d.type as 'type'
                FROM registrations a
                INNER JOIN roles b on b.id = a.role_id
                LEFT JOIN courses c on c.id = a.course_id
                LEFT JOIN student_types d on d.id = a.student_type_id
                WHERE a.id = ".$id);
                  
        return $query->getRow();
    }

    public function getClearanceFieldsList()
    {
        $query = $this->db->query("SELECT a.id as 'id', a.field_name as 'name', a.description as 'desc', a.clearance_type_id as 'type_id', b.type as 'clearance_type' FROM `clearance_fields` a LEFT JOIN `clearance_types` b on a.clearance_type_id = b.id where a.deleted = 0;");

        return $query->getResult();
    }

    public function getCoursesList()
    {
        $query = $this->db->query("SELECT a.id as 'id',a.abbreviation as 'abb', a.course_name as 'name', a.max_year_level as 'year_levels', a.student_organization_id as 'org_id', b.organization_name as 'organization' FROM `courses` a LEFT JOIN `student_organizations` b on a.student_organization_id = b.id where a.deleted = 0;");

        return $query->getResult();
    }

    public function getOrganizationsList()
    {
        $query = $this->db->query("SELECT a.id as 'id', a.organization_name as 'name', a.organization_type_id as 'type_id', b.type as 'organization_type' FROM `student_organizations` a LEFT JOIN `organization_types` b on a.organization_type_id = b.id where a.deleted = 0;");

        return $query->getResult();
    }

    public function getPositionsList($exceptionField = "", $pos_ID = "")
    {
        $string = "SELECT a.id as 'id', a.position_name as 'name', a.clearance_field_id as 'field_id', b.field_name as 'field',d.id as co_id ,CONCAT(d.first_name,' ',d.middle_name,' ',d.last_name) as 'co_name' , c.id as 'co_pos_id'
                FROM `positions` a 
                LEFT JOIN `clearance_fields` b on a.clearance_field_id = b.id 
                LEFT JOIN `clearance_officer_positions` c on c.position_id = a.id 
                LEFT JOIN `users` d on d.id = c.clearance_officer_id 
                where a.deleted = 0";

         if(!empty($pos_ID))
        {
            $string = $string." AND a.id != '".$pos_ID;
        }

        if(!empty($exceptionField))
        {
            $string = $string." AND b.field_name != '".$exceptionField."' GROUP BY b.id";
        }
        else
        {
            $string = $string." GROUP BY a.id;";
        }        

        $query = $this->db->query($string);

        return $query->getResult();
    }

    public function getOrganizationOfficersList()
    {
        $query = $this->db->query("SELECT a.id as 'org_id', a.organization_name as 'org_name' ,c.id as 'co_id',CONCAT(c.first_name,' ',c.middle_name,' ',c.last_name) as 'co_name', e.id as 'pos_id', e.position_name as 'pos_name', d.id as 'co_pos_id', b.id as 'co_org_id'
                    FROM `student_organizations` a
                    LEFT JOIN `student_organization_officers` b on b.student_organization_id = a.id
                    LEFT JOIN `users` c on c.id = b.clearance_officer_id
                    LEFT JOIN `clearance_officer_positions` d on d.clearance_officer_id = c.id
                    LEFT JOIN `positions` e on e.id = d.position_id
                    LEFT JOIN `clearance_fields` f on f.id = e.clearance_field_id
                    where a.deleted = 0 && f.field_name = 'Student Organization';");

        return $query->getResult();
    }

    public function getRequirementsList($field_id = "")
    {
        $string = "SELECT a.id as 'id', a.requirement_name as 'name', a.clearance_field_id as 'field_id', b.field_name as 'field', a.submission_type as 'sub_type', a.file_type_id as 'file_type_id', c.type as 'file_type', a.instruction as 'ins'
                FROM `requirements` a 
                LEFT JOIN `clearance_fields` b on a.clearance_field_id = b.id
                LEFT JOIN `file_types` c on c.id = a.file_type_id
                where a.deleted = 0 && b.deleted = 0";

        if(!empty($field_id))
        {
            $string = $string." AND b.id = ".$field_id;
        }

        $query = $this->db->query($string);

        return $query->getResult();
    }

    public function initiatingCleranceEntriesData()
    {
        $query = $this->db->query("SELECT a.id as 'field_id', a.field_name, d.id as 'clearance_officer_id', CONCAT(d.first_name,' ',d.middle_name,' ',d.last_name,' ',d.suffix_name) as 'clearance_officer_name', b.id as 'position_id', b.position_name as 'pos_name', f.id as 'org_id', f.organization_name as 'org_name', IF(e.deleted = 0, 1, 0) as 'org_activeness'
                FROM clearance_fields a
                LEFT JOIN positions b on b.clearance_field_id = a.id
                LEFT JOIN clearance_officer_positions c on c.position_id = b.id
                LEFT JOIN users d on d.id = c.clearance_officer_id
                LEFT JOIN student_organization_officers e on e.clearance_officer_id = d.id
                LEFT JOIN student_organizations f on f.id = e.student_organization_id
                WHERE a.deleted = 0 && b.deleted = 0 && c.deleted = 0 && d.deleted = 0;");

        return $query->getResult();
    }

    public function getStudentOrg($id)
    {
        $query = $this->db->query("SELECT a.id as 'student_id', CONCAT(u.first_name,' ',u.middle_name,' ',u.last_name,' ',u.suffix_name) as 'name', c.id as 'org_id', c.organization_name as 'org_name'
                FROM students a
                LEFT JOIN users u on u.id = a.user_id
                LEFT JOIN courses b on b.id = a.course_id
                LEFT JOIN student_organizations c on c.id = b.student_organization_id
                WHERE a.id = ".$id.";");

        return $query->getRow();
    }

    public function getClearanceEntriesForCO($periodID,$field_id,$courseFil,$yearFil,$statusFil,$posID = FALSE,$co_org = FALSE)
    {
        $string = "SELECT a.id as 'form_id',c.id as 'student_id', c.student_number, CONCAT(u.first_name,' ',u.middle_name,' ',u.last_name) as 'student_name', d.abbreviation as 'course', c.year_level as 'year', b.clearance_field_id, b.id as 'entry_id', b.clearance_field_status as 'status', a.clearance_status as 'form_status', e.position_name as 'position'
                FROM clearance_forms a
                LEFT JOIN clearance_entries b on b.clearance_form_id = a.id
                LEFT JOIN students c on c.id = a.student_id
                LEFT JOIN users u on u.id = c.user_id
                LEFT JOIN courses d on d.id = c.course_id
                LEFT JOIN positions e on e.clearance_field_id = b.clearance_field_id
                INNER JOIN clearance_officer_positions f on f.clearance_officer_id = b.clearance_officer_id && f.position_id = e.id
                WHERE a.deleted = 0 && b.deleted = 0 && c.deleted = 0 && u.deleted = 0 && a.clearance_period_data_id = ".$periodID." && b.clearance_field_id = ".$field_id;

        if($courseFil != "all")
        {
            $string = $string." && c.course_id = ".$courseFil;
        }            

        if($yearFil != "all")
        {
            $string = $string." && c.year_level = ".$yearFil;
        }
        
        if($statusFil != "all")
        {
            $string = $string." && b.clearance_field_status = ".$statusFil;
        }

        if($co_org)
        {
            $string = $string." && d.student_organization_id = ".$co_org;
        }

        if($posID)
        {
            $string = $string." && e.id = ".$posID;
        }
        
        $string = $string." ;";

        //echo $string."<br><br>";

        $query = $this->db->query($string);
        return $query->getResult();
    }

    public function getGradClearanceEntriesForCO($field_id,$courseFil,$statusFil,$posID = FALSE,$co_org = FALSE)
    {
        $string = "SELECT a.id as 'form_id',c.id as 'student_id', c.student_number, CONCAT(u.first_name,' ',u.middle_name,' ',u.last_name) as 'student_name', d.abbreviation as 'course', c.year_level as 'year', b.clearance_field_id, b.id as 'entry_id', b.clearance_field_status as 'status', a.clearance_status as 'form_status', e.position_name as 'position'
                FROM clearance_forms a
                LEFT JOIN clearance_entries b on b.clearance_form_id = a.id
                LEFT JOIN students c on c.id = a.student_id
                LEFT JOIN users u on u.id = c.user_id
                LEFT JOIN courses d on d.id = c.course_id
                LEFT JOIN positions e on e.clearance_field_id = b.clearance_field_id
                INNER JOIN clearance_officer_positions f on f.clearance_officer_id = b.clearance_officer_id && f.position_id = e.id
                WHERE a.deleted = 0 && b.deleted = 0 && c.deleted = 0 && u.deleted = 0 && a.clearance_type_id = 2 && b.clearance_field_id = ".$field_id;

        if($courseFil != "all")
        {
            $string = $string." && c.course_id = ".$courseFil;
        }            
        
        if($statusFil != "all")
        {
            $string = $string." && b.clearance_field_status = ".$statusFil;
        }

        if($co_org)
        {
            $string = $string." && d.student_organization_id = ".$co_org;
        }

        if($posID)
        {
            $string = $string." && e.id = ".$posID;
        }
        
        $string = $string." ;";

        //echo $string."<br><br>";

        $query = $this->db->query($string);
        return $query->getResult();
    }

    public function getEntryDeficiencies($id)
    {
        $query = $this->db->query("SELECT a.id as 'def_id', b.id as 'req_id' ,b.requirement_name as 'req_name', a.clearance_entry_id as 'entry_id', a.status as 'def_status'
                FROM deficiencies a
                LEFT JOIN requirements b on b.id = a.requirement_id
                WHERE a.deleted = 0 && a.clearance_entry_id = ".$id);
        return $query->getResult();
    }

    //get all Student Infor using student id
    public function getStudentInfo($id)
    {
        $query = $this->db->query("SELECT u.last_name, u.first_name, u.middle_name, u.suffix_name, a.student_number, c.type, b.course_name, a.year_level, u.contact_no
                FROM students a
                LEFT JOIN users u on u.id = a.user_id
                LEFT JOIN courses b on b.id = a.course_id
                LEFT JOIN student_types c on c.id = a.student_type_id
                WHERE a.id = ".$id);

        return $query->getRow();
    }

    public function getUserNotifications($id)
    {
        $query = $this->db->query("SELECT *
                FROM notifications 
                WHERE deleted = 0 AND (recipient_user_id = 0 OR recipient_user_id = ".$id.")
                ORDER BY id DESC;");

        return $query->getResult();
    }

    public function getSubmissions($period_id,$co_id)
    {
        $query = $this->db->query("SELECT a.id as 'submission_id', a.file_path as 'submitted_file'
                FROM submissions a
                INNER JOIN deficiencies b on b.id = a.deficiency_id
                INNER JOIN clearance_entries c on c.id = b.clearance_entry_id
                INNER JOIN clearance_forms d on d.id = c.clearance_form_id
                WHERE d.clearance_period_data_id = ".$period_id." AND c.clearance_officer_id = ".$co_id." AND b.status = 0 AND a.file_path != '';");

        return $query->getResult();
    }

    //get clearance Form Full information
    public function getFormInfo($id = "all",$stat = 0,$period = "", $exept_field = "")
    {
        $string = "SELECT a.id as 'form_id',b.id as 'studID', CONCAT(u.last_name,', ',u.first_name,' ',u.middle_name,' ',u.suffix_name) as 'student_name',b.student_number,c.abbreviation as 'course', b.year_level as 'year', d.type as 'studType', u.contact_no, e.id as 'period' ,f.school_year as 'sc_year', g.type as 'clearanceType', a.clearance_status as 'status', c.abbreviation as 'course_code', e.semester as 'sem'
                FROM clearance_forms a
                LEFT JOIN students b on b.id = a.student_id
                LEFT JOIN users u on u.id = b.user_id
                LEFT JOIN courses c on c.id = b.course_id
                LEFT JOIN student_types d on d.id = b.student_type_id
                LEFT JOIN existing_clearance_periods e on e.id = a.clearance_period_data_id
                LEFT JOIN sc_years f on f.id = e.sc_year_id
                LEFT JOIN clearance_types g on g.id = a.clearance_type_id
                WHERE b.deleted = 0 && a.deleted = 0 && a.clearance_type_id = 1";

        if(!empty($exept_field))
        {
            $string .= " AND a.id != ".$exept_field;
        } 

        if($stat != 2)
        {
            $string .= " AND a.clearance_status = ".$stat;
        }

        if($id != 'all')
        {
            $string .= " AND a.id = ".$id;
            $query = $this->db->query($string);  
            return $query->getRow();
        } 
        else
        {
            $string .= " AND a.clearance_period_data_id = ".$period;
            $query = $this->db->query($string);  
            return $query->getResult();          
        }       
        
        // echo $string;        
    }    

    //get graduation clearance Form Full information
    public function getGradFormInfo($id = "all",$stat = 0,$exept_field = "")
    {
        $string = "SELECT a.id as 'form_id', h.id as 'grad_form_id',b.id as 'studID', CONCAT(u.last_name,', ',u.first_name,' ',u.middle_name,' ',u.suffix_name) as 'student_name',b.student_number,c.course_name as 'course', b.year_level as 'year', d.type as 'studType', u.contact_no, e.id as 'period' ,f.school_year as 'sc_year', g.type as 'clearanceType', a.clearance_status as 'status', c.abbreviation as 'course_code', e.semester as 'sem', i.school_year as 'grad_sc_year', h.address, h.gender, j.major, i2.school_year as 'admitted_year', h.admitted_sem, h.elementary, h.elementary_graduated_year, h.highschool, h.highschool_graduated_year, h.date_of_birth
                FROM clearance_forms a
                LEFT JOIN students b on b.id = a.student_id
                LEFT JOIN users u on u.id = b.user_id
                LEFT JOIN courses c on c.id = b.course_id
                LEFT JOIN student_types d on d.id = b.student_type_id
                LEFT JOIN existing_clearance_periods e on e.id = a.clearance_period_data_id
                LEFT JOIN sc_years f on f.id = e.sc_year_id
                LEFT JOIN clearance_types g on g.id = a.clearance_type_id
                LEFT JOIN graduation_clearances h on h.form_id = a.id
                LEFT JOIN sc_years i on i.id = h.graduation_school_year_id
                LEFT JOIN sc_years i2 on i2.id = h.admitted_scyear
                LEFT JOIN majors j on j.id = h.major_id
                WHERE b.deleted = 0 && a.deleted = 0 && h.status = 1 && h.deleted = 0";

        if(!empty($exept_field))
        {
            $string .= " AND a.id != ".$exept_field;
        } 

        if($stat != 2)
        {
            $string .= " AND a.clearance_status = ".$stat;
        }

        if($id != 'all')
        {
            $string .= " AND a.id = ".$id;
            $query = $this->db->query($string);  
            return $query->getRow();
        } 
        else
        {
            $query = $this->db->query($string);  
            return $query->getResult();           
        }       
        
        // echo $string; 
    }

    public function getCurrentStudentEntries($id,$pID,$posID = "")
    {
         $string = "SELECT a.id as 'entry_id', c.field_name as 'field', CONCAT(d.first_name,' ',d.middle_name,' ',d.last_name,' ',d.suffix_name) as 'officer_name', a.clearance_field_status as 'status', b.id as 'form_id', c.id as 'field_id', f.position_name as 'position' , f.id as 'position_id'
                FROM clearance_entries a
                INNER JOIN clearance_forms b on b.id = a.clearance_form_id
                INNER JOIN clearance_fields c on c.id = a.clearance_field_id
                INNER JOIN users d on d.id = a.clearance_officer_id
                INNER JOIN clearance_officer_positions e on e.clearance_officer_id = a.clearance_officer_id 
                INNER JOIN positions f on f.id = e.position_id AND f.clearance_field_id = a.clearance_field_id
                WHERE b.student_id = ".$id." AND b.clearance_period_data_id = ".$pID." AND e.deleted = 0 AND f.deleted = 0";

        if(!empty($posID))
        {
            $string .= " AND f.id = ".$posID;

            $query = $this->db->query($string);
            
            return $query->getRow();
        }
        else
        {
            $query = $this->db->query($string);
            return $query->getResult();
        }

    }

    public function getCurrentStudentDeficiency($entry_id)
    {
         $query = $this->db->query("SELECT a.id, c.requirement_name as 'req_name', a.status as 'def_status', c.submission_type as 'sub_type', c.instruction as 'ins', c.file_type_id, a.description as 'reason'
            FROM deficiencies a
            INNER JOIN clearance_entries b on b.id = a.clearance_entry_id
            INNER JOIN requirements c on c.id = a.requirement_id
            WHERE a.clearance_entry_id = ".$entry_id." AND a.deleted = 0;");

        return $query->getResult();
    }

    public function getSubmissionsForCField($field_id,$period_id,$courseFil,$yearFil,$statusFil,$reqFil,$posID = FALSE,$co_org = FALSE)
    {
        $string = "SELECT a.id as 'sub_id', b.id as 'def_id', c.id as 'entry_id', a.modified as 'submitted_date', e.student_number as 'studNum', CONCAT(u.first_name,' ',u.middle_name,' ',u.last_name,' ',u.suffix_name) as 'studName', f.abbreviation as 'course', e.year_level as 'year', g.requirement_name as 'requirement', CONCAT( h.type,'s/',a.file_path) as 'file', b.status as 'status'
            FROM `submissions` a
            INNER JOIN deficiencies b on b.id = a.deficiency_id
            INNER JOIN clearance_entries c on c.id = b.clearance_entry_id
            INNER JOIN clearance_forms d on d.id = c.clearance_form_id
            INNER JOIN students e on e.id = d.student_id
            LEFT JOIN users u on u.id = e.user_id
            INNER JOIN courses f on f.id = e.course_id
            INNER JOIN requirements g on g.id = b.requirement_id
            INNER JOIN file_types h on h.id = g.file_type_id
            LEFT JOIN positions i on i.clearance_field_id = c.clearance_field_id
            INNER JOIN clearance_officer_positions j on j.clearance_officer_id = c.clearance_officer_id && j.position_id = i.id
            WHERE a.file_path != '' && c.clearance_field_id = ".$field_id." && d.clearance_period_data_id = ".$period_id;

        if($posID)
        {
            $string = $string." && i.id = ".$posID;
        }

        if($co_org)
        {
            $string = $string." && f.student_organization_id = ".$co_org;
        }

        if($courseFil != "all")
        {
            $string = $string." && e.course_id = ".$courseFil;
        }            

        if($yearFil != "all")
        {
            $string = $string." && e.year_level = ".$yearFil;
        }
        
        if($reqFil != 'all')
        {
            $string = $string." && g.id = ".$reqFil;
        }

        if($statusFil == 0 || $statusFil == 2)
        {
            $string = $string." && a.deleted = 0 && b.status = ".$statusFil;
        }
        if($statusFil == 1)
        {
            $string = $string." && a.deleted = 1 && b.status = ".$statusFil;
            //$string = $string." GROUP BY a.deficiency_id"; //if they only want single submission per student
        }

        $string = $string." ORDER BY 'submitted' ASC;";

        $query = $this->db->query($string);

        //echo $string."<br";

        return $query->getResult();
    }

    public function getSubmissionsForGradCField($field_id,$courseFil,$statusFil,$reqFil,$posID = FALSE,$co_org = FALSE)
    {
        $string = "SELECT a.id as 'sub_id', b.id as 'def_id', c.id as 'entry_id', a.modified as 'submitted_date', e.student_number as 'studNum', CONCAT(u.first_name,' ',u.middle_name,' ',u.last_name,' ',u.suffix_name) as 'studName', f.abbreviation as 'course', e.year_level as 'year', g.requirement_name as 'requirement', CONCAT( h.type,'s/',a.file_path) as 'file', b.status as 'status'
            FROM `submissions` a
            INNER JOIN deficiencies b on b.id = a.deficiency_id
            INNER JOIN clearance_entries c on c.id = b.clearance_entry_id
            INNER JOIN clearance_forms d on d.id = c.clearance_form_id
            INNER JOIN students e on e.id = d.student_id
            LEFT JOIN users u on u.id = e.user_id
            INNER JOIN courses f on f.id = e.course_id
            INNER JOIN requirements g on g.id = b.requirement_id
            INNER JOIN file_types h on h.id = g.file_type_id
            LEFT JOIN positions i on i.clearance_field_id = c.clearance_field_id
            INNER JOIN clearance_officer_positions j on j.clearance_officer_id = c.clearance_officer_id && j.position_id = i.id
            WHERE a.file_path != '' && c.clearance_field_id = ".$field_id." && d.clearance_type_id = 2";

        if($posID)
        {
            $string = $string." && i.id = ".$posID;
        }

        if($co_org)
        {
            $string = $string." && f.student_organization_id = ".$co_org;
        }

        if($courseFil != "all")
        {
            $string = $string." && e.course_id = ".$courseFil;
        }            
        
        if($reqFil != 'all')
        {
            $string = $string." && g.id = ".$reqFil;
        }

        if($statusFil == 0 || $statusFil == 2)
        {
            $string = $string." && a.deleted = 0 && b.status = ".$statusFil;
        }
        if($statusFil == 1)
        {
            $string = $string." && a.deleted = 1 && b.status = ".$statusFil;
            //$string = $string." GROUP BY a.deficiency_id"; //if they only want single submission per student
        }

        $string = $string." ORDER BY 'submitted' ASC;";

        $query = $this->db->query($string);

        //echo $string."<br";

        return $query->getResult();
    }

    public function getClearancePeriods()
    {
        $query = $this->db->query("SELECT a.id, b.school_year as 'year', a.semester
            FROM existing_clearance_periods a
            INNER JOIN sc_years b on b.id = a.sc_year_id
            WHERE a.deleted = 0
            ORDER BY a.id DESC
            ");

        return $query->getResult();
    }

    //Get student's deficiencies using deficiency id
    public function getStudentInfo1($def_id)
    {
        $query = $this->db->query("SELECT CONCAT(u.first_name,' ',u.middle_name,' ',u.last_name,' ',u.suffix_name) as 'student_name', d.id as 'student_id', d.user_id as 'user_id'
            FROM `deficiencies` a
            INNER JOIN `clearance_entries` b on b.id = a.clearance_entry_id
            INNER JOIN `clearance_forms` c on c.id = b.clearance_form_id
            INNER JOIN `students` d on d.id = c.student_id
            LEFT join users u on u.id = d.user_id
            WHERE a.id = ".$def_id." ;");

        return $query->getResult();
    }

    //For Clerance Field Status Report Generation
    public function clearanceStatusReport($period, $field, $status, $course, $level)
    {
        $string = "SELECT CONCAT(u.last_name,', ',u.first_name,' ',u.middle_name,' ',u.suffix_name) as 'student_name',c.student_number , d.course_name, c.year_level as 'level', a.clearance_field_status as 'status', d.abbreviation as 'course_code'
                FROM `clearance_entries` a
                INNER JOIN `clearance_forms` b on b.id = a.clearance_form_id
                INNER JOIN `students` c on c.id = b.student_id
                LEFT join users u on u.id = c.user_id
                INNER JOIN `courses` d on d.id = c.course_id
                WHERE a.deleted = 0 AND b.clearance_period_data_id = ".$period." AND a.clearance_field_id = ".$field;

        if($status != 'all')
        {
            $string = $string." AND a.clearance_field_status = ".$status;
        }

        if($course != 'all')
        {
            $string = $string." AND c.course_id = ".$course;
        }

        if($level != 'all')
        {
            $string = $string." AND c.year_level = ".$level;
        }

        $string = $string." ORDER BY d.course_name ASC, c.year_level ASC, student_name ASC";


        $query = $this->db->query($string);

        return $query->getResult();
    }

    //For Clerance Forms Status Report Generation
    public function clearanceFormsReport($period, $status, $course, $level)
    {
        $string = "SELECT a.id as 'fID', CONCAT(c.last_name,', ',c.first_name,' ',c.middle_name,' ',c.suffix_name) as 'student_name',b.student_number , d.course_name, b.year_level as 'level', a.clearance_status as 'status', d.abbreviation as 'course_code'
            FROM clearance_forms a
            LEFT JOIN `students` b on b.id = a.student_id
            LEFT join users c on c.id = b.user_id
            LEFT JOIN `courses` d on d.id = b.course_id
            WHERE a.deleted = 0 AND a.clearance_period_data_id = ".$period;

        if($status != 'all')
        {
            $string = $string." AND a.clearance_status = ".$status;
        }

        if($course != 'all')
        {
            $string = $string." AND b.course_id = ".$course;
        }

        if($level != 'all')
        {
            $string = $string." AND b.year_level = ".$level;
        }

        $string = $string." ORDER BY d.course_name ASC, b.year_level ASC, student_name ASC"; 

        $query = $this->db->query($string);

        return $query->getResult();
    }

    //For Clerance Forms Status Report Generation
    public function gradClearanceFormsReport($scYear, $status, $course)
    {
        $string = "SELECT a.id as 'fID', CONCAT(c.last_name,', ',c.first_name,' ',c.middle_name,' ',c.suffix_name) as 'student_name',b.student_number , d.course_name, b.year_level as 'level', a.clearance_status as 'status', d.abbreviation as 'course_code', e.id as 'grad_clearance_id', f.school_year as 'graduated_year'
            FROM clearance_forms a
            LEFT JOIN `students` b on b.id = a.student_id
            LEFT join users c on c.id = b.user_id
            LEFT JOIN `courses` d on d.id = b.course_id
            LEFT JOIN graduation_clearances e on e.form_id = a.id
            LEFT JOIN sc_years f on f.id = e.graduation_school_year_id
            WHERE a.deleted = 0 AND a.clearance_type_id = 2 AND e.status = 1";

        if($scYear != 'all')
        {
            $string = $string." AND e.graduation_school_year_id = ".$scYear;
        }        

        if($status != 'all')
        {
            $string = $string." AND a.clearance_status = ".$status;
        }

        if($course != 'all')
        {
            $string = $string." AND b.course_id = ".$course;
        }

        $string = $string." ORDER BY f.id ASC, d.course_name ASC, student_name ASC"; 

        $query = $this->db->query($string);

        return $query->getResult();
    }

    //Get clearance period info
    public function getPeriodInfo($id)
    {
        $query = $this->db->query("SELECT b.school_year as 'year', IF(a.semester=1, '1st Semester', IF(a.semester=2, '2nd Semester', 'Summer Semester')) as 'sem'
                FROM existing_clearance_periods a
                INNER JOIN sc_years b on b.id = a.sc_year_id
                WHERE a.id = ".$id);

        return $query->getRow();
    }

    public function getBlackList($field_id,$org = FALSE)
    {
        $string = "SELECT a.id as 'blID', a.student_id as 'studID', CONCAT(c.last_name,', ',c.first_name,' ',c.middle_name,' ',c.suffix_name) as 'student_name', e.abbreviation as 'course', b.year_level as 'year', d.requirement_name as 'deficiency'
            FROM black_list a
            LEFT JOIN students b on b.id = a.student_id
            LEFT JOIN users c on c.id = b.user_id
            LEFT JOIN requirements d on d.id = a.requirement_id
            LEFT JOIN courses e on e.id = b.course_id
            WHERE a.deleted = 0 AND a.clearance_field_id = '".$field_id."'";

        if($org)
        {
            $string .= " AND e.student_organization_id =".$org;
        }

        $query = $this->db->query($string);

        return $query->getResult();
    }

    public function getStudents($course = "", $year = "", $org = "")
    {
        $string = "SELECT a.id,CONCAT(b.last_name,', ',b.first_name,' ',b.middle_name,' ',b.suffix_name) as 'student_name', c.id as 'course_id', c.abbreviation as 'course', a.year_level as 'year'
                FROM students a
                LEFT JOIN users b on b.id = a.user_id
                LEFT JOIN courses c on c.id = a.course_id
                WHERE a.deleted = 0";

        if(!empty($course))
        {
             $string = $string." AND a.course = ".$course;
        }

        if(!empty($year))
        {
            $string = $string." AND a.year_level = ".$year;
        }

        if(!empty($org))
        {
            $string = $string." AND c.student_organization_id = ".$org;
        }

        $string = $string." ORDER BY c.course_name, a.year_level ASC, 'student_name' ASC;";

        $query = $this->db->query($string);

        return $query->getResult();
    }

    public function getGradForm($student_id)
    {
        $query = $this->db->query("SELECT a.id, a.status as 'approval_status', a.note as 'reject_reason', b.id as 'clearance_form_id'
            FROM graduation_clearances a
            LEFT JOIN clearance_forms b on b.id = a.form_id
            LEFT JOIN students c on c.id = b.student_id
            LEFT JOIN users d on d.id = c.user_id
            WHERE c.id = '".$student_id."' AND a.deleted = 0
            ORDER BY a.id DESC");

        return $query->getRow();
    }

    public function getStudentForms($id) // For Forms and Periods
    {
        $query = $this->db->query("SELECT b.id, b.semester,c.school_year as 'year', a.clearance_status
            FROM clearance_forms a
            LEFT JOIN existing_clearance_periods b on b.id = a.clearance_period_data_id
            LEFT JOIN sc_years c on c.id = b.sc_year_id
            WHERE b.deleted = 0 AND a.student_id = '".$id."'");

        return $query->getResult();
    }

    public function getFormEntries($form_id)
    {
        $query = $this->db->query("SELECT a.id as 'entry_id', c.field_name as 'field', CONCAT(d.first_name,' ',d.middle_name,' ',d.last_name,' ',d.suffix_name) as 'officer_name', a.clearance_field_status as 'status', b.id as 'form_id', c.id as 'field_id', f.position_name as 'position'
                FROM clearance_entries a
                INNER JOIN clearance_forms b on b.id = a.clearance_form_id
                INNER JOIN clearance_fields c on c.id = a.clearance_field_id
                INNER JOIN users d on d.id = a.clearance_officer_id
                INNER JOIN clearance_officer_positions e on e.clearance_officer_id = a.clearance_officer_id 
                INNER JOIN positions f on f.id = e.position_id AND f.clearance_field_id = a.clearance_field_id
                WHERE f.deleted = 0 AND e.deleted = 0 AND c.deleted = 0 AND c.field_name != \"Director's Office \" AND b.id = '".$form_id."'");

        return $query->getResult();
    }

    public function getPreRequisites($id)
    {
        $query = $this->db->query("SELECT a.id, b.id as 'position_id', b.position_name as 'position_name', c.id as 'field_id', c.field_name as 'field_name'
            FROM clearance_field_prerequisites a
            LEFT JOIN positions b on b.id = a.requisite_clearance_field_id
            LEFT JOIN clearance_fields c on c.id = b.clearance_field_id
            WHERE a.clearance_field_id = ".$id );

        return $query->getResult();
    }

    public function getEntryFormInfo($id)
    {
        $query = $this->db->query("SELECT b.id  as 'form_id' ,b.clearance_status as 'status'
            FROM clearance_entries a 
            LEFT JOIN clearance_forms b on b.id = a.clearance_form_id
            WHERE a.id = ".$id );

        return $query->getRow();
    }

    public function getMajorsList($course = "")
    {
        $string = "SELECT a.id, a.major, a.course_id, b.course_name as 'course'
            FROM `majors` a
            LEFT JOIN courses b on b.id = a.course_id
            WHERE a.deleted = 0 && b.deleted = 0";

        if(!empty($course))
        {
            $string .= " && a.course_id = ".$course;
        }

        $query = $this->db->query($string);

        return $query->getResult();
    }
    
    public function setTimeZone()
    {
        $this->db->query("SET time_zone = '+8:00' ");
    }

}