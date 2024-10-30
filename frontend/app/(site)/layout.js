import "../(site)/styles/app.scss";
import "@fontsource/dela-gothic-one";
import "nprogress/nprogress.css";
import localFont from "next/font/local";

const myFont = localFont({ src: "../../public/fonts/gilroy-regular.ttf" });

export default async function RootLayout({ children }) {

  return (
    <html lang="en">
      {/* <link rel="icon" href="/logo/favicon.ico" /> */}
      <body className={myFont.className}>{children}</body>
    </html>
  );
}
