"use client";

import {useEffect} from "react";

const RouteLoader = () => {
    const hide = () => {
        hideLoader()
    }

    useEffect(() => {
        hide()
    }, [])


    return (
        <div id="preloader" style={{background: '#12121233'}}>
            <Loader/>
        </div>
    )
}
export default RouteLoader

export const showLoader = () => {
    try {
        document.querySelector('#preloader').style.visibility = 'visible'
    } catch (e) {

    }
}

export const hideLoader = () => {
    try {
        document.querySelector('#preloader').style.visibility = 'hidden'
    } catch (e) {

    }
}

export const Loader = () => {
    return (
        <div id="status">
            <div className="spinner-chase">
                <div className="chase-dot"/>
                <div className="chase-dot"/>
                <div className="chase-dot"/>
                <div className="chase-dot"/>
                <div className="chase-dot"/>
                <div className="chase-dot"/>
            </div>
        </div>
    )
}