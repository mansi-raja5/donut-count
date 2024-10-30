import "@fontsource/inter";
import './styles/app.scss'
import '@fontsource/nunito-sans/400.css';
import '@fontsource/nunito-sans/500.css';
import '@fontsource/nunito-sans/600.css';
import '@fontsource/nunito-sans/700.css';
import '@fontsource/nunito-sans/800.css';
import '@fontsource/nunito-sans/900.css';

export const metadata = {
    title: "Donut Count",
    description: "Donut Count Website",
};

export default function RootLayout({ children }) {
    return (
        <html lang="en">
            <body className="!font-nunitoFont">
                {/* <MainLoader /> */}
                {children}
            </body>
        </html>
    );
}
