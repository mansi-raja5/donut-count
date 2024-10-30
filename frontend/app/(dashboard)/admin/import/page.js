"use client";
import React, { useState } from "react";
import { Card, Checkbox, Col, DatePicker, Form, Row } from "antd";
import FormSelect from "../../components/form/select";
import Button from "../../components/common/button";


const Import = () => {
  const [checked, setChecked] = useState(false);
  const onChange = (e) => {
    setChecked(e?.target?.checked);
  };

  const onFinish = (values) => {
    console.log("🚀 ~ file: index.js:Import.onFinish ~ values:", values);
  };

  const onFinish_1 = (values) => {
    console.log("🚀 ~ file: index.js:Import.onFinish_1 ~ values:", values);
  };

  return (
    <Card>
      <p className="text-content lg:text-2xl text-[20px] font-bold mb-4">Import Data</p>
      <div className="mb-6">
        <Checkbox onChange={onChange}>Checkbox</Checkbox>
      </div>

      {!checked ? (
        <Form onFinish={onFinish_1} layout="vertical">
          <Row gutter={24}>
            <Col lg={12} xs={24}>
              <Form.Item
                label="Select File"
                name="file"
                initialValue={[]}
                valuePropName="files"
                rules={[{ required: true, message: "Please select a file" }]}
              >
                <input
                  type="file"
                  className="form-input"
                  accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                />
              </Form.Item>
            </Col>
            {/* <div className="lg:w-1/2">
                  <Button>Import</Button>
                  <button className="mx-auto mt-2 bg-mainColor text-white p-2 rounded-md w-full">
                    <div className="flex items-center justify-center gap-3 w-full">
                      <MdOutlineDriveFolderUpload />
                      <h1 className="">Import</h1>
                    </div>
                  </button>
                </div> */}
          </Row>
          <Button>Import</Button>
        </Form>
      ) : (
        <Form layout="vertical" onFinish={onFinish}>
          <Row gutter={24}>
            <Col md={12} xs={24}>
              <FormSelect
                name={"store"}
                label={"Select Store"}
                required
                options={[
                  {
                    label: "350437",
                    value: "350437",
                  },
                  {
                    label: "351323",
                    value: "351323",
                  },
                  {
                    label: "352613",
                    value: "352613",
                  },
                  {
                    label: "353927",
                    value: "353927",
                  },
                  {
                    label: "354030",
                    value: "354030",
                  },
                  {
                    label: "355439",
                    value: "355439",
                  },
                  {
                    label: "357150",
                    value: "357150",
                  },
                ]}
              />
            </Col>
            <Col md={12} xs={24}>
              <Form.Item
                label="Start Date"
                name="start"
                rules={[
                  { required: true, message: "Please select a start date" },
                ]}
              >
                <DatePicker format="YYYY-MM-DD" />
              </Form.Item>
            </Col>
            <Col md={12} xs={24}>
              <Form.Item
                label="End Date"
                name="end"
                rules={[
                  { required: true, message: "Please select a end date" },
                ]}
              >
                <DatePicker format="YYYY-MM-DD" />
              </Form.Item>
            </Col>
            <Col md={12} xs={24}>
              <Form.Item
                label="Select File"
                name="file"
                initialValue={[]}
                valuePropName="files"
                rules={[{ required: true, message: "Please select a file" }]}
              >
                <input
                  type="file"
                  className="form-input"
                  accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                />
              </Form.Item>
            </Col>
          </Row>
          <Button>Import</Button>
        </Form>
      )}
    </Card>
  );
};

export default Import;
