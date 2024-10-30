"use client";
import { useRouter } from "next/navigation";
import { useEffect } from "react";

const Home = async () => {
  const router = useRouter();
  const user = false;

  useEffect(() => {
    if (!user) {
      router.push("/login");
    }
  }, []);

  return <div className="flex flex-col gap-y-14 mb-12"></div>;
};
export default Home;