import Header from "@/Components/Header";
import { Head } from '@inertiajs/react';
import NavBar from "@/Components/NavBar";
import { router } from '@inertiajs/react';
import { Link } from '@inertiajs/react';
import FlashMessage from '@/Components/FlashMessage';
import { useMemo } from 'react';
import { useCurrency } from '@/Hooks/useCurrency';

export default function Index({ items, currencies }) {
    const { selectedCurrency, setCurrency, convert } = useCurrency(currencies);

    const grandTotal = useMemo(() => {
        const total = items.reduce((sum, item) => {
            const servicesSum = item.selected_services.reduce((sSum, service) => {
                const servicePrice = parseFloat(service.pivot?.price || 0);
                return sSum + servicePrice;
            }, 0);

            const productPrice = parseFloat(item.product?.price || 0);
            const quantity = parseInt(item.quantity || 0);

            return sum + (productPrice + servicesSum) * quantity;
        }, 0);

        return Math.round(total * 100) / 100;
    }, [items]);

    return (
        <>
            <Head title="Каталог" />
            <Header
                currencySlot={
                    <select
                        value={selectedCurrency?.id}
                        onChange={(e) => setCurrency(e.target.value)}
                        className="w-24 bg-cyan-200 h-10 border rounded-lg"
                    >
                        {currencies.map(c => <option key={c.id} value={c.id}>{c.name}</option>)}
                    </select>
                }
            />
            <FlashMessage />

            {items.length > 0 ?
                <>
                    <h2 className="font-bold text-2xl text-center p-3">Ваши товары</h2>
                    <ul className="list-decimal list-inside">
                        {items.map(item => {
                            // Считаем сумму только услуг для этого товара
                            const servicesTotal = item.selected_services.reduce((sum, s) => sum + (parseFloat(s.pivot.price)), 0);
                            // Считаем итог для всей строки (товар + услуги) * кол-во
                            const rowTotal = (parseFloat(item.product.price) + servicesTotal) * item.quantity;

                            return (
                                <li className="flex flex-row pl-5 p-3 even:bg-gray-100 odd:bg-white" key={item.cart_id} >
                                    <img src={item.product.image_url} alt={item.product.name} className="h-40 border" />
                                    <div className="ml-4 w-1/3">
                                        <p><span className="font-semibold">Название товара: </span>{item.product.name}</p>
                                        <p><span className="font-semibold">Цена: </span>
                                            {convert(item.product.price)}</p>
                                        <p><span className="font-semibold">Количество: </span>{item.quantity}</p>

                                        {item.selected_services.length > 0
                                            ?
                                            <> <h3 className="font-bold p-1">Выбранные услуги</h3>
                                                <ol className="pl-1 list-disc list-inside">
                                                    {item.selected_services.map(service => (
                                                        <li key={service.id}>
                                                            <p className="inline"><span className="font-semibold">
                                                                {service.name}</span> - {convert(service.pivot.price)}</p>
                                                        </li>
                                                    ))}
                                                </ol>
                                            </>
                                            :
                                            <h3 className="font-bold p-1">Услуги не добавлены</h3>
                                        }

                                    </div>
                                    <div className="flex flex-col items-center justify-center gap-3">
                                        <div className="flex flex-row items-center gap-4 mt-2">
                                            <button
                                                onClick={() => router.post(route('basket.update', item.cart_id), {
                                                    _method: 'patch',
                                                    quantity: item.quantity - 1
                                                }, { preserveScroll: true })}
                                                disabled={item.quantity <= 1}
                                                className="border border-gray-200 rounded px-2 py-1"
                                            >
                                                -
                                            </button>

                                            <span className="font-bold">{item.quantity}</span>

                                            <button
                                                onClick={() => router.post(route('basket.update', item.cart_id), {
                                                    _method: 'patch',
                                                    quantity: item.quantity + 1
                                                }, { preserveScroll: true })}
                                                className="border border-gray-200 rounded px-2 py-1"
                                            >
                                                +
                                            </button>

                                            <Link
                                                href={route('basket.destroy', item.cart_id)}
                                                method="delete"
                                                as="button"
                                                preserveScroll
                                                className="text-red-500 hover:text-red-700 text-sm font-medium"
                                            >
                                                <i className="text-lg fa-solid fa-trash-can"></i>
                                            </Link>

                                        </div>
                                        <p className="mt-2 text-center"><span className="font-semibold">
                                            Итого по позиции:</span> {convert(rowTotal)}
                                        </p>

                                    </div>
                                    <div className="flex flex-row grow">
                                        <Link
                                            href={route('catalog.show', {
                                                product: item.product.slug,
                                                selected: item.selected_services.map(s => s.id),
                                                edit_cart_id: item.cart_id
                                            })}
                                            className="border rounded-xl h-12 p-3 mx-auto text-blue-600 hover:text-blue-800 transition"
                                        >
                                            <i className="mr-2 fa-solid fa-person-walking-arrow-loop-left"></i>Изменить выбор услуг
                                        </Link>
                                    </div>
                                </li>)
                        }
                        )}
                    </ul >
                    <div className="w-full mt-1 p-6 bg-cyan-200 text-white rounded-xl shadow-lg flex justify-between items-center">
                        <div>
                            <h2 className="text-xl font-semibold text-gray-700 uppercase tracking-widest">К оплате</h2>
                            <p className="text-3xl font-semibold text-gray-700">
                                {convert(grandTotal)}</p>
                        </div>
                        <Link href={route('order.create')} className="bg-white text-stone-900 px-8 py-4 rounded-lg font-bold hover:bg-stone-200 transition">
                            Оформить заказ
                        </Link>
                    </div>
                </>
                :
                <p className="font-bold text-2xl text-center p-3 mt-5">Вы еще не добавили ни одного товара в корзину</p>
            }
        </>
    );
}