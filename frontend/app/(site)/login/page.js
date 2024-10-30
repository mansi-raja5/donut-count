"use client";
import { useRouter } from "next/navigation";
import { Form, message } from "antd";
import Link from "next/link";
import FormInput from "../components/form/input";
import { useState } from "react";

const Login = () => {
  const router = useRouter();

  const [value, setValue] = useState(1);
  const onChange = (e) => {
    setValue(e.target.value);
  };

  return (
    <div className="bg-[url('/login.png')] h-screen w-full bg-fixed bg-no-repeat flex justify-center items-center">
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
                Log In
              </h1>
              <div className="paragraph py-2 md:px-0 px-5">
                Don't have an account?{" "}
                <Link href="/signup" className="text-mainColor !font-semibold">
                  Register
                </Link>
              </div>
            </div>
            <hr />
            <div className="px-6 py-3 text-left ">
              <Form
                layout="vertical"
                onFinish={async (values) => {

                  try {
                    const response = await fetch('https://mansiraja.tech/api/login', {
                      method: 'POST',
                      body: JSON.stringify({
                        email: values.email,
                        password: values.password,
                      }),
                      headers: {
                        'Content-type': 'application/json',
                      },
                    });

                    if (!response.ok) {
                      throw new Error(`HTTP error! Status: ${response.status}`);
                    }

                    const data = await response.json();

                    if (data?.status === 'success') {
                      message.success(data?.message);
                      localStorage.setItem('token', data?.token);
                      const role = await fetch('https://mansiraja.tech/api/user', {
                        method: 'GET',
                        headers: {
                          'Content-type': 'application/json',
                          Authorization: `Bearer ${localStorage.getItem('token')}`,
                        },
                      }).then((response) => response.json()
                      ).then((data) => data?.role.toLowerCase()
                      ).catch((error) => console.error('Error during login:', error)
                      );

                      router.push(`/${role}`);
                    } else {
                      message.error(data?.message);
                    }
                  } catch (error) {
                    message.error('Something went wrong!');
                  }
                }}
              >
                {/* <p className="text-[#292B49] text-base font-poppins font-normal break-words mt-1">
                  Select Your Role
                </p>
                <div className="flex gap-x-1 items-center mt-3 mb-4">
                  <Radio.Group onChange={onChange} value={value}>
                    <Radio value={1}>
                      <span className="heading_4">Employee</span>
                    </Radio>
                    <Radio value={2}>
                      <span className="heading_4">Manager</span>
                    </Radio>
                  </Radio.Group>
                </div> */}

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

                {/* <Link
                  href="#"
                  className="paragraph  !text-mainColor flex justify-end underline -mt-2"
                >
                  Forgot Password
                </Link> */}
                <button className="rounded-md w-full py-3 my-5 text-white bg-mainColor hover:bg-opacity-80 heading_3">
                  Login
                </button>
              </Form>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Login;
