"use client";

import {Form} from "antd";
import {BiLockAlt, BiLockOpenAlt} from "react-icons/bi";
import {useState} from "react";

const FormPassword = ({name, label, required, placeholder, min = 6,   confirm = false, noCurrent = false}) => {
    const t = d => d
    let rules = [
        {required, message: t('Please enter a password')},
        {min: confirm ? 0 : min, message: t('Password must be at least 6 characters')}
    ]

    if (confirm) {
        rules.push(({getFieldValue}) => ({
            validator(_, value) {
                if ((!value && required) || getFieldValue('password') === value) {
                    return Promise.resolve();
                }
                return Promise.reject(new Error('The two passwords that you entered do not match!'));
            },
        }))
    }

    if (noCurrent) {
        rules.push(({getFieldValue}) => ({
            validator(_, value) {
                if ((!value && required) || getFieldValue('current_password') !== value) {
                    return Promise.resolve();
                }
                return Promise.reject(new Error(t("New password can't be same as current password")));
            },
        }))
    }

    return (
        <Form.Item
            label={label}
            name={name}
            className="mb-5"
            rules={rules}
            initialValue=""
        >
            <PasswordInputField
                className="form-control"
                placeholder={placeholder}
            />
        </Form.Item>
    )
}

export default FormPassword



const PasswordInputField = ({value, onChange, placeholder}) => {
    const [visible, setVisible] = useState(false)
    return (
        <div className="relative">
            <input
                className="form-control"
                style={{paddingRight: "40px"}}
                type={visible ? "text" : "password"}
                value={value}
                onChange={onChange}
                placeholder={placeholder}
            />
            <div
                style={{
                    position: "absolute",
                    right: "10px",
                    top: "11px",
                    color: "#6c757d"
                }}
                role="button"
                onClick={() => setVisible(!visible)}>
                {visible ? <BiLockOpenAlt size={18}/> : <BiLockAlt size={18}/>}

            </div>
        </div>
    )
}