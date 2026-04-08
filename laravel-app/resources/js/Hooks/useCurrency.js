import { useState, useEffect, useCallback } from 'react';

export function useCurrency(currencies) {
    // 1. Инициализация из localStorage или дефолт
    const [selectedCurrency, setSelectedCurrency] = useState(() => {
        const savedId = localStorage.getItem('user_currency_id');
        return currencies?.find(c => String(c.id) === savedId) ||
            currencies?.find(c => c.name === 'BYN') ||
            currencies?.[0];
    });

    // 2. Функция смены валюты
    const setCurrency = useCallback((id) => {
        const cur = currencies.find(c => String(c.id) === String(id));
        if (cur) {
            setSelectedCurrency(cur);
            localStorage.setItem('user_currency_id', id);
        }
    }, [currencies]);

    // 3. Универсальная функция конвертации
    const convert = useCallback((price, showOnlyConverted = true) => {
        const basePrice = Number(price || 0);
        const rate = selectedCurrency?.rate || 1;
        const converted = (basePrice / rate).toLocaleString('ru-RU', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        if (showOnlyConverted || selectedCurrency?.name === 'BYN') {
            return `${converted} ${selectedCurrency?.name || 'BYN'}`;
        }

        // Формат для оформления заказа: 100 BYN (30 USD)
        const baseFormatted = basePrice.toLocaleString('ru-RU', { minimumFractionDigits: 2 });
        return `${baseFormatted} BYN (${converted} ${selectedCurrency.name})`;
    }, [selectedCurrency]);

    return {
        selectedCurrency,
        setCurrency,
        convert
    };
}
