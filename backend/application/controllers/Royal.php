<?php
class Royal extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('royal_model');
    }

    public function index()
    {
        $data['title']      = 'Weelkly';
        $this->template->load('listing', 'bill/royal', $data);
    }

    public function royal()
    {
        $data['title']      = 'Weelkly';
        echo $this->load->view('bill/royal', $data, true);
        exit;
    }

    public function getRoyaltyData()
    {
        $postData = $this->input->post();

        //get stores data
        $sql    = "SELECT * FROM `store_master` SM ORDER BY SM.key";
        $stores = $this->royal_model->query_result($sql);

        //get royalty admin settings
        $sql               = "SELECT * FROM `admin_royalty`";
        $adminSetting      = $this->royal_model->query_result($sql);
        $adminSettingStore = [];
        foreach ($adminSetting as $_adminSetting) {
            $adminSettingStore[$_adminSetting->store_key][$_adminSetting->type] = $_adminSetting;
        }

        //get POS data
        $weekInfo = getDateInfo($postData['week_ending_date']);
        $sql      = "SELECT * FROM `master_pos_daily`
        WHERE DATE(`cdate`) >= '" . $weekInfo['start_of_week'] . "'
        AND  DATE(`cdate`)  <= '" . $weekInfo['end_of_week'] . "'";
        $posData = $this->royal_model->query_result($sql);

        //get royalty data
        $sql = "SELECT * FROM `royalty`
        WHERE DATE(`week_ending_date`) >= '" . $weekInfo['end_of_week'] . "'";
        $royaltyData = $this->royal_model->query_result($sql);
        $royalty     = [];
        foreach ($royaltyData as $_royaltyData) {
            $royalty[$_royaltyData->store_key][$_royaltyData->royal_type] = $_royaltyData;
        }

        $returnData = [];
        $royalType  = ['DD', 'BR'];
        if (count($stores) && count($posData)) {
            foreach ($stores as $_stores) {
                $netSale[$_stores->key]['DD'] = 0;
                $netSale[$_stores->key]['BR'] = 0;
                $customerCount[$_stores->key] = 0;
                foreach ($posData as $_posData) {
                    $posInfo = json_decode($_posData->data);
                    if ($_stores->key == $_posData->store_key) {
                        $netSale[$_stores->key]['DD'] += $posInfo->dd_retail_net_sales;
                        $netSale[$_stores->key]['BR'] += $posInfo->br_retail_net_sales;
                        $customerCount[$_stores->key] += $posInfo->trans_count_qty;
                    }
                }

                foreach ($royalType as $_royalType) {
                    $returnData[$_stores->key][$_royalType]['store_name'] = $_stores->name;

                    if (isset($royalty[$_stores->key][$_royalType])) {
                        $returnData[$_stores->key][$_royalType] = (array)$royalty[$_stores->key][$_royalType];
                    } elseif (isset($adminSettingStore[$_stores->key][$_royalType])
                        && $adminSettingStore[$_stores->key][$_royalType]->royalty_percentage
                        && $adminSettingStore[$_stores->key][$_royalType]->adfund_percentage
                        && $adminSettingStore[$_stores->key][$_royalType]->customer_count_for_br) {
                        $royalty_amt = ($netSale[$_stores->key][$_royalType] * 100) / $adminSettingStore[$_stores->key][$_royalType]->royalty_percentage;
                        $adfund_amt  = ($netSale[$_stores->key][$_royalType] * 100) / $adminSettingStore[$_stores->key][$_royalType]->adfund_percentage;

                        $returnData[$_stores->key][$_royalType]['status']      = "success";
                        $returnData[$_stores->key][$_royalType]['net_sales']    = '$' . number_format($netSale[$_stores->key][$_royalType], 2);
                        $returnData[$_stores->key][$_royalType]['royalty_amt'] = '$' . number_format($royalty_amt, 2);
                        $returnData[$_stores->key][$_royalType]['adfund_amt']  = '$' . number_format($adfund_amt, 2);

                        if ($_royalType == 'BR') {
                            $returnData[$_stores->key][$_royalType]['cust_count'] = $adminSettingStore[$_stores->key][$_royalType]->customer_count_for_br;
                        } else {
                            $returnData[$_stores->key][$_royalType]['cust_count'] = $customerCount[$_stores->key] ? $customerCount[$_stores->key] - $adminSettingStore[$_stores->key][$_royalType]->customer_count_for_br : 0;
                        }
                        $returnData[$_stores->key][$_royalType]['sys_eft_amt'] = '$' . number_format(($royalty_amt + $adfund_amt), 2);
                        $returnData[$_stores->key][$_royalType]['actual_eft_amt'] = 0;

                    } else {
                        $returnData[$_stores->key][$_royalType]['msg'] = "Store Setting not found for " . $_stores->key;

                        $returnData[$_stores->key][$_royalType]['status']      = "failure";
                        $returnData[$_stores->key][$_royalType]['net_sales']    = 0;
                        $returnData[$_stores->key][$_royalType]['royalty_amt'] = 0;
                        $returnData[$_stores->key][$_royalType]['adfund_amt']  = 0;
                        $returnData[$_stores->key][$_royalType]['cust_count']  = 0;
                        $returnData[$_stores->key][$_royalType]['sys_eft_amt'] = 0;
                        $returnData[$_stores->key][$_royalType]['actual_eft_amt'] = 0;
                    }
                }
            }
        }
        $royal_data['royal_data'] = $returnData;
        $data['royhtml']          = $this->load->view('bill/royal_data', $royal_data, true);
        $data['status']           = "success";
        echo json_encode($data);
        exit;
    }

    public function getDataFromOtherSite()
    {
        $postData    = $this->input->post();
        $websitedata = $postData['websitedata'];
        $returnData  = [];

        $periodEndingPos    = strpos($websitedata, "Period Ending:");
        $periodEndingLength = strlen("Period Ending:");

        $pcPos    = strpos($websitedata, "PC #:");
        $pcLength = strlen("PC #:");

        $custNoPos    = strpos($websitedata, "Customer No:");
        $custNoLength = strlen("Customer No:");

        $brandPos    = strpos($websitedata, "Brand:");
        $brandLength = strlen("Brand:");

        $totalSalesPos    = strpos($websitedata, "Total Sales:");
        $totalSalesLength = strlen("Total Sales:");

        $wholeSalesPos    = strpos($websitedata, "Wholesale Sales:");
        $wholeSalesLength = strlen("Wholesale Sales:");

        $royaltyAmtPos    = strpos($websitedata, "Royalty Amount:");
        $royaltyAmtLength = strlen("Royalty Amount:");

        $adfundPos    = strpos($websitedata, "Adfund Amount:");
        $adfundLength = strlen("Adfund Amount:");

        $customerCountPos    = strpos($websitedata, "Customer Count:");
        $customerCountLength = strlen("Customer Count:");

        $wholeSalesAcctPos    = strpos($websitedata, "Wholesale Accounts:");
        $wholeSalesAcctLength = strlen("Wholesale Accounts:");

        $perRentPos    = strpos($websitedata, "% Rent:");
        $perRentLength = strlen("% Rent:");

        $interestChargePos    = strpos($websitedata, "Interest Charge:");
        $interestChargeLength = strlen("Interest Charge:");

        $eftTransferAmtPos    = strpos($websitedata, "EFT Transfer Amount:");
        $eftTransferAmtLength = strlen("EFT Transfer Amount:");

        $lastLinePos = strpos($websitedata, "Your bank will initiate the EFT transaction on");

        if (!$periodEndingPos || !$pcPos || !$custNoPos || !$brandPos || !$totalSalesPos || !$wholeSalesPos || !$royaltyAmtPos || !$adfundPos || !$customerCountPos || !$wholeSalesAcctPos || !$perRentPos || !$interestChargePos || !$eftTransferAmtPos || !$lastLinePos) {
            $returnData['status'] = "failure";
        } else {

            $returnData['status']        = "success";
            $returnData['period_ending'] = removeSpaces(substr($websitedata, ($periodEndingPos + $periodEndingLength), $pcPos - ($periodEndingPos + $periodEndingLength)));
            $returnData['pc']            = removeSpaces(substr($websitedata, ($pcPos + $pcLength), $custNoPos - ($pcPos + $pcLength)));
            $returnData['customer_no']   = removeSpaces(substr($websitedata, ($custNoPos + $custNoLength), $brandPos - ($custNoPos + $custNoLength)));

            $brand               = substr($websitedata, ($brandPos + $brandLength), $totalSalesPos - ($brandPos + $brandLength));
            $returnData['brand'] = removeSpaces($brand);

            $brandAry                         = explode("\n", $brand);
            $returnData['royalType']          = removeSpaces($brandAry[0]);
            $returnData['total_sales']        = removeSpaces(substr($websitedata, ($totalSalesPos + $totalSalesLength), $wholeSalesPos - ($totalSalesPos + $totalSalesLength)));
            $returnData['whole_sales']        = removeSpaces(substr($websitedata, ($wholeSalesPos + $wholeSalesLength), $royaltyAmtPos - ($wholeSalesPos + $wholeSalesLength)));
            $returnData['royalty_amt']        = removeSpaces(substr($websitedata, ($royaltyAmtPos + $royaltyAmtLength), $adfundPos - ($royaltyAmtPos + $royaltyAmtLength)));
            $returnData['adfund_amt']         = removeSpaces(substr($websitedata, ($adfundPos + $adfundLength), $customerCountPos - ($adfundPos + $adfundLength)));
            $returnData['customer_count']     = removeSpaces(substr($websitedata, ($customerCountPos + $customerCountLength), $wholeSalesAcctPos - ($customerCountPos + $customerCountLength)));
            $returnData['wholesale_Accounts'] = removeSpaces(substr($websitedata, ($wholeSalesAcctPos + $wholeSalesAcctLength), $perRentPos - ($wholeSalesAcctPos + $wholeSalesAcctLength)));
            $returnData['per_rent']           = removeSpaces(substr($websitedata, ($perRentPos + $perRentLength), $interestChargePos - ($perRentPos + $perRentLength)));
            $returnData['interest_charge']    = removeSpaces(substr($websitedata, ($interestChargePos + $interestChargeLength), $eftTransferAmtPos - ($interestChargePos + $interestChargeLength)));
            $returnData['eft_transfer_amt']   = removeSpaces(substr($websitedata, ($eftTransferAmtPos + $eftTransferAmtLength), $lastLinePos - ($eftTransferAmtPos + $eftTransferAmtLength)));
        }
        echo json_encode($returnData);
        exit;
    }

    public function saveRoyaltyData()
    {
        $postData = $this->input->post();
        if (count($postData['royal'])) {
            $royalInsertData = [];
            foreach ($postData['royal'] as $storekey => $royalData) {
                foreach ($royalData as $_royalData) {
                    $royalInsertData[] = array(
                        'store_key'        => $storekey,
                        'week_ending_date' => date("Y-m-d", strtotime($postData['weekend_date'])),
                        'royal_type'       => $_royalData['royal_type'],
                        'net_sales'        => remove_format(trim($_royalData['net_sales'], '$')),
                        'royalty_amt'      => remove_format(trim($_royalData['royalty_amt'], '$')),
                        'adfund_amt'       => remove_format(trim($_royalData['adfund_amt'], '$')),
                        'cust_count'       => $_royalData['cust_count'],
                        'sys_eft_amt'      => remove_format(trim($_royalData['sys_eft_amt'], '$')),
                        'actual_eft_amt'   => remove_format(trim($_royalData['actual_eft_amt'], '$')),
                        'created_on'       => date('Y-m-d H:i:s'),
                    );
                }
            }
            $this->royal_model->Add_Batch($royalInsertData);
        }
        $data['status'] = "success";
        echo json_encode($data);
        exit;
    }
}
