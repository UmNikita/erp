export function formatDate(date: string): string {
    const [year, month, day] = date.split(' ')[0].split('-');

    return `${day}.${month}.${year}`;
}

export function formatNumber(number: number): string {
    return number.toLocaleString('ru-RU');
}