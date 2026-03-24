import { Head } from '@inertiajs/react';
import Header from '@/Components/Header';
import { Link } from '@inertiajs/react';
import NavBar from '@/Components/NavBar';
import { useState, useEffect, useCallback } from 'react';
import { router } from '@inertiajs/react';
import debounce from 'lodash/debounce';
import Search from '@/Components/Search';
import Pagination from '@/Components/Pagination';

export default function Index({ products, filters, categories }) {
    const [values, setValues] = useState({
        search: filters?.search ?? '',
        category: filters?.category ?? '',
        sort: typeof filters?.sort === 'string' ? filters.sort : '',
    });

    useEffect(() => {
        setValues({
            search: filters?.search || '', // Используем опциональную цепочку ?.
            category: filters?.category || '',
            sort: typeof filters?.sort === 'string' ? filters.sort : '',
        });
    }, [filters]);

    const applyFilters = (newValues) => {
        router.get(route('catalog.index'), newValues, {
            preserveState: true,
            replace: true,
        });
    }

    const debouncedSearch = useCallback(
        debounce((currentValues) => {
            applyFilters(currentValues);
        }, 400),
        []);

    const handleChange = (e) => {
        const { name, value } = e.target;
        const nextValues = { ...values, [name]: value };
        setValues(nextValues);

        if (name === 'search') {
            debouncedSearch(nextValues);
        } else {
            applyFilters(nextValues);
        }
    }

    const handleSubmit = (e) => {
        e.preventDefault();
        debouncedSearch.cancel();
        applyFilters(values);
    };

    const resetAll = () => router.get(route('catalog.index'), {}, { replace: true });

    const removeFilter = (key) => {
        const nextValues = { ...values, [key]: '' };
        applyFilters(nextValues);
    };

    const getCategoryName = (id) => categories.find(c => c.id == id)?.name;

    return (
        <>
            <Head title="Каталог" />
            <Header>
                <Search
                    onSubmit={handleSubmit}
                    name="search"
                    value={values.search}
                    onChange={handleChange}
                />
                <NavBar />
            </Header>

            {/* Фильтры */}
            <div className="flex flex-row justify-between p-4 border-b">
                <select className="border rounded-lg py-2 w-fit"
                    name="category"
                    value={String(values.category || '')}
                    onChange={handleChange}>
                    <option value="">Все категории</option>
                    {categories.map(cat => (
                        <option key={cat.id} value={cat.id}>{cat.name}</option>
                    ))}
                </select>
                <div className='flex flex-row gap-2'>
                    {Object.entries(filters).map(([key, value]) => {
                        if (!value) return null;

                        let label = value;
                        if (key === 'category') label = `Категория: ${getCategoryName(value)}`;
                        if (key === 'sort' && value.startsWith('price_')) label = value === 'price_asc' ? 'Сначала дешевые' : 'Сначала дорогие';
                        if (key === 'sort' && value.startsWith('release_')) label = value === 'release_asc' ? 'Сначала новые' : 'Сначала старые';
                        if (key === 'search') label = `Поиск: ${value}`;

                        return (
                            <span key={key} className="flex items-center bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                                {label}
                                <button onClick={() => removeFilter(key)} className="ml-2 hover:text-red-500">×</button>
                            </span>
                        );
                    })}

                    {Object.values(filters).some(v => v) && (
                        <button onClick={resetAll} className="text-sm text-gray-500 underline ml-2">
                            Сбросить всё
                        </button>
                    )}
                </div>
                <select className="border rounded-lg py-2 w-fit"
                    name="sort" value={values.sort || ''} onChange={handleChange}>
                    <option value="">Все товары</option>
                    <option value="price_asc">Сначала дешевые</option>
                    <option value="price_desc">Сначала дорогие</option>
                    <option value="release_desc">Санчала новые</option>
                    <option value="release_asc">Сначала старые</option>
                </select>
            </div>

            {/* Сетка товаров */}
            {products.data.length > 0 ? (
                <div className="grid grid-cols-4 gap-4 p-4">
                    {products.data.map((product) => (
                        <Link key={product.id} href={route('catalog.show', { product: product.slug, ...filters })}
                            className="hover:scale-105 transition">
                            <div className='p-4 bg-gray-300 h-full'>
                                <img src={product.image_url} alt={product.name} className="w-full aspect-square object-cover" />
                                <p className="mt-2 font-semibold text-center truncate">{product.name}</p>
                                <p className='text-center'>
                                    {Number(product.price).toLocaleString('ru-RU')} BYN
                                </p>
                            </div>
                        </Link>
                    ))}
                </div>
            ) : <p className="p-4 text-center">Товаров нет.</p>}

            <div className="p-4">
                <Pagination links={products.links} />
            </div>
        </>
    );
}
