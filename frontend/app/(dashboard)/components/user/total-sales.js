"use client";
import React, { useEffect, useState } from "react";
import { Progress, Select } from "antd";
import { IoMdCalendar } from "react-icons/io";
import { CiShop } from "react-icons/ci";
import Slider from "../common/slider";
import { SwiperSlide } from "swiper/react";
import { fetchSalesReport } from "../../../helpers/backend";
import { useFetch } from "../../../helpers/hooks";
const TotalSales = ({ storeData }) => {
  const [storeId, setStoreId] = useState(null);
  const [time_range, setTimeRange] = useState("last_7_days");
  const [size, setSize] = useState(180);

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
    if (storeId && time_range) {
      getSalesReport({
        store_key: storeId,
        time_range: time_range,
        total: 1,
      });
    }
  }, [storeId, time_range]);

  useEffect(() => {
    const handleResize = () => {
      if (window.innerWidth < 640) {
        setSize(140);
      } else if (window.innerWidth < 1260) {
        setSize(100);
      } else {
        setSize(180);
      }
    };

    window.addEventListener("resize", handleResize);
    handleResize();

    return () => {
      window.removeEventListener("resize", handleResize);
    };
  }, []);

  const storeDataFind =
    salesReport && salesReport[storeId] ? salesReport[storeId] : null;
  const findKeys = storeDataFind ? Object.keys(storeDataFind) : [];

  // Find 'Donuts' data dynamically
  const donutData = findKeys.includes("Donuts")
    ? storeDataFind["Donuts"][0]
    : null;
  const fancyData = findKeys.includes("Fancy")
    ? storeDataFind["Fancy"][0]
    : null;
  const munkinsData = findKeys.includes("Munkins")
    ? storeDataFind["Munkins"][0]
    : null;

  // if (loading) {
  //   return (
  //     <>
  //       <MainLoader />
  //     </>
  //   );
  // }

  return (
    <>
      <div className="progress">
        <div className="flex flex-wrap justify-between items-center mx-5 py-6">
          <p className="text-2xl font-bold text-gray-700">Total Sales</p>
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
                        {time_range === "YTD"
                          ? "YTD"
                          : time_range === "2YTD"
                          ? "2YTD"
                          : formatTimeRange(time_range)}
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
                {
                  value: "YTD",
                  label: (
                    <>
                      <div className="flex items-center space-x-2 mt-1">
                        <IoMdCalendar size={24} className="text-gray-400" />
                        <p className="text-sm font-normal text-[#8697A8]">
                          YTD
                        </p>
                      </div>
                    </>
                  ),
                },
                {
                  value: "2YTD",
                  label: (
                    <>
                      <div className="flex items-center space-x-2 mt-1">
                        <IoMdCalendar size={24} className="text-gray-400" />
                        <p className="text-sm font-normal text-[#8697A8]">
                          2YTD
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
                    <div
                      key={index}
                      className="flex items-center space-x-2 mt-1"
                    >
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

        <Slider SliderView={[0, 1, 2]}>
          <div className="grid grid-cols-1 lg:grid-cols-1 xl:grid-cols-1 gap-8">
            <SwiperSlide>
              <div className="col-span-1 bg-white rounded-lg shadow-lg">
                <div className="py-5 lg:px-8 px-7">
                  <div className="flex justify-between items-center">
                    <div className="flex flex-col justify-between h-[140px] md:h-[120px] lg:h-[190px]">
                      <div>
                        <p className="text-md font-normal text-[#8697A8]">
                          {donutData?.donut_type
                            ? donutData?.donut_type
                            : "Donuts"}
                        </p>
                        <div className="flex flex-wrap items-baseline">
                          <p className="text-2xl font-bold text-gray-700 mt-3">
                            {donutData?.total_sales
                              ? parseFloat(donutData?.total_sales).toFixed(2)
                              : 0}
                            <span className="mx-1">/</span>
                          </p>
                          <p className="text-base font-bold text-gray-700">
                            {donutData?.total_orders
                              ? parseFloat(donutData?.total_orders).toFixed(2)
                              : 0}
                          </p>
                        </div>
                      </div>
                      <p className="text-sm font-normal text-[#44566C)]">
                        <span className="text-[#E48586]">
                          {donutData?.total_sales
                            ? (
                                (
                                  parseFloat(donutData?.total_sales).toFixed(
                                    2
                                  ) /
                                  parseFloat(donutData?.total_orders).toFixed(2)
                                ).toFixed(2) * 100
                              )?.toFixed(2)
                            : 0}
                          %
                        </span>{" "}
                        Sales on your order
                      </p>
                    </div>
                    <div className="relative">
                      <Progress
                        trailColor="rgba(239, 237, 248, 0.50);"
                        strokeWidth={10}
                        type="circle"
                        percent={
                          donutData?.total_sales
                            ? (
                                (
                                  parseFloat(donutData?.total_sales).toFixed(
                                    2
                                  ) /
                                  parseFloat(donutData?.total_orders).toFixed(2)
                                ).toFixed(2) * 100
                              )?.toFixed(2)
                            : 0
                        }
                        strokeColor={{ "0%": "#FAAFBE", "100%": "#FFE0E6" }}
                        size={size}
                      />
                      <div className="h-4 w-4 rounded-full bg-[#FAAFBE] absolute top-2 right-0"></div>
                      <div className="h-3 w-3 rounded-full bg-[#7FC7D9] absolute bottom-0 left-0"></div>
                      <div className="h-2 w-2 rounded-full bg-[#DED0B6] absolute bottom-3 right-3"></div>
                    </div>
                  </div>
                </div>
              </div>
            </SwiperSlide>
            <SwiperSlide>
              <div className="col-span-1 bg-white rounded-lg shadow-lg">
                <div className="py-5 lg:px-8 px-7">
                  <div className="flex justify-between items-center">
                    <div className="flex flex-col justify-between h-[140px] md:h-[120px] lg:h-[190px]">
                      <div>
                        <p className="text-md font-normal text-[#8697A8]">
                          {fancyData?.donut_type
                            ? fancyData?.donut_type
                            : "Fancy"}
                        </p>
                        <div className="flex flex-wrap items-baseline">
                          <p className="text-2xl font-bold text-gray-700 mt-3">
                            {fancyData?.total_sales
                              ? parseFloat(fancyData?.total_sales).toFixed(2)
                              : 0}
                            <span className="mx-1">/</span>
                          </p>
                          <p className="text-base font-bold text-gray-700">
                            {fancyData?.total_orders
                              ? parseFloat(fancyData?.total_orders).toFixed(2)
                              : 0}
                          </p>
                        </div>
                      </div>
                      <p className="text-sm font-normal text-[#44566C)]">
                        <span className="text-[#7FC7D9]">
                          {fancyData?.total_sales
                            ? (
                                (
                                  parseFloat(fancyData?.total_sales).toFixed(
                                    2
                                  ) /
                                  parseFloat(fancyData?.total_orders).toFixed(2)
                                ).toFixed(2) * 100
                              )?.toFixed(2)
                            : 0}
                          %
                        </span>{" "}
                        Sales on your order
                      </p>
                    </div>
                    <div className="relative">
                      <Progress
                        trailColor="rgba(239, 237, 248, 0.50);"
                        strokeWidth={10}
                        type="circle"
                        percent={
                          fancyData?.total_sales
                            ? (
                                (
                                  parseFloat(fancyData?.total_sales).toFixed(
                                    2
                                  ) /
                                  parseFloat(fancyData?.total_orders).toFixed(2)
                                ).toFixed(2) * 100
                              )?.toFixed(2)
                            : 0
                        }
                        strokeColor={{
                          "0%": "#7FC7D9 ",
                          "50%": "#B5E2ED",
                          "100%": "#7FC7D9",
                        }}
                        size={size}
                      />
                      <div className="h-4 w-4 rounded-full bg-[#7FC7D9] absolute top-2 right-0"></div>
                      <div className="h-3 w-3 rounded-full bg-[#FAAFBE] absolute bottom-0 left-0"></div>
                      <div className="h-2 w-2 rounded-full bg-[#DED0B6] absolute bottom-3 right-3"></div>
                    </div>
                  </div>
                </div>
              </div>
            </SwiperSlide>
            <SwiperSlide>
              <div className="col-span-1 bg-white rounded-lg shadow-lg">
                <div className="py-5 lg:px-8 px-7">
                  <div className="flex justify-between items-center">
                    <div className="flex flex-col justify-between h-[140px] md:h-[120px] lg:h-[190px]">
                      <div>
                        <p className="text-md font-normal text-[#8697A8]">
                          {munkinsData?.donut_type
                            ? munkinsData?.donut_type
                            : "Munkins"}
                        </p>
                        <div className="flex flex-wrap items-baseline">
                          <p className="text-2xl font-bold text-gray-700 mt-3">
                            {munkinsData?.total_sales
                              ? parseFloat(munkinsData?.total_sales).toFixed(2)
                              : 0}
                            <span className="mx-1">/</span>
                          </p>
                          <p className="text-base font-bold text-gray-700">
                            {munkinsData?.total_orders
                              ? parseFloat(munkinsData?.total_orders).toFixed(2)
                              : 0}
                          </p>
                        </div>
                      </div>
                      <p className="text-sm font-normal text-[#44566C)]">
                        <span className="text-[#DED0B6]">
                          {munkinsData?.total_sales
                            ? (
                                (
                                  parseFloat(munkinsData?.total_sales).toFixed(
                                    2
                                  ) /
                                  parseFloat(munkinsData?.total_orders).toFixed(
                                    2
                                  )
                                ).toFixed(2) * 100
                              )?.toFixed(2)
                            : 0}
                          %
                        </span>{" "}
                        Sales on your order
                      </p>
                    </div>
                    <div className="relative">
                      <Progress
                        trailColor="rgba(239, 237, 248, 0.50);"
                        strokeWidth={10}
                        type="circle"
                        percent={
                          munkinsData?.total_sales
                            ? (
                                (
                                  parseFloat(munkinsData?.total_sales).toFixed(
                                    2
                                  ) /
                                  parseFloat(munkinsData?.total_orders).toFixed(
                                    2
                                  )
                                ).toFixed(2) * 100
                              )?.toFixed(2)
                            : 0
                        }
                        strokeColor={{ "0%": "#F2E4CA", "100%": "#DED0B6" }}
                        size={size}
                      />
                      <div className="h-4 w-4 rounded-full bg-[#DED0B6] absolute top-2 right-0"></div>
                      <div className="h-3 w-3 rounded-full bg-[#7FC7D9] absolute bottom-0 left-0"></div>
                      <div className="h-2 w-2 rounded-full bg-[#FAAFBE] absolute bottom-3 right-3"></div>
                    </div>
                  </div>
                </div>
              </div>
            </SwiperSlide>
          </div>
        </Slider>
      </div>
    </>
  );
};

export default TotalSales;

export function formatTimeRange(timeRange) {
  const formattedTimeRange = timeRange.replace(/_/g, " ");

  return formattedTimeRange
    .toLowerCase()
    .split(" ")
    .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
    .join(" ");
}
