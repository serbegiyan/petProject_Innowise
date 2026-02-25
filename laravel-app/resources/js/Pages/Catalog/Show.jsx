import { Head } from '@inertiajs/react';
import Header from '@/Components/Header';
import { useState } from 'react';
import NavBar from '@/Components/NavBar';

export default function Show({ product }) {
    const [totalPrice, setTotalPrice] = useState(product.price);

    function getPrice(event) {
        if (event.target.checked) {
            setTotalPrice(Number(totalPrice) + Number(event.target.value));
        } else {
            setTotalPrice(Number(totalPrice) - Number(event.target.value));
        }
    }

    return (
        <div>
            <Head title={product.name} />
            <Header><NavBar /> </Header>
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
                        <div key={product.id} className='even:bg-stone-100 p-2'>
                            <div className='flex flex-col'>
                                <div className='flex flex-row items-center'>
                                    <input className='mr-3'
                                        type='checkbox'
                                        onChange={getPrice}
                                        value={service.pivot.price} />
                                    <h4 className='font-semibold'>{service.name}</h4>
                                </div>
                                <div className='flex flex-row justify-end'>
                                    <div className='mr-4'>Цена: {service.pivot.price} BYN</div>
                                    <div>Срок исполнения: {service.pivot.term}</div>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
                <div className='bg-cyan-50 p-4 rounded-xl flex flex-col items-center gap-3 h-fit'>
                    <p><span className='font-semibold'>Стоимость товара: </span>{product.price} BYN</p>
                    <p><span className='font-semibold'>Итоговая стоимость: </span>{Number(totalPrice).toFixed(2)} BYN</p>

                    <button className='bg-blue-600 text-white hover:bg-blue-700 w-fit px-4 py-2 rounded mt-3'>Добавить в корзину</button>
                </div>
            </div>

        </div>
    );
}