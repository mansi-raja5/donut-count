import React from 'react';
import { Select } from 'antd';

const { Option } = Select;

export const MultiYearSelect = ({ startYear, endYear, onSelect, onChange, value }) => {
  const years = [];
  const currentYear = new Date().getFullYear();

  for (let year = startYear; year <= currentYear; year++) {
    years.push(year);
  }

  // const handleChange = (value) => {
  //   const selectedYear = parseInt(value, 10);
  //   onSelect(selectedYear);
  // };

  return (
    <Select placeholder="Select Year" onChange={onChange} mode="multiple" value={value} className='select-year'>
      {years?.map((year) => (
        <Option key={year} value={year}>
          {year}
        </Option>
      ))}
    </Select>
  );
};
