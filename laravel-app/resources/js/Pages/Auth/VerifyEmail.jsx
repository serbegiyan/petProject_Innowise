import PrimaryButton from '@/Components/PrimaryButton';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function VerifyEmail({ status }) {
    const { post, processing } = useForm({});

    const submit = (e) => {
        e.preventDefault();

        post(route('verification.send'));
    };

    return (
        <GuestLayout>
            <Head title="Подтверждение почты" />

            <div className="mb-4 text-sm text-gray-600">
                Спасибо за регистрацию! Прежде чем начать, пожалуйста, подтвердите свой адрес электронной почты, перейдя по ссылке, которую мы только что вам отправили. Если вы не получили письмо, мы с удовольствием вышлем вам еще одно.
            </div>

            {status === 'verification-link-sent' && (
                <div className="mb-4 text-sm font-medium text-green-600">
                    На указанный вами при регистрации адрес электронной почты отправлена ​​новая ссылка для подтверждения.
                </div>
            )}

            <form onSubmit={submit}>
                <div className="mt-4 flex items-center justify-between">
                    <PrimaryButton disabled={processing}>
                        Отправить ссылку для подтверждения
                    </PrimaryButton>

                    <Link
                        href={route('logout')}
                        method="post"
                        as="button"
                        className="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Выйти
                    </Link>
                </div>
            </form>
        </GuestLayout>
    );
}
