export default function Search({ onSubmit, ...props }) {
    return (
        <form className="flex flex-col justify-around" onSubmit={onSubmit}>
            <div className="flex flex-row">
                <input className="bg-cyan-200  border rounded-lg p-2 w-full"
                    name="search"
                    type="text"
                    {...props}
                    placeholder="Поиск"
                />
                <button className="-ml-14 w-fit px-4 rounded" type="submit">
                    <i className="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
        </form>
    );
}