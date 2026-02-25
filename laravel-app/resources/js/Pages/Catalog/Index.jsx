import { Head } from '@inertiajs/react';
import Header from '@/Components/Header';
import { Link } from '@inertiajs/react';
import NavBar from '@/Components/NavBar';

export default function Index({ products }) {

    return (
        <>
            <Head title="Каталог" />
            <Header><NavBar /> </Header>
            {products.length > 0
                ? (
                    <div className="grid grid-cols-4 gap-4">
                        {products.map((product) => (
                            <div key={product.id}>
                                <Link title={product.name} href={`/catalog/${product.slug}`}>
                                    <div className='p-4 bg-gray-300'>
                                        <img src={product.image} alt={product.name} className="w-full h-full object-cover" />
                                        <p className="mt-2 font-semibold text-center truncate">{product.name}</p>
                                        <p className='text-center'><span className='font-semibold'>Цена: </span>{product.price} BYN</p>
                                    </div>
                                </Link>
                            </div>
                        ))}
                    </div>
                )
                : <p>Товаров нет.</p>
            }
        </>
    );
}