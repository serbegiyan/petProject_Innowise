import { Head } from '@inertiajs/react';
import Header from '@/Components/Header';
import { Link } from '@inertiajs/react';
import { useState, useEffect, useCallback } from 'react';
import { router } from '@inertiajs/react';
import debounce from 'lodash/debounce';
import Search from '@/Components/Search';
import Pagination from '@/Components/Pagination';
import { useCurrency } from '@/Hooks/useCurrency';

export default function Index({ products, filters, categories, sortOptions = [] }) {
    const { selectedCurrency, setCurrency, convert, currencies } = useCurrency();

    const [values, setValues] = useState({
        search: filters?.search ?? '',
        category: filters?.category ?? '',
        sort: filters?.sort ?? '',
    });

    useEffect(() => {
        setValues({
            search: filters?.search || '',
            category: filters?.category ? String(filters.category) : '',
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

    const getSortLabel = (value) => sortOptions.find(o => o.value === value)?.label || value;

    return (
        <>
            <Head title="Каталог" />
            <Header
                searchSlot={
                    <Search
                        onSubmit={handleSubmit}
                        value={values.search || ''}
                        onChange={handleChange}
                        name="search"
                    />
                }
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

            {/* Фильтры */}
            <div className="flex flex-row justify-between p-4 border-b">
                <select
                    className="border rounded-lg py-2 w-fit"
                    name="category"
                    value={String(values.category || '')}
                    onChange={handleChange}
                >
                    <option value="">Все категории</option>
                    {categories.map(cat => (
                        <option key={cat.id} value={String(cat.id)}>
                            {cat.name}
                        </option>
                    ))}
                </select>

                <div className='flex flex-row gap-2'>
                    {Object.entries(filters).map(([key, value]) => {
                        if (!value) return null;

                        let label = value;
                        if (key === 'category') label = `Категория: ${getCategoryName(value)}`;
                        if (key === 'sort') label = `Сортировка: ${getSortLabel(value)}`;
                        if (key === 'search') label = `Поиск: ${value}`;

                        return (
                            <span key={key} className="flex items-center bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                                {label}
                                <button onClick={() => removeFilter(key)} className="ml-2 hover:text-red-500">×</button>
                            </span>
                        );
                    })}

                    {Object.entries(filters).some(([k, v]) => v && k !== 'currency') && (
                        <button onClick={resetAll} className="text-sm text-gray-500 underline ml-2">
                            Сбросить всё
                        </button>
                    )}
                </div>

                <select
                    className="border rounded-lg py-2 w-fit"
                    name="sort"
                    value={String(values.sort || '')}
                    onChange={handleChange}
                >
                    {sortOptions.map(option => (
                        <option key={option.value} value={option.value}>
                            {option.label}
                        </option>
                    ))}
                </select>
            </div>

            {/* Сетка товаров */}
            {
                products.data.length > 0 ? (
                    <div className="grid grid-cols-4 gap-4 p-4">
                        {products.data.map((product) => (
                            <Link key={product.id} href={route('catalog.show', { product: product.slug, ...filters })}
                                className="hover:scale-105 transition">
                                <div className='p-4 bg-gray-300 h-full'>
                                    <img src={product.image_url} alt={product.name} className="w-full aspect-square object-contain" />
                                    <p className="mt-2 font-semibold text-center truncate">{product.name}</p>
                                    <p className='text-center'>
                                        {convert(product.price)}
                                    </p>
                                </div>
                            </Link>
                        ))}
                    </div>
                ) : <p className="p-4 text-center">Товаров нет.</p>
            }

            <div className="p-4">
                <Pagination links={products.meta?.links} />
            </div>
        </>
    );
}
