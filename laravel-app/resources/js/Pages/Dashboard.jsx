import { Head } from '@inertiajs/react';
import Header from '@/Components/Header';
import NavBar from '@/Components/NavBar';
import FlashMessage from '@/Components/FlashMessage';

export default function Dashboard({ orders }) {
    const calculateOrderTotal = (order) => {
        return order.items?.reduce((total, item) => {
            const itemBasePrice = Number(item.price) * item.quantity;

            const servicesTotal = item.services?.reduce((sTotal, service) => {
                return sTotal + Number(service.pivot.price);
            }, 0) || 0;

            return total + itemBasePrice + servicesTotal;
        }, 0);
    };
    return (
        <>
            <Header><NavBar /></Header>
            <Head title="Dashboard" />
            <FlashMessage />

            {orders.length === 0 && <div className="py-12">
                <p className='text-center font-bold mb-3'>Вы успешно вошли в систему!</p>
                <p className='text-center font-bold'>У вас пока нет заказов.</p>
            </div>}
            {orders.length > 0 && <h2 className="text-center font-bold text-2xl text-gray-700 my-5 ">Ваши заказы</h2>}
            <div className='px-5 w-1/2 mx-auto'>
                <ul className=' py-3 space-y-3'>
                    {orders.map(order => {
                        return (
                            <li key={order.id} className='even:bg-white odd:bg-stone-100 p-5 rounded-lg'>
                                <p className='pt-2'><span className='font-bold'>
                                    Заказ на имя: </span>{order.customer_name}</p>
                                <p className='pt-2'><span className='font-bold'>
                                    Дата заказа:&nbsp;
                                </span>{
                                        new Date(order.created_at).toLocaleString('ru-RU', {
                                            day: '2-digit',
                                            month: '2-digit',
                                            year: 'numeric',
                                            hour: '2-digit',
                                            minute: '2-digit'
                                        })
                                    }</p>
                                <p className='pt-2'>
                                    <span className='font-bold'>
                                        Статус:&nbsp;
                                    </span>
                                    <span className={`px-2 py-1 rounded border ${order.status_css}`}>
                                        {order.status_label}
                                    </span>
                                </p>
                                {order.payment_method && <p>Платеж: {order.payment_method}</p>}
                                {order.comment && <p>Комментарий: {order.comment}</p>}

                                <div className="space-y-3">
                                    <h3 className="text-lg font-bold mt-4">Товары</h3>
                                    {order.items?.map(item => (
                                        <div key={item.id} className="border-l-4 border-blue-500 pl-3 py-1">
                                            <div className="flex justify-between font-medium">
                                                <span>{item.product_name} x {item.quantity}</span>
                                                <span>{Number(item.price * item.quantity).toLocaleString()} BYN</span>
                                            </div>

                                            {item.services && item.services.length > 0 && (
                                                <ul className="text-xs text-gray-500 mt-1">
                                                    {item.services.map((service, idx) => (
                                                        <li key={idx}>
                                                            + {service.name} ({Number(service.pivot.price).toLocaleString()} BYN)
                                                        </li>
                                                    ))}
                                                </ul>
                                            )}
                                        </div>
                                    ))}
                                </div>

                                <div className="mt-4 pt-2 border-t flex justify-between items-center font-bold text-lg">
                                    <span>Сумма заказа:</span>
                                    <span className="text-blue-600">
                                        {Number(calculateOrderTotal(order)).toLocaleString('ru-RU', { minimumFractionDigits: 2 })} BYN
                                    </span>
                                </div>
                            </li>
                        )
                    }
                    )}
                </ul>
            </div>
        </>
    );
}
