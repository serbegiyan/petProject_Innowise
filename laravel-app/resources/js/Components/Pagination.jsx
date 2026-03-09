import { Link } from '@inertiajs/react';

export default function Pagination({ links }) {
    if (!links || links.length <= 3) return null;

    return (
        <div className="flex flex-row justify-center my-5">
            {links.map((link, key) => {
                const label = link.label
                    .replace('Next &raquo;', 'Вперед')
                    .replace('&laquo; Previous', 'Назад');

                return link.url === null ? (
                    <div
                        key={key}
                        className="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded"
                        dangerouslySetInnerHTML={{ __html: label }}
                    />
                ) : (
                    <Link
                        key={key}
                        href={link.url}
                        className={`mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-white focus:border-indigo-500 focus:text-indigo-500 ${link.active ? 'bg-blue-600 text-white font-bold' : 'bg-white'}`}
                        dangerouslySetInnerHTML={{ __html: label }}
                    />
                );
            })}
        </div>
    );
}
