export function useRequestDeduplication(timeout = 5000) {
    const requests = new Set<number>();

    function canRequest(id: number): boolean {
        if (requests.has(id)) {
            return false;
        }

        requests.add(id);

        return true;
    }

    function release(id: number): void {
        setTimeout(() => {
            requests.delete(id);
        }, timeout);
    }

    return {
        canRequest,
        release,
    };
}