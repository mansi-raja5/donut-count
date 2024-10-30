<?php

class PDFT extends FPDF {

    function Header() {
        //get nurse information
        if ($this->PageNo() == 1) {

            $this->SetXY(10, 29);
            $this->SetFont('helvetica', '', 10);
            if (isset($_POST['print_page']) && $_POST['print_page'] == 'L') {
                $this->Line(10, 28, 285, 28);
            } else {
                $this->Line(10, 28, 200, 28);
            }
//            $this->SetWidths(array(17,28,12,20,45,22,25,25));
//            $this->Row(array("DATE ","TRANSACTION TYPE","NUM","NAME","MEMO/DESCRIPTION","ACCOUNT","DEBIT","CREDIT"));
            if (isset($_POST['print_page']) && $_POST['print_page'] == 'L') {
                $this->SetWidths(array(17, 90, 49, 50, 34, 34));
            } else {
                $this->SetWidths(array(17, 60, 39, 30, 24, 24));
            }
            $this->SetAligns(array('L', 'L', 'L', 'L', 'R', 'R'));
            $this->Row(array("DATE", "ACCOUNTS", "NUM", "NAME", "DEBIT", "CREDIT"));
            if (isset($_POST['print_page']) && $_POST['print_page'] == 'L') {
                $this->Line(10, 28, 285, 28);
            } else {
                $this->Line(10, 40, 200, 40);
            }
        } else {
            $this->SetXY(10, 13);
            $this->SetFont('helvetica', '', 10);
            if (isset($_POST['print_page']) && $_POST['print_page'] == 'L') {
                $this->Line(10, 28, 285, 28);
            } else {
                $this->Line(10, 12, 200, 12);
            }
//            $this->SetWidths(array(17,28,12,20,45,22,25,25));
//            $this->Row(array("DATE ","TRANSACTION TYPE","NUM","NAME","MEMO/DESCRIPTION","ACCOUNT","DEBIT","CREDIT"));
            if (isset($_POST['print_page']) && $_POST['print_page'] == 'L') {
                $this->SetWidths(array(17, 90, 49, 50, 34, 34));
            } else {
                $this->SetWidths(array(17, 60, 39, 30, 24, 24));
            }
            $this->SetAligns(array('L', 'L', 'L', 'L', 'R', 'R'));
            $this->Row(array("DATE", "ACCOUNTS", "NUM", "NAME", "DEBIT", "CREDIT"));
            if (isset($_POST['print_page']) && $_POST['print_page'] == 'L') {
                $this->Line(10, 28, 285, 28);
            } else {
                $this->Line(10, 23, 200, 23);
            }
        }
    }

    function Footer() {
        // Go to 1.5 cm from bottom
        $this->SetY(-15);
        // Select Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Print centered page number
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'R');
    }

}
