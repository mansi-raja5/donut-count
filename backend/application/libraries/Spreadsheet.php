<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Spreadsheet extends PhpOffice\PhpSpreadsheet\Spreadsheet
{
    public function __construct()
    {
        parent::__construct();
    }
}
