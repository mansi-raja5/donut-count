import { Dropdown, Space } from "antd";
import { FaBars } from "react-icons/fa";
import { FiLogOut } from "react-icons/fi";
import { BiUser } from "react-icons/bi";
import { useRouter } from "next/navigation";
import { useUser } from "../../../contexts/user";
import { fetchSpecialDays } from "../../../helpers/backend";
import { useFetch } from "../../../helpers/hooks";

const Header = ({ title }) => {
  const user = useUser();
  const router = useRouter();
  const [specialDays, getSpecialDays] = useFetch(fetchSpecialDays, {
    status: "A",
    type: "upcoming",
  });

  const handleLogout = () => {
    localStorage.removeItem("token");
    router.push("/login");
  };

  const items = [
    {
      label: "Logout",
      icon: <FiLogOut />,
      key: "4",
      onClick: handleLogout,
    },
  ];
  return (
    <header className="header z-10">
      <div className="flex justify-between items-center h-full p-5">
        <div className="">
          <FaBars
            className="md:hidden"
            role="button"
            onClick={() => {
              window.document
                .querySelector(".sidebar")
                .classList.toggle("open");
              window.document
                .querySelector(".sidebar-overlay")
                .classList.toggle("open");
            }}
          />
        </div>

        <div>
          <h4 className="text-[#44566C] md:text-base text-xs text-center font-semibold">
            Upcoming Events
          </h4>
          <div className="flex md:gap-x-10 gap-x-2 mt-3">
            {specialDays?.slice(0, 2).map((day, i) => (
              <div key={i} className="text-center">
                <p className="md:text-xs text-[10px] text-[#8697A8] font-semibold">
                  {day?.event_date}
                </p>
                <h1 className="text-[#FF6666] md:text-xl text-base font-bold">
                  {day?.event_name}
                </h1>
              </div>
            ))}
          </div>
        </div>

        <div className="flex items-center gap-x-6">
          <Dropdown
            menu={{
              items,
            }}
          >
            <a>
              <Space>
                <BiUser className="cursor-pointer" size={24} />
                {user && (
                  <div>
                    <p className="cursor-pointer md:text-base font-semibold text-xs">
                      {user?.name}
                    </p>
                    <p className="cursor-pointer font-semibold md:text-xs text-[10px]">
                      {user?.role}
                    </p>
                  </div>
                )}
              </Space>
            </a>
          </Dropdown>
        </div>
      </div>
    </header>
  );
};

export default Header;
