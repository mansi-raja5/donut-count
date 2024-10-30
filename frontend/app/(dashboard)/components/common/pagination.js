import ReactPaginate from "react-paginate";

const Pagination = ({page, total, limit, totalPages, onPageChange, onSizeChange}) => {

    return (
        <div className="flex flex-wrap justify-between mb-2">
            <div className="flex items-center !mb-6 md:!mb-0 ">
                {onSizeChange && (
                    <div className="flex items-center mr-3 text-sm text-gray-700 h-[24px]">
                        Show
                        <select value={limit} onChange={(e) => {
                            onSizeChange(+e.target.value)
                        }} className="h-[24px] px-1 rounded mx-2 text-center focus:outline-0">
                            <option value={10}>10</option>
                            <option value={25}>25</option>
                            <option value={50}>50</option>
                            <option value={100}>100</option>
                        </select>
                    </div>
                )}
                <p className="text-sm text-gray-700">
                    Showing {((page - 1) * limit) + 1 || 0}
                    &nbsp;to {Math.min(total || 0, (page * limit) || 0)} of {total || 0} entries
                </p>
            </div>

            <ReactPaginate
                breakLabel="..."
                previousLabel={"Previous"}
                disabledLinkClassName="text-gray-300"
                previousLinkClassName="text-sm bg-gray-700 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-l"
                nextLinkClassName="text-sm bg-gray-700 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-r"
                pageLinkClassName="text-sm bg-gray-700 hover:bg-gray-600 text-white font-semibold py-2 px-4"
                pageClassName="!mb-3 md:!mb-0"
                activeLinkClassName="text-primary"
                nextLabel={"Next"}
                className="flex flex-wrap"
                onPageChange={({selected}) => onPageChange(selected + 1)}
                pageRangeDisplayed={3}
                pageCount={totalPages || 1}
                renderOnZeroPageCount={null}
            />
        </div>
    )
}
export default Pagination