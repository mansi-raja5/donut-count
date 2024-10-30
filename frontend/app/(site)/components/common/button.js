import Link from "next/link";

const Button = ({ children, href, onClick, className, alt, disabled, ...props }) => {
  let Wrapper = !!href ? Link : "button";

  return (
    <Wrapper
      href={href}
      disabled={disabled}
      className={`text-lg px-7 py-2 duration-300 font-bold ${
        alt
          ? "bg-transparent text-bodyText  hover:bg-mainColor hover:text-white"
          : "bg-mainColor text-white hover:text-mainColor hover:bg-white  "
      }  rounded   border border-mainColor  ${className || ""}`}
      onClick={onClick}
      {...props}
    >
      {children}
    </Wrapper>
  );
};

export default Button;
