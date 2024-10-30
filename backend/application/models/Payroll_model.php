<?php
class Payroll_model extends CI_Model
{
    public function add($table, $data)
    {
        // check if entry is there date and store
        $query = $this->db->get_where($table, array(
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'store_key' => $data['store_key'],
        ));
        $count = $query->num_rows();
        $row = $query->row();

        if ($count === 0) {
            $this->db->insert($table, $data);
            $data['id'] = $this->db->insert_id();
            $data['type'] = 'add';
        } else {
            if (!$row->is_lock) {
                $this->db->where('id', $row->id);
                $this->db->update($table, $data);
                $data['id'] = $row->id;
                $data['type'] = 'update';
            }
        }
        return $data;
    }
}
