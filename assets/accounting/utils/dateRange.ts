export interface DateRange {
    from: string;
    to: string;
}

export function toLocalIso(date: Date): string {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

export function parseLocalIso(value: string): Date {
    return new Date(`${value}T12:00:00`);
}

export function monthRange(offset = 0): DateRange {
    const now = new Date();
    const first = new Date(now.getFullYear(), now.getMonth() + offset, 1);
    const last = new Date(now.getFullYear(), now.getMonth() + offset + 1, 0);
    return { from: toLocalIso(first), to: toLocalIso(last) };
}

export function lastDaysRange(days: number): DateRange {
    const end = new Date();
    const start = new Date(end.getFullYear(), end.getMonth(), end.getDate());
    start.setDate(start.getDate() - Math.max(0, days - 1));
    return { from: toLocalIso(start), to: toLocalIso(end) };
}

export function lastMonthsRange(months: number): DateRange {
    const end = new Date();
    const start = new Date(end.getFullYear(), end.getMonth(), end.getDate());
    start.setMonth(start.getMonth() - months);
    return { from: toLocalIso(start), to: toLocalIso(end) };
}

export function formatPeriodLabel(from: string, to: string): string {
    const current = monthRange();
    const previous = monthRange(-1);
    if (from === current.from && to === current.to) {
        const label = new Intl.DateTimeFormat('ru-RU', { month: 'long', year: 'numeric' }).format(parseLocalIso(from));
        return label.charAt(0).toUpperCase() + label.slice(1);
    }
    if (from === previous.from && to === previous.to) return 'Прошлый месяц';

    const formatter = new Intl.DateTimeFormat('ru-RU', { day: 'numeric', month: 'short', year: 'numeric' });
    return `${formatter.format(parseLocalIso(from))} — ${formatter.format(parseLocalIso(to))}`;
}
