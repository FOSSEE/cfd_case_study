<?php
function generate_pdf()
{
	$mpath = drupal_get_path('module', 'cfd_case_study');
	require($mpath.'/pdf/fpdf/fpdf.php');
	require($mpath.'/pdf/phpqrcode/qrlib.php');
	global $user;
	$x                 = $user->uid;
	$proposal_id       = arg(3);
	$query3 = db_query("SELECT * FROM case_study_proposal WHERE approval_status=3 AND uid= :uid AND id=:proposal_id", array(
		':uid' => $user->uid,
		':proposal_id'=>$proposal_id
	));
	$data3             = $query3->fetchObject();
	if($data3){
	if($data3->uid != $x){
		drupal_set_message('Certificate is not available','error');
		return;
	}
	}
	$gender            = array(
		'salutation' => 'Mr. /Ms.',
		'gender' => 'He/She'
	);
	if ($data3->gender) {
		if ($data3->gender == 'M') {
			$gender = array(
				'salutation' => 'Mr.',
				'gender' => 'He'
			);
		} //$data3->gender == 'M'
		else {
			$gender = array(
				'salutation' => 'Ms.',
				'gender' => 'She'
			);
		}
	} //$data3->gender
	$pdf = new FPDF('L', 'mm', 'Letter');
	if (!$pdf) {
		echo "Error!";
	} //!$pdf
	$pdf->AddPage();

	$date1 = new DateTime("2020-12-01");
	$date2 = new DateTime("2021-12-31");
	$approval_date = date('Y-m-d', $data3->approval_date); 

	if (($date1>$approval_date) && ($approval_date>$date2))
	{	
		$image_bg = $mpath . "/pdf/images/bg_cert_1.png";
		//$pdf->Image($image_bg, 0, 0, $pdf->w, $pdf->h);
		$pdf->Image($image_bg, 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight());
		//$pdf->Rect(5, 5, 267, 207, 'D');
		$pdf->SetMargins(18, 1, 18);
		//$pdf->Line(7.0, 7.0, 270.0, 7.0);
		//$pdf->Line(7.0, 7.0, 7.0, 210.0);
		//$pdf->Line(270.0, 210.0, 270.0, 7.0);
		//$pdf->Line(7.0, 210.0, 270.0, 210.0);
		$path   = drupal_get_path('module', 'cfd_case_study');
		//$image1 = $mpath . "/pdf/images/dwsim_logo.png";
		$pdf->Ln(30);
		//$pdf->Cell(200, 8, $pdf->Image($image1, 105, 15, 0, 28), 0, 1, 'C');
		//$pdf->Ln(20);
		//$pdf->SetTextColor(139, 69, 19);
		//$pdf->Cell(240, 8, 'Certificate of Participation', '0', 1, 'C');
		//$pdf->Ln(26);
		$pdf->SetFont('Times', 'I', 18);
		$pdf->SetTextColor(0, 0, 0);
		$pdf->Cell(320, 10, 'This is to certify that', '0', '1', 'C');
		$pdf->Ln(0);
		$pdf->SetFont('Times', 'I', 18);
		//$pdf->SetFont('Arial', 'BI', 25);
		$pdf->SetTextColor(37, 22, 247);
		$pdf->Cell(320, 10, $data3->name_title . '. ' . $data3->contributor_name, '0', '1', 'C');
		$pdf->Ln(0);
		$pdf->SetFont('Times', 'I', 18);
		if (strtolower($data3->branch) != "others") {
			$title = WordWrap($data3->project_title,60);
			$university = 'from ' . $data3->university;
			$pdf->SetTextColor(0, 0, 0);
			//$pdf->Cell(240, 8, 'from ' . $data3->university . ' has successfully', '0', '1', 'C');
			$pdf->MultiCell(320, 10, $university, '0','C');
			$pdf->Ln(0);
			$pdf->Cell(320, 10, 'has successfully completed the case study on', '0', '1', 'C');
			$pdf->Ln(0);
			$pdf->SetTextColor(37, 22, 247);
			$pdf->SetFont('Times','I',18);
			$pdf->MultiCell(320, 10, $title, '0', 'C');
			$pdf->SetTextColor(0, 0, 0);
			$pdf->Ln(0);
			$pdf->SetFont('Times','I',18);
			$pdf->Cell(320, 8, 'using OpenFOAM.', '0', '1', 'C');
			$pdf->SetTextColor(0, 0, 0);
			$pdf->Ln(4);
			$pdf->Cell(320, 8, 'The work done is available at', '0', '1', 'C');
			$pdf->Cell(320, 4, '', '0', '1', 'C');
			$pdf->SetX(130);
			$pdf->SetFont('Times', 'I', 'U');
			$pdf->SetTextColor(0, 0, 0);
			$pdf->write(0, 'https://cfd.fossee.in/case-study-project', 'https://cfd.fossee.in/case-study-project');
			/*$pdf->SetTextColor(0, 0, 0);
			$pdf->Ln(10);	
			$pdf->SetFont('Times','I',16);
			$pdf->Cell(320, 8, ' FOSSEE CFD-OpenFOAM project is partly supported by the CSR funds from ', '0', '1', 'C');
			$pdf->Cell(320, 8, ' The New India Assurance Co. Ltd.', '0', '1', 'C');*/
			$pdf->Ln(0);
			//$pdf->SetFont('Times', 'I', 16);
			//$pdf->Cell(320, 8, 'under Case Study Project', '0', '1', 'C');
			//$pdf->Ln(0);
			//$pdf->SetFont('Helvetica', '', 18);
			//$pdf->Cell(0, 0, ' Case Study Project', '0', '0', 'C');
			//$pdf->Cell(240, 4, '', '0', '1', 'C');
			//$pdf->SetX(120);
			//$pdf->SetFont('', 'U');
			//$pdf->SetTextColor(139, 69, 19);
			//$pdf->write(0, 'http://CFD.fossee.in/', 'http://CFD.fossee.in/');
			//$pdf->Ln(0);
			//$pdf->Cell(240, 8, 'Book: ' . $data2->book . ', Author: ' . $data2->author . '.', '0', '1', 'C');
			//$pdf->MultiCell(240, 8, 'Book: ' . $data2->book . ', Author: ' . $data2->author . '.', '0','C');
			$pdf->Ln(0);
		} //strtolower($data3->branch) != "others"
		else {
			$pdf->SetTextColor(0, 0, 0);
			$pdf->Cell(240, 8, 'from ' . $data3->university . ' college', '0', '1', 'C');
			$pdf->Ln(0);
			$pdf->Cell(240, 8, 'has successfully completed the case study of', '0', '1', 'C');
			$pdf->Ln(0);
			$pdf->SetTextColor(139, 69, 19);
			$pdf->Cell(320, 12, $data3->project_title, '0', '1', 'C');
			$pdf->SetTextColor(0, 0, 0);
			$pdf->Ln(0);
			$pdf->SetFont('Times', '', 16);
			$pdf->Cell(320, 8, ' under Case Study Project', '0', '1', 'C');
			/*$pdf->SetTextColor(0, 0, 0);
			$pdf->Ln(10);	
			$pdf->SetFont('Times','I',16);
			$pdf->Cell(320, 8, ' FOSSEE CFD-OpenFOAM project is partly supported by the CSR funds from ', '0', '1', 'C');
			$pdf->Cell(320, 8, ' The New India Assurance Co. Ltd.', '0', '1', 'C');*/
			$pdf->Ln(0);
			//$pdf->Cell(240, 8, 'He/she has coded ' . $number_of_example . ' solved examples using DWSIM from the', '0', '1', 'C');
			//$pdf->Ln(0);
			//$pdf->Cell(240, 8, 'Book: ' . $data2->book . ', Author: ' . $data2->author . '.', '0', '1', 'C');
			//$pdf->Ln(0);
		}
		$proposal_get_id = 0;
		$UniqueString    = "";
		$tempDir         = $path . "/pdf/temp_prcode/";
		$query           = db_select('case_study_qr_code');
		$query->fields('case_study_qr_code');
		$query->condition('proposal_id', $proposal_id);
		$result          = $query->execute();
		$data            = $result->fetchObject();
		$DBString        = $data->qr_code;
		$proposal_get_id = $data->proposal_id;
		if ($DBString == "" || $DBString == "null") {
			$UniqueString = generateRandomString();
			$query        = "
					INSERT INTO case_study_qr_code
					(proposal_id,qr_code)
					VALUES
					(:proposal_id,:qr_code)
					";
			$args         = array(
				":proposal_id" => $proposal_id,
				":qr_code" => $UniqueString
			);
			$result       = db_query($query, $args, array(
				'return' => Database::RETURN_INSERT_ID
			));
		} //$DBString == "" || $DBString == "null"
		else {
			$UniqueString = $DBString;
		}
		$codeContents        = "https://cfd.fossee.in/case-study-project/certificates/verify/" . $UniqueString;
		$fileName            = 'generated_qrcode.png';
		$pngAbsoluteFilePath = $tempDir . $fileName;
		$urlRelativeFilePath = $path . "/pdf/temp_prcode/" . $fileName;
		QRcode::png($codeContents, $pngAbsoluteFilePath);
		$pdf->SetY(85);
		$pdf->SetX(320);
		$pdf->Ln(25);
		/*$sign = $path . "/pdf/images/sign.png";
		$pdf->Image($sign, $pdf->GetX()+70, $pdf->GetY() + 40, 80, 0);*/
		$pdf->Image($pngAbsoluteFilePath, $pdf->GetX() + 15 , $pdf->GetY() + 51, 32,30, 0);
		//$pdf->Cell(240, 8, 'Prof. Kannan M. Moudgalya', 0, 1, 'R');
		//$pdf->SetX(199);
		//$pdf->SetFont('Arial', '', 10);
		//$pdf->Cell(0, 7, 'Co - Principal Investigator - FOSSEE', 0, 1, 'L');
		//$pdf->SetX(190);
		//$pdf->Cell(0, 7, ' Dept. of Chemical Engineering, IIT Bombay.', 0, 1, 'L');
		//$pdf->SetX(29);
		$pdf->SetFont('Times', 'I', 15);
		//$pdf->SetY(-58);
		$pdf->Ln(32);
		$pdf->SetTextColor(0, 0, 0);
		$pdf->Cell(40, 25, $UniqueString, '0', '1', 'R');
		//$pdf->SetX(29);
		//$pdf->SetY(-50);
		//$image4 = $path . "/pdf/images/bottom_line.png";
		//$pdf->Image($image4, $pdf->GetX(), $pdf->GetY(), 20, 0);
		//$pdf->SetY(-50);
		//$pdf->SetX(80);
		//$image3 = $path . "/pdf/images/iitb.png";
		//$image2 = $path . "/pdf/images/fossee.png"; 
		
		//$pdf->Ln(8);
		//$pdf->Image($image2, $pdf->GetX() +15, $pdf->GetY() + 7, 40, 0);
		//$pdf->Ln(6);
		$pdf->SetY(150);
		$pdf->SetX(800);
		//$pdf->Ln(2);
	}
	else
	{
		$image_bg = $mpath . "/pdf/images/bg_cert.png";
		//$pdf->Image($image_bg, 0, 0, $pdf->w, $pdf->h);
		$pdf->Image($image_bg, 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight());
		//$pdf->Rect(5, 5, 267, 207, 'D');
		$pdf->SetMargins(18, 1, 18);
		//$pdf->Line(7.0, 7.0, 270.0, 7.0);
		//$pdf->Line(7.0, 7.0, 7.0, 210.0);
		//$pdf->Line(270.0, 210.0, 270.0, 7.0);
		//$pdf->Line(7.0, 210.0, 270.0, 210.0);
		$path   = drupal_get_path('module', 'cfd_case_study');
		//$image1 = $mpath . "/pdf/images/dwsim_logo.png";
		$pdf->Ln(30);
		//$pdf->Cell(200, 8, $pdf->Image($image1, 105, 15, 0, 28), 0, 1, 'C');
		//$pdf->Ln(20);
		
		//$pdf->SetTextColor(139, 69, 19);
		//$pdf->Cell(240, 8, 'Certificate of Participation', '0', 1, 'C');
		//$pdf->Ln(26);
		$pdf->SetFont('Times', 'I', 18);
		$pdf->SetTextColor(0, 0, 0);
		$pdf->Cell(320, 10, 'This is to certify that', '0', '1', 'C');
		$pdf->Ln(0);
		$pdf->SetFont('Times', 'I', 18);
		//$pdf->SetFont('Arial', 'BI', 25);
		$pdf->SetTextColor(37, 22, 247);
		$pdf->Cell(320, 10, $data3->name_title . '. ' . $data3->contributor_name, '0', '1', 'C');
		$pdf->Ln(0);
		$pdf->SetFont('Times', 'I', 18);
		if (strtolower($data3->branch) != "others") {
			$title = WordWrap($data3->project_title,60);
			$university = 'from ' . $data3->university;
			$pdf->SetTextColor(0, 0, 0);
			//$pdf->Cell(240, 8, 'from ' . $data3->university . ' has successfully', '0', '1', 'C');
			$pdf->MultiCell(320, 10, $university, '0','C');
			$pdf->Ln(0);
			$pdf->Cell(320, 10, 'has successfully completed the case study on', '0', '1', 'C');
			$pdf->Ln(0);
			$pdf->SetTextColor(37, 22, 247);
			$pdf->SetFont('Times','I',20);
			$pdf->MultiCell(320, 10, $title, '0', 'C');
			$pdf->SetTextColor(0, 0, 0);
			$pdf->Ln(0);
			$pdf->SetFont('Times','I',18);
			$pdf->Cell(320, 8, 'using OpenFOAM.', '0', '1', 'C');
			$pdf->SetTextColor(0, 0, 0);
			$pdf->Ln(4);
			$pdf->Cell(320, 8, 'The work done is available at', '0', '1', 'C');
			$pdf->Cell(320, 4, '', '0', '1', 'C');
			$pdf->SetX(130);
			$pdf->SetFont('Times', 'I', 'U');
			$pdf->SetTextColor(37, 22, 247);
			$pdf->write(0, 'https://cfd.fossee.in/case-study-project', 'https://cfd.fossee.in/case-study-project');
			$pdf->Ln(0);
			//$pdf->SetFont('Times', 'I', 16);
			//$pdf->Cell(320, 8, 'under Case Study Project', '0', '1', 'C');
			//$pdf->Ln(0);
			//$pdf->SetFont('Helvetica', '', 18);
			//$pdf->Cell(0, 0, ' Case Study Project', '0', '0', 'C');
			//$pdf->Cell(240, 4, '', '0', '1', 'C');
			//$pdf->SetX(120);
			//$pdf->SetFont('', 'U');
			//$pdf->SetTextColor(139, 69, 19);
			//$pdf->write(0, 'http://CFD.fossee.in/', 'http://CFD.fossee.in/');
			//$pdf->Ln(0);
			//$pdf->Cell(240, 8, 'Book: ' . $data2->book . ', Author: ' . $data2->author . '.', '0', '1', 'C');
			//$pdf->MultiCell(240, 8, 'Book: ' . $data2->book . ', Author: ' . $data2->author . '.', '0','C');
			$pdf->Ln(0);
		} //strtolower($data3->branch) != "others"
		else {
			$pdf->SetTextColor(0, 0, 0);
			$pdf->Cell(240, 8, 'from ' . $data3->university . ' college', '0', '1', 'C');
			$pdf->Ln(0);
			$pdf->Cell(240, 8, 'has successfully completed the case study of', '0', '1', 'C');
			$pdf->Ln(0);
			$pdf->SetTextColor(139, 69, 19);
			$pdf->Cell(320, 12, $data3->project_title, '0', '1', 'C');
			$pdf->SetTextColor(0, 0, 0);
			$pdf->Ln(0);
			$pdf->SetFont('Times', '', 16);
			$pdf->Cell(320, 8, ' under Case Study Project', '0', '1', 'C');
			//$pdf->Cell(240, 8, 'He/she has coded ' . $number_of_example . ' solved examples using DWSIM from the', '0', '1', 'C');
			//$pdf->Ln(0);
			//$pdf->Cell(240, 8, 'Book: ' . $data2->book . ', Author: ' . $data2->author . '.', '0', '1', 'C');
			//$pdf->Ln(0);
		}
		$proposal_get_id = 0;
		$UniqueString    = "";
		$tempDir         = $path . "/pdf/temp_prcode/";
		$query           = db_select('case_study_qr_code');
		$query->fields('case_study_qr_code');
		$query->condition('proposal_id', $proposal_id);
		$result          = $query->execute();
		$data            = $result->fetchObject();
		$DBString        = $data->qr_code;
		$proposal_get_id = $data->proposal_id;
		if ($DBString == "" || $DBString == "null") {
			$UniqueString = generateRandomString();
			$query        = "
					INSERT INTO case_study_qr_code
					(proposal_id,qr_code)
					VALUES
					(:proposal_id,:qr_code)
					";
			$args         = array(
				":proposal_id" => $proposal_id,
				":qr_code" => $UniqueString
			);
			$result       = db_query($query, $args, array(
				'return' => Database::RETURN_INSERT_ID
			));
		} //$DBString == "" || $DBString == "null"
		else {
			$UniqueString = $DBString;
		}
		$codeContents        = "https://cfd.fossee.in/case-study-project/certificates/verify/" . $UniqueString;
		$fileName            = 'generated_qrcode.png';
		$pngAbsoluteFilePath = $tempDir . $fileName;
		$urlRelativeFilePath = $path . "/pdf/temp_prcode/" . $fileName;
		QRcode::png($codeContents, $pngAbsoluteFilePath);
		$pdf->SetY(85);
		$pdf->SetX(320);
		$pdf->Ln(25);
		$sign = $path . "/pdf/images/sign.png";
		$pdf->Image($sign, $pdf->GetX()+70, $pdf->GetY() + 40, 80, 0);
		$pdf->Image($pngAbsoluteFilePath, $pdf->GetX() + 206 , $pdf->GetY() + 40, 25,25, 0);
		//$pdf->Cell(240, 8, 'Prof. Kannan M. Moudgalya', 0, 1, 'R');
		//$pdf->SetX(199);
		//$pdf->SetFont('Arial', '', 10);
		//$pdf->Cell(0, 7, 'Co - Principal Investigator - FOSSEE', 0, 1, 'L');
		//$pdf->SetX(190);
		//$pdf->Cell(0, 7, ' Dept. of Chemical Engineering, IIT Bombay.', 0, 1, 'L');
		//$pdf->SetX(29);
		$pdf->SetFont('Times', 'I', 15);
		//$pdf->SetY(-58);
		$pdf->Ln(32);
		$pdf->SetTextColor(0, 0, 0);
		$pdf->Cell(228, 8, $UniqueString, '0', '1', 'R');
		//$pdf->SetX(29);
		//$pdf->SetY(-50);
		//$image4 = $path . "/pdf/images/bottom_line.png";
		//$pdf->Image($image4, $pdf->GetX(), $pdf->GetY(), 20, 0);
		//$pdf->SetY(-50);
		//$pdf->SetX(80);
		//$image3 = $path . "/pdf/images/iitb.png";
		//$image2 = $path . "/pdf/images/fossee.png"; 
		
		//$pdf->Ln(8);
		//$pdf->Image($image2, $pdf->GetX() +15, $pdf->GetY() + 7, 40, 0);
		//$pdf->Ln(6);
		$pdf->SetY(150);
		$pdf->SetX(800);
		//$pdf->Ln(2);
	}

	//$pdf->Image($image3, $pdf->GetX() + 200, $pdf->GetY() -3, 15, 0);
	//$pdf->Image($image4, $pdf->GetX() +50, $pdf->GetY() + 28, 150, 0);
	$pdf->SetFont('Arial', 'I', 8);
	$pdf->SetTextColor(0, 0, 0);
	$filename = str_replace(' ', '-', $data3->contributor_name) . '-CFD-case-study-Certificate.pdf';
	$file     = $path . '/pdf/temp_certificate/' . $proposal_id . '_' . $filename;
	$pdf->Output($file, 'F');
	ob_clean();
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: public");
	header("Content-Description: File Transfer");
	header("Content-Type: application/pdf");
	header("Content-Disposition: attachment; filename=" . $filename);
	header("Content-Length: " . filesize($file));
	header("Content-Transfer-Encoding: binary");
	header("Expires: 0");
	header("Pragma: no-cache");
	flush();
	$fp = fopen($file, "r");
	while (!feof($fp)) {
		echo fread($fp, filesize($file));
		flush();
	} //!feof($fp)
	ob_end_flush();
	ob_clean();
	fclose($fp);
	unlink($file);
	//drupal_goto('flowsheeting-project/certificate');
	return;
}
function generateRandomString($length = 5)
{
	$characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString     = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	} //$i = 0; $i < $length; $i++
	return $randomString;
}
function generate_copyright_form_pdf()
{
	$mpath = drupal_get_path('module', 'circuit_simulation');
	require($mpath . '/pdf/fpdf/fpdf.php');
	global $user;
	$x           = $user->uid;
	$proposal_id = arg(3);
	$query2 = db_query("SELECT id FROM case_study_proposal WHERE approval_status=3 AND uid= :uid", array(
		':uid' => $user->uid
	));
	$data2             = $query2->fetchObject();
	$query3 = db_query("SELECT * FROM case_study_proposal WHERE approval_status=3 AND uid= :uid", array(
		':uid' => $user->uid
	));
	$data3       = $query3->fetchObject();
	$gender      = array(
		'salutation' => 'Mr. /Ms.',
		'gender' => 'He/She'
	);
	if ($data3->gender) {
		if ($data3->gender == 'M') {
			$gender = array(
				'salutation' => 'Mr.',
				'gender' => 'He'
			);
		} //$data3->gender == 'M'
		else {
			$gender = array(
				'salutation' => 'Ms.',
				'gender' => 'She'
			);
		}
	} //$data3->gender
	$pdf = new FPDF('P', 'mm', 'Letter');
	if (!$pdf) {
		echo "Error!";
	} //!$pdf
	$pdf->AddPage();
	$path = drupal_get_path('module', 'cfd_case_study');
	$pdf->SetFont('Arial', 'B', 25);
	$pdf->Ln(30);
	$pdf->Cell(200, 8, 'Copyright Transfer Form', 0, 1, 'C');
	$pdf->Ln(20);
	$pdf->SetFont('Arial', '', 12);
	$pdf->MultiCell(200, 8, 'I hereby transfer the copyrights of the Case study for ' . $data2->project_title . ' to FOSSEE Project, IIT Bombay.', 0);
	$pdf->Ln(10);
	$pdf->MultiCell(200, 8, 'I  understand  that  the  FOSSEE  project  will  release  the  Flowsheet  under  the Creative Commons (CC) license.');
	$pdf->SetX(75);
	$pdf->SetFont('', 'U');
	$pdf->SetTextColor(0, 0, 255);
	$pdf->SetFont('', '');
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('', '');
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetY(-10);
	$pdf->SetX(209);
	$cur_date = date('jS F, Y');
	$pdf->SetY(140);
	$pdf->SetFont('', '');
	$pdf->Ln(0);
	$pdf->Cell(200, 0, ' Date: ' . $cur_date . '', 0, 1, 'L');
	$pdf->Cell(200, 20, ' Place: _________________', 0, 1, 'L');
	$pdf->SetY(140);
	$pdf->SetX(120);
	$pdf->Cell(180, 0, 'Signature: _________________', 0, 1, 'L');
	$pdf->SetY(144);
	$pdf->SetX(120);
	$pdf->multicell(140, 14, 'Name: ' . $gender['salutation'] . ' ' . $data3->full_name . '', 0, '');
	$filename = str_replace(' ', '-', $data3->full_name) . '-CFD-case-study-copyright-form.pdf';
	$file     = $path . '/pdf/temp_certificate/' . $proposal_id . '_' . $filename;
	$pdf->Output($file, 'F');
	header("Content-Type: application/octet-stream");
	header("Content-Disposition: attachment; filename=" . $filename);
	header("Content-Type: application/octet-stream");
	header("Content-Type: application/download");
	header("Content-Description: File Transfer");
	header("Content-Length: " . filesize($file));
	flush();
	$fp = fopen($file, "r");
	while (!feof($fp)) {
		echo fread($fp, 65536);
		flush();
	} //!feof($fp)
	fclose($fp);
	unlink($file);
	drupal_goto('Summer_Internship_Forms/forms');
	return;
}
function generate_undertaking_form_pdf()
{
	$mpath = drupal_get_path('module', 'circuit_simulation');
	require($mpath . '/pdf/fpdf/fpdf.php');
	global $user;
	$x           = $user->uid;
	$proposal_id = arg(3);
	$query2 = db_query("SELECT id FROM case_study_proposal WHERE approval_status=3 AND uid= :uid", array(
		':uid' => $user->uid
	));
	$data2             = $query2->fetchObject();
	$query3 = db_query("SELECT * FROM case_study_proposal WHERE approval_status=3 AND uid= :uid", array(
		':uid' => $user->uid
	));
	$data3       = $query3->fetchObject();
	$gender      = array(
		'salutation' => 'Mr. /Ms.',
		'gender' => 'He/She'
	);
	if ($data3->gender) {
		if ($data3->gender == 'M') {
			$gender = array(
				'salutation' => 'Mr.',
				'gender' => 'He'
			);
		} //$data3->gender == 'M'
		else {
			$gender = array(
				'salutation' => 'Ms.',
				'gender' => 'She'
			);
		}
	} //$data3->gender
	$pdf = new FPDF('P', 'mm', 'Letter');
	if (!$pdf) {
		echo "Error!";
	} //!$pdf
	$pdf->AddPage();
	$path = drupal_get_path('module', 'cfd_case_study');
	$pdf->SetFont('Arial', 'B', 25);
	$pdf->Ln(30);
	$pdf->Cell(200, 8, 'Undertaking Form', 0, 1, 'C');
	$pdf->Ln(0);
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->Cell(200, 8, '(To be signed by college teacher)', 0, 1, 'C');
	$pdf->Ln(20);
	$pdf->SetFont('Arial', '', 12);
	$pdf->MultiCell(200, 8, 'I hereby certify that all the codes written by ' . $gender['salutation'] . ' ' . $data3->full_name . ' under the DWSIM Textbook Companion Project for the book ' . $data2->book . ' ( Author: ' . $data2->author . ', Edition: ' . $data2->edition . ', Publisher: ' . $data2->publisher . ', Year: ' . $data2->year . ') are correctly reproducing the results given in the aforementioned book.', 0);
	$pdf->Ln(10);
	$pdf->MultiCell(200, 8, 'I understand that the DWSIM Textbook Companion created is a part of FOSSEE project, IIT Bombay, and is sponsored by the National Mission on Education through Information and Communication Technology (NMEICT), under MHRD, Govt. of India. The project requires that the textbook companion is made available for public access as an open source document. Hence I undertake that this DWSIM Textbook Companion can be made public along with the information that I have certified all the codes as giving the correct answer.');
	$pdf->SetX(75);
	$pdf->SetFont('', 'U');
	$pdf->SetTextColor(0, 0, 255);
	$pdf->SetFont('', '');
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('', '');
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetY(-10);
	$pdf->SetX(209);
	$cur_date = date('jS F, Y');
	$pdf->SetY(180);
	$pdf->SetFont('', '');
	$pdf->Ln(0);
	$pdf->Cell(200, 0, ' Date: ' . $cur_date . '', 0, 1, 'L');
	$pdf->Cell(200, 20, ' Place: _________________', 0, 1, 'L');
	$pdf->SetY(180);
	$pdf->SetX(120);
	$pdf->Cell(140, 0, 'Signature: _________________', 0, 1, 'L');
	$pdf->SetX(120);
	$pdf->multicell(140, 14, 'Name: ' . '____________________', 0, '');
	$pdf->SetX(120);
	$pdf->multicell(140, 14, 'Designation: ' . '______________', 0, '');
	$filename = str_replace(' ', '-', $data3->full_name) . '-CFD-case-study-undertaking-form.pdf';
	$file     = $path . '/pdf/temp_certificate/' . $proposal_id . '_' . $filename;
	$pdf->Output($file, 'F');
	header("Content-Type: application/octet-stream");
	header("Content-Disposition: attachment; filename=" . $filename);
	header("Content-Type: application/octet-stream");
	header("Content-Type: application/download");
	header("Content-Description: File Transfer");
	header("Content-Length: " . filesize($file));
	flush();
	$fp = fopen($file, "r");
	while (!feof($fp)) {
		echo fread($fp, 65536);
		flush();
	} //!feof($fp)
	fclose($fp);
	unlink($file);
	drupal_goto('Summer_Internship_Forms/forms');
	return;
}
