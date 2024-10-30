'use client'

import {useEffect} from 'react'

export default function Error({error, reset,}) {
    useEffect(() => {
        if (error) {
            console.log(error)
        }
    }, [error])

    return (
        <div>
            <h2>Something went wrong!</h2>
            <button
                onClick={() => {
                    localStorage.clear()
                    reset()
                }}
            >
                Try again
            </button>
        </div>
    )
}