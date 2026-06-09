import { useCallback } from 'react';
import { usePage, router } from '@inertiajs/react';

export function useCurrency() {
    const { auth, currencies } = usePage().props;

    const selectedCurrency = auth?.currency
        ?? currencies?.find(c => c.name === 'BYN')
        ?? currencies?.[0];

    const isByn = selectedCurrency?.name === 'BYN';

    const setCurrency = useCallback((id) => {
        router.post(route('currency.change'), { id }, {
            preserveScroll: true,
            only: ['auth', 'currencies'],
        });
    }, []);

    const convert = useCallback((amountByn, showOnlyConverted = true) => {
        const unitRate = selectedCurrency?.unit_rate ?? 1;
        const converted = (Number(amountByn || 0) / unitRate).toLocaleString('ru-RU', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });

        if (showOnlyConverted || isByn) {
            return `${converted} ${selectedCurrency?.name ?? 'BYN'}`;
        }

        const baseFormatted = Number(amountByn || 0).toLocaleString('ru-RU', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });

        return `${baseFormatted} BYN (${converted} ${selectedCurrency.name})`;
    }, [selectedCurrency, isByn]);

    return {
        selectedCurrency,
        isByn,
        setCurrency,
        convert,
        currencies,
    };
}
