export function archiveTableUrl(page: number = 1) {
    return `/crm/archive?page=${page}`;
}

export function getCurrentLead(id: number = 1) {
    return `/crm/lead/${id}`;
}