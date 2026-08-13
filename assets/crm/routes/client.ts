export function clientUrl(id: number) {
    return `/crm/client/${id}/review`;
}

export function clientEmailUrl(id: number) {
    return `/crm/client/${id}/emails`;
}

export function clientLeadsUrl(id: number) {
    return `/crm/client/${id}/leads`;
}

export function clientContactsUrl(id: number) {
    return `/crm/client/${id}/contacts`;
}

export function clientStoryUrl(id: number) {
    return `/crm/client/${id}/story`;
}
export function clientTableUrl(page: number = 1) {
    return `/crm/clients?page=${page}`;
}