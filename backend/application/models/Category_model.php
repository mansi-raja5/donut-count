<?php
class Category_model extends CI_Model
{
    public function Get($id = null, $search = array())
    {
        $this->db->select('SQL_CALC_FOUND_ROWS bill_category.*, vendor.company, GROUP_CONCAT(bill_breakdown_category.description) as breakdown_description', false);
        $this->db->join('vendor', 'vendor.id = bill_category.vendor_id', 'LEFT');
        $this->db->join('bill_breakdown_category', 'bill_breakdown_category.bill_category_id = bill_category.id', 'LEFT');
        $this->db->from('bill_category');
        // Check if we're getting one row or all records
        if ($id != null) {
            // Getting only ONE row
            $this->db->where('bill_category.id', $id);
            $this->db->limit('1');
            $query = $this->db->get();
            if ($query->num_rows() == 1) {
                // One row, match!
                return $query->row();
            } else {
                // None
                return false;
            }
        } else {
            // Get all
            if (!empty($search)) {
                $defaultSearch = array(
                    'category'         => '',
                    'type'             => '',
                    'description'      => '',
                    'company'          => '',
                    'status'           => '',
                    'exclude_category' => '',
                );
                $search = array_merge($defaultSearch, $search);
                if ($search['category'] != '') {
                    $this->db->where('category_name', $search['category']);
                }
                if ($search['type'] != '') {
                    $this->db->where('type', $search['type']);
                }
                if ($search['description'] != '') {
                    $this->db->where('bill_category.description', $search['description']);
                }
                if ($search['company'] != '') {
                    $this->db->where('company', $search['company']);
                }
                if ($search['status'] != '') {
                    $this->db->where('bill_category.status', $search['status']);
                }
                if ($search['exclude_category'] != '') {
                    $this->db->where('bill_category.type != ', $search['exclude_category']);
                }

                if (isset($search['order'])) {
                    $order = $search['order'];
                    if ($order[0]['column'] == 1) {
                        $orderby = "category_name " . strtoupper($order[0]['dir']);
                    }
                    if ($order[0]['column'] == 2) {
                        $orderby = "description " . strtoupper($order[0]['dir']);
                    }
                    if ($order[0]['column'] == 4) {
                        $orderby = "company " . strtoupper($order[0]['dir']);
                    }
                    if ($order[0]['column'] == 5) {
                        $orderby = "bill_category.status" . strtoupper($order[0]['dir']);
                    }
                    $this->db->order_by($orderby);
                }
                if (isset($search['start'])) {
                    $start  = $search['start'];
                    $length = $search['length'];
                    if ($length != -1) {
                        $this->db->limit($length, $start);
                    }
                }
                $this->db->group_by('bill_category.id');
                $query           = $this->db->get();
                $data["records"] = array();
                if ($query->num_rows() > 0) {
                    // Got some rows, return as assoc array
                    $data["records"] = $query->result();
                }
                $count                 = $this->db->query('SELECT FOUND_ROWS() AS Count');
                $data["countTotal"]    = $this->db->count_all('bill_category');
                $data["countFiltered"] = $count->row()->Count;
                return $data;
            } else {
                $data["records"]       = array();
                $data["countTotal"]    = 0;
                $data["countFiltered"] = 0;
                return $data;
            }
        }
    }

    public function Add($data)
    {
        // Run query to insert blank row

        $this->db->insert('bill_category', $data);
        // Get id of inserted record
        $id = $this->db->insert_id();

        return $id;
    }

    public function Edit($id, $data)
    {
        $this->db->where('id', $id);
        $result = $this->db->update('bill_category', $data);

        // Return
        if ($result) {
            return $id;
        } else {
            return false;
        }
    }

    public function Delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('bill_category');
    }

    public function Delete_Batch_Description($id)
    {
        $this->db->where('bill_category_id', $id);
        $this->db->delete('bill_breakdown_category');
    }
    public function Get_breakdown_category($id)
    {
        $this->db->where('bill_category_id', $id);
        $query = $this->db->get('bill_breakdown_category');
        return $query->result();
    }
//    function Get_breakdown_description($id){
    //         $this->db->where('bill_category_id', $id);
    //         $query = $this->db->get('bill_breakdown_category');
    //         return $query->result();
    //    }
    public function AddBatch($data)
    {
        if (!empty($data)) {
            $this->db->insert_batch('bill_breakdown_category', $data);
            return true;
        } else {
            return false;
        }
    }
    public function get_category_desc($type, $category_name)
    {
        $this->db->select('SQL_CALC_FOUND_ROWS bill_category.*', false);
        if ($type == 'breakdown_description') {
            $this->db->where('type', $type);
        } else {
            $this->db->where('type !=', 'breakdown_description');
        }
        $this->db->where('category_key', $category_name);
        $res = $this->db->get('bill_category');
//        echo $this->db->last_query();
        return $res->result();
    }

}
