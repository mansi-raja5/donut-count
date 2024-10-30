"use client";
import { useRouter } from "next/navigation";
import { Form, Radio, Select, message } from "antd";
import Link from "next/link";
import FormInput from "../components/form/input";
import { useState } from "react";
import { useFetch } from "../../helpers/hooks";
import { fetchAllStores, postRegister } from "../../helpers/backend";

const Signup = () => {
  const router = useRouter();
  const [size, setSize] = useState("middle");
  const [role, setRole] = useState("Employee");
  const [stores, getStores] = useFetch(fetchAllStores);
  const [allStore, setAllStore] = useState("custom");
  const [multiStoreKey, setMultiStoreKey] = useState([]);
  
  const options = [];
  for (let i = 1; i <= 12; i++) {
    options.push({
      value: i.toString(),
      label: i.toString(),
    });
  }

  const handleSelectStore = (value) => {
    if (value.includes("selectAll")) {
      setMultiStoreKey(storeData || []);
    } else {
      setMultiStoreKey(value);
    }

  };

  const storeKeyString = multiStoreKey?.join(",");

  return (
    <div className="bg-[url('/login.png')] lg:h-screen h-auto w-full bg-cover bg-no-repeat flex justify-center items-center">
      <div className="container mx-auto my-4">
        <div className="flex lg:justify-end justify-center">
          <div className="bg-white lg:w-[495px] w-full rounded-2xl bg-opacity-90 border">
            <div className="text-center mt-6 ">
              <span className="text-center heading_2 !font-semibold text-[#292B49]">
                Welcome to{" "}
              </span>{" "}
              <span className="text-[#E48586] heading_2 !font-semibold">
                Donut Count
              </span>
            </div>
            <div className="pt-6 text-start lg:px-6">
              <h1 className="heading_3 text-[#292B49] md:px-0 px-5 !font-bold">
                Registration
              </h1>
              <div className="paragraph py-2 md:px-0 px-5">
                Do you have an account?{" "}
                <Link href="/login" className="text-mainColor !font-semibold">
                  Login
                </Link>
              </div>
            </div>
            <hr />
            <div className="px-6 py-2 text-left ">
              <Form
                layout="vertical"
                onFinish={async (values) => {
                  values.role = role;
                  if (allStore === "custom") {
                    values.store_id = storeKeyString;
                  }else{
                    values.store_id = "all"
                  }

                  await postRegister(values).then((data) => {
                    if (data?.status === "success") {
                      message.success(data?.message);
                      router.push("/login")

                    }else{
                      message.error(data?.message);
                    }
                  });
                }}
              >
                <p className="text-[#292B49] text-base font-poppins font-normal break-words mt-1">
                  Select Your Role
                </p>
                <div className="flex gap-x-1 items-center mt-3">
                  <Radio.Group
                    onChange={(e) => setRole(e.target.value)}
                    value={role}
                  >
                    <Radio value={"Employee"}>
                      <span className="heading_4">Employee</span>
                    </Radio>
                    <Radio value={"Manager"}>
                      <span className="heading_4">Manager</span>
                    </Radio>
                  </Radio.Group>
                </div>

                <div className="grid lg:grid-cols-2 grid-cols-1 gap-x-2 mt-6">
                  <div>
                    <FormInput
                      name="first_name"
                      className={
                        "border w-full px-3 py-2 text-lg outline-mainColor rounded-md"
                      }
                      label="First Name"
                      required
                    />
                  </div>

                  <div>
                    <FormInput
                      name="last_name"
                      className={
                        "border w-full px-3 py-2 text-lg outline-mainColor rounded-md"
                      }
                      label="Last Name"
                      required
                    />
                  </div>
                </div>

                <FormInput
                  name="email"
                  className={
                    "border w-full px-3 py-2 text-lg outline-mainColor rounded-md"
                  }
                  label="Email"
                  required
                />

                <FormInput
                  name="password"
                  type="password"
                  className={
                    "border w-full px-3 py-2 text-lg outline-mainColor rounded-md"
                  }
                  label="Password"
                  required
                />

                <FormInput
                  name="confirm_password"
                  type="password"
                  className={
                    "border w-full px-3 py-2 text-lg outline-mainColor rounded-md"
                  }
                  label="Confirm Password"
                  required
                />

                <p className="text-[#292B49] text-base font-poppins font-semibold mt-1">
                  Select Store
                </p>

                <div className="flex gap-x-1 items-center mt-3">
                  <Radio.Group
                    onChange={(e) => setAllStore(e.target.value)}
                    value={allStore}
                  >
                    <Radio value={"custom"}>
                      <span className="heading_4">Custom</span>
                    </Radio>
                    <Radio value={"all"}>
                      <span className="heading_4">All Store</span>
                    </Radio>
                  </Radio.Group>
                </div>

                {allStore === "custom" && (
                  <div className="mt-2">
                    <Select
                      mode="multiple"
                      className="bg-white border w-full h-auto paragraph outline-mainColor rounded-md mt-2 text-base"
                      size={size}
                      placeholder="Please select store"
                      // defaultValue={['1', '2']}
                      onChange={handleSelectStore}
                      style={{
                        width: "100%",
                        height: "auto",
                      }}
                      options={[
                        ...(stores?.map((store) => ({
                          label: store?.key,
                          value: store?.key,
                        })) || []),
                      ]}
                      showSearch={false}
                    />
                  </div>
                )}

                <button className="rounded-md w-full py-3 my-5 text-white bg-mainColor hover:bg-opacity-80 heading_3">
                  Registration
                </button>
              </Form>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Signup;
