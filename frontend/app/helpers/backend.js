import { del, get, patch, post, postForm, patchForm, put } from "./api";


//authenitacation
export const fetchLogout = (data) => get("/logout", data);
export const postLogin = (data) => post("/login", data);
export const postRegister = (data) => post("/register", data);
export const fetchUser = (data) => get("/user", data);

//donut-count
export const fetchDonutCount = (data) => get("/donut_counts", data);
export const fetchAllStores = (data) => get("/stores", data);
export const fetchSpecialDays = (data) => get("/special_days", data);
export const addSpecialDay = (data) => post("/special_day/add", data);
export const getSpecialDayData = (data) => get("/special_day/donut_counts", data);

//sales-report
export const fetchSalesReport = (data) => get("/salesreport", data);

//best-sale
export const fetchBestSale = (data) => get("/bestsale", data);