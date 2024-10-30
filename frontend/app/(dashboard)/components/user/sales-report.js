"use client";
import { Radio, Select } from "antd";
import React, { useEffect, useState } from "react";
import { IoMdCalendar } from "react-icons/io";
import { CiShop } from "react-icons/ci";
import {
  BarChart,
  Bar,
  Rectangle,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  Legend,
  ResponsiveContainer,
} from "recharts";
import { useFetch } from "../../../helpers/hooks";
import { fetchSalesReport } from "../../../helpers/backend";
import dayjs from "dayjs";
import { formatTimeRange } from "./total-sales";

const SalesReport = ({ storeData }) => {
  const [storeId, setStoreId] = useState(null);
  const [time_range, setTimeRange] = useState("current_month");
  const [productType, setProductType] = useState("Donuts");

  const [data, setData] = useState();

  const [salesReport, getSalesReport, { loading }] = useFetch(
    fetchSalesReport,
    {},
    false
  );

  useEffect(() => {
    if (storeData?.length > 0) {
      setStoreId(storeData[1]);
    }
  }, [storeData]);

  useEffect(() => {
    if (storeId && time_range && productType) {
      getSalesReport({
        store_key: storeId,
        time_range: time_range,
        donut_type: productType,
      });
    }
  }, [storeId, time_range, productType]);

  const donutsData =
    salesReport && salesReport[storeId] && salesReport[storeId][productType]
      ? salesReport[storeId][productType]
      : null;

  useEffect(() => {
    if (storeId && time_range && productType) {
      const dataNew =
        donutsData &&
        Object.keys(donutsData).map((date) => {
          const entry = donutsData[date];
          return {
            name: dayjs(entry.daily_date).format("MMM DD"),
            orders: parseInt(entry.total_orders, 10),
            sales: parseFloat(entry.total_sales),
          };
        });

      setData(dataNew);
    }
  }, [storeId, time_range, productType, donutsData]);

  return (
    <>
      <div className="bg-white rounded-lg shadow-lg">
        <div className="py-5 md:px-8 px-4">
          <div className="flex flex-wrap justify-between items-center gap-y-4 ">
            <div className="flex flex-wrap md:gap-x-8 items-center gap-y-4 sales">
              <p className="text-xl font-bold text-[#44566C]">Sales Report</p>
              <Radio.Group
                options={[
                  { label: "Donuts", value: "Donuts" },
                  { label: "Fancy", value: "Fancy" },
                  { label: "Munkins", value: "Munkins" },
                ]}
                onChange={(e) => setProductType(e.target.value)}
                value={productType}
              />
            </div>

            <div className="flex items-center space-x-3">
              <Select
                style={{
                  width: 160,
                }}
                labelInValue
                value={{
                  value: time_range,
                  label: (
                    <>
                      <div className="flex items-center space-x-2 mt-1">
                        <IoMdCalendar size={24} className="text-gray-400" />
                        <p className="text-sm font-normal text-[#8697A8]">
                          {formatTimeRange(time_range)}
                        </p>
                      </div>
                    </>
                  ),
                }}
                onChange={(e) => {
                  setTimeRange(e?.value);
                }}
                options={[
                  {
                    value: "last_7_days",
                    label: (
                      <>
                        <div className="flex items-center space-x-2 mt-1">
                          <IoMdCalendar size={24} className="text-gray-400" />
                          <p className="text-sm font-normal text-[#8697A8]">
                            Last 7 Days
                          </p>
                        </div>
                      </>
                    ),
                  },
                  {
                    value: "current_month",
                    label: (
                      <>
                        <div className="flex items-center space-x-2 mt-1">
                          <IoMdCalendar size={24} className="text-gray-400" />
                          <p className="text-sm font-normal text-[#8697A8]">
                            Current Month
                          </p>
                        </div>
                      </>
                    ),
                  },
                  {
                    value: "last_month",
                    label: (
                      <>
                        <div className="flex items-center space-x-2 mt-1">
                          <IoMdCalendar size={24} className="text-gray-400" />
                          <p className="text-sm font-normal text-[#8697A8]">
                            Last Month
                          </p>
                        </div>
                      </>
                    ),
                  },
                ]}
              />
              <Select
                style={{
                  width: 120,
                }}
                labelInValue
                value={{
                  value: storeId,
                  label: (
                    <>
                      <div className="flex items-center space-x-2 mt-1">
                        <CiShop size={24} className="text-gray-400" />
                        <p className="text-sm font-normal text-[#8697A8]">
                          {storeId}
                        </p>
                      </div>
                    </>
                  ),
                }}
                onChange={(e) => {
                  setStoreId(e?.value);
                }}
                options={storeData?.map((store, index) => ({
                  value: store,
                  label: (
                    <>
                      <div className="flex items-center space-x-2 mt-1">
                        <CiShop size={24} className="text-gray-400" />
                        <p className="text-sm font-normal text-[#8697A8]">
                          {store}
                        </p>
                      </div>
                    </>
                  ),
                }))}
              />
            </div>
          </div>
          <div className="h-full w-full mt-16 overflow-x-auto">
            <ResponsiveContainer
              className={`md:!w-full md:!h-[500px] !h-[350px] ${
                time_range === "last_7_days"
                  ? "!w-full"
                  : time_range === "current_month"
                  ? "!w-[150%]"
                  : "!w-[300%]"
              }`}
            >
              <BarChart
                // width={"100%"}
                height={500}
                data={data}
                margin={{
                  top: 5,
                  right: 0,
                  left: 0,
                  bottom: 5,
                }}
              >
                <CartesianGrid strokeDasharray="3 3" />
                <XAxis dataKey="name" />
                <YAxis />
                <Tooltip />
                <Legend />
                <Bar dataKey="sales" fill="#3BBDC4" activeBar={<Rectangle />} />
                <Bar
                  dataKey="orders"
                  fill="#FAAFBE"
                  activeBar={<Rectangle />}
                />
              </BarChart>
            </ResponsiveContainer>
          </div>
        </div>
      </div>
    </>
  );
};

export default SalesReport;
