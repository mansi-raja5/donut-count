"use client"
import React from 'react'
import Table from '../../components/common/table'
import SearchInput from '../../components/form/search'
import { MdOutlineFileDownload } from "react-icons/md";
import { MdOutlineSettingsBackupRestore } from "react-icons/md";

const columns = [
    {
        text: 'File ID',
        dataField: 'fild_id',
        key: 'fild_id',
    },
    {
        text: 'Field Name',
        dataField: 'fild_name',
        key: 'fild_name',
    },
    {
        text: 'Success Row',
        dataField: 'success_row',
        key: 'success_row',
    },
    {
        text: 'Failed Row',
        dataField: 'failed_row',
        key: 'failed_row',
    },
    {
        text: 'Upload Date & Time',
        dataField: 'date_time',
        key: 'date_time',
    },
    {
        text: 'Action',
        key: 'action',
        formatter: (data) => <div className="flex justify-center gap-2">
            <button className="mx-auto mt-2 bg-[#0D894F1A] bg-opacity-10  text-[#0D894F] p-2 rounded-md w-full">
                <div className="flex items-center justify-center gap-3 w-full font-semibold">
                    <MdOutlineFileDownload />
                    <h1 className="">Download</h1>
                </div>
            </button>
            <button className="mx-auto mt-2 bg-[#FEEDEC]  text-[#F04438] p-2 rounded-md w-full">
                <div className="flex items-center justify-center gap-3 w-full font-semibold">
                    <MdOutlineFileDownload />
                    <h1 className="text-semi">Failed Download </h1>
                </div>
            </button>
            <button className="mx-auto mt-2 bg-mainColor text-white p-2 rounded-md w-full">
                <div className="flex items-center justify-center gap-3 w-full font-semibold">
                <MdOutlineSettingsBackupRestore />
                    <h1 className="text-semi">Revert</h1>
                </div>
            </button>
        </div>
    }
];

const data = [
    {
        key: 1,
        fild_id: 1,
        fild_name: 'John Brown',
        success_row: 'John Brown',
        failed_row: 'John Brown',
        date_time: '1/11/1',
        page: 11
    },
    {
        key: 2,
        fild_id: 2,
        fild_name: 'John Brown',
        success_row: 'John Brown',
        failed_row: 'John Brown',
        date_time: '1/11/1',
        // action: 'action',
    },
    {
        key: 3,
        fild_id: 3,
        fild_name: 'John Brown',
        success_row: 'John Brown',
        failed_row: 'John Brown',
        date_time: '1/11/1',
        // action: 'action',
    }
];


let noHeader = <div className="flex md:flex-row flex-col gap-y-4 justify-between items-center px-4 pb-2 pt-6">
    <h1 className='text-content lg:text-2xl text-[20px] font-bold'>File History</h1>
    <div className="flex flex-wrap">
        <SearchInput className="w-44" onChange={e => {
            // onReload({ search: e.target.value || undefined, page: 1 })
            // onSearchChange && onSearchChange(e.target.value || '')
        }} />
    </div>
</div>

const History = () => {
    return (
        <div className='text-sm text-[#44566C]'>
            <Table data={data} columns={columns} noActions={true} noHeader={noHeader} loading={false} ></Table>
        </div>
    )
}

export default History