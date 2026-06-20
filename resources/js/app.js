document.addEventListener('input', (event) => {
    const input = event.target;

    if (!(input instanceof HTMLInputElement)) {
        return;
    }

    if (input.matches('[data-digits-only]')) {
        const maxLength = input.maxLength > 0 ? input.maxLength : undefined;
        const value = input.value.replace(/\D/g, '');
        input.value = maxLength ? value.slice(0, maxLength) : value;
        return;
    }

    if (input.matches('[data-decimal-only]')) {
        const maxLength = input.maxLength > 0 ? input.maxLength : undefined;
        let value = input.value
            .replace(/,/g, '.')
            .replace(/[^0-9.]/g, '')
            .replace(/(\..*)\./g, '$1');

        value = maxLength ? value.slice(0, maxLength) : value;

        if (Number.parseFloat(value) > 1) {
            value = '1';
        }

        input.value = value;
    }
});