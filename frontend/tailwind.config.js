/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ["./app/**/*.js"],
    theme: {
        fontFamily: {
            // montserrat: ["Montserrat", "sans-serif"],
            // inter: ["Inter", "sans-serif"],
            dela: ["Dela Gothic One", "sans-serif"],
            "nunitoFont": ['Nunito Sans', 'sans-serif'],
        },
        extend: {
            fontSize: {
                d1: ["48px", {
                    lineHeight: "70px",
                    fontWeight: "400",
                }],
                d2: ["32px", {
                    lineHeight: "62px",
                    fontWeight: "400",
                }],
            },
            colors: {
                // mainColor:'#F34100',
                mainColor:'#E48586',
                bodyText:'#666152',
                bgLine:'#CED7E2',
                heading:'#24315E',
                dark:'#332D1D',
                content:'#44566C'
            },
            boxShadow: {
                sm: "0px 1px 3px 0px #03004717",
                DEFAULT: "0px 0px 10px 0px rgba(0, 0, 0, 0.06)",
                lg: "0px 8px 45px 0px #03004717",
            },
            lineClamp: {
                7: '7',
                8: '8',
                12: '12',
              },
        },
    },
    plugins: [
        require('tailwindcss-rtl'),
    ],
}

