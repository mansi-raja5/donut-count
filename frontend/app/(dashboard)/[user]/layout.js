"use client";

import Sidebar from "../components/layout/sidebar";
import Header from "../components/layout/header";
import { FaChartBar, FaHome } from "react-icons/fa";
import MainLoader from "../components/common/loader";
import "@fontsource/nunito-sans/400.css";
import "@fontsource/nunito-sans/500.css";
import "@fontsource/nunito-sans/600.css";
import "@fontsource/nunito-sans/700.css";
import "@fontsource/nunito-sans/800.css";
import "@fontsource/nunito-sans/900.css";
import UserContext from "../../contexts/user";
import { fetchUser } from "../../helpers/backend";
import { useFetch } from "../../helpers/hooks";

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
    <div className="min-h-screen bg-gray-100 !font-nunitoFont">
      <div className="min-h-screen bg-[#F5F6F9]">
        {!!user && (
          <>
            <UserContext.Provider value={user}>
              <Sidebar title="Donut Count" menu={menu} />
              <Header title="Donut Count" />
              <div className="content font-nunitoFont">
                <div className="p-4">{children}</div>
              </div>
            </UserContext.Provider>
          </>
        )}
      </div>
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
    href: "/employee",
    icon: <FaHome />,
  },
  {
    label: "Donut Count",
    href: "/employee/donut-count",
    icon: <FaChartBar />,
  },
];
