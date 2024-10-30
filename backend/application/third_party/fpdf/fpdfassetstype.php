<?php


class PDFAT extends FPDF {
    function Header() {
        //get nurse information
        if($this->PageNo() == 1){
            $this->SetXY(10, 29);
            $this->SetFont('helvetica', '', 8);
            $this->Line(10, 28, 200, 28);
            $this->SetWidths(array(13,25,18,16,14,20,20,25,10,30));
            $this->Row(array("Assets Number",
                "Assets Name",
                "Date Inservice",
                "Assets Type",
                "Purchase Price",
                "Current Book Value",
                "Accumulated Depreciation",
                "Ending Depreciation Year",
                "Useful Life",
                "Method"));
            $this->Line(10, 40, 200, 40);
        }else{
            $this->SetXY(10, 13);
            $this->SetFont('helvetica', '', 8);
            $this->Line(10, 12, 200, 12);
            $this->SetWidths(array(13,25,18,16,14,20,20,25,10,30));
            $this->Row(array("Assets Number",
                "Assets Name",
                "Date Inservice",
                "Assets Type",
                "Purchase Price",
                "Current Book Value",
                "Accumulated Depreciation",
                "Ending Depreciation Year",
                "Useful Life",
                "Method"));
            $this->Line(10, 23, 200, 23);
        }
    }
    function Footer()
    {
        // Go to 1.5 cm from bottom
        $this->SetY(-15);
        // Select Arial italic 8
        $this->SetFont('Arial','I',8);
        // Print centered page number
        $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'R');
    }
}