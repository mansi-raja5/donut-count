"use client";
import React from "react";
import PageTitle from "../../components/common/title";
import { Card, Col, DatePicker, Form, Row } from "antd";
import FormSelect from "../../components/form/select";
import Button from "../../components/common/button";

const Import = () => {
  return (
    <div>
      <PageTitle title={"Import"} />
      <Card>
        <Form layout="vertical">
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
                  }
                ]}
              />
            </Col>
            <Col md={12} xs={24}>
              <Form.Item
                label="Select Year"
                name="year"
                rules={[{ required: true, message: "Please select a year" }]}
              >
                <DatePicker picker="year" />
              </Form.Item>
            </Col>
          </Row>

          <Row gutter={24}>
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

          <div className="">
            <div className="flex ">
              <Button className="mt-2.5"> Submit </Button>
            </div>
          </div>
        </Form>
      </Card>
    </div>
  );
};

export default Import;
