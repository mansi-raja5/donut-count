<?php

/**
 * Description of Import Model
 *
 * @author TechArise Team
 *
 * @email  info@techarise.com
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Reconcile_model extends CI_Model {

    private $_batchImport;

    function Get() {
      $query =  $this->db->query('SELECT *, GROUP_CONCAT(id) as ids, GROUP_CONCAT(type) as types FROM (SELECT DISTINCT store_key,  month, year, "ledger" as type, (SELECT id FROM bank_statement WHERE bank_statement.store_key = ledger.store_key AND bank_statement.month = ledger.month AND bank_statement.year = ledger.year) as id
FROM ledger
UNION
SELECT store_key, month, year, "bank" as type,  (SELECT id FROM ledger WHERE bank_statement.store_key = ledger.store_key AND bank_statement.month = ledger.month AND bank_statement.year = ledger.year) as id
FROM bank_statement) as q GROUP BY q.month, q.store_key, q.year');
//      echo $this->db->last_query();
//      exit;
      return $query->result();

    }
    function ledger_bank_join($search_Arr = array()){
        $this->db->select('ledger.status, ledger.is_locked, ledger.id as ledger_id, bank_statement.id as bank_id, ledger.store_key,ledger.month, ledger.year, pos.id as posid,payroll.id as payrollid,IF(filename = "Auto",1,0) as isautoledger');
        $this->db->join('bank_statement', 'ledger.month = bank_statement.month AND ledger.year = bank_statement.year AND ledger.store_key = bank_statement.store_key', 'LEFT');
        $this->db->join('master_pos_daily pos', "ledger.`store_key` = pos.`store_key` AND month(pos.`cdate`) = ledger.month AND year(pos.`cdate`) = ledger.year", 'LEFT');
        $this->db->join('master_payroll payroll', "ledger.`store_key` = payroll.`store_key` AND month(payroll.`end_date`) = ledger.month AND year(payroll.`end_date`) = ledger.year", 'LEFT');
        if(isset($search_Arr['store_id']) && !empty($search_Arr['store_id'])){
            $this->db->where('ledger.store_key', $search_Arr['store_id']);
        }
        if(isset($search_Arr['month']) && !empty($search_Arr['month'])){
            $this->db->where('ledger.month', $search_Arr['month']);
        }
        if(isset($search_Arr['year']) && !empty($search_Arr['year'])){
            $this->db->where('ledger.year', $search_Arr['year']);
        }
        $this->db->order_by('store_key DESC, year DESC, month DESC');
        $query = $this->db->get('ledger');
               // echo $this->db->last_query();
        return $query->result();
    }

    function getPosPayrollIfNoLedger($search_Arr = array()){
        $this->db->select('ledger.status, ledger.is_locked, ledger.id as ledger_id, "" as bank_id,
            pospayroll.store_key,
            pospayroll.month as monthnumber,
            pospayroll.year, pospayroll.posid ,pospayroll.payrollid');
        $this->db->join('(SELECT `pos`.`store_key`, month(pos.cdate) as month, year(pos.cdate) as year,payroll.id as payrollid, pos.id as posid from master_pos_daily pos join master_payroll payroll ON pos.store_key=payroll.store_key AND month(pos.cdate) = month(payroll.end_date) AND year(pos.cdate) = year(payroll.end_date) GROUP BY `pos`.`store_key`, month(pos.cdate), year(pos.cdate)) as pospayroll', "ledger.`store_key` = pospayroll.`store_key` AND pospayroll.month = ledger.month AND pospayroll.year = ledger.year", 'RIGHT');
        if(isset($search_Arr['store_id']) && !empty($search_Arr['store_id'])){
            $this->db->where('ledger.store_key', $search_Arr['store_id']);
        }
        if(isset($search_Arr['month']) && !empty($search_Arr['month'])){
            $this->db->where('ledger.month', $search_Arr['month']);
        }
        if(isset($search_Arr['year']) && !empty($search_Arr['year'])){
            $this->db->where('ledger.year', $search_Arr['year']);
        }
        $this->db->where('ledger.id is null');
        $this->db->group_by('pospayroll.store_key');
        $this->db->group_by('pospayroll.year');
        $this->db->group_by('pospayroll.month');
        $this->db->order_by('pospayroll.store_key DESC, pospayroll.year DESC, pospayroll.month DESC');
        $query = $this->db->get('ledger');
               // echo $this->db->last_query();exit;
        return $query->result();
    }

    function bank_ledger_join($search_Arr = array()){
        $this->db->select('bank_statement.status, bank_statement.is_locked, ledger.id as ledger_id, bank_statement.id as bank_id,bank_statement.store_key,bank_statement.month,bank_statement.month as monthnumber, bank_statement.year');
        $this->db->join('ledger', 'ledger.month = bank_statement.month AND ledger.year = bank_statement.year AND ledger.store_key = bank_statement.store_key', 'LEFT');
         if(isset($search_Arr['store_id']) && !empty($search_Arr['store_id'])){
            $this->db->where('bank_statement.store_key', $search_Arr['store_id']);
        }
        if(isset($search_Arr['month']) && !empty($search_Arr['month'])){
            $this->db->where('bank_statement.month', $search_Arr['month']);
        }
        if(isset($search_Arr['year']) && !empty($search_Arr['year'])){
            $this->db->where('bank_statement.year', $search_Arr['year']);
        }
        $query = $this->db->get('bank_statement');
        return $query->result();
    }
    function getnotes($ledger_id){
        $this->db->select('notes');
        $this->db->from('ledger');
        $this->db->where('id',$ledger_id);
        return $this->db->get()->row()->notes;
    }
    function setnotes($notes,$ledger_id){
        $result =  $this->db->query("UPDATE `ledger` set notes='".$notes."' where id = ".$ledger_id);
        return $result;
    }
}
?>