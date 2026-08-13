export function formatDate(date: string): string {
    const [year, month, day] = date.split(' ')[0].split('-');

    return `${day}.${month}.${year}`;
}

export function formatStatus(status: string): string {
    switch(status) {
        case "active": {
            return "Активный";
        }
        case "won": {
            return "Успешно завершен";
        }
        case "lost": {
            return "Забракованный";
        }
        default: {
            return "Активный";
        }
    }
}

export function formatResponseDate(date: string): string {

    const [year, month, day] = date.split('T')[0].split('-');


    return `${day}.${month}.${year}`;
}

export function formatPhone(phone: string): string {
    let digits = phone.replace(/\D/g, '');

    if (digits.startsWith('8')) {
        digits = '7' + digits.slice(1);
    }

    if (!digits.startsWith('7') || digits.length !== 11) {
        return phone;
    }

    return `+7 (${digits.slice(1, 4)}) ${digits.slice(4, 7)}-${digits.slice(7, 9)}-${digits.slice(9, 11)}`;
}

export function formatAmount(value: number | string | null | undefined): string {
    if (value === null || value === undefined || value === '') {
        return '';
    }

    return new Intl.NumberFormat('ru-RU').format(Number(value));
}

export function formatNumber(number: number): string {
    return number.toLocaleString('ru-RU');
}