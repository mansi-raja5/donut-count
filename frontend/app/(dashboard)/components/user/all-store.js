"use client";
import React, { useEffect } from "react";
import { useState } from "react";
import { Swiper, SwiperSlide } from "swiper/react";

import Slider from "../common/slider";
import "swiper/css";
import "swiper/css/pagination";

import "swiper/css";
import "swiper/css/free-mode";
import "swiper/css/pagination";

import { Autoplay, FreeMode, Pagination } from "swiper/modules";
import { useFetch } from "../../../helpers/hooks";
import { fetchDonutCount } from "../../../helpers/backend";
import MainLoader from "../common/loader";
import dayjs from "dayjs";
import SingleTableData from "../dashboard/singleTableData";

const AllStore = ({ storeData }) => {
  const [storeId, setStoreId] = useState(null);
  const [donut, getDonut, { loading }] = useFetch(fetchDonutCount, {
    week_ending: dayjs().subtract(0, "week").format("YYYY-MM-DD"),
    store_key: storeId,
  });

  useEffect(() => {
    if (storeData.length > 0) {
      setStoreId(storeData[0]);
    }
  }, [storeData]);

  useEffect(() => {
    if (!!storeId) {
      getDonut({
        week_ending: dayjs().subtract(0, "week").format("YYYY-MM-DD"),
        store_key: storeId,
      });
    }
  }, [storeId]);

  return (
    <div className="mt-8">
      <div className="flex md:flex-row flex-col md:justify-between gap-y-4">
        <h2 className="text-content text-2xl font-bold">Last 7 Days Data</h2>
        <div className={`relative mr-2 `}></div>
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
                    onClick={() => setStoreId(store)}
                  >
                    <h1
                      className={`${
                        storeId == store
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
          <div className="mt-8 ">
            {donut?.length === 0 ? (
              <h1 className="text-center text-2xl text-mainColor py-8">
                No Data Found for last 7 days!
              </h1>
            ) : (
              <Slider >
                {Object.values(donut)?.map((pd, index) => {
                  const result = convertProductDataWeek(pd?.Donuts);

                  return (
                    <SwiperSlide key={index}>
                      <SingleTableData product="Donuts" result={result} />
                      <table className="w-full order mt-7 bg-white">
                        <thead>
                          <tr>
                            <th>Date</th>
                            <th>Order</th>
                            <th>Sale</th>
                          </tr>
                        </thead>
                        <tbody className="max-h-[500px] overflow-hidden">
                          {pd?.Donuts?.map((p, i) => (
                            <tr key={i}>
                              {!!p?.special_day_name ? (
                                <td className="flex justify-center items-center flex-col">
                                  <p className="text-mainColor bg-mainColor bg-opacity-10 text-xs rounded-md px-2 py-1 border border-mainColor w-fit mb-2">
                                    {p?.special_day_name}
                                  </p>
                                  <p>
                                    {p?.week_day.slice(0, 3)}-
                                    {dayjs(p?.daily_date).format("MM/DD/YYYY")}
                                  </p>
                                </td>
                              ) : (
                                <td>
                                  {p?.week_day.slice(0, 3)}-
                                  {dayjs(p?.daily_date).format("MM/DD/YYYY")}
                                </td>
                              )}
                              <td>{p?.total_order}</td>
                              <td>{p?.total_sale}</td>
                            </tr>
                          ))}
                        </tbody>
                      </table>
                    </SwiperSlide>
                  );
                })}
                {Object.values(donut)?.map((pd, index) => {
                  const result = convertProductDataWeek(pd?.Fancy);

                  return (
                    <SwiperSlide key={index}>
                      <SingleTableData product="Fancy" result={result} />
                      <table className="w-full order mt-7 bg-white">
                        <thead>
                          <tr>
                            <th>Date</th>
                            <th>Order</th>
                            <th>Sale</th>
                          </tr>
                        </thead>
                        <tbody className="max-h-[500px] overflow-hidden">
                          {pd?.Fancy?.map((p, i) => (
                            <tr key={i}>
                              {!!p?.special_day_name ? (
                                <td className="flex justify-center items-center flex-col">
                                  <p className="text-mainColor bg-mainColor bg-opacity-10 text-xs rounded-md px-2 py-1 border border-mainColor w-fit mb-2">
                                    {p?.special_day_name}
                                  </p>
                                  <p>
                                    {p?.week_day.slice(0, 3)}-
                                    {dayjs(p?.daily_date).format("MM/DD/YYYY")}
                                  </p>
                                </td>
                              ) : (
                                <td>
                                  {p?.week_day.slice(0, 3)}-
                                  {dayjs(p?.daily_date).format("MM/DD/YYYY")}
                                </td>
                              )}
                              <td>{p?.total_order}</td>
                              <td>{p?.total_sale}</td>
                            </tr>
                          ))}
                        </tbody>
                      </table>
                    </SwiperSlide>
                  );
                })}
                {Object.values(donut)?.map((pd, index) => {
                  const result = convertProductDataWeek(pd?.Munkins);

                  return (
                    <SwiperSlide key={index}>
                      <SingleTableData product="Munkins" result={result} />
                      <table className="w-full order mt-7 bg-white">
                        <thead>
                          <tr>
                            <th>Date</th>
                            <th>Order</th>
                            <th>Sale</th>
                          </tr>
                        </thead>
                        <tbody className="max-h-[500px] overflow-hidden">
                          {pd?.Munkins?.map((p, i) => (
                            <tr key={i}>
                              {!!p?.special_day_name ? (
                                <td className="flex justify-center items-center flex-col">
                                  <p className="text-mainColor bg-mainColor bg-opacity-10 text-xs rounded-md px-2 py-1 border border-mainColor w-fit mb-2">
                                    {p?.special_day_name}
                                  </p>
                                  <p>
                                    {p?.week_day.slice(0, 3)}-
                                    {dayjs(p?.daily_date).format("MM/DD/YYYY")}
                                  </p>
                                </td>
                              ) : (
                                <td>
                                  {p?.week_day.slice(0, 3)}-
                                  {dayjs(p?.daily_date).format("MM/DD/YYYY")}
                                </td>
                              )}
                              <td>{p?.total_order}</td>
                              <td>{p?.total_sale}</td>
                            </tr>
                          ))}
                        </tbody>
                      </table>
                    </SwiperSlide>
                  );
                })}
              </Slider>
            )}
          </div>
        </>
      )}
    </div>
  );
};

export default AllStore;

export const convertProductDataWeek = (product) => {
  const multiYear = ["Total"];
  return multiYear.map((year) => {
    const totalOrderSum = product?.reduce((sum, item) => {
      return sum + (parseFloat(item?.total_order) || 0);
    }, 0);

    const totalSaleSum = product?.reduce((sum, item) => {
      return sum + (parseFloat(item?.total_sale) || 0);
    }, 0);

    return {
      year,
      totalOrderSum,
      totalSaleSum,
    };
  });
};
