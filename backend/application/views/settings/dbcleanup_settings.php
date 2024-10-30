<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-globe"></i>
            <span class="caption-subject bold uppercase"><?php echo $title; ?></span>
        </div>
    </div>
    <div class="portlet-body">
        <?php echo $this->session->flashdata('msg'); ?>
        <form method="post" action=""<?php echo base_url('settings/dbcleansetting'); ?>" onSubmit="return confirmation()">
            <p>
                <div class="row">
                    <div class="col-lg-12">
                        <button type="submit" class="btn blue" name="type" value="ledger">Ledger</button>
                        <button type="submit" class="btn green" name="type" value="bank">Bank</button>
                        <button type="submit" class="btn yellow" name="type" value="reconcilation">Reconcilation</button>
                    </div>
                </div>
            </p>
            <p>
                <div class="row">
                    <div class="col-lg-12">
                        <button type="submit" class="btn blue" name="type" value="bill">Bill</button>
                    </div>
                </div>
            </p>
            <p>
                <div class="row">
                    <div class="col-lg-12">
                        <button type="submit" class="btn blue" name="type" value="donut">Donut</button>
                        <button type="submit" class="btn green" name="type" value="labor">Labour</button>
                        <button type="submit" class="btn yellow" name="type" value="payroll">Master Payroll</button>
                        <button type="submit" class="btn purple" name="type" value="pos">POS</button>
                    </div>                                                             
                </div>
            </p>
            <hr/>
            <p>
                <div class="row">
                    <div class="col-lg-12">
                        <button type="submit" class="btn red" name="type" value="all">Clean except Settings</button>
                    </div>                                                             
                </div>        
            </p>
        </form>   
    </div>
</div>
<script>
function confirmation()
{
    if(confirm("Are you sure you want to cleanup?"))
    {
        return true;
    }
    return false;
}
</script>