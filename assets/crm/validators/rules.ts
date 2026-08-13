type Rule = (value?: string | number) => string | null;

export function required(message: string) {
    return (value?: string | number): string | null => {
        if (value == null)
            return message;

        if(typeof value === "string") {
            if (!value.trim()) {
                return message;
            }
        }
        if(typeof value === "number") {
            if (!value) {
                return message;
            }
        }

        return null;
    };
}

export function min(message: string, minLength: number) {
    return (value?: string | number): string | null => {
        if(typeof value === "string") {
            if (value.length < minLength) {
                return message;
            }
        }
        if(typeof value === "number") {
            if (value < minLength) {
                return message;
            }
        }

        return null;
    };
}

export function max(message: string, maxLength: number) {
    return (value?: string | number): string | null => {
        if(typeof value === "string") {
            if (value.length > maxLength) {
                return message;
            }
        }
        if(typeof value === "number") {
            if (value > maxLength) {
                return message;
            }
        }

        return null;
    };
}

export function email(message: string) {
    return (value?: string | number): string | null => {
        if(typeof value === "number") {
            return message;
        }
        if(value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if(!emailRegex.test(value)) {
                return message;
            }
        }

        return null;
    };
}

export function phone(message: string) {
    return (value?: string | number): string | null => {
        if(typeof value === "number") {
            return message;
        }
        if(value) {
            const digits = value.replace(/\D/g, '');

            if (digits.length < 10 || digits.length > 15) {
                return message;
            }
        }

        return null;
    };
}

export function INN(message: string) {
    return (value?: string | number): string | null => {
        if (typeof value === 'number') {
            value = value.toString();
        }

        if (!value) {
            return null;
        }

        if (!/^\d{10}$|^\d{12}$/.test(value)) {
            return message;
        }

        return null;
    };
}

export function validate(err: Record<string, string>, key: string, rules: Rule[], value?: string | number) {
    for (const rule of rules) {
        const error = rule(value);

        if (error) {
            err[key] = error;
            return;
        }
    }
}