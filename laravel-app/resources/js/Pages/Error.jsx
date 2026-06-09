import React from 'react';

export default function Error({ status }) {
    const description = {
        403: 'Извините, у вас нет доступа к этой странице.',
        404: 'Страница не найдена.',
    }[status];

    return (
        <div className="flex flex-col items-center justify-center min-h-screen">
            <h1 className="text-4xl font-bold">{status}</h1>
            <p className="mt-2 text-gray-600">{description}</p>
            <a href="/catalog" className="mt-4 px-4 py-2 bg-blue-500 text-white rounded">
                На главную
            </a>
        </div>
    );
}
