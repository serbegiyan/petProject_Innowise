import Header from "@/Components/Header";
import NavBar from "@/Components/NavBar";
import FlashMessage from '@/Components/FlashMessage';
import TextInput from "@/Components/TextInput";
import InputLabel from "@/Components/InputLabel";
import { usePage, useForm, Link } from '@inertiajs/react';


export default function Index({ items, totalAmount, userEmail, services }) {
    const { auth } = usePage().props;

    const { data, setData, post, errors, processing } = useForm({
        customer_name: auth.user?.name || '',
        customer_email: auth.user?.email || '',
        customer_phone: '',
        customer_address: '',
        customer_comment: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('order.store'));
    };

    return (
        <div>
            <Header><NavBar /> </Header>
            <FlashMessage />
            <div className="flex flex-row justify-items-end">
                <h2 className="font-bold text-2xl mx-auto text-center p-3">Ваш заказ</h2>
                <Link
                    href={route('basket.index')}
                    className="border rounded-xl h-12 p-3 mt-3 mr-5 text-blue-600 hover:text-blue-800 transition"
                >
                    <i className="mr-2 fa-solid fa-person-walking-arrow-loop-left"></i>Вернуться в корзину
                </Link>
            </div>
            <div className="flex flex-row gap-5 px-5">
                <ul className="list-decimal list-inside w-1/2">
                    {items.map(item => {
                        return (
                            <li className="flex flex-row pl-5 p-3 even:bg-gray-100 odd:bg-white" key={item.product.id} >
                                <img src={item.product.image} alt={item.product.name} className="h-20 border" />
                                <div className="ml-4">
                                    <p><span className="font-semibold">Название товара: </span>{item.product.name}</p>
                                    <p><span className="font-semibold">Цена: </span>
                                        {Number(item.product.price).toLocaleString('ru-RU', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        })} BYN</p>
                                    <p><span className="font-semibold">Количество: </span>{item.quantity}</p>

                                    {item.services.length > 0
                                        ?
                                        <> <h3 className="font-bold p-1">Выбранные услуги</h3>
                                            <ol className="pl-1 list-disc list-inside">
                                                {item.services.map(service => (
                                                    <li key={service.id}>
                                                        <p className="inline"><span className="font-semibold">
                                                            {service.name}</span> - {service.price} BYN</p>
                                                    </li>
                                                ))}
                                            </ol>
                                        </>
                                        :
                                        <h3 className="font-bold p-1">Услуги не добавлены</h3>
                                    }
                                </div>
                            </li>)
                    }
                    )}
                </ul >

                <form id="checkout-form" onSubmit={submit}
                    className="w-1/2 flex flex-col gap-3">
                    <InputLabel value="Имя получателя" />
                    <TextInput
                        type="text"
                        value={data.customer_name}
                        onChange={e => setData('customer_name', e.target.value)}
                        placeholder="Имя получателя" />
                    {errors.customer_name && <p className="text-red-500 text-xs">{errors.customer_name}</p>}

                    <InputLabel value="Email" />
                    <TextInput
                        type="email"
                        value={data.customer_email}
                        onChange={e => setData('customer_email', e.target.value)}
                        placeholder="Email" />
                    {errors.customer_email && <p className="text-red-500 text-xs">{errors.customer_email}</p>}

                    <InputLabel value="Телефон" />
                    <TextInput
                        type="phone"
                        value={data.customer_phone}
                        onChange={e => setData('customer_phone', e.target.value)}
                        placeholder="Телефон" />
                    {errors.customer_phone && <p className="text-red-500 text-xs">{errors.customer_phone}</p>}

                    <InputLabel value="Адрес" />
                    <TextInput
                        type="text"
                        value={data.customer_address}
                        onChange={e => setData('customer_address', e.target.value)}
                        placeholder="Адрес" />
                    {errors.customer_address && <p className="text-red-500 text-xs">{errors.customer_address}</p>}

                    <InputLabel value="Комментарий" />
                    <textarea
                        className="w-full border rounded-lg p-2"
                        rows="2"
                        onChange={e => setData('customer_comment', e.target.value)}
                        placeholder="Комментарий"></textarea>
                </form>
            </div>
            <div className="w-full mt-3 p-6 bg-cyan-200 text-white rounded-xl shadow-lg flex justify-between items-center">
                <p className="text-3xl font-semibold text-gray-700">Сумма заказа: {Number(totalAmount).toLocaleString('ru-RU', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })} BYN</p>
                <button
                    type="submit"
                    form="checkout-form"
                    disabled={processing}
                    className="bg-white text-stone-900 px-8 py-4 rounded-lg font-bold hover:bg-stone-200 transition">
                    {processing ? 'Оформляем...' : 'Подтвердить заказ'}
                </button>
            </div>
        </div >
    )
}
