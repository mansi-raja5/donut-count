import { Form } from "antd";

const FormInput = ({
  name,
  label,
  required,
  isEmail,
  rules = [],
  type,
  textArea,
  onChange,
  ...props
}) => {
  const t = (d) => d;

  let initRules = [
    {
      required: required,
      message: `Please provide ${
        (typeof label === "string" && label?.toLowerCase()) || "a value"
      }`,
    },
  ];
  if (isEmail === true) {
    initRules.push({
      type: "email",
      message: t("Please enter a valid email address"),
    });
  }

  const getInput = () => {
    if (textArea) {
      return <textarea className="form-control" {...props} />;
    }

    return <input type={type} onChange={onChange} className="form-control" {...props} />;
  };

  return (
    <Form.Item
      label={label}
      name={name}
      className="mb-5"
      rules={[...initRules, ...rules]}
      initialValue=""
    >
      {getInput()}
    </Form.Item>
  );
};

export default FormInput;

export const HiddenFormItem = ({ name, initialValue = "" }) => {
  return (
    <Form.Item name={name} initialValue={initialValue} hidden>
      <input />
    </Form.Item>
  );
};
