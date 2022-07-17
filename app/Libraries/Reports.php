<?php 
	
namespace App\Libraries;

use TCPDF;

define('K_PATH_IMAGES', 'assets/img/');
//define ('PDF_MARGIN_TOP', 40);

class Reports extends TCPDF{

    public function Header() {
        $image_file = K_PATH_IMAGES.'PUPLogo.png';
        $this->Image($image_file, 25,10,20);
        $this->Ln(3);
        $this->Cell(0, 15, 'Republic of the Philippines', 0, 0, 'C');
        $this->Ln(5);
        $this->SetFont('helvetica','B', 12);
        $this->Cell(0,15,'POLYTECHNIC UNIVERSITY OF THE PHILIPPINES',0,0,'C');
        $this->Ln(5);
        $this->SetFont('helvetica', '', 12);
        $this->Cell(0, 15, 'Taguig Branch', 'B', 0, 'C');

    }

    public function Footer() {
        $fill = 0;
        date_default_timezone_set('Asia/Manila');
        $today = date("F j, Y");
        //$ctime = date('h:i A');
        $this->SetY(-23);
        $this->SetFont('helvetica', '', 9);
        $this->Cell(0, 10,'Note: This is a computer-generated document. No signature is required.', '', 0, 'C', $fill);
        $this->Ln(10);
        $this->Cell(0,10,'Date Printed: '.$today,'T',0,'L', $fill);
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 'T', false, 'C', 0, '', 0, false, 'T', 'M');
    }

	public static function ClearanceFieldStatus($data,$data2,$field)
	{
		$pdf = new Reports('P','mm','A4');
		
		$fill = 0;




        //Doc Info
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('PUPT_SCIS');
		$pdf->SetTitle('Students Clearance Report');
		$pdf->SetSubject('Report');
		$pdf->SetKeywords('PDF, Clearance, Students Clearance, Report');

        //Header
		$pdf->SetPrintHeader(true);

		//Footer
		$pdf->SetPrintFooter(true);

		//font spacing
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		//Margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		//Page Break
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		//Font configuration
		$pdf->SetFont('helvetica', '', 12);



		//Create Page
		$pdf->AddPage();

        $period = $data2->year.", ".$data2->sem;

        //content
        $pdf->SetFont('helvetica', 'B', 13);
        $pdf->Cell(0, 15, strtoupper($field. ' CLEARANCE STATUS REPORT'), 0, 0, 'C');
        $pdf->Ln(6);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 15, 'SY. ' .$period, 0, 0, 'C');

        // Color and font restoration
        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('helvetica', '', 11);

        $course = "";
        $year = "";

        foreach($data as $row) 
        {
            if($course != $row->course_name)
            {
                $course = $row->course_name;
                $year = $row->level;

                $pdf->Ln(15);
                $pdf->Cell(0, 6,$course." ".$year."-1", 0, 0, 'C');
                $pdf->Ln(8);
                // Colors, line width and bold font
                $pdf->SetFillColor(255,255,255);
                $pdf->SetTextColor(0);
                $pdf->SetDrawColor(0);
                $pdf->SetLineWidth(0.3);
                $pdf->SetFont('', 'B');

                $columns = array('No.','Name','Student Number','Status');
                $w = array(15, 70, 65, 30);
                for($i = 0; $i < 4; ++$i) {
                    $pdf->Cell($w[$i], 7, $columns[$i], 1, 0, 'C', 1);
                }
                
                // Color and font restoration
                $pdf->SetFillColor(224, 235, 255);
                $pdf->SetTextColor(0);
                $pdf->SetFont('');

                // Data
                $fill = 0;
                $count = 1;                
            }

            else if($year != $row->level)
            {
                $year = $row->level;
                $pdf->Ln(8);
                $pdf->Cell(0, 6,$course." ".$year."-1", 0, 0, 'C');
                $pdf->Ln(8);
                // Colors, line width and bold font
                $pdf->SetFillColor(255,255,255);
                $pdf->SetTextColor(0);
                $pdf->SetDrawColor(0);
                $pdf->SetLineWidth(0.3);
                $pdf->SetFont('', 'B');

                $columns = array('No.','Name','Student Number','Status');
                $w = array(15, 70, 65, 30);
                for($i = 0; $i < 4; ++$i) {
                    $pdf->Cell($w[$i], 7, $columns[$i], 1, 0, 'C', 1);
                }
                
                // Color and font restoration
                $pdf->SetFillColor(224, 235, 255);
                $pdf->SetTextColor(0);
                $pdf->SetFont('');

                // Data
                $fill = 0;
                $count = 1;
            } 
            

            if ($row->status == 0) {
                $stat = "Not Cleared";
            } else {
                $stat = "Cleared";
            }

            $pdf->Ln();
            $pdf->Cell($w[0], 6,number_format($count++), 'LRB', 0, 'C', $fill);
            $pdf->Cell($w[1], 6, $row->student_name, 'LRB', 0, 'L', $fill);
            $pdf->Cell($w[2], 6, $row->student_number, 'LRB', 0, 'L', $fill);
            $pdf->Cell($w[3], 6, $stat, 'LRB', 0, 'C', $fill);

        }
		ob_end_clean();
		$pdf->Output();
		exit();
	}

	public static function ClearanceForm($data,$gradClearance = FALSE)
	{
		$pdf = new Reports('P','mm','LETTER');
		$fill = 0;

		//Doc Info
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('PUPT_SCIS');
		$pdf->SetTitle('Students Clearance Form');
		$pdf->SetSubject('ClearanceForm');
		$pdf->SetKeywords('PDF, Clearance, Students Clearance, Clearance Form');

		//Header
		$pdf->SetPrintHeader(true);

		//Footer
		$pdf->SetPrintFooter(true);

		//font spacing
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		//Margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		//Page Break
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		//Font configuration
		$pdf->SetFont('helvetica', '', 12);

        $sem = ($data['form']->sem == 1) ? "FIRST SEMESTER" : (($data['form']->sem == 2) ? "SECOND SEMESTER" : "SUMMER");

		//Create Page
		$pdf->AddPage();

        //Content
        if($gradClearance)
        {
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(0, 15, strtoupper('Student Graduation Clearance Form'), 0, 0, 'C');
            $pdf->Ln(6);
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(0, 15,"(Year End ".$data['form']->grad_sc_year." Graduating Students)", 0, 0, 'C');
            $pdf->Ln(20);

            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(31, 6,'Student Number: ', 0, 0, 'L', $fill);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(30, 6,$data['form']->student_number, 'B', 0, 'L', $fill);
            $pdf->Ln();

            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(14, 6,'Name: ', 0, 0, 'L', $fill);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(0, 6,preg_replace('/\s+/', ' ', $data['form']->student_name), 'B', 0, 'L', $fill);
            $pdf->Ln();

            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(36, 6,'Complete Address: ', 0, 0, 'L', $fill);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(0, 6,$data['form']->address, 'B', 0, 'L', $fill);
            $pdf->Ln();

            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(33, 6,'Contact Number: ', 0, 0, 'L', $fill);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(103, 6,$data['form']->contact_no, 'B', 0, 'L', $fill);
            $gender = ($data['form']->gender == 1) ? "Male" : "Female";
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(17, 6,'Gender: ', 0, 0, 'L', $fill);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(0, 6,$gender, 'B', 0, 'L', $fill);
            $pdf->Ln();

            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(17, 6,'Course: ', 0, 0, 'L', $fill);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(64, 6,$data['form']->course_code, 'B', 0, 'L', $fill);
            $major = ($data['form']->major) ? $data['form']->major : "N/A";
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(13, 6,'Major: ', 0, 0, 'L', $fill);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(0, 6,$major, 'B', 0, 'L', $fill);
            $pdf->Ln();

            $admitted_sem = ($data['form']->admitted_sem == 1) ? "First Semester" : "Second Semester";
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(56, 6,'Admitted in PUP | School Year: ', 0, 0, 'L', $fill);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(25, 6,$data['form']->admitted_year, 'B', 0, 'L', $fill);
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(20, 6,'Semester: ', 0, 0, 'L', $fill);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(35, 6,$admitted_sem, 'B', 0, 'L', $fill);
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(25, 6,'Date of Birth: ', 0, 0, 'L', $fill);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(0, 6,$data['form']->date_of_birth, 'B', 0, 'L', $fill);
            $pdf->Ln();

            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(37, 6,'Elementary School: ', 0, 0, 'L', $fill);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(99, 6,$data['form']->elementary, 'B', 0, 'L', $fill);
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(31, 6,'Year Graduated: ', 0, 0, 'L', $fill);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(0, 6,$data['form']->elementary_graduated_year, 'B', 0, 'L', $fill);
            $pdf->Ln();

            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(24, 6,'High School: ', 0, 0, 'L', $fill);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(112, 6,$data['form']->highschool, 'B', 0, 'L', $fill);
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(31, 6,'Year Graduated: ', 0, 0, 'L', $fill);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(0, 6,$data['form']->highschool_graduated_year, 'B', 0, 'L', $fill);

        }
        else
        {
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(0, 15, strtoupper('Student Clearance Form'), 0, 0, 'C');
            $pdf->Ln(6);
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(0, 15, $sem.', AY ' .$data['form']->sc_year, 0, 0, 'C');
            $pdf->Ln(20);

            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(13, 6,'Name: ', 0, 0, 'L', $fill);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(60, 6,$data['form']->student_name, 'B', 0, 'L', $fill);
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(31, 6,'Student Number: ', 0, 0, 'L', $fill);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(35, 6,$data['form']->student_number, 'B', 0, 'L', $fill);
            $pdf->Ln();

            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(32, 6,'Course and Year: ', 0, 0, 'L', $fill);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(41, 6,$data['form']->course_code.' '.$data['form']->year.'-1', 'B', 0, 'L', $fill);
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(26, 6,'Student Type: ', '', 0, 'L', $fill);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(27, 6,$data['form']->studType, 'B', 0, 'L', $fill);
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(23, 6,'Contact no: ', 0, 0, 'L', $fill);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(26, 6,$data['form']->contact_no, 'B', 0, 'L', $fill);

        }

      	$pdf->Ln(10);
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 6,'The above student is cleared of all responsibilities in this office:', '', 0, 'L', $fill);
        $pdf->Ln(10);

        if($gradClearance)
        {
            $pdf->SetFont('helvetica', 'B', 11);
            $pdf->Cell(0, 6,'Respective Professors', '', 0, 'L', $fill);
            $pdf->Ln(8);

            // Colors, line width and bold font
            $pdf->SetFillColor(0);
            $pdf->SetTextColor(0);
            $pdf->SetDrawColor(0);
            $pdf->SetLineWidth(0);
            $pdf->SetFont('helvetica', 'B',11);

            $columns = array('No.','Subject','Name of Professor','Status');
            $w = array(15, 70, 65, 30);
            for($i = 0; $i < 4; ++$i) {
                $pdf->Cell($w[$i], 7, $columns[$i], 1, 0, 'C');
            }

            // Color and font restoration
            $pdf->SetFillColor(224, 235, 255);
            $pdf->SetTextColor(0);
            $pdf->SetFont('');

            // Data
            $fill = 0;
            $count = 1;
            foreach($data['respective_professors'] as $row) {

                $stat = ($row->status == 1) ? "Cleared" : "Not Cleared";
                $subject = $row->sub_code."|".$row->sub_name;

                $pdf->Ln();
                $pdf->SetFont('helvetica', '', 11);
                $pdf->Cell($w[0], 6,number_format($count++), 1, 0, 'C');
                $pdf->Cell($w[1], 6, $subject, 1, 0, 'L');

                $pdf->Cell($w[2], 6, preg_replace('/\s+/', ' ', $row->professor_name), 1, 0, 'L');
                $pdf->SetFont('helvetica', 'B', 11);
                $pdf->Cell($w[3], 6, $stat, 1, 0, 'C');
            }
            $pdf->Ln(10);
        }


        // Colors, line width and bold font
        $pdf->SetFillColor(0);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(0);
        $pdf->SetLineWidth(0);
        $pdf->SetFont('helvetica', 'B',11);

        $columns = array('No.','Office','Clearance Officer','Status');
        $w = array(15, 70, 65, 30);
        for($i = 0; $i < 4; ++$i) {
            $pdf->Cell($w[$i], 7, $columns[$i], 1, 0, 'C');
        }
        
        // Color and font restoration
        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('');

        // Data
        $fill = 0;
        $count = 1;
        foreach($data['clearance_field'] as $row) {

            if($row['field'] != "Director's Office")
            {
             	$stat = ($row['entry_status'] == 1) ? "Cleared" : "Not Cleared";

             	$pdf->Ln();
                $pdf->SetFont('helvetica', '', 11);
                $pdf->Cell($w[0], 6,number_format($count++), 1, 0, 'C');
                $pdf->Cell($w[1], 6, $row['field'], 1, 0, 'L');
                $officer = preg_replace('/\s+/', ' ', $row['officer']);

                $pdf->Cell($w[2], 6, $officer, 1, 0, 'L');
                $pdf->SetFont('helvetica', 'B', 11);
                $pdf->Cell($w[3], 6, $stat, 1, 0, 'C');
            }
        }


        $directorSign = ($data['form']->director_sign) ? "Signed" : "";
        $registrarSign = ($data['form']->status) ? "Signed" : "";

        $pdf->Ln(30);
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(10, 6,'', '', 0, 'L', $fill);
        if($directorSign == 'Signed'):
            $pdf->Cell(115, 6,'Approved by', '', 0, 'L', $fill);
        endif;
        if($registrarSign == 'Signed'):
            $pdf->Cell(0, 6,'Received by', '', 0, 'L', $fill);
        endif;

        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(10, 6,'', '', 0, 'L', $fill);
        if($directorSign == 'Signed'):
            $pdf->Cell(115, 6,strtoupper('Dr. Marissa B. Ferrer'), '', 0, 'L', $fill);
        endif;
        if($registrarSign == 'Signed'):
            $pdf->Cell(0, 6,strtoupper('Mhel P. Garcia'), '', 0, 'L', $fill);
        endif;

        $pdf->Ln();
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(10, 6,'', '', 0, 'L', $fill);
        if($directorSign == 'Signed'):
            $pdf->Cell(115, 6,'Branch Director', 0, 0, 'L', $fill);
        endif;
        if($registrarSign == 'Signed'):
            $pdf->Cell(0, 6,'Registrar', 0, 0, 'L', $fill);
        endif;


        $pdf->Ln(31);
        $pdf->SetFont('helvetica', '', 10);


        ob_end_clean();
		$pdf->Output();
		exit();
	}

    public static function clearanceFormsStatus($data,$data2)
    {
        $pdf = new Reports('P','mm','A4');
        $fill = 0;

        //Doc Info
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('PUPT_SCIS');
        $pdf->SetTitle('Students Clearance Report');
        $pdf->SetSubject('Report');
        $pdf->SetKeywords('PDF, Clearance, Students Clearance, Report');

        //Header
        $pdf->SetPrintHeader(true);

        //Footer
        $pdf->SetPrintFooter(true);

        //font spacing
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        //Margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        //Page Break
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        //Font configuration
        $pdf->SetFont('helvetica', '', 12);



        //Create Page
        $pdf->AddPage();

        $period = $data2->year.", ".$data2->sem;

        //content
        $pdf->SetFont('helvetica', 'B', 13);
        $pdf->Cell(0, 15, 'CLEARANCE STATUS REPORT', 0, 0, 'C');
        $pdf->Ln(6);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 15, 'SY. ' .$period, 0, 0, 'C');

        // Color and font restoration
        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('helvetica', '', 11);

        $course = "";
        $year = "";

        foreach($data as $row)
        {
            if($course != $row->course_name)
            {
                $course = $row->course_name;
                $year = $row->level;

                $pdf->Ln(15);
                $pdf->Cell(0, 6,$course." ".$year."-1", 0, 0, 'C');
                $pdf->Ln(8);
                // Colors, line width and bold font
                $pdf->SetFillColor(255,255,255);
                $pdf->SetTextColor(0);
                $pdf->SetDrawColor(0);
                $pdf->SetLineWidth(0.3);
                $pdf->SetFont('', 'B');

                $columns = array('No.','Name','Student Number','Status');
                $w = array(15, 70, 65, 30);
                for($i = 0; $i < 4; ++$i) {
                    $pdf->Cell($w[$i], 7, $columns[$i], 1, 0, 'C', 1);
                }

                // Color and font restoration
                $pdf->SetFillColor(224, 235, 255);
                $pdf->SetTextColor(0);
                $pdf->SetFont('');

                // Data
                $fill = 0;
                $count = 1;
            }

            else if($year != $row->level)
            {
                $year = $row->level;
                $pdf->Ln(8);
                $pdf->Cell(0, 6,$course." ".$year."-1", 0, 0, 'C');
                $pdf->Ln(8);
                // Colors, line width and bold font
                $pdf->SetFillColor(255,255,255);
                $pdf->SetTextColor(0);
                $pdf->SetDrawColor(0);
                $pdf->SetLineWidth(0.3);
                $pdf->SetFont('', 'B');

                $columns = array('No.','Name','Student Number','Status');
                $w = array(15, 70, 65, 30);
                for($i = 0; $i < 4; ++$i) {
                    $pdf->Cell($w[$i], 7, $columns[$i], 1, 0, 'C', 1);
                }

                // Color and font restoration
                $pdf->SetFillColor(224, 235, 255);
                $pdf->SetTextColor(0);
                $pdf->SetFont('');

                // Data
                $fill = 0;
                $count = 1;
            }


            if ($row->status == 0) {
                $stat = "Not Cleared";
            } else {
                $stat = "Cleared";
            }

            $pdf->Ln();
            $pdf->Cell($w[0], 6,number_format($count++), 'LRB', 0, 'C', $fill);
            $pdf->Cell($w[1], 6, $row->student_name, 'LRB', 0, 'L', $fill);
            $pdf->Cell($w[2], 6, $row->student_number, 'LRB', 0, 'L', $fill);
            $pdf->Cell($w[3], 6, $stat, 'LRB', 0, 'C', $fill);

        }
        ob_end_clean();
        $pdf->Output();
        exit();
    }



    public static function clearanceForms($data,$gradClearance = FALSE)
    {
        $pdf = new Reports('P','mm','LETTER');
        $fill = 0;

        //Doc Info
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('PUPT_SCIS');
        $pdf->SetTitle('Students Clearance Form');
        $pdf->SetSubject('ClearanceForm');
        $pdf->SetKeywords('PDF, Clearance, Students Clearance, Clearance Form');

        //Header
        $pdf->SetPrintHeader(true);

        //Footer
        $pdf->SetPrintFooter(true);

        //font spacing
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        //Margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        //Page Break
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        //Font configuration
        $pdf->SetFont('helvetica', '', 12);

        //Create Page
        $pdf->AddPage();

        foreach($data as $data)
        {
            $sem = ($data['form']->sem == 1) ? "FIRST SEMESTER" : (($data['form']->sem == 2) ? "SECOND SEMESTER" : "SUMMER");

            //Content
            if($gradClearance)
            {
                $pdf->SetFont('helvetica', 'B', 12);
                $pdf->Cell(0, 15, strtoupper('Student Graduation Clearance Form'), 0, 0, 'C');
                $pdf->Ln(6);
                $pdf->SetFont('helvetica', '', 11);
                $pdf->Cell(0, 15,"(Year End ".$data['form']->grad_sc_year." Graduating Students)", 0, 0, 'C');
                $pdf->Ln(20);

                $pdf->SetFont('helvetica', '', 11);
                $pdf->Cell(31, 6,'Student Number: ', 0, 0, 'L', $fill);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(30, 6,$data['form']->student_number, 'B', 0, 'L', $fill);
                $pdf->Ln();

                $pdf->SetFont('helvetica', '', 11);
                $pdf->Cell(14, 6,'Name: ', 0, 0, 'L', $fill);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(0, 6,preg_replace('/\s+/', ' ', $data['form']->student_name), 'B', 0, 'L', $fill);
                $pdf->Ln();

                $pdf->SetFont('helvetica', '', 11);
                $pdf->Cell(36, 6,'Complete Address: ', 0, 0, 'L', $fill);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(0, 6,$data['form']->address, 'B', 0, 'L', $fill);
                $pdf->Ln();

                $pdf->SetFont('helvetica', '', 11);
                $pdf->Cell(33, 6,'Contact Number: ', 0, 0, 'L', $fill);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(103, 6,$data['form']->contact_no, 'B', 0, 'L', $fill);
                $gender = ($data['form']->gender == 1) ? "Male" : "Female";
                $pdf->SetFont('helvetica', '', 11);
                $pdf->Cell(17, 6,'Gender: ', 0, 0, 'L', $fill);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(0, 6,$gender, 'B', 0, 'L', $fill);
                $pdf->Ln();

                $pdf->SetFont('helvetica', '', 11);
                $pdf->Cell(17, 6,'Course: ', 0, 0, 'L', $fill);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(64, 6,$data['form']->course_code, 'B', 0, 'L', $fill);
                $major = ($data['form']->major) ? $data['form']->major : "N/A";
                $pdf->SetFont('helvetica', '', 11);
                $pdf->Cell(13, 6,'Major: ', 0, 0, 'L', $fill);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(0, 6,$major, 'B', 0, 'L', $fill);
                $pdf->Ln();

                $admitted_sem = ($data['form']->admitted_sem == 1) ? "First Semester" : "Second Semester";
                $pdf->SetFont('helvetica', '', 11);
                $pdf->Cell(56, 6,'Admitted in PUP | School Year: ', 0, 0, 'L', $fill);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(25, 6,$data['form']->admitted_year, 'B', 0, 'L', $fill);
                $pdf->SetFont('helvetica', '', 11);
                $pdf->Cell(20, 6,'Semester: ', 0, 0, 'L', $fill);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(35, 6,$admitted_sem, 'B', 0, 'L', $fill);
                $pdf->SetFont('helvetica', '', 11);
                $pdf->Cell(25, 6,'Date of Birth: ', 0, 0, 'L', $fill);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(0, 6,$data['form']->date_of_birth, 'B', 0, 'L', $fill);
                $pdf->Ln();

                $pdf->SetFont('helvetica', '', 11);
                $pdf->Cell(37, 6,'Elementary School: ', 0, 0, 'L', $fill);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(99, 6,$data['form']->elementary, 'B', 0, 'L', $fill);
                $pdf->SetFont('helvetica', '', 11);
                $pdf->Cell(31, 6,'Year Graduated: ', 0, 0, 'L', $fill);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(0, 6,$data['form']->elementary_graduated_year, 'B', 0, 'L', $fill);
                $pdf->Ln();

                $pdf->SetFont('helvetica', '', 11);
                $pdf->Cell(24, 6,'High School: ', 0, 0, 'L', $fill);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(112, 6,$data['form']->highschool, 'B', 0, 'L', $fill);
                $pdf->SetFont('helvetica', '', 11);
                $pdf->Cell(31, 6,'Year Graduated: ', 0, 0, 'L', $fill);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(0, 6,$data['form']->highschool_graduated_year, 'B', 0, 'L', $fill);

            }
            else
            {
                $pdf->SetFont('helvetica', 'B', 12);
                $pdf->Cell(0, 15, strtoupper('Student Clearance Form'), 0, 0, 'C');
                $pdf->Ln(6);
                $pdf->SetFont('helvetica', '', 11);
                $pdf->Cell(0, 15, $sem.', AY ' .$data['form']->sc_year, 0, 0, 'C');
                $pdf->Ln(20);

                $pdf->SetFont('helvetica', '', 11);
                $pdf->Cell(13, 6,'Name: ', 0, 0, 'L', $fill);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(60, 6,$data['form']->student_name, 'B', 0, 'L', $fill);
                $pdf->SetFont('helvetica', '', 11);
                $pdf->Cell(31, 6,'Student Number: ', 0, 0, 'L', $fill);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(35, 6,$data['form']->student_number, 'B', 0, 'L', $fill);
                $pdf->Ln();

                $pdf->SetFont('helvetica', '', 11);
                $pdf->Cell(32, 6,'Course and Year: ', 0, 0, 'L', $fill);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(41, 6,$data['form']->course_code.' '.$data['form']->year.'-1', 'B', 0, 'L', $fill);
                $pdf->SetFont('helvetica', '', 11);
                $pdf->Cell(26, 6,'Student Type: ', '', 0, 'L', $fill);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(27, 6,$data['form']->studType, 'B', 0, 'L', $fill);
                $pdf->SetFont('helvetica', '', 11);
                $pdf->Cell(23, 6,'Contact no: ', 0, 0, 'L', $fill);
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(26, 6,$data['form']->contact_no, 'B', 0, 'L', $fill);

            }

            $pdf->Ln(10);
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(0, 6,'The above student is cleared of all responsibilities in this office:', '', 0, 'L', $fill);
            $pdf->Ln(10);

            //For Graduation Clearance
            if($gradClearance)
            {
                $pdf->SetFont('helvetica', 'B', 11);
                $pdf->Cell(0, 6,'Respective Professors', '', 0, 'L', $fill);
                $pdf->Ln(8);

                // Colors, line width and bold font
                $pdf->SetFillColor(0);
                $pdf->SetTextColor(0);
                $pdf->SetDrawColor(0);
                $pdf->SetLineWidth(0);
                $pdf->SetFont('helvetica', 'B',11);

                $columns = array('No.','Subject','Professor','Status');
                $w = array(15, 70, 65, 30);
                for($i = 0; $i < 4; ++$i) {
                    $pdf->Cell($w[$i], 7, $columns[$i], 1, 0, 'C');
                }
                
                // Color and font restoration
                $pdf->SetFillColor(224, 235, 255);
                $pdf->SetTextColor(0);
                $pdf->SetFont('');

                // Data
                $fill = 0;
                $count = 1;
                foreach($data['respective_professors'] as $row) {

                    $stat = ($row->status == 1) ? "Cleared" : "Not Cleared";
                    $subject = $row->sub_code."|".$row->sub_name;

                    $pdf->Ln();
                    $pdf->SetFont('helvetica', '', 11);
                    $pdf->Cell($w[0], 6,number_format($count++), 1, 0, 'C');
                    $pdf->Cell($w[1], 6, $subject, 1, 0, 'L');

                    $pdf->Cell($w[2], 6, preg_replace('/\s+/', ' ', $row->professor_name), 1, 0, 'L');
                    $pdf->SetFont('helvetica', 'B', 11);
                    $pdf->Cell($w[3], 6, $stat, 1, 0, 'C');
                }
                $pdf->Ln(10);
            }


            // Colors, line width and bold font
            $pdf->SetFillColor(0);
            $pdf->SetTextColor(0);
            $pdf->SetDrawColor(0);
            $pdf->SetLineWidth(0);
            $pdf->SetFont('helvetica', 'B',11);

            $columns = array('No.','Office','Clearance Officer','Status');
            $w = array(15, 70, 65, 30);
            for($i = 0; $i < 4; ++$i) {
                $pdf->Cell($w[$i], 7, $columns[$i], 1, 0, 'C');
            }

            // Color and font restoration
            $pdf->SetFillColor(224, 235, 255);
            $pdf->SetTextColor(0);
            $pdf->SetFont('');

            // Data
            $fill = 0;
            $count = 1;
            foreach($data['clearance_field'] as $row) {

                $stat = ($row['entry_status'] == 1) ? "Cleared" : "Not Cleared";

                $pdf->Ln();
                $pdf->SetFont('helvetica', '', 11);
                $pdf->Cell($w[0], 6,number_format($count++), 1, 0, 'C');
                $pdf->Cell($w[1], 6, $row['field'], 1, 0, 'L');
                $officer = preg_replace('/\s+/', ' ', $row['officer']);

                $pdf->Cell($w[2], 6, $officer, 1, 0, 'L');
                $pdf->SetFont('helvetica', 'B', 11);
                $pdf->Cell($w[3], 6, $stat, 1, 0, 'C');
            }


            $directorSign = ($data['form']->director_sign) ? "Signed" : "";
            $registrarSign = ($data['form']->status) ? "Signed" : "";

            $pdf->Ln(30);
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(10, 6,'', '', 0, 'L', $fill);
            if($directorSign == 'Signed'):
                $pdf->Cell(115, 6,'Approved by', '', 0, 'L', $fill);
            endif;
            if($registrarSign == 'Signed'):
                $pdf->Cell(0, 6,'Received by', '', 0, 'L', $fill);
            endif;

            $pdf->Ln(10);
            $pdf->SetFont('helvetica', 'B', 11);
            $pdf->Cell(10, 6,'', '', 0, 'L', $fill);
            if($directorSign == 'Signed'):
                $pdf->Cell(115, 6,strtoupper('Dr. Marissa B. Ferrer'), '', 0, 'L', $fill);
            endif;
            if($registrarSign == 'Signed'):
                $pdf->Cell(0, 6,strtoupper('Mhel P. Garcia'), '', 0, 'L', $fill);
            endif;

            $pdf->Ln();
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(10, 6,'', '', 0, 'L', $fill);
            if($directorSign == 'Signed'):
                $pdf->Cell(115, 6,'Branch Director', 0, 0, 'L', $fill);
            endif;
            if($registrarSign == 'Signed'):
                $pdf->Cell(0, 6,'Registrar', 0, 0, 'L', $fill);
            endif;

            $pdf->AddPage();

        }


        $lastPage = $pdf->getPage();
        $pdf->deletePage($lastPage);
        ob_end_clean();
        $pdf->Output();
        exit();
    }
}

?>