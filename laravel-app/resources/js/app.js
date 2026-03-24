window.changeCurrency = function (id) {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (!token) {
        console.error('CSRF-токен не найден!');
        return;
    }

    fetch('/currency/change', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ id: id })
    })
        .then(response => {
            if (response.ok) {
                window.location.reload();
            } else {
                console.error('Ошибка сервера');
            }
        })
        .catch(error => console.error('Ошибка запроса:', error));
}
