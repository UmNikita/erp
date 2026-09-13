import { ref } from 'vue';

export function useLRUCache<T>(limit: number) {
    const cache = new Map<number, T>();
    const order = ref<number[]>([]);

    function get(key: number): T | undefined {
        const value = cache.get(key);

        if (value === undefined) {
            return undefined;
        }

        touch(key);

        return value;
    }

    function set(key: number, value: T) {
        if (cache.has(key)) {
            order.value = order.value.filter(item => item !== key);
        }

        cache.set(key, value);
        order.value.push(key);

        if (order.value.length > limit) {
            const oldest = order.value.shift();

            if (oldest !== undefined) {
                cache.delete(oldest);
            }
        }
    }

    function has(key: number) {
        return cache.has(key);
    }

    function clear() {
        cache.clear();
        order.value = [];
    }

    function touch(key: number) {
        order.value = order.value.filter(item => item !== key);
        order.value.push(key);
    }

    return {
        get,
        set,
        has,
        clear
    };
}