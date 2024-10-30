import Skeleton from 'react-loading-skeleton'
import 'react-loading-skeleton/dist/skeleton.css'

export const ProductSkeleton = () => {
    return (
        <div className="bg-white rounded p-2.5">
            <div className="h-56 p-2 mb-3 shadow-sm text-center rounded overflow-hidden relative">
                <Skeleton className="h-full"/>
            </div>
            <div className="px-2">
                <Skeleton/>
                <hr className="mb-1.5"/>
                <Skeleton/>
            </div>
        </div>
    )
}

