import { Head } from '@inertiajs/react';
import Header from '@/Components/Header';
import { useState, useMemo } from 'react';
import NavBar from '@/Components/NavBar';
import { router } from '@inertiajs/react';
import FlashMessage from '@/Components/FlashMessage';
import { usePage, Link, } from '@inertiajs/react';

export default function Show({ product, preSelectedIds, edit_cart_id, filters }) {
    const { auth } = usePage().props;
    const [selectedServices, setSelectedServices] = useState(
        preSelectedIds ? preSelectedIds.map(id => Number(id)) : []
    );

    const isInCart = useMemo(() => {
        if (!auth.user || !auth.user.basket) return false;

        return auth.user.basket.some(item => {
            if (item.product_id !== product.id) return false;

            // 2. Сравниваем наборы услуг
            const basketServiceIds = item.services.map(s => Number(s.id)).sort();
            const currentServiceIds = selectedServices.map(id => Number(id)).sort();

            // Сравниваем массивы (превращаем в строку для быстрого сравнения)
            return JSON.stringify(basketServiceIds) === JSON.stringify(currentServiceIds);
        });
    }, [product.id, selectedServices, auth.user]);

    const totalPrice = useMemo(() => {
        const servicesSum = product.services
            .filter(s => selectedServices.includes(s.id))
            .reduce((sum, s) => sum + parseFloat(s.pivot.price), 0);
        return Number(product.price) + servicesSum;
    }, [selectedServices, product]);

    function handleServiceChange(serviceId) {
        setSelectedServices(prev =>
            prev.includes(serviceId)
                ? prev.filter(id => id !== serviceId)
                : [...prev, serviceId]
        );
    }

    function handleBasket() {
        // Формируем массив объектов услуг на основе выбранных ID
        const formattedServices = product.services
            .filter(service => selectedServices.includes(service.id))
            .map(service => ({
                id: service.id,
                name: service.name,
                price: parseFloat(service.pivot.price)
            }));

        router.post(route('basket.store'), {
            product_id: product.id,
            services: formattedServices, // Отправляем готовые объекты
            quantity: 1,
            edit_cart_id: edit_cart_id
        }, {
            preserveScroll: true,
        });
    }

    return (
        <div>
            <Head title={product.name} />
            <Header><NavBar /> </Header>
            <FlashMessage />
            <h1 className='text-2xl text-center font-bold my-3'>{product.name}</h1>
            <div className='flex flex-row justify-between mx-5'>
                <img src={`/${product.image}`} alt={product.name} className='w-1/3 border' />
                <div className='flex flex-col gap-2 '>
                    <p>
                        <span className='font-semibold'>
                            Категория товара:
                        </span>{product.categories.map((category) => (
                            <span key={product.id}>
                                {` ${category.name}`}
                            </span>
                        ))}</p>
                    <p><span className='font-semibold'>Производитель: </span>{product.brand}</p>
                    <p><span className='font-semibold'>Дата выпуска: </span>{product.release_date}</p>
                    <p><span className='font-semibold'>Описание: </span>{product.description}</p>
                    <h3 className='text-xl text-center font-semibold my-3'>Доступные услуги для этого товара</h3>
                    {product.services.map((service) => (
                        <div key={service.id} className='even:bg-stone-100 p-2'>
                            <div className='flex flex-col'>
                                <div className='flex flex-row items-center'>
                                    <input className='mr-3'
                                        type='checkbox'
                                        checked={selectedServices.includes(service.id)}
                                        onChange={() => handleServiceChange(service.id)}
                                        value={service.pivot.price} />
                                    <h4 className='font-semibold'>{service.name}</h4>
                                </div>
                                <div className='flex flex-row justify-end'>
                                    <div className='mr-4'>Цена: +{service.pivot.price} BYN</div>
                                    <div>Срок исполнения: {service.pivot.term}</div>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
                <div className='flex flex-col '>
                    <Link
                        href={route('catalog.index', filters)}
                        className="w-full text-center border rounded-xl mb-5 h-12 p-3 mx-auto text-blue-600 hover:text-blue-800 transition"
                    >
                        <i className="mr-2 fa-solid fa-person-walking-arrow-loop-left"></i>Вернуться в каталог
                    </Link>
                    <div className='bg-cyan-50 p-4 rounded-xl flex flex-col items-center gap-3 h-fit'>
                        <p><span className='font-semibold'>Стоимость товара: </span>{Number(product.price).toLocaleString('ru-RU', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        })} BYN</p>
                        <p><span className='font-semibold'>Итоговая стоимость: </span>{Number(totalPrice).toLocaleString('ru-RU', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        })} BYN</p>
                        {isInCart ? (
                            <Link
                                href={route('basket.index')}
                                className="bg-blue-600 text-white hover:bg-blue-700 w-fit px-4 py-2 rounded mt-3"
                            >
                                Перейти в корзину
                            </Link>
                        ) : (
                            <button
                                onClick={handleBasket}
                                className="bg-blue-600 text-white hover:bg-blue-700 w-fit px-4 py-2 rounded mt-3"
                            >
                                {edit_cart_id ? 'Обновить в корзине' : 'Добавить в корзину'}
                            </button>
                        )}
                    </div>
                </div>
            </div>

        </div>
    );
}