<?php

Class Vendor_model extends CI_Model {

    function Get($id = NULL, $search = array(), $field = NULL, $company_id = NULL) {
        $this->db->select('SQL_CALC_FOUND_ROWS vendor.*', FALSE);
        $this->db->from('vendor');
        if ($field != NULL) {
            $this->db->where('vendor.' . $field, 1);
        }
        if ($id != NULL) {
            // Getting only ONE row
            $this->db->limit('1');
            $this->db->group_by("vendor.id");
            $query = $this->db->where('vendor.id', $id)->get();

            if ($query->num_rows() > 0) {
                $data["records"] = $query->row();
                return $data;
            } else {
                return false;
            }
        } else {
            if (!empty($search)) {
                $defaultSearch = array(
                    'company' => '',
                    'payment_from' => '',
                    'payment_to' => '',
                    'bill_from' => '',
                    'bill_to' => '',
                    'payment_mode' => '',
                );
                $search = array_merge($defaultSearch, $search);

                if ($search['company'] != '') {
                    $this->db->where('company', $search['company']);
                }
                
                if ($search['payment_from'] != '') {
                    $this->db->where('schedule_payment_date >= ', date("Y-m-d", strtotime($search['payment_from'])));
                }
                if ($search['payment_to'] != '') {
                    $this->db->where('schedule_payment_date <= ', date("Y-m-d", strtotime($search['payment_to'])));
                }
                if ($search['bill_from'] != '') {
                    $this->db->where('bill_due_date >= ', date("Y-m-d", strtotime($search['bill_from'])));
                }
                if ($search['bill_to'] != '') {
                    $this->db->where('bill_due_date <= ', date("Y-m-d", strtotime($search['bill_to'])));
                }
                if ($search['payment_mode'] != '') {
                    $this->db->like('preferred_payment_method', $search['payment_mode']);
                }
                                
                
                if (isset($search['order'])) {
                    $order = $search['order'];
                    if ($order[0]['column'] == 0) {
                        $orderby = "company " . strtoupper($order[0]['dir']);
                    } elseif ($order[0]['column'] == 1) {
                         $orderby = "username " . strtoupper($order[0]['dir']);
                    } elseif ($order[0]['column'] == 3) {
                        $orderby = "schedule_payment_date " . strtoupper($order[0]['dir']);
                    } else {
                        $orderby = "username";
                    }
                } else {
                    $orderby = "username";
                }

                $this->db->order_by($orderby);
                if (isset($search['start'])) {
                    $start = $search['start'];
                    $length = $search['length'];
                    if ($length != -1) {
                        $this->db->limit($length, $start);
                    }
                }
            } else {
                $this->db->order_by("name_f ASC");
            }
            $this->db->group_by("vendor.id");
            $query = $this->db->get();
//echo $this->db->last_query();
            $data["records"] = array();
            if ($query->num_rows() > 0) {
                // Got some rows, return as assoc array
                $data["records"] = $query->result();
            }
            $count = $this->db->query('SELECT FOUND_ROWS() AS Count');
            $this->db->from("vendor");
            $data["countTotal"] = $this->db->count_all_results();
            $data["countFiltered"] = $count->row()->Count;
            return $data;
        }
    }

    function Add($data) {
        $this->db->insert('vendor', $data);
        // Get id of inserted record
        $id = $this->db->insert_id();
        return true;
    }

    function Edit($id, $data) {
        $this->db->where('id', $id);
        $result = $this->db->update('vendor', $data);
        // Return
        if ($result) {
            return $id;
        } else {
            return false;
        }
    }

    function Delete($id) {

        $this->db->where('id', $id);
        $this->db->delete('vendor');

        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function GetFromField($where) {
        $this->db->select('*', FALSE)->from('vendor');
        $query = $this->db->where($where)->get();
        $data["records"] = $query->result();
        return $data;
    }

    function GetFromFieldLimit($where, $limit) {
        $this->db->select('*', FALSE)->from('vendor');
        $query = $this->db->where($where)->limit($limit)->get();
        $data["records"] = $query->result();
        return $data;
    }

 

    function check_customer_name($name) {
        $this->db->where('username', $name);
        $num = $this->db->count_all_results('vendor');
        return $num;
    }


}
