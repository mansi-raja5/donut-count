import {Form, Select} from "antd";

const FormSelect = ({label, name, required, className, initialValue, options, search, rules = [], multi, tags, placeholder, onSelect, allowClear}) => {

    let initRules = [
        {required: required, message: `Please select ${label || 'a option'}`},
    ]

    return (
        <Form.Item
            label={label}
            name={name}
            className={className || 'mb-8'}
            rules={[...initRules, ...rules]}
            initialValue={initialValue}
        >
            <Select
                mode={multi ? 'multiple' : tags ? 'tags' : 'default'}
                popupClassName={tags ? 'd-none' : ''}
                allowClear={allowClear}
                onSelect={onSelect}
                placeholder={placeholder}
                filterOption={(input, option) => {
                    if (typeof option.children === 'string') {
                        return option.children?.toLowerCase().indexOf(input.toLowerCase()) >= 0
                    }
                    return 0
                }}
                showSearch={search}
            >
                {options?.map((option, index) => (
                    <Select.Option key={index} disabled={option.disabled}
                                   value={option?.id || option?.value}>{option.name || option?.label}</Select.Option>
                ))}
            </Select>
        </Form.Item>

    )
}

export default FormSelect
