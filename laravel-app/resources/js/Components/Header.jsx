
export default function Header({ children }) {
    return (
        <header className="w-full h-14.5 flex flex-row justify-between p-4 bg-cyan-200">
            <img src="/images/logo.jpg" className="w-10 rounded-full" />
            {children}
        </header>
    );
}