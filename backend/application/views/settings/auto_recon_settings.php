<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-globe"></i>
            <span class="caption-subject bold uppercase"><?php echo $title; ?></span>
        </div>
        <div class="caption btn-group pull-right">
            <a class="btn green btn-outline btn-circle btn-sm active" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false"> Note
                <i class="fa fa-angle-down"></i>
            </a>
            <ul class="dropdown-menu pull-right">
                <li>
                    <a href="javascript:;"> 
                    <ul>
                        <li>Multiple same Ledger Description should be followed by space</li>
                        <ul>
                        <li>M</li>
                        <li>M leave-space</li>
                        <li>M leave-space leave-space</li>
                        </ul>
                    </ul>
                    <ul>
                        <li>Match any bank description which start with M%</li>
                        <li>Match any bank description which ends with %M</li>
                        <li>Match any bank description which has M in any position %M%</li>
                    </ul>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="portlet-body">
        <div class="table-toolbar">
            <div class="row">
                <div class="col-md-6">
                    <div class="btn-group">
                        <button class="btn green" id="reconcilation_setting_new">
                            Add New
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <table class="table table-striped table-hover" id="reconcilation_setting" >
            <thead>
                <tr role="row">
                    <th>
                        Ledger Description
                    </th>
                    <th>
                        Bank Description
                    </th>
                    <th>
                        Edit
                    </th>
                    <th>
                        Delete
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php
                if($auto_reconcilation)
                {
                    foreach ($auto_reconcilation['records'] as $_auto_reconcilation) {
                    ?>
                    <tr class="odd" role="row" desc_id="<?php echo $_auto_reconcilation->id; ?>">
                        <td class="sorting_1">
                            <?php echo $_auto_reconcilation->ledger_desc; ?>
                        </td>
                        <td>
                            <?php echo $_auto_reconcilation->bank_desc; ?>
                        </td>
                        <td>
                            <a class="edit" href="javascript:;">
                                Edit
                            </a>
                        </td>
                        <td>
                            <a class="delete" href="javascript:;">
                                Delete
                            </a>
                        </td>
                    </tr>
                    <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>