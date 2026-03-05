import { usePage } from '@inertiajs/react';
import { useState, useEffect } from 'react';

export default function FlashMessage() {
    const { flash } = usePage().props;
    const [visible, setVisible] = useState(false);

    useEffect(() => {
        if (flash.success || flash.error) {
            setVisible(true);
            const timer = setTimeout(() => setVisible(false), 2000);
            return () => clearTimeout(timer);
        }
    }, [flash]);

    if (!visible) return null;

    return (
        <div className="fixed top-5 right-5 z-50 animate-bounce-in">
            {flash.success && (
                <div className="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg border border-green-600">
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