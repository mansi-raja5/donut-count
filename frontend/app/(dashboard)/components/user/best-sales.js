"use client";
import { Select } from "antd";
import React, { useEffect, useState } from "react";
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from "chart.js";
import { Doughnut } from "react-chartjs-2";
import ChartDataLabels from "chartjs-plugin-datalabels";
import { fetchBestSale } from "../../../helpers/backend";
import { useFetch } from "../../../helpers/hooks";
import { IoMdCalendar } from "react-icons/io";
import MainLoader from "../common/loader";
import { formatTimeRange } from "./total-sales";
import { GiDonut } from "react-icons/gi";
import dayjs from "dayjs";

// ChartJS.register(ArcElement, Tooltip, Legend);
ChartJS.register(ArcElement, Tooltip, Legend, ChartDataLabels);

const BestSales = () => {
  const [time_range, setTimeRange] = useState("last_7_days");
  const [productType, setProductType] = useState("Donuts");
  const [data, setData] = useState({
    datasets: [
      {
        label: "Percentage",
        data: [0, 0],
        backgroundColor: ["#4942E4", "#4CB9E7", "#FF6384"],
        borderColor: ["#4942E4", "#4CB9E7", "#FF6384"],
        borderWidth: 1,
      },
    ],
    labels: ["Order", "Sales"],
  });

  const [bestSale, getBestSale, { loading }] = useFetch(
    fetchBestSale,
    {},
    false
  );

  useEffect(() => {
    if (time_range === "last_7_days" && productType) {
      getBestSale({ type: productType, days: 7, months: null, years: null });
    } else if (time_range === "current_month" && productType) {
      getBestSale({ type: productType, months: 0, days: null, years: null });
    } else if (time_range === "current_year" && productType) {
      getBestSale({ type: productType, years: 0, days: null, months: null });
    } else if (time_range === "last_month" && productType) {
      getBestSale({
        type: productType,
        months: dayjs().subtract(1, "month").format("YYYY-MM"),
        years: null,
        days: null,
      });
    } else if (time_range === "last_year" && productType) {
      getBestSale({
        type: productType,
        years: dayjs().subtract(1, "year").format("YYYY"),
        months: null,
        days: null,
      });
    }
  }, [time_range, productType]);

  useEffect(() => {
    if (bestSale) {
      setData({
        datasets: [
          {
            label: "Percentage",
            data: [
              (100 - bestSale?.order_per_sale_percentage).toFixed(2),
              bestSale?.order_per_sale_percentage,
            ],
            backgroundColor: ["#4942E4", "#4CB9E7", "#FF6384", "#4CB9E7"],
            borderColor: ["#4942E4", "#4CB9E7", "#FF6384"],
            borderWidth: 1,
          },
        ],
        labels: ["Order", "Sales"],
      });
    }
  }, [bestSale]);

  const options = {
    cutout: "50%",
    responsive: true,
    plugins: {
      legend: {
        display: true,
      },
      tooltip: {
        enabled: false,
      },
      datalabels: {
        formatter: (value, context) => {
          return `${value}%`;
        },
        color: "#ffffff",
        font: {
          size: "18",
          weight: "bold",
        },
      },
    },
  };

  if (loading) {
    return (
      <>
        <MainLoader />
      </>
    );
  }

  return (
    <>
      <div className="bg-white rounded-lg shadow-lg h-full">
        <div className="py-5 px-8">
          <div className="flex flex-wrap justify-between items-center gap-2">
            <div className="flex flex-wrap space-x-8 items-center ">
              <p className="text-xl font-bold text-[#44566C]">Best Sales</p>
            </div>

            <div className="flex flex-wrap gap-2 ">
              <Select
                style={{
                  width: 160,
                }}
                labelInValue
                value={{
                  value: "time_range",
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
                onChange={(e) => setTimeRange(e.value)}
                options={[
                  {
                    value: "last_7_days",
                    label: (
                      <>
                        <div className="flex items-center space-x-2 mt-1">
                          <IoMdCalendar size={24} className="text-gray-400" />
                          <p className="text-sm font-normal text-[#8697A8]">
                            Last 7 days
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
                    value: "current_year",
                    label: (
                      <>
                        <div className="flex items-center space-x-2 mt-1">
                          <IoMdCalendar size={24} className="text-gray-400" />
                          <p className="text-sm font-normal text-[#8697A8]">
                            Current Year
                          </p>
                        </div>
                      </>
                    ),
                  },
                  {
                    value: "last_year",
                    label: (
                      <>
                        <div className="flex items-center space-x-2 mt-1">
                          <IoMdCalendar size={24} className="text-gray-400" />
                          <p className="text-sm font-normal text-[#8697A8]">
                            Last Year
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
                  value: productType,
                  label: (
                    <>
                      <div className="flex items-center space-x-2 mt-1">
                        <GiDonut size={24} className="text-gray-400" />
                        <p className="text-sm font-normal text-[#8697A8]">
                          {productType}
                        </p>
                      </div>
                    </>
                  ),
                }}
                onChange={(e) => setProductType(e.value)}
                options={[
                  {
                    value: "Donuts",
                    label: (
                      <>
                        <div className="flex items-center space-x-2 mt-1">
                          <GiDonut size={24} className="text-gray-400" />
                          <p className="text-sm font-normal text-[#8697A8]">
                            Donuts
                          </p>
                        </div>
                      </>
                    ),
                  },
                  {
                    value: "Fancy",
                    label: (
                      <>
                        <div className="flex items-center space-x-2 mt-1">
                          <GiDonut size={24} className="text-gray-400" />
                          <p className="text-sm font-normal text-[#8697A8]">
                            Fancy
                          </p>
                        </div>
                      </>
                    ),
                  },
                  {
                    value: "Munkins",
                    label: (
                      <>
                        <div className="flex items-center space-x-2 mt-1">
                          <GiDonut size={24} className="text-gray-400" />
                          <p className="text-sm font-normal text-[#8697A8]">
                            Munkins
                          </p>
                        </div>
                      </>
                    ),
                  },
                ]}
              />
            </div>
          </div>
          <div className="flex gap-4 justify-between mt-4 mb-2">
            <p className="text-[#4942E4] font-semibold text-base">
              Order: {bestSale?.total_order_sum}
            </p>
            <p className="text-[#4CB9E7] font-semibold text-base">
              Sale: {parseFloat(bestSale?.total_sale_sum).toFixed(2)}
            </p>
          </div>
          <div className="h-full w-full flex justify-center items-center">
            <Doughnut data={data} options={options} />
          </div>
          <p className="text-center text-[#44566C] mt-3 text-lg font-medium">
            {bestSale?.order_per_sale_percentage}% sales on your order.
          </p>
          <p className="text-center text-[#44566C] text-lg font-medium">
            Store : {bestSale?.store_key ? bestSale?.store_key : "NA"}
          </p>
        </div>
      </div>
    </>
  );
};

export default BestSales;
