"use client";

import Sidebar from "../components/layout/sidebar";
import Header from "../components/layout/header";
import { FaChartBar, FaFileImport, FaHistory, FaHome } from "react-icons/fa";
import MainLoader from "../components/common/loader";
import { useRouter } from "next/navigation";
import { fetchUser } from "../../helpers/backend";
import { useFetch } from "../../helpers/hooks";
import UserContext from "../../contexts/user";
import { useEffect } from "react";

const Layout = ({ children }) => {
  const [user] = useFetch(fetchUser);

  if (!user) {
    return (
      <>
        <MainLoader />
      </>
    );
  }


  return (
    <div className="min-h-screen bg-gray-100">
      {!!user && (
        <>
          <UserContext.Provider value={user}>
          <Sidebar title="Donut Count" menu={menu} />
          <Header title="Donut Count" />
          <div className="content">
            <div className="p-4">{children}</div>
          </div>
          </UserContext.Provider>
        </>
      )}
    </div>
  );
};

export default Layout;

const menu = [
  // {
  //   menu: "Menu",
  // },
  {
    label: "Dashboard",
    href: "/admin",
    icon: <FaHome />,
  },
  {
    label: "Donut Count",
    href: "/admin/donut-count",
    icon: <FaChartBar />,
  },
  {
    label: "File History",
    href: "/admin/history",
    icon: <FaHistory />,
  },
  {
    label: "Import Data",
    href: "/admin/import",
    icon: <FaFileImport />,
  },
];
