var FormInputMask = function () {
    
    var handleInputMasks = function () {
        $("#phone").inputmask({
            "mask": "999-999-9999"
        });
        $("#edit_phone").inputmask({
            "mask": "999-999-9999"
        });
        $("#zip").inputmask({
            "mask": "99999"
        });
        $("#zip4").inputmask({
            "mask": "9999"
        });
        $("#mailing_zip").inputmask({
            "mask": "99999"
        });
        $("#phone1").inputmask({
            "mask": "999-999-9999"
        });
        $("#phone2").inputmask({
            "mask": "999-999-9999"
        });
        $("#ssn_ein").inputmask({
            "mask": "99-9999999"
        });
        $("#policy_expire_date").inputmask({
            "mask": "99-99-9999"
        });
        $("#dob").inputmask({
            "mask": "99/99/9999"
        });
        $("#start_date").inputmask({
            "mask": "99/99/9999"
        });
        $("#end_date").inputmask({
            "mask": "99/99/9999"
        });
        $("#anticipated_date").inputmask({
            "mask": "99/99/9999"
        });
        $("#phn_number").inputmask({
            "mask": "999-999-9999"
        });
    }

    return {
        //main function to initiate the module
        init: function () {
            handleInputMasks();
        }
    };

}();
