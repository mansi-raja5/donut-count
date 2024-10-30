<?php
Class Masterpos_model extends CI_Model {

    function add($table,$data) { 
    	// check if entry is there date and store
        if($table=="master_pos_weekly"):
        	$query = $this->db->get_where($table, array(
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'store_key' => $data['store_key'],
            ));
        elseif($table=="master_pos_monthly"):
                $query = $this->db->get_where($table, array(
                    'start_date' => $data['start_date'],
                    'end_date' => $data['end_date'],
                    'store_key' => $data['store_key'],
                ));            
        else:
            $query = $this->db->get_where($table, array(
                'cdate' => $data['cdate'],
                'store_key' => $data['store_key'],
            ));
        endif;

        $count = $query->num_rows(); 
        $row = $query->row();
        if ($count === 0) {
            $this->db->insert($table, $data);
            $data['id'] = $this->db->insert_id();
            $data['type'] = 'add';
        }else{
        	$this->db->where('id', $row->id);
        	$this->db->update($table, $data);
            $data['id'] = $row->id;
            $data['type'] = 'update';
        }
        return $data;
    }
    function getmonthlyrecapkeys(){
        $this->db->from('dynamic_monthlyrecap_column');
        $result = $this->db->get();
        $return = array();
        if($result->num_rows() > 0) {
            foreach($result->result_array() as $row) {
            $return[] = $row['key_name'];
            }
        }
        return $return;
    }
    function getallkey(){
        $this->db->from('pos_master_key');
        $result = $this->db->get();
        $return = array();
        if($result->num_rows() > 0) {
            foreach($result->result_array() as $row) {
            $return[$row['key_name']] = $row['key_label'];
            }
        }
        return $return;
    }

    function checkKeyExist($key){
        $query = $this->db->get_where("pos_master_key", array(
                'key_label' => $key
            ));
        $count = $query->num_rows(); 
        $row = $query->row();
        if ($count === 0) {
           return true;
        }else{
           return false;
        }
    }

    function query_result($sql = FALSE) {
        if ($sql) {
            $query = $this->db->query($sql);
            return $query->result();
        }else{
            return FALSE;
        }
    }

    /*** 
     * $uniqueKeysFromSheet = storekey - startdate - enddate
     * 
     * function seraches for daily pos data which are present in database and locked
    */
    function locked_entry_Res($uniqueKeysFromSheet){
        /*$this->db->select("concat(store_key,DATE_FORMAT(cdate, '%Y%m%d'),DATE_FORMAT(cdate, '%Y%m%d')) as locked_uid");
        $this->db->where_in("concat(store_key,DATE_FORMAT(cdate, '%Y%m%d'),DATE_FORMAT(cdate, '%Y%m%d'))", $uniqueKeysFromSheet);
        $this->db->where("is_lock", 1);
        $query = $this->db->get('master_pos_daily');*/

        //get locked entris from pos_daily
        $posData = [];
        $sql = "SELECT store_key,date(cdate) as cdate,data,concat(store_key,DATE_FORMAT(cdate, '%Y%m%d'),DATE_FORMAT(cdate, '%Y%m%d')) as locked_uid FROM `master_pos_daily` WHERE concat(store_key,DATE_FORMAT(cdate, '%Y%m%d'),DATE_FORMAT(cdate, '%Y%m%d')) IN('".implode("','",$uniqueKeysFromSheet) ."') AND `is_lock` = 1";
        $query = $this->db->query($sql);
        $resultArray = $query->result_array();
        foreach($resultArray as $_resultArray) {
            $uid = $_resultArray['locked_uid'];
            unset($_resultArray['locked_uid']);
            $posData[$uid]['data'] = $_resultArray;
        }

        //get locked entries from week
        $sql = "SELECT store_key,date(start_date) as start_date,date(end_date) as end_date,
                        data,
                        concat(store_key,
                                DATE_FORMAT(start_date, '%Y%m%d'),
                                DATE_FORMAT(end_date, '%Y%m%d')) as locked_uid 
                        FROM `master_pos_weekly` 
                        WHERE concat(store_key,
                            DATE_FORMAT(start_date, '%Y%m%d'),
                            DATE_FORMAT(end_date, '%Y%m%d')) IN('".implode("','",$uniqueKeysFromSheet) ."') AND `is_lock` = 1";
        $query = $this->db->query($sql);
        $resultArray = $query->result_array();
        foreach($resultArray as $_resultArray) {
            $uid = $_resultArray['locked_uid'];
            unset($_resultArray['locked_uid']);
            $posData[$uid]['data'] = $_resultArray;
        }

        //get locked entries from week
        $sql = "SELECT store_key,date(start_date) as start_date,date(end_date) as end_date,
                        data,
                        concat(store_key,
                                DATE_FORMAT(start_date, '%Y%m%d'),
                                DATE_FORMAT(end_date, '%Y%m%d')) as locked_uid 
                        FROM `master_pos_monthly` 
                        WHERE concat(store_key,
                            DATE_FORMAT(start_date, '%Y%m%d'),
                            DATE_FORMAT(end_date, '%Y%m%d')) IN('".implode("','",$uniqueKeysFromSheet) ."') AND `is_lock` = 1";
        $query = $this->db->query($sql);
        $resultArray = $query->result_array();
        foreach($resultArray as $_resultArray) {
            $uid = $_resultArray['locked_uid'];
            unset($_resultArray['locked_uid']);
            $posData[$uid]['data'] = $_resultArray;
        }        
        return $posData;
    }
}