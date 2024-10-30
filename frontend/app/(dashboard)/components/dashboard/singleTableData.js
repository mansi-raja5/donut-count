import React from "react";
import MainLoader from "../common/loader";

const SingleTableData = ({ product, result }) => {

  if (!result) {
    return (
      <>
        <MainLoader />
      </>
    );
  }
  return (
    <div className="border border-[#EAEEF7] rounded">
      <h1 className="text-content font-bold lg:text-[48px] text-[34px] py-[14px] text-center bg-white">
        {product}
      </h1>
      <table className="w-full order bg-white">
        <thead>
          <tr>
            <th>{result[0]?.year === "Total" ? "": "Year"}</th>
            <th>Order</th>
            <th>Sale</th>
          </tr>
        </thead>

        <tbody className="max-h-[500px] overflow-hidden ">
          {result?.map((res, i) => (
            <tr key={i}>
              <td>{res?.year}</td>
              <td>{res?.totalOrderSum?.toFixed(2)}</td>
              <td>{res?.totalSaleSum?.toFixed(2)}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
};

export default SingleTableData;
