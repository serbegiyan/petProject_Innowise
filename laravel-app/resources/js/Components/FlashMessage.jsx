import { usePage } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { FLASH_DISMISS_MS } from '@/shared/flashDismiss';

export default function FlashMessage() {
    const { flash } = usePage().props;
    const [visible, setVisible] = useState(false);

    useEffect(() => {
        if (flash.success || flash.error) {
            setVisible(true);
            const timer = setTimeout(() => setVisible(false), FLASH_DISMISS_MS);
            return () => clearTimeout(timer);
        }
    }, [flash]);

    if (!visible) return null;

    return (
        <div className="fixed top-4 left-4 transform animate-[slideIn_0.2s_ease-out_forwards] bg-white shadow-xl border-l-4 border-green-500 p-3 rounded flex items-center space-x-3">
            {flash.success && (
                <div className="bg-green-500 mx-auto text-white px-6 py-3 rounded-lg shadow-lg border border-green-600">
                    ✅ {flash.success}
                </div>
            )}
            {flash.error && (
                <div className="bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg border border-red-600">
                    ❌ {flash.error}
                </div>
            )}
        </div>
    );
}