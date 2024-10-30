'use client'
import TotalSales from "../components/user/total-sales";
import SalesReport from "../components/user/sales-report";
import BestSales from "../components/user/best-sales";
import AllStore from "../components/user/all-store";
import { useEffect, useState } from "react";
import { useUser } from "../../contexts/user";
import { useRouter } from "next/navigation";

const Dashboard = () => {
  const router = useRouter()
  const user = useUser()
  const [storeData, setStoreData] = useState([]);
  
  useEffect(() => {
    if (!!user?.stores) {
      const storeIdsArray = user.stores
        .split(",")
        .map((storeId) => storeId.trim());
      setStoreData(storeIdsArray);
    }
  }, [user?.stores]);

  
  useEffect(()=>{
    if(!user?.role){
      router.push("/login")
      localStorage.removeItem('token')
    }
  },[])
  
  if (!storeData) return null;
  
  return (
    <>
      {
        storeData && <TotalSales
          storeData={storeData}
        />
      }
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-y-8 md:gap-x-8 mt-8" >
        <div className="lg:col-span-2" >
          <SalesReport storeData={storeData} />
        </div>
        <div >
          <BestSales />
        </div>

      </div>
      {
        storeData && <AllStore
          storeData={storeData}
        />
      }

    </>
  );
};

export default Dashboard;
