<?php
Class Settings_model extends CI_Model {

    protected $_excludeCalcData = null;

    //get dynamc column for both paidout and masterpos
    function getDynamiccolumn($table,$key,$columnname){
        $query = $this->db->select("*")->get_where($table, array(
                'is_active' => 1
            ));
        $return = array();
        if($query->num_rows() > 0) {
            foreach($query->result_array() as $row) {
            $return[$row[$key]] = $row[$columnname];
            }
        }
        return $return;
    }
   //common getdata method
    public function getData($where,$table){
        if($where!=null){
            $query = $this->db->select("*")->where($where)->get($table);
        }else{
            $query = $this->db->select("*")->get($table);
        }
        
        $return = array();
        if($query->num_rows() > 0) {
            $return = $query->result_array();

        }
        return $return;
    }
    public function delete($table, $id)
    {
        $query = $this->db->where('id', $id)->delete($table);
        return $this->db->affected_rows();
    }
    public function save($table,$insert_array) {
        // check if entry is there date and store
        if(isset($insert_array['id'])):
            $query = $this->db->get_where($table, array(
                'id' => $insert_array['id'],
            ));

       
        $store_key_Arr  = $insert_array['store_key'];
        $store_key_value_Arr  = $insert_array['key_value'];
        if(isset($store_key_Arr) && !empty($store_key_Arr)){
            for($i = 0; $i < count($store_key_Arr); $i++){
                $my_Arr[] = array("store_key" => $store_key_Arr[$i],
                    "key_value" => $store_key_value_Arr[$i]);
            }
        }
        $ins_data = array();
        $ins_data['key_name'] = $insert_array['key_name'];
        if($insert_array['key_name'] == 'check_number_starting'){
            $ins_data['key_value'] = json_encode($my_Arr);
        }else{
            $ins_data['key_value'] = $insert_array['key_value'];
        }
            $count = $query->num_rows();
            $row = $query->row();
            if ($count === 0) {
                $this->db->insert($table, $ins_data);
                $guid = $this->db->insert_id();
            }else{
                $this->db->where('id', $row->id);
                $this->db->update($table, $ins_data);
                $guid = $this->db->insert_id();
            }
        else:
            $this->db->insert($table, $insert_array);
            $guid = $this->db->insert_id();
        endif;

        return true;
    }
    public function savestore($table,$insert_array) {
        $query = $this->db->get_where($table, array(
                'year' => $insert_array['year'],
                'store_key' => $insert_array['store_key'],
            ));
        $count = $query->num_rows();
        $row = $query->row();
        if ($count === 0) {
            $pos_key = $this->getDynamiccolumn("pos_master_key","key_name","key_label");
            $data = array();
            foreach($pos_key as $key=>$value){
                if($key==$insert_array['pos_key']):
                    $data[$key] = $insert_array['checked'];
                else:
                    $data[$key] = 0;
                endif;
            }
            unset($insert_array['pos_key']);
            unset($insert_array['checked']);
            $insert_array['data'] = json_encode($data);
            $this->db->insert($table, $insert_array);
            $guid = $this->db->insert_id();
        }else{
            $data = array();
            $jsondata = json_decode($row->data);
            foreach($jsondata as $key=>$value):
                if($insert_array['pos_key']==$key):
                    $data[$key] = $insert_array['checked'];
                else:
                    $data[$key] =$value;
                endif;
            endforeach;
            $insert['data'] = json_encode($data);
            $this->db->where('id', $row->id);
            $this->db->update($table, $insert);
            $guid = $this->db->insert_id();
        }
        return true;
   }

    public function getExcludeCalcData() {
        if ($this->_excludeCalcData == null) {
            $excludeCalcData = [];
            $rows = $this->db
                ->select('store_key, from_date, to_date, is_infinite')
                ->from('admin_excludecalculation_settings')
                ->where(array('is_active' => 1))
                ->get()
                ->result();
            foreach ($rows as $row) {
                $excludeCalcData[$row->store_key][] = array(
                    'from_date' => $row->from_date,
                    'to_date'   => $row->is_infinite == 1 ? null : $row->to_date,
                );
            }
            $this->_excludeCalcData = $excludeCalcData;
        }
        return $this->_excludeCalcData;
    }

   // conditional
   public function saveconditional($table,$data) {
        $query = $this->db->get_where($table, array(
                'year' => $data['year'],
                'store_key' => $data['store_key'],
                'pos_key' => $data['pos_key'],
                'expression_type' => $data['expression_type'],
                'value_type' => $data['value_type'],
            ));
        $count = $query->num_rows();
        if ($count === 0) {
            $this->db->insert($table, $data);
            $guid = $this->db->insert_id();
        }else{
            $row = $query->row();
            $this->db->where('id', $row->id);
            $this->db->update($table, $data);
            $guid = $this->db->insert_id();
        }
        return $guid;
    }

    public function dbcleansetting($tables) {
        $this->db->query('SET FOREIGN_KEY_CHECKS=0;');
        foreach ($tables as $_table) {
            $this->db->truncate($_table);
        }
        $this->db->query('SET FOREIGN_KEY_CHECKS=1;');
    }
}