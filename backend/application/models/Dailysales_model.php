<?php
Class Dailysales_model extends CI_Model {

    function add($table,$data) { 
    	// check if entry is there date and store
    	$query = $this->db->get_where($table, array(
            'cdate' => $data['cdate'],
            'store_key' => $data['store_key'],
        ));

        $count = $query->num_rows(); 
        $row = $query->row();
        if ($count === 0) {
            $this->db->insert($table, $data);
        	$guid = $this->db->insert_id();
        }else{
        	$this->db->where('id', $row->id);
        	$this->db->update($table, $data);
        	$guid = $this->db->insert_id();
        }
       
        return true;
    }

    function getAllColumn(){
        $query = $this->db->select("key_name,column_name")->get_where("dynamic_dailysales_column", array(
                'is_active' => 1
            ));
        $return = array();
        if($query->num_rows() > 0) {
            foreach($query->result_array() as $row) {
            $return[$row['key_name']] = $row['column_name'];
            }
        }
        return $return;
    }
     public function adddailydata($table,$insert_array) { 
        // check if entry is there date and store
        $query = $this->db->get_where($table, array(
            'cdate' => $insert_array['cdate'],
            'store_key' => $insert_array['store_key'],
        ));

        $count = $query->num_rows(); 
        $row = $query->row();
        if ($count === 0) {
            $this->db->insert($table, $insert_array);
            $guid = $this->db->insert_id();
        }else{
            $this->db->where('id', $row->id);
            $this->db->update($table, $insert_array);
            $guid = $this->db->insert_id();
        }
       
        return true;
    }
    
    
    public function getColumns(){
        $query = $this->db->select("key_name,column_name")->get_where("dynamic_dailysales_column", array(
                'is_active' => 1
            ));
        $return = array();
        $return[] = array("key"=>"","value"=>'-- Select Description  --');
        if($query->num_rows() > 0) {
            foreach($query->result_array() as $row) {
                $return[] = array("key"=>$row['key_name'],"value"=>$row['column_name']);
            }
        }
        return $return;
    }

    public function getdescription(){
        $this->db->select('DISTINCT description', false);
        $query = $this->db->get('ledger_statement');
        $return = array();
        $return[] = array("description"=>'-- Select Description  --');
        if($query->num_rows() > 0) {
            foreach($query->result_array() as $row) {
                $return[] = array("description"=>$row['description']);
            }
        }
        return $return;
    }

    //DAILY SAES GRID ADD DATA
    public function getDailysalesGridRows($store_key,$month,$year){
        $html="";
        $data = [];
        $data['store_key']  = $this->input->post('store_key');
        $data['month']  = $this->input->post('month');
        $data['year']  = $this->input->post('year');
        $alldates = $this->get_dates($month,$year);
        $dynamic_column = $this->getAllColumn();
        // echo "<pre>";
        // print_r($alldates);
        $paid_out = $this->paid_out($data);
        $amt_paidout = "0.00";
        foreach($alldates as $counter=>$value){
            $getdata = $this->getInserteddailysales($data,$value);

            $html .= "<tr id='row_".$counter."'>";
            $html.="<td><input type='hidden' name='cdate[]' id='cdate_$counter' value='".$value."'>".$value."</td>";
            $html.="<td style='width:50%'><input type='hidden' name='day[]' value='".date('D',strtotime($value))."'>".date('D',strtotime($value))."</td>";
            $setvalue = "";
            $disabled = "";
            $button = "";
            $chkdisabled = "";
            $actual_total ='0.00';
            if(sizeof($getdata) > 0 ) {
                $chkdisabled = $getdata[0]["is_lock"];
                $actual_total = $getdata[0]["total"];
                if($chkdisabled==1){
                    $disabled = "readonly";
                    $button ="<input type='button' value='Unlock' onclick='dailysales.setlock(this,$counter)' name='lockrow' id='lock_$counter' class='btn btn-sm primary' lockrow>";
                }else{
                    $button ="<input type='button' value='lock' onclick='dailysales.setlock(this,$counter)' name='unlockrow' id='lock_$counter' class='btn btn-sm primary unlockrow'>";
                }
            }else{
                  $button ="<input type='button' value='lock' onclick='dailysales.setlock(this,$counter)' name='lockrow' id='lock_$counter' class='btn btn-sm primary' lockrow>";
            }
            if(sizeof($dynamic_column)) {
                foreach($dynamic_column as $key=>$dvalue){
                    //check if any invoice is uploaded for this column or not
                    $noofinvoice = $this->checkalreadyuploadinvoice($data,$key,$value);
                    $style="display:none";
                    if($noofinvoice > 0){
                        $style="display:block";
                    }
                    if(sizeof($getdata) > 0 ) 
                        $setvalue = $getdata[0][$key];
                   
                    $html.="<td><input type='number' name='".$key."[]' id='".$key."_$counter' style='width:100%'   onfocusout='dailysales.calculatetotal(this,".$counter.",2)' value='".$setvalue."' ".$disabled." step=1><i class='fa fa-paperclip pull-right' id='alreadyupload-$key"."_$counter' style='".$style."'></i> <a  href='javascript:void(0);' id='addattachment-$key' data-id='addattachment_".$counter."'  onclick='dailysales.display_attachment(this,".$counter.")'><span class='fa fa-upload pull-right'></span></a></td>";
               }
            }
            $html.="<td><input type='hidden' value='".$actual_total ."' name='total[]' id='total_".$counter."'><label id='lbltotal_".$counter."'>".$actual_total."</label></td>";
            if(array_key_exists($value,$paid_out)){
                $amt_paidout = $paid_out[$value];
            }
            $html.="<td><input type='hidden' value='".$amt_paidout."' name='paidout[]' id='paidout_".$counter."'><label id='paidout_".$counter."'>".$amt_paidout."</label></td>";
            $html.="<td><input type='hidden' value='".$chkdisabled."' name='is_lock[]' id='is_lock_".$counter."'>".$button."</td>";
            $html.="</tr>";
        }
        // exit;
        return $html;
    }
    public function get_dates($month,$year){
        $numbers = array('1','2','3','4','5','6','7','8','9');
        $datesArray = array();
        $num_of_days = date('t',mktime (0,0,0,$month,1,$year));
        if(in_array($month,$numbers)) $month = '0'.$month;
        for( $i=1; $i<= $num_of_days; $i++) {
            if(in_array($i,$numbers)) $i = '0'.$i;
            $datesArray[] = $i.'-'.$month.'-'.$year;
        }
        return $datesArray;
    }

    //get already inserted daily sales data
    public function getInserteddailysales($data,$cdate){
        $query = $this->db->select("*")->like('cdate', date('Y-m-d',strtotime($cdate)))->where('store_key',$data['store_key'])->get("paid_out_recap");
        $return = array();
        if($query->num_rows() > 0) {
            $return = $query->result_array();
        }
        return $return;
    }
    public function paid_out($data){
        $list=array();
        $month = $data['month'];
        $year = $data['year'];
        $days = cal_days_in_month( 0, $month, $year);
        for($d=1; $d<=$days; $d++)
        {
            $time=mktime(12, 0, 0, $month, $d, $year);          
            if (date('m', $time)==$month)       
                $list[]=date('Y-m-d', $time);
        }
        $query = $this->db->select("cdate,data,store_key")->where('store_key',$data['store_key'])->where_in('cdate', $list)->get("master_pos_daily");
        $return = array();
        $query->num_rows();
        if($query->num_rows() > 0) {
            foreach($query->result_array() as $row) {
                $paid_out = "";
                $jsondata = json_decode($row['data']);
               foreach($jsondata as $value){
                    $jdata = (array) $value;
                    if(array_key_exists("paid_out",$jdata))
                        $paid_out= $jdata['paid_out'];
               }
                $return[date("d-m-Y",strtotime($row['cdate']))] = $paid_out;
            }
        }
        return $return;
    }
    function adddailysales($data){
        // echo "<pre>";
        // print_r($_FILES);
        // print_r($data);exit;
        $counter = sizeof($data['cdate']);
        $store_key = $data['store_key'];
        $year = $data['year'];
        $month = $data['month'];
        $insert_array = array();
        $dynamic_column = $this->getAllColumn();
         $month_arr = array("January", "February", "March", "April", "May", "Jun", "July", "August", "September", "October", "November", "December");
        for($i=0;$i<$counter;$i++){
            $insert_array['store_key'] = $store_key;
            $insert_array['year'] = $year;
            $insert_array['month'] = $month_arr[$month-1];
            $insert_array['day'] = $data['day'][$i];
            $insert_array['cdate'] = date("Y-m-d H:i:s",strtotime($data['cdate'][$i]));
            foreach($dynamic_column as $key=>$value) {
                $insert_array[$key] = $data[$key][$i];
            }
            $insert_array['total'] = $data['total'][$i];
            $insert_array['is_lock'] = $data['is_lock'][$i];
            $this->adddailydata('paid_out_recap',$insert_array);
        }
        return true;
    }
   
   function adduploadattachment($data){
        $insert_array = array();
        $guid = 0;
        $month_arr = array("January", "February", "March", "April", "May", "Jun", "July", "August", "September", "October", "November", "December");
        if(!empty($_FILES["files"]["name"])){ 
            $insert_array['store_key'] = $data['store_key'];
            $insert_array['month'] =  $month_arr[$data['month']-1];
            $insert_array['year'] = $data['year'];
            $insert_array['dynamic_column_id'] = $data['dynamic_column'];  
            $insert_array['cdate'] =date("Y-m-d",strtotime($data['hd_cdate']));  
            // File path config 
            $fileName = basename($_FILES["files"]["name"]); 
            $targetPath =  FCPATH ."/files_upload/daily_sales_upload/".$data['store_key']."/".$data['year']."/".$data['month']."/".date("Ymd",strtotime($data['hd_cdate']))."/invoice/";
            $uploaded_url = "/files_upload/daily_sales_upload/".$data['store_key']."/".$data['year']."/".$data['month']."/".date("Ymd",strtotime($data['hd_cdate']))."/invoice/";
           
             if (!file_exists($targetPath)) {
                mkdir($targetPath, 0777, true);
            }
            $tmp        = explode('.', $_FILES["files"]["name"]);
            $file_ext   = end($tmp);
            $uploaded_file_name = $this->checkuploadsetting($data['dynamic_column']);
            if($uploaded_file_name!=""){
                $uploaded_file_name = $uploaded_file_name . "_" . $data['month'] . "_" . $data['year'] . "_" . $data['store_key'] . "." . $file_ext;
            }else{
                $uploaded_file_name = $_FILES['files']['name'];
            }
            $targetFilePath = $targetPath . $uploaded_file_name; 
            if(move_uploaded_file($_FILES["files"]["tmp_name"], $targetFilePath)){ 
                $uploadedFile = $fileName; 
                $insert_array['original_file_name'] = $fileName;
                $insert_array['uploaded_file_name'] = $uploaded_file_name;
                $insert_array['uploaded_url'] = $uploaded_url;

                $this->db->insert('dailysales_attachments_upload', $insert_array);
                $guid = $this->db->insert_id();
            }
        } 
        
        return $guid;
   }

   function checkuploadsetting($dynamic_column){
     $query = $this->db->select("*")->where('description',$dynamic_column)->get("attachment_name_setting");
        $return = array();
        $invoice_name = "";
        if($query->num_rows() > 0) {
            $return = $query->result_array();
            $invoice_name = $return[0]['invoice_name'];
        }
        return $invoice_name;
   }

   function checkalreadyuploadinvoice($data,$dynamic_column,$cdate){
        $month_arr = array("January", "February", "March", "April", "May", "Jun", "July", "August", "September", "October", "November", "December");
        $query = $this->db->select("*")->where(array("store_key"=>$data['store_key'],"year"=>$data['year'],"month"=>$month_arr[$data['month']-1],"dynamic_column_id"=>$dynamic_column))->like("cdate",date("Y-m-d",strtotime($cdate)))->get("dailysales_attachments_upload");
        return $query->num_rows();
   }
}