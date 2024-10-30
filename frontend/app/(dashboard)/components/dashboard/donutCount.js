import {
  Card,
  Col,
  DatePicker,
  Form,
  Modal,
  Radio,
  Row,
  Select,
  message,
} from "antd";
import { FaPlus } from "react-icons/fa";
import React, { useEffect, useState } from "react";
import { MultiYearSelect } from "../form/multiSelectYear";
import FormSelect from "../form/select";
import Slider from "../common/slider";
import Button from "../common/button";
import { Swiper, SwiperSlide } from "swiper/react";
import "swiper/css";
import "swiper/css/pagination";

import "swiper/css";
import "swiper/css/free-mode";
import "swiper/css/pagination";

import { Autoplay, FreeMode, Pagination } from "swiper/modules";
import {
  fetchDonutCount,
  fetchSpecialDays,
  getSpecialDayData,
} from "../../../helpers/backend";
import { useFetch } from "../../../helpers/hooks";
import MainLoader from "../common/loader";
import dayjs from "dayjs";
import weekday from "dayjs/plugin/weekday";
import localeData from "dayjs/plugin/localeData";
import FormInput from "../form/input";
import SingleTableData from "./singleTableData";
import { useUser } from "../../../contexts/user";
import { convertProductDataWeek } from "../user/all-store";
dayjs.extend(weekday);
dayjs.extend(localeData);

const DonutCountComponent = () => {
  const [form] = Form.useForm();
  const [specialDayForm] = Form.useForm();
  const [open, setOpen] = useState(false);
  const [type, setType] = useState("day");
  const [showData, setShowData] = useState(false);
  const [startDateC, setStartDateC] = useState(null);
  const [showSpecialData, setShowSpecialData] = useState(false);
  const [multiYear, setMultiYear] = useState([]);
  const [multiStoreKey, setMultiStoreKey] = useState([]);

  const onChange = (e) => {
    setType(e.target.value);
    getDonut({
      start_date: null,
      end_date: null,
      year: null,
      month: null,
      week_ending: null,
      date: null,
      yearsrange: null,
    });
    getSpecialData({
      year: null,
      name: null,
    });
    setShowData(false);
    setShowSpecialData(false);
    setMultiYear([]);
  };

  const handleYearSelect = (year) => {
    setShowData(false);
    setShowSpecialData(false);
    setMultiYear(year);
  };

  const [storeValue, setStoreValue] = useState("");
  const [donut, getDonut, { loading }] = useFetch(fetchDonutCount);
  const [specialData, getSpecialData, { loading: specialLoading }] =
    useFetch(getSpecialDayData);
  const user = useUser();
  const [storeData, setStoreData] = useState([]);
  const [specialDays, getSpecialDays] = useFetch(fetchSpecialDays, {
    status: "A",
  });

  useEffect(() => {
    if (!!user?.stores) {
      const storeIdsArray = user?.stores
        .split(",")
        .map((storeId) => storeId.trim());
      setStoreData(storeIdsArray);
    }
  }, [user?.stores]);

  useEffect(() => {
    if (storeData?.length > 0) {
      setStoreValue(storeData[0]);
      getDonut({ store_key: storeData[0] });
      getSpecialData({ store_key: storeData[0] });
    }
  }, [storeData]);

  const disabledDate = (current) => {
    return current && current.day() !== 6;
  };

  const disabledEndDate = (endValue) => {
    const { getFieldValue } = form;
    const startDate = getFieldValue("start_date");

    if (!endValue || !startDate) {
      return false;
    }

    return endValue.isBefore(startDate, "day");
  };

  const handleChangeStoreKey = (value) => {
    if (value.includes("selectAll")) {
      setMultiStoreKey(storeData || []);
    } else {
      setMultiStoreKey(value);
    }
  };

  const handleDateSelect = (date) => {
    setStartDateC(date);
    if (date) {
      const selectedYear = date.year();
      setMultiYear([selectedYear]);
    }
  };

  const storeKeyString = multiStoreKey?.join(",");

  if (!donut) {
    return (
      <>
        <MainLoader />
      </>
    );
  }

  return (
    <div>
      <Card className="font-nunitoFont">
        <p className="text-content lg:text-2xl text-[20px] font-bold mb-4">
          Select Type
        </p>
        <Radio.Group onChange={onChange} value={type} className="mb-6">
          <div className="flex flex-wrap md:flex-row flex-col gap-x-6 gap-y-3">
            <Radio value={"special_day"}>
              <span className="text-base lg:font-bold font-semibold text-content">
                Special Day
              </span>
            </Radio>
            <Radio value={"day"}>
              <span className="text-base lg:font-bold font-semibold text-content">
                Day
              </span>
            </Radio>
            <Radio value={"week"}>
              <span className="text-base lg:font-bold font-semibold text-content">
                Week
              </span>
            </Radio>
            <Radio value={"month"}>
              <span className="text-base lg:font-bold font-semibold text-content">
                Month
              </span>
            </Radio>
            <Radio value={"year"}>
              <span className="text-base lg:font-bold font-semibold text-content">
                Year
              </span>
            </Radio>
            <Radio value={"custom"}>
              <span className="text-base lg:font-bold font-semibold text-content">
                Custom Date Range
              </span>
            </Radio>
          </div>
        </Radio.Group>

        <Form
          form={form}
          layout="vertical"
          onFinish={async (values) => {
            if (type === "custom") {
              // if (multiYear?.length < 1) {
              //   message.error("Must Select an Year!");
              // } else {
              // const yearsString = multiYear?.join(",");
              getDonut({
                // yearsrange: yearsString,
                start_date: dayjs(values.start_date).format("YYYY-MM-DD"),
                end_date: dayjs(values.end_date).format("YYYY-MM-DD"),
              });
              setShowData(true);
              // }
            } else if (type === "year") {
              if (multiYear?.length < 1) {
                message.error("Must Select an Year!");
              } else {
                const yearsString = multiYear?.join(",");
                getDonut({
                  yearsrange: yearsString,
                });
                setShowData(true);
              }
            } else if (type === "month") {
              if (multiYear?.length < 1) {
                message.error("Must Select an Year!");
              } else {
                const yearsString = multiYear?.join(",");
                getDonut({
                  yearsrange: yearsString,
                  month: dayjs(values.month).format("YYYY-MM"),
                });
                setShowData(true);
              }
            } else if (type === "week") {
              if (multiYear?.length < 1) {
                message.error("Must Select an Year!");
              } else {
                const yearsString = multiYear?.join(",");
                getDonut({
                  yearsrange: yearsString,
                  week_ending: dayjs(values.week_eding_date).format(
                    "YYYY-MM-DD"
                  ),
                });
                setShowData(true);
              }
            } else if (type === "day") {
              if (multiYear?.length < 1) {
                message.error("Must Select an Year!");
              } else {
                const yearsString = multiYear?.join(",");
                getDonut({
                  yearsrange: yearsString,
                  date: dayjs(values.date).format("YYYY-MM-DD"),
                });
                setShowData(true);
              }
            } else if (type === "special_day") {
              if (multiYear?.length < 1) {
                message.error("Must Select an Year!");
              } else {
                const yearsString = multiYear?.join(",");
                getSpecialData({
                  year: yearsString,
                  name: values.name,
                });
                setShowSpecialData(true);
              }
            }
          }}
        >
          {type === "special_day" && (
            <Row gutter={24}>
              <Col lg={12} xs={24}>
                <Form.Item label="Select Year">
                  <MultiYearSelect
                    startYear={2020}
                    onChange={handleYearSelect}
                    value={multiYear}
                  />
                </Form.Item>
              </Col>

              <Col lg={12} xs={24} className="flex gap-x-2 items-start">
                <div className="w-full h-fit">
                  <FormSelect
                    name={"name"}
                    label={"Select Special Day"}
                    required
                    options={specialDays?.map((day) => ({
                      label: day?.name,
                      value: day?.name,
                    }))}
                  />
                </div>
                <span
                  onClick={() => setOpen(true)}
                  title="Add New Special Day"
                  className="bg-mainColor cursor-pointer text-white rounded px-3 py-2 lg:text-base text-sm font-bold  mt-8"
                >
                  <FaPlus size={20} />
                </span>
              </Col>
            </Row>
          )}

          {type === "day" && (
            <Row gutter={24}>
              <Col lg={12} xs={24}>
                <Form.Item
                  label="Select Date"
                  name="date"
                  rules={[{ required: true, message: "Please select a date" }]}
                >
                  <DatePicker format="YYYY-MM-DD" onChange={handleDateSelect} />
                </Form.Item>
              </Col>
              <Col lg={12} xs={24}>
                <Form.Item label="Select Year">
                  <MultiYearSelect
                    startYear={2020}
                    onChange={handleYearSelect}
                    value={multiYear}
                  />
                </Form.Item>
              </Col>
            </Row>
          )}
          {type === "week" && (
            <Row gutter={24}>
              <Col lg={12} xs={24}>
                <Form.Item
                  label="Select Week Ending Date"
                  name="week_eding_date"
                  rules={[{ required: true, message: "Please select a date" }]}
                >
                  <DatePicker
                    format="YYYY-MM-DD"
                    picker="week"
                    onChange={handleDateSelect}
                    disabledDate={disabledDate}
                  />
                </Form.Item>
              </Col>
              <Col lg={12} xs={24}>
                <Form.Item label="Select Year">
                  <MultiYearSelect
                    startYear={2020}
                    onChange={handleYearSelect}
                    value={multiYear}
                  />
                </Form.Item>
              </Col>
            </Row>
          )}

          {type === "month" && (
            <Row gutter={24}>
              <Col lg={12} xs={24}>
                <Form.Item
                  label="Select Month"
                  name="month"
                  rules={[{ required: true, message: "Please select a month" }]}
                >
                  <DatePicker
                    format={"MM"}
                    picker="month"
                    onChange={handleDateSelect}
                  />
                </Form.Item>
              </Col>
              <Col lg={12} xs={24}>
                <Form.Item label="Select Year">
                  <MultiYearSelect
                    startYear={2020}
                    onChange={handleYearSelect}
                    value={multiYear}
                  />
                </Form.Item>
              </Col>
            </Row>
          )}

          {type === "year" && (
            <Row gutter={24}>
              {/* <Col lg={12} xs={24}>
                <Form.Item
                  label="Select Year"
                  name="year"
                  rules={[{ required: true, message: "Please select a year" }]}
                >
                  <DatePicker picker="year" />
                </Form.Item>
              </Col> */}
              <Col lg={12} xs={24}>
                <Form.Item label="Select Year">
                  <MultiYearSelect
                    startYear={2020}
                    onChange={handleYearSelect}
                    value={multiYear}
                  />
                </Form.Item>
              </Col>
            </Row>
          )}

          {type === "custom" && (
            <Row gutter={24}>
              <Col lg={12} xs={24}>
                <Form.Item
                  label="Select Start Date"
                  name="start_date"
                  rules={[
                    { required: true, message: "Please select a start date" },
                  ]}
                >
                  <DatePicker format="YYYY-MM-DD" onChange={handleDateSelect} />
                </Form.Item>
              </Col>
              {!!startDateC && (
                <Col lg={12} xs={24}>
                  <Form.Item
                    label="Select End Date"
                    name="end_date"
                    rules={[
                      { required: true, message: "Please select a end date" },
                    ]}
                  >
                    <DatePicker
                      format="YYYY-MM-DD"
                      disabledDate={disabledEndDate}
                      defaultValue={dayjs(startDateC, "YYYY-MM-DD")}
                    />
                  </Form.Item>
                </Col>
              )}

              {/* <Col lg={12} xs={24} className="hidden">
                <Form.Item label="Select Year">
                  <MultiYearSelect
                    startYear={2020}
                    onChange={handleYearSelect}
                    value={multiYear}
                  />
                </Form.Item>
              </Col> */}
            </Row>
          )}

          <div className="mt-2.5">
            <div className="flex gap-x-4">
              <Button className=""> Show Result </Button>
              <span
                onClick={() => {
                  form.resetFields();
                  setShowData(false);
                  setShowSpecialData(false);
                  setMultiYear([]);
                }}
                className="!bg-[#E48586] !bg-opacity-10 cursor-pointer !text-mainColor rounded px-4 py-2 text-base font-bold"
              >
                {" "}
                Reset{" "}
              </span>
            </div>
          </div>
        </Form>

        {showData === true && (
          <div className="mt-8">
            <div className="flex md:flex-row flex-col md:justify-between gap-y-4">
              <h2 className="text-content text-2xl font-bold">All Store</h2>
            </div>
            {loading ? (
              <div>
                <MainLoader />
              </div>
            ) : (
              <>
                <div className="pr-4 swiper-tabbar mt-8">
                  <Swiper
                    slidesPerView="auto"
                    spaceBetween={24}
                    freeMode={true}
                    modules={[Autoplay, FreeMode, Pagination]}
                    className="mySwiper w-full"
                  >
                    {storeData?.map((store, index) => (
                      <SwiperSlide key={index}>
                        <div
                          className="border-none outline-none"
                          onClick={() => setStoreValue(store)}
                        >
                          <h1
                            onClick={() => getDonut({ store_key: store })}
                            className={`${
                              storeValue === store
                                ? "bg-[#E48586] text-mainColor border border-mainColor bg-opacity-10 "
                                : "bg-white text-[#44566C]"
                            } 
                  } py-[10px] w-fit flex justify-center items-center px-5  rounded capitalize cursor-pointer hover:bg-[#E48586] hover:text-white`}
                          >
                            {store}
                          </h1>
                        </div>
                      </SwiperSlide>
                    ))}
                  </Swiper>
                </div>
                {type === "day" && (
                  <div className="mt-8 ">
                    {donut?.length !== 0 ? (
                      <Slider SliderView={[0, 1, 2]}>
                        {Object.values(donut)?.map((product, index) => (
                          <SwiperSlide key={index}>
                            <div className="border border-[#EAEEF7] rounded">
                              <h1 className="text-content font-bold lg:text-[48px] text-[34px] py-[14px] text-center ">
                                Donut
                              </h1>
                              <table className="w-full order">
                                <thead>
                                  <tr>
                                    <th>Date</th>
                                    <th>Order</th>
                                    <th>Sale</th>
                                  </tr>
                                </thead>
                                <tbody className="max-h-[500px] overflow-hidden">
                                  {product?.Donuts?.map((p) => (
                                    <tr>
                                      {!!p?.special_day_name ? (
                                        <td className="flex justify-center items-center flex-col">
                                          <p className="text-mainColor bg-mainColor bg-opacity-10 text-xs rounded-md px-2 py-1 border border-mainColor w-fit mb-2">
                                            {p?.special_day_name}
                                          </p>
                                          <p>
                                            {p?.week_day.slice(0, 3)}-
                                            {dayjs(p?.daily_date).format(
                                              "MM/DD/YYYY"
                                            )}
                                          </p>
                                        </td>
                                      ) : (
                                        <td>
                                          {p?.week_day.slice(0, 3)}-
                                          {dayjs(p?.daily_date).format(
                                            "MM/DD/YYYY"
                                          )}
                                        </td>
                                      )}
                                      <td>{p?.total_order}</td>
                                      <td>{p?.total_sale}</td>
                                    </tr>
                                  ))}
                                </tbody>
                              </table>
                            </div>
                          </SwiperSlide>
                        ))}
                        {Object.values(donut)?.map((product, index) => (
                          <SwiperSlide key={index}>
                            <div className="border border-[#EAEEF7] rounded">
                              <h1 className="text-content font-bold lg:text-[48px] text-[34px] py-[14px] text-center ">
                                Fancy
                              </h1>
                              <table className="w-full order">
                                <thead>
                                  <tr>
                                    <th>Date</th>
                                    <th>Order</th>
                                    <th>Sale</th>
                                  </tr>
                                </thead>
                                <tbody className="max-h-[500px] overflow-hidden">
                                  {product?.Fancy?.map((p) => (
                                    <tr>
                                      {!!p?.special_day_name ? (
                                        <td className="flex justify-center items-center flex-col">
                                          <p className="text-mainColor bg-mainColor bg-opacity-10 text-xs rounded-md px-2 py-1 border border-mainColor w-fit mb-2">
                                            {p?.special_day_name}
                                          </p>
                                          <p>
                                            {p?.week_day.slice(0, 3)}-
                                            {dayjs(p?.daily_date).format(
                                              "MM/DD/YYYY"
                                            )}
                                          </p>
                                        </td>
                                      ) : (
                                        <td>
                                          {p?.week_day.slice(0, 3)}-
                                          {dayjs(p?.daily_date).format(
                                            "MM/DD/YYYY"
                                          )}
                                        </td>
                                      )}
                                      <td>{p?.total_order}</td>
                                      <td>{p?.total_sale}</td>
                                    </tr>
                                  ))}
                                </tbody>
                              </table>
                            </div>
                          </SwiperSlide>
                        ))}
                        {Object.values(donut)?.map((product, index) => (
                          <SwiperSlide key={index}>
                            <div className="border border-[#EAEEF7] rounded">
                              <h1 className="text-content font-bold lg:text-[48px] text-[34px] py-[14px] text-center ">
                                Munkins
                              </h1>
                              <table className="w-full order">
                                <thead>
                                  <tr>
                                    <th>Date</th>
                                    <th>Order</th>
                                    <th>Sale</th>
                                  </tr>
                                </thead>
                                <tbody className="max-h-[500px] overflow-hidden">
                                  {product?.Munkins?.map((p) => (
                                    <tr>
                                      {!!p?.special_day_name ? (
                                        <td className="flex justify-center items-center flex-col">
                                          <p className="text-mainColor bg-mainColor bg-opacity-10 text-xs rounded-md px-2 py-1 border border-mainColor w-fit mb-2">
                                            {p?.special_day_name}
                                          </p>
                                          <p>
                                            {p?.week_day.slice(0, 3)}-
                                            {dayjs(p?.daily_date).format(
                                              "MM/DD/YYYY"
                                            )}
                                          </p>
                                        </td>
                                      ) : (
                                        <td>
                                          {p?.week_day.slice(0, 3)}-
                                          {dayjs(p?.daily_date).format(
                                            "MM/DD/YYYY"
                                          )}
                                        </td>
                                      )}
                                      <td>{p?.total_order}</td>
                                      <td>{p?.total_sale}</td>
                                    </tr>
                                  ))}
                                </tbody>
                              </table>
                            </div>
                          </SwiperSlide>
                        ))}
                      </Slider>
                    ) : (
                      <div className="py-7 text-center text-xl text-red-500 border rounded-lg">
                        No data available.
                      </div>
                    )}
                  </div>
                )}

                {type === "week" && (
                  <div className="mt-8 ">
                    {donut?.length !== 0 ? (
                      <Slider SliderView={[0, 1, 2]}>
                        {Object.values(donut)?.map((product, index) => {
                          const result = convertProductData(
                            multiYear,
                            product?.Donuts
                          );

                          return (
                            <SwiperSlide key={index}>
                              <SingleTableData
                                product="Donut"
                                result={result}
                              />
                              <table className="w-full order mt-7">
                                <thead>
                                  <tr>
                                    <th>Date</th>
                                    <th>Order</th>
                                    <th>Sale</th>
                                  </tr>
                                </thead>
                                <tbody className="max-h-[500px] overflow-hidden">
                                  {product?.Donuts?.map((p) => (
                                    <tr>
                                      {!!p?.special_day_name ? (
                                        <td className="flex justify-center items-center flex-col">
                                          <p className="text-mainColor bg-mainColor bg-opacity-10 text-xs rounded-md px-2 py-1 border border-mainColor w-fit mb-2">
                                            {p?.special_day_name}
                                          </p>
                                          <p>
                                            {p?.week_day.slice(0, 3)}-
                                            {dayjs(p?.daily_date).format(
                                              "MM/DD/YYYY"
                                            )}
                                          </p>
                                        </td>
                                      ) : (
                                        <td
                                          className={
                                            p?.week_day === "Saturday" &&
                                            "bg-mainColor bg-opacity-10"
                                          }
                                        >
                                          {p?.week_day.slice(0, 3)}-
                                          {dayjs(p?.daily_date).format(
                                            "MM/DD/YYYY"
                                          )}
                                        </td>
                                      )}
                                      <td
                                        className={
                                          p?.week_day === "Saturday" &&
                                          "bg-mainColor bg-opacity-10"
                                        }
                                      >
                                        {p?.total_order}
                                      </td>
                                      <td
                                        className={
                                          p?.week_day === "Saturday" &&
                                          "bg-mainColor bg-opacity-10"
                                        }
                                      >
                                        {p?.total_sale}
                                      </td>
                                    </tr>
                                  ))}
                                </tbody>
                              </table>
                            </SwiperSlide>
                          );
                        })}
                        {Object.values(donut)?.map((product, index) => {
                          const result = convertProductData(
                            multiYear,
                            product?.Fancy
                          );

                          return (
                            <SwiperSlide key={index}>
                              <SingleTableData
                                product="Fancy"
                                result={result}
                              />
                              <table className="w-full order mt-7">
                                <thead>
                                  <tr>
                                    <th>Date</th>
                                    <th>Order</th>
                                    <th>Sale</th>
                                  </tr>
                                </thead>
                                <tbody className="max-h-[500px] overflow-hidden">
                                  {product?.Fancy?.map((p) => (
                                    <tr>
                                      {!!p?.special_day_name ? (
                                        <td className="flex justify-center items-center flex-col">
                                          <p className="text-mainColor bg-mainColor bg-opacity-10 text-xs rounded-md px-2 py-1 border border-mainColor w-fit mb-2">
                                            {p?.special_day_name}
                                          </p>
                                          <p>
                                            {p?.week_day.slice(0, 3)}-
                                            {dayjs(p?.daily_date).format(
                                              "MM/DD/YYYY"
                                            )}
                                          </p>
                                        </td>
                                      ) : (
                                        <td
                                          className={
                                            p?.week_day === "Saturday" &&
                                            "bg-mainColor bg-opacity-10"
                                          }
                                        >
                                          {p?.week_day.slice(0, 3)}-
                                          {dayjs(p?.daily_date).format(
                                            "MM/DD/YYYY"
                                          )}
                                        </td>
                                      )}
                                      <td
                                        className={
                                          p?.week_day === "Saturday" &&
                                          "bg-mainColor bg-opacity-10"
                                        }
                                      >
                                        {p?.total_order}
                                      </td>
                                      <td
                                        className={
                                          p?.week_day === "Saturday" &&
                                          "bg-mainColor bg-opacity-10"
                                        }
                                      >
                                        {p?.total_sale}
                                      </td>
                                    </tr>
                                  ))}
                                </tbody>
                              </table>
                            </SwiperSlide>
                          );
                        })}
                        {Object.values(donut)?.map((product, index) => {
                          const result = convertProductData(
                            multiYear,
                            product?.Munkins
                          );

                          return (
                            <SwiperSlide key={index}>
                              <SingleTableData
                                product="Munkins"
                                result={result}
                              />
                              <table className="w-full order mt-7">
                                <thead>
                                  <tr>
                                    <th>Date</th>
                                    <th>Order</th>
                                    <th>Sale</th>
                                  </tr>
                                </thead>
                                <tbody className="max-h-[500px] overflow-hidden">
                                  {product?.Munkins?.map((p) => (
                                    <tr>
                                      {!!p?.special_day_name ? (
                                        <td className="flex justify-center items-center flex-col">
                                          <p className="text-mainColor bg-mainColor bg-opacity-10 text-xs rounded-md px-2 py-1 border border-mainColor w-fit mb-2">
                                            {p?.special_day_name}
                                          </p>
                                          <p>
                                            {p?.week_day.slice(0, 3)}-
                                            {dayjs(p?.daily_date).format(
                                              "MM/DD/YYYY"
                                            )}
                                          </p>
                                        </td>
                                      ) : (
                                        <td
                                          className={
                                            p?.week_day === "Saturday" &&
                                            "bg-mainColor bg-opacity-10"
                                          }
                                        >
                                          {p?.week_day.slice(0, 3)}-
                                          {dayjs(p?.daily_date).format(
                                            "MM/DD/YYYY"
                                          )}
                                        </td>
                                      )}
                                      <td
                                        className={
                                          p?.week_day === "Saturday" &&
                                          "bg-mainColor bg-opacity-10"
                                        }
                                      >
                                        {p?.total_order}
                                      </td>
                                      <td
                                        className={
                                          p?.week_day === "Saturday" &&
                                          "bg-mainColor bg-opacity-10"
                                        }
                                      >
                                        {p?.total_sale}
                                      </td>
                                    </tr>
                                  ))}
                                </tbody>
                              </table>
                            </SwiperSlide>
                          );
                        })}
                      </Slider>
                    ) : (
                      <div className="py-7 text-center text-xl text-red-500 border rounded-lg">
                        No data available.
                      </div>
                    )}
                  </div>
                )}

                {type === "month" && (
                  <div className="mt-8 ">
                    {donut?.length !== 0 ? (
                      <Slider SliderView={[0, 1, 2]}>
                        {Object.values(donut)?.map((product, index) => {
                          const result = convertProductData(
                            multiYear,
                            product?.Donuts
                          );
                          return (
                            <SwiperSlide key={index}>
                              <SingleTableData
                                product="Donut"
                                result={result}
                              />
                              <table className="w-full order mt-7">
                                <thead>
                                  <tr>
                                    <th>Date</th>
                                    <th>Order</th>
                                    <th>Sale</th>
                                  </tr>
                                </thead>
                                <tbody className="max-h-[500px] overflow-hidden">
                                  {product?.Donuts?.map((p) => (
                                    <tr>
                                      {!!p?.special_day_name ? (
                                        <td
                                          className={`flex justify-center items-center flex-col ${
                                            p?.week_day === "Saturday" &&
                                            "bg-mainColor bg-opacity-10"
                                          }`}
                                        >
                                          <p className="text-mainColor bg-mainColor bg-opacity-10 text-xs rounded-md px-2 py-1 border border-mainColor w-fit mb-2">
                                            {p?.special_day_name}
                                          </p>
                                          <p>
                                            {p?.week_day.slice(0, 3)}-
                                            {dayjs(p?.daily_date).format(
                                              "MM/DD/YYYY"
                                            )}
                                          </p>
                                        </td>
                                      ) : (
                                        <td
                                          className={
                                            p?.week_day === "Saturday" &&
                                            "bg-mainColor bg-opacity-10"
                                          }
                                        >
                                          {p?.week_day.slice(0, 3)}-
                                          {dayjs(p?.daily_date).format(
                                            "MM/DD/YYYY"
                                          )}
                                        </td>
                                      )}
                                      <td
                                        className={
                                          p?.week_day === "Saturday" &&
                                          "bg-mainColor bg-opacity-10"
                                        }
                                      >
                                        {p?.total_order}
                                      </td>
                                      <td
                                        className={
                                          p?.week_day === "Saturday" &&
                                          "bg-mainColor bg-opacity-10"
                                        }
                                      >
                                        {p?.total_sale}
                                      </td>
                                    </tr>
                                  ))}
                                </tbody>
                              </table>
                            </SwiperSlide>
                          );
                        })}
                        {Object.values(donut)?.map((product, index) => {
                          const result = convertProductData(
                            multiYear,
                            product?.Fancy
                          );

                          return (
                            <SwiperSlide key={index}>
                              <SingleTableData
                                product="Fancy"
                                result={result}
                              />
                              <table className="w-full order mt-7">
                                <thead>
                                  <tr>
                                    <th>Date</th>
                                    <th>Order</th>
                                    <th>Sale</th>
                                  </tr>
                                </thead>
                                <tbody className="max-h-[500px] overflow-hidden">
                                  {product?.Fancy?.map((p) => (
                                    <tr>
                                      {!!p?.special_day_name ? (
                                        <td
                                          className={`flex justify-center items-center flex-col ${
                                            p?.week_day === "Saturday" &&
                                            "bg-mainColor bg-opacity-10"
                                          }`}
                                        >
                                          <p className="text-mainColor bg-mainColor bg-opacity-10 text-xs rounded-md px-2 py-1 border border-mainColor w-fit mb-2">
                                            {p?.special_day_name}
                                          </p>
                                          <p>
                                            {p?.week_day.slice(0, 3)}-
                                            {dayjs(p?.daily_date).format(
                                              "MM/DD/YYYY"
                                            )}
                                          </p>
                                        </td>
                                      ) : (
                                        <td
                                          className={
                                            p?.week_day === "Saturday" &&
                                            "bg-mainColor bg-opacity-10"
                                          }
                                        >
                                          {p?.week_day.slice(0, 3)}-
                                          {dayjs(p?.daily_date).format(
                                            "MM/DD/YYYY"
                                          )}
                                        </td>
                                      )}
                                      <td
                                        className={
                                          p?.week_day === "Saturday" &&
                                          "bg-mainColor bg-opacity-10"
                                        }
                                      >
                                        {p?.total_order}
                                      </td>
                                      <td
                                        className={
                                          p?.week_day === "Saturday" &&
                                          "bg-mainColor bg-opacity-10"
                                        }
                                      >
                                        {p?.total_sale}
                                      </td>
                                    </tr>
                                  ))}
                                </tbody>
                              </table>
                            </SwiperSlide>
                          );
                        })}
                        {Object.values(donut)?.map((product, index) => {
                          const result = convertProductData(
                            multiYear,
                            product?.Munkins
                          );

                          return (
                            <SwiperSlide key={index}>
                              <SingleTableData
                                product="Munkins"
                                result={result}
                              />
                              <table className="w-full order mt-7">
                                <thead>
                                  <tr>
                                    <th>Date</th>
                                    <th>Order</th>
                                    <th>Sale</th>
                                  </tr>
                                </thead>
                                <tbody className="max-h-[500px] overflow-hidden">
                                  {product?.Munkins?.map((p) => (
                                    <tr>
                                      {!!p?.special_day_name ? (
                                        <td
                                          className={`flex justify-center items-center flex-col ${
                                            p?.week_day === "Saturday" &&
                                            "bg-mainColor bg-opacity-10"
                                          }`}
                                        >
                                          <p className="text-mainColor bg-mainColor bg-opacity-10 text-xs rounded-md px-2 py-1 border border-mainColor w-fit mb-2">
                                            {p?.special_day_name}
                                          </p>
                                          <p>
                                            {p?.week_day.slice(0, 3)}-
                                            {dayjs(p?.daily_date).format(
                                              "MM/DD/YYYY"
                                            )}
                                          </p>
                                        </td>
                                      ) : (
                                        <td
                                          className={
                                            p?.week_day === "Saturday" &&
                                            "bg-mainColor bg-opacity-10"
                                          }
                                        >
                                          {p?.week_day.slice(0, 3)}-
                                          {dayjs(p?.daily_date).format(
                                            "MM/DD/YYYY"
                                          )}
                                        </td>
                                      )}
                                      <td
                                        className={
                                          p?.week_day === "Saturday" &&
                                          "bg-mainColor bg-opacity-10"
                                        }
                                      >
                                        {p?.total_order}
                                      </td>
                                      <td
                                        className={
                                          p?.week_day === "Saturday" &&
                                          "bg-mainColor bg-opacity-10"
                                        }
                                      >
                                        {p?.total_sale}
                                      </td>
                                    </tr>
                                  ))}
                                </tbody>
                              </table>
                            </SwiperSlide>
                          );
                        })}
                      </Slider>
                    ) : (
                      <div className="py-7 text-center text-xl text-red-500 border rounded-lg">
                        No data available.
                      </div>
                    )}
                  </div>
                )}

                {type === "year" && (
                  <div className="mt-8 ">
                    {donut?.length !== 0 ? (
                      <Slider SliderView={[0, 1, 2]}>
                        {Object.values(donut)?.map((product, index) => {
                          const result = convertProductData(
                            multiYear,
                            product?.Donuts
                          );
                          return (
                            <SwiperSlide key={index}>
                              <SingleTableData
                                product="Donut"
                                result={result}
                              />
                            </SwiperSlide>
                          );
                        })}
                        {Object.values(donut)?.map((product, index) => {
                          const result = convertProductData(
                            multiYear,
                            product?.Fancy
                          );

                          return (
                            <SwiperSlide key={index}>
                              <SingleTableData
                                product="Fancy"
                                result={result}
                              />
                            </SwiperSlide>
                          );
                        })}
                        {Object.values(donut)?.map((product, index) => {
                          const result = convertProductData(
                            multiYear,
                            product?.Munkins
                          );

                          return (
                            <SwiperSlide key={index}>
                              <SingleTableData
                                product="Munkins"
                                result={result}
                              />
                            </SwiperSlide>
                          );
                        })}
                      </Slider>
                    ) : (
                      <div className="py-7 text-center text-xl text-red-500 border rounded-lg">
                        No data available.
                      </div>
                    )}
                  </div>
                )}

                {type === "custom" && (
                  <div className="mt-8 ">
                    {donut?.length !== 0 ? (
                      <Slider SliderView={[0, 1, 2]}>
                        {Object.values(donut)?.map((product, index) => {
                          const result = convertProductDataWeek(
                            product?.Donuts
                          );
                          return (
                            <SwiperSlide key={index}>
                              <SingleTableData
                                product="Donut"
                                result={result}
                              />
                              <table className="w-full order mt-7">
                                <thead>
                                  <tr>
                                    <th>Date</th>
                                    <th>Order</th>
                                    <th>Sale</th>
                                  </tr>
                                </thead>
                                <tbody className="max-h-[500px] overflow-hidden">
                                  {product?.Donuts?.map((p) => (
                                    <tr>
                                      {!!p?.special_day_name ? (
                                        <td
                                          className={`flex justify-center items-center flex-col ${
                                            p?.week_day === "Saturday" &&
                                            "bg-mainColor bg-opacity-10"
                                          }`}
                                        >
                                          <p className="text-mainColor bg-mainColor bg-opacity-10 text-xs rounded-md px-2 py-1 border border-mainColor w-fit mb-2">
                                            {p?.special_day_name}
                                          </p>
                                          <p>
                                            {p?.week_day.slice(0, 3)}-
                                            {dayjs(p?.daily_date).format(
                                              "MM/DD/YYYY"
                                            )}
                                          </p>
                                        </td>
                                      ) : (
                                        <td
                                          className={
                                            p?.week_day === "Saturday" &&
                                            "bg-mainColor bg-opacity-10"
                                          }
                                        >
                                          {p?.week_day.slice(0, 3)}-
                                          {dayjs(p?.daily_date).format(
                                            "MM/DD/YYYY"
                                          )}
                                        </td>
                                      )}
                                      <td
                                        className={
                                          p?.week_day === "Saturday" &&
                                          "bg-mainColor bg-opacity-10"
                                        }
                                      >
                                        {p?.total_order}
                                      </td>
                                      <td
                                        className={
                                          p?.week_day === "Saturday" &&
                                          "bg-mainColor bg-opacity-10"
                                        }
                                      >
                                        {p?.total_sale}
                                      </td>
                                    </tr>
                                  ))}
                                </tbody>
                              </table>
                            </SwiperSlide>
                          );
                        })}
                        {Object.values(donut)?.map((product, index) => {
                          const result = convertProductDataWeek(product?.Fancy);

                          return (
                            <SwiperSlide key={index}>
                              <SingleTableData
                                product="Fancy"
                                result={result}
                              />
                              <table className="w-full order mt-7">
                                <thead>
                                  <tr>
                                    <th>Date</th>
                                    <th>Order</th>
                                    <th>Sale</th>
                                  </tr>
                                </thead>
                                <tbody className="max-h-[500px] overflow-hidden">
                                  {product?.Fancy?.map((p) => (
                                    <tr>
                                      {!!p?.special_day_name ? (
                                        <td
                                          className={`flex justify-center items-center flex-col ${
                                            p?.week_day === "Saturday" &&
                                            "bg-mainColor bg-opacity-10"
                                          }`}
                                        >
                                          <p className="text-mainColor bg-mainColor bg-opacity-10 text-xs rounded-md px-2 py-1 border border-mainColor w-fit mb-2">
                                            {p?.special_day_name}
                                          </p>
                                          <p>
                                            {p?.week_day.slice(0, 3)}-
                                            {dayjs(p?.daily_date).format(
                                              "MM/DD/YYYY"
                                            )}
                                          </p>
                                        </td>
                                      ) : (
                                        <td
                                          className={
                                            p?.week_day === "Saturday" &&
                                            "bg-mainColor bg-opacity-10"
                                          }
                                        >
                                          {p?.week_day.slice(0, 3)}-
                                          {dayjs(p?.daily_date).format(
                                            "MM/DD/YYYY"
                                          )}
                                        </td>
                                      )}
                                      <td
                                        className={
                                          p?.week_day === "Saturday" &&
                                          "bg-mainColor bg-opacity-10"
                                        }
                                      >
                                        {p?.total_order}
                                      </td>
                                      <td
                                        className={
                                          p?.week_day === "Saturday" &&
                                          "bg-mainColor bg-opacity-10"
                                        }
                                      >
                                        {p?.total_sale}
                                      </td>
                                    </tr>
                                  ))}
                                </tbody>
                              </table>
                            </SwiperSlide>
                          );
                        })}
                        {Object.values(donut)?.map((product, index) => {
                          const result = convertProductDataWeek(
                            product?.Munkins
                          );

                          return (
                            <SwiperSlide key={index}>
                              <SingleTableData
                                product="Munkins"
                                result={result}
                              />
                              <table className="w-full order mt-7">
                                <thead>
                                  <tr>
                                    <th>Date</th>
                                    <th>Order</th>
                                    <th>Sale</th>
                                  </tr>
                                </thead>
                                <tbody className="max-h-[500px] overflow-hidden">
                                  {product?.Munkins?.map((p) => (
                                    <tr>
                                      {!!p?.special_day_name ? (
                                        <td
                                          className={`flex justify-center items-center flex-col ${
                                            p?.week_day === "Saturday" &&
                                            "bg-mainColor bg-opacity-10"
                                          }`}
                                        >
                                          <p className="text-mainColor bg-mainColor bg-opacity-10 text-xs rounded-md px-2 py-1 border border-mainColor w-fit mb-2">
                                            {p?.special_day_name}
                                          </p>
                                          <p>
                                            {p?.week_day.slice(0, 3)}-
                                            {dayjs(p?.daily_date).format(
                                              "MM/DD/YYYY"
                                            )}
                                          </p>
                                        </td>
                                      ) : (
                                        <td
                                          className={
                                            p?.week_day === "Saturday" &&
                                            "bg-mainColor bg-opacity-10"
                                          }
                                        >
                                          {p?.week_day.slice(0, 3)}-
                                          {dayjs(p?.daily_date).format(
                                            "MM/DD/YYYY"
                                          )}
                                        </td>
                                      )}
                                      <td
                                        className={
                                          p?.week_day === "Saturday" &&
                                          "bg-mainColor bg-opacity-10"
                                        }
                                      >
                                        {p?.total_order}
                                      </td>
                                      <td
                                        className={
                                          p?.week_day === "Saturday" &&
                                          "bg-mainColor bg-opacity-10"
                                        }
                                      >
                                        {p?.total_sale}
                                      </td>
                                    </tr>
                                  ))}
                                </tbody>
                              </table>
                            </SwiperSlide>
                          );
                        })}
                      </Slider>
                    ) : (
                      <div className="py-7 text-center text-xl text-red-500 border rounded-lg">
                        No data available.
                      </div>
                    )}
                  </div>
                )}
              </>
            )}
          </div>
        )}

        {showSpecialData === true && (
          <div className="mt-8">
            <div className="flex md:flex-row flex-col md:justify-between gap-y-4">
              <h2 className="text-content text-2xl font-bold">All Store</h2>
            </div>
            {specialLoading ? (
              <div>
                <MainLoader />
              </div>
            ) : (
              <>
                <div className="pr-4 swiper-tabbar mt-8">
                  <Swiper
                    slidesPerView="auto"
                    spaceBetween={24}
                    freeMode={true}
                    modules={[Autoplay, FreeMode, Pagination]}
                    className="mySwiper w-full"
                  >
                    {storeData?.map((store, index) => (
                      <SwiperSlide key={index}>
                        <div
                          className="border-none outline-none"
                          onClick={() => setStoreValue(store)}
                        >
                          <h1
                            onClick={() => getSpecialData({ store_key: store })}
                            className={`${
                              storeValue === store
                                ? "bg-[#E48586] text-mainColor border border-mainColor bg-opacity-10"
                                : "bg-white text-[#44566C]"
                            } 
                  } py-[10px] w-fit flex justify-center items-center px-5  rounded capitalize cursor-pointer hover:bg-[#E48586] hover:text-white`}
                          >
                            {store}
                          </h1>
                        </div>
                      </SwiperSlide>
                    ))}
                  </Swiper>
                </div>
                <div className="mt-8 ">
                  {specialData?.length !== 0 ? (
                    <Slider SliderView={[0, 1, 2]}>
                      {Object.values(specialData)?.map((product, index) => (
                        <SwiperSlide key={index}>
                          <div className="border border-[#EAEEF7] rounded">
                            <h1 className="text-content font-bold lg:text-[48px] text-[34px] py-[14px] text-center ">
                              Donut
                            </h1>
                            <table className="w-full order">
                              <thead>
                                <tr>
                                  <th>Date</th>
                                  <th>Order</th>
                                  <th>Sale</th>
                                </tr>
                              </thead>
                              <tbody className="max-h-[500px] overflow-hidden">
                                {product?.Donuts?.map((p) => (
                                  <tr>
                                    {!!p?.special_day_name ? (
                                      <td className="flex justify-center items-center flex-col">
                                        <p className="text-mainColor bg-mainColor bg-opacity-10 text-xs rounded-md px-2 py-1 border border-mainColor w-fit mb-2">
                                          {p?.special_day_name}
                                        </p>
                                        <p>
                                          {p?.week_day.slice(0, 3)}-
                                          {dayjs(p?.daily_date).format(
                                            "MM/DD/YYYY"
                                          )}
                                        </p>
                                      </td>
                                    ) : (
                                      <td>
                                        {p?.week_day.slice(0, 3)}-
                                        {dayjs(p?.daily_date).format(
                                          "MM/DD/YYYY"
                                        )}
                                      </td>
                                    )}
                                    <td>{p?.total_order}</td>
                                    <td>{p?.total_sale}</td>
                                  </tr>
                                ))}
                              </tbody>
                            </table>
                          </div>
                        </SwiperSlide>
                      ))}
                      {Object.values(specialData)?.map((product, index) => (
                        <SwiperSlide key={index}>
                          <div className="border border-[#EAEEF7] rounded">
                            <h1 className="text-content font-bold lg:text-[48px] text-[34px] py-[14px] text-center ">
                              Fancy
                            </h1>
                            <table className="w-full order">
                              <thead>
                                <tr>
                                  <th>Date</th>
                                  <th>Order</th>
                                  <th>Sale</th>
                                </tr>
                              </thead>
                              <tbody className="max-h-[500px] overflow-hidden">
                                {product?.Fancy?.map((p) => (
                                  <tr>
                                    {!!p?.special_day_name ? (
                                      <td className="flex justify-center items-center flex-col">
                                        <p className="text-mainColor bg-mainColor bg-opacity-10 text-xs rounded-md px-2 py-1 border border-mainColor w-fit mb-2">
                                          {p?.special_day_name}
                                        </p>
                                        <p>
                                          {p?.week_day.slice(0, 3)}-
                                          {dayjs(p?.daily_date).format(
                                            "MM/DD/YYYY"
                                          )}
                                        </p>
                                      </td>
                                    ) : (
                                      <td>
                                        {p?.week_day.slice(0, 3)}-
                                        {dayjs(p?.daily_date).format(
                                          "MM/DD/YYYY"
                                        )}
                                      </td>
                                    )}
                                    <td>{p?.total_order}</td>
                                    <td>{p?.total_sale}</td>
                                  </tr>
                                ))}
                              </tbody>
                            </table>
                          </div>
                        </SwiperSlide>
                      ))}
                      {Object.values(specialData)?.map((product, index) => (
                        <SwiperSlide key={index}>
                          <div className="border border-[#EAEEF7] rounded">
                            <h1 className="text-content font-bold lg:text-[48px] text-[34px] py-[14px] text-center ">
                              Munkins
                            </h1>
                            <table className="w-full order">
                              <thead>
                                <tr>
                                  <th>Date</th>
                                  <th>Order</th>
                                  <th>Sale</th>
                                </tr>
                              </thead>
                              <tbody className="max-h-[500px] overflow-hidden">
                                {product?.Munkins?.map((p) => (
                                  <tr>
                                    {!!p?.special_day_name ? (
                                      <td className="flex justify-center items-center flex-col">
                                        <p className="text-mainColor bg-mainColor bg-opacity-10 text-xs rounded-md px-2 py-1 border border-mainColor w-fit mb-2">
                                          {p?.special_day_name}
                                        </p>
                                        <p>
                                          {p?.week_day.slice(0, 3)}-
                                          {dayjs(p?.daily_date).format(
                                            "MM/DD/YYYY"
                                          )}
                                        </p>
                                      </td>
                                    ) : (
                                      <td>
                                        {p?.week_day.slice(0, 3)}-
                                        {dayjs(p?.daily_date).format(
                                          "MM/DD/YYYY"
                                        )}
                                      </td>
                                    )}
                                    <td>{p?.total_order}</td>
                                    <td>{p?.total_sale}</td>
                                  </tr>
                                ))}
                              </tbody>
                            </table>
                          </div>
                        </SwiperSlide>
                      ))}
                    </Slider>
                  ) : (
                    <div className="py-7 text-center text-xl text-red-500 border rounded-lg">
                      No data available.
                    </div>
                  )}
                </div>
              </>
            )}
          </div>
        )}
      </Card>

      <Modal
        open={open}
        onCancel={() => setOpen(false)}
        title={"Add New Special Day"}
        footer={null}
      >
        <Form
          form={specialDayForm}
          layout="vertical"
          onFinish={async (values) => {
            // values.date = dayjs(values.date).format("YYYY-MM-DD");
            // values.status = "A"
            // return useAction(addSpecialDay, values, () => {
            //   setOpen(false);
            // });
            fetch("https://mansiraja.tech/api/special_day/add", {
              method: "POST",
              body: JSON.stringify({
                name: values.name,
                date: dayjs(values.date).format("YYYY-MM-DD"),
                store_key: storeKeyString,
                status: "A",
              }),
              headers: {
                "Content-type": "application/json",
                Authorization: `Bearer ${localStorage.getItem("token")}`,
              },
            })
              .then((response) => {
                if (!response.ok) {
                  throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
              })
              .then((data) => {
                message.success(data?.message);
                specialDayForm.resetFields();
                setOpen(false);
                getSpecialDays();
              })
              .catch((error) => {
                message.error("Something went wrong!");
              });
          }}
        >
          <Row gutter={24} className="mb-5">
            <Col xs={24}>
              <FormInput label="Name" name="name" required />
            </Col>
            <Col xs={24}>
              <Form.Item
                label="Select Date"
                name="date"
                rules={[{ required: true, message: "Please select a date" }]}
              >
                <DatePicker format="YYYY-MM-DD" />
              </Form.Item>
            </Col>
            <Col xs={24}>
              <Select
                mode="multiple"
                size={"small"}
                placeholder="Please select"
                onChange={handleChangeStoreKey}
                style={{
                  width: "100%",
                  height: "auto",
                }}
                options={[
                  { label: "Select All Stores", value: "selectAll" },
                  ...(storeData?.map((store) => ({
                    label: store,
                    value: store,
                  })) || []),
                ]}
                value={multiStoreKey}
                showSearch={true}
                allowClear
              />
            </Col>
          </Row>

          <Button className="mt-2.5">Submit</Button>
        </Form>
      </Modal>
    </div>
  );
};

export default DonutCountComponent;

export const convertProductData = (multiYear, product) => {
  return multiYear.map((year) => {
    const filteredItems = product?.filter((item) => {
      return parseInt(dayjs(item?.daily_date).format("YYYY")) === year;
    });

    const totalOrderSum = filteredItems.reduce((sum, item) => {
      return sum + (parseFloat(item?.total_order) || 0);
    }, 0);

    const totalSaleSum = filteredItems.reduce((sum, item) => {
      return sum + (parseFloat(item?.total_sale) || 0);
    }, 0);

    return {
      year,
      totalOrderSum,
      totalSaleSum,
    };
  });
};
