"use client";

import {default as Input} from 'react-phone-number-input/input'
import {Form} from "antd";
import {isValidPhoneNumber} from "react-phone-number-input";


const PhoneInput = ({name = 'phone', label = 'Phone', required}) => {
    const i18n = useI18n()
    return (
        <Form.Item
            name={name}
            label={label}
            rules={[
                {required: required , message: "Please provide phone number"},
                () => ({
                    validator(_, value) {
                        if (value && !isValidPhoneNumber(value)) {
                            return Promise.reject(new Error('Invalid Phone number'))
                        }
                        return Promise.resolve();
                    },
                })
            ]}
            initialValue="">
            <Input className="form-input" withCountryCallingCode international/>
        </Form.Item>
    )
}
export default PhoneInput