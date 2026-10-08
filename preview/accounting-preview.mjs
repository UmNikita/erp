import http from 'node:http';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const buildRoot = path.join(root, 'public', 'build');
const port = 8042;

function currentAccountingEntry() {
    return JSON.parse(fs.readFileSync(path.join(buildRoot, '.vite', 'entrypoints.json'), 'utf8')).entryPoints.accounting;
}

const users = [
    { id: 1, name: 'Данил', email: 'danil@example.test' },
    { id: 2, name: 'Анна Петрова', email: 'anna@example.test' },
];

let categories = [
    ['Создание сайтов', 'income', '🌐', '#3f7af6'],
    ['Рекламная подписка', 'income', '📣', '#1faf79'],
    ['Партнёрская программа', 'income', '🤝', '#8a63ea'],
    ['Консультации', 'income', '💬', '#ed9a32'],
    ['Серверы и хостинг', 'expense', '🖥️', '#e75a69'],
    ['Зарплаты', 'expense', '👥', '#ee8b3d'],
    ['Сервисы и подписки', 'expense', '🧩', '#8d6be8'],
    ['Офисные расходы', 'expense', '🏢', '#4e8cd8'],
].map(([name, type, icon, color], index) => ({
    id: index + 1, name, type, icon, color, active: true,
    createdAt: '2026-09-01T09:00:00+03:00', createdBy: users[0],
}));

let operationId = 7;
let allocationId = 7;
let auditId = 5;
let operations = [
    operation(1, 'income', 68000, '2026-09-03', 'ООО «Сфера»', 'Разработка корпоративного сайта', true, 1, [[1, 68000]]),
    operation(2, 'income', 42000, '2026-09-06', 'Альфа', 'Ежемесячное ведение рекламы', true, 2, [[2, 42000]]),
    operation(3, 'expense', 56000, '2026-09-09', 'Команда', 'Зарплата за первую половину месяца', true, 1, [[6, 56000]]),
    operation(4, 'income', 15000, '2026-09-12', 'Иван Иванов', 'Новая заявка', true, 1, []),
    operation(5, 'expense', 3200, '2026-09-14', '', 'Оплата сервиса Selectel', true, 2, []),
    operation(6, 'expense', 5000, '2026-09-17', 'Selectel', 'Сервер за сентябрь', true, 1, [[5, 5000]]),
];

let audit = [
    auditEntry(1, 'transaction.created', 'transaction', 6, { amount: 5000 }),
    auditEntry(2, 'transaction.allocated', 'transaction', 6, { category: 'Серверы и хостинг', amount: 5000 }),
    auditEntry(3, 'transaction.created', 'transaction', 5, { amount: 3200 }),
    auditEntry(4, 'category.updated', 'category', 2, { name: 'Рекламная подписка' }),
];

function operation(id, type, amount, date, counterparty, comment, included, userId, allocationPairs) {
    return {
        id, type, amount, date, counterparty, comment, included,
        createdBy: users.find((user) => user.id === userId) ?? null,
        createdAt: `${date}T10:00:00+03:00`, updatedAt: `${date}T10:00:00+03:00`,
        allocations: allocationPairs.map(([categoryId, allocatedAmount]) => ({ id: allocationId++, categoryId, amount: allocatedAmount })),
    };
}

function auditEntry(id, action, entityType, entityId, payload, restoredAt = null) {
    return { id, user: users[0], action, entityType, entityId, payload, createdAt: '2026-09-18T10:00:00+03:00', restoredAt };
}

function addAudit(action, entityType, entityId, payload = {}) {
    audit.unshift(auditEntry(auditId++, action, entityType, entityId, payload));
}

function json(res, status, data) {
    const body = status === 204 ? '' : JSON.stringify(data);
    res.writeHead(status, { 'Content-Type': 'application/json; charset=utf-8', 'Cache-Control': 'no-store' });
    res.end(body);
}

async function body(req) {
    let value = '';
    for await (const chunk of req) value += chunk;
    return value ? JSON.parse(value) : {};
}

function html() {
    const accounting = currentAccountingEntry();
    const css = accounting.css.map((url) => `<link rel="stylesheet" href="${url}">`).join('');
    const preload = (accounting.preload ?? []).map((url) => `<link rel="modulepreload" href="${url}">`).join('');
    const scripts = accounting.js.map((url) => `<script type="module" src="${url}"></script>`).join('');
    return `<!doctype html><html lang="ru"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Бухгалтерия — локальный просмотр</title>${css}${preload}<style>
    *{box-sizing:border-box}html,body{margin:0;min-height:100%;font-family:Inter,Arial,sans-serif;color:#101828}a{text-decoration:none}
    .crm-app{min-height:100vh;display:grid;margin-left:78px;width:calc(100vw - 78px)}
    .rail{position:fixed;inset:0 auto 0 0;width:78px;height:100vh;padding:14px 10px;background:#111824;display:flex;flex-direction:column;align-items:center;gap:12px;z-index:1000;border-right:1px solid rgba(255,255,255,.04)}
    .logo{width:40px;height:40px;border:1px solid rgba(22,184,217,.5);border-radius:13px;display:grid;place-items:center;color:#fff;font-weight:850;margin-bottom:10px}
    .rail-nav{width:100%;display:flex;flex-direction:column;align-items:center;gap:9px}.rail-btn{width:42px;height:42px;border-radius:13px;color:#b8c0cc;display:grid;place-items:center;position:relative;font-size:17px}.rail-btn.active{background:#202b3a;color:#fff}.rail-btn.active:before{content:"";position:absolute;left:-10px;width:3px;height:22px;border-radius:99px;background:#16b8d9}
    .side{width:250px;min-width:250px;min-height:100vh;padding:22px 18px;background:#fff;border-right:1px solid #e1e6ee;grid-column:1}.brand{font-size:23px;font-weight:850}.brand b{color:#16a7ca}.side-sub{margin-top:2px;color:#929cad;font-size:11px;letter-spacing:.12em;text-transform:uppercase}.side-section{margin-top:28px}.side-title{margin:0 0 10px;color:#9aa3af;font-size:11px;letter-spacing:.08em;text-transform:uppercase}.nav a{display:flex;align-items:center;gap:10px;margin-bottom:5px;padding:10px 12px;border-radius:13px;color:#2a303b;font-size:14px}.nav a.active{color:#0788a5;background:#e9f7fa;font-weight:700}
    </style></head><body><div class="crm-app"><aside class="rail"><div class="logo">RH</div><nav class="rail-nav"><span class="rail-btn">⌂</span><span class="rail-btn">▦</span><span class="rail-btn">⇄</span><span class="rail-btn">◎</span><span class="rail-btn active">▤</span></nav></aside><div id="accounting-app"></div></div>${scripts}</body></html>`;
}

async function api(req, res, url) {
    const pathname = url.pathname;
    if (req.method === 'GET' && pathname === '/api/v1/accounting/bootstrap') {
        const from = url.searchParams.get('from') ?? '0000-01-01';
        const to = url.searchParams.get('to') ?? '9999-12-31';
        return json(res, 200, { categories, transactions: operations.filter((item) => item.date >= from && item.date <= to), audit, users, currentUser: users[0] });
    }
    if (req.method === 'GET' && pathname === '/api/v1/accounting/audit') return json(res, 200, audit);

    if (req.method === 'POST' && pathname === '/api/v1/accounting/transactions') {
        const data = await body(req);
        const item = operation(operationId++, data.type, Number(data.amount), data.date, data.counterparty ?? '', data.comment ?? null, Boolean(data.included), 1, []);
        operations.unshift(item); addAudit('transaction.created', 'transaction', item.id, { amount: item.amount });
        return json(res, 201, item);
    }

    const transactionMatch = pathname.match(/^\/api\/v1\/accounting\/transactions\/(\d+)$/);
    if (transactionMatch) {
        const id = Number(transactionMatch[1]);
        const item = operations.find((candidate) => candidate.id === id);
        if (!item) return json(res, 404, { error: 'Операция не найдена' });
        if (req.method === 'DELETE') {
            operations = operations.filter((candidate) => candidate.id !== id);
            addAudit('transaction.deleted', 'transaction', id, { snapshot: structuredClone(item) });
            return json(res, 204, null);
        }
        if (req.method === 'PATCH') {
            const data = await body(req);
            Object.assign(item, data, { updatedAt: new Date().toISOString() });
            if ('createdById' in data) item.createdBy = users.find((user) => user.id === Number(data.createdById)) ?? null;
            if ('categoryId' in data) item.allocations = data.categoryId ? [{ id: allocationId++, categoryId: Number(data.categoryId), amount: Number(item.amount) }] : [];
            addAudit('transaction.updated', 'transaction', id, data);
            return json(res, 200, item);
        }
    }

    const allocationMatch = pathname.match(/^\/api\/v1\/accounting\/transactions\/(\d+)\/allocate$/);
    if (req.method === 'POST' && allocationMatch) {
        const id = Number(allocationMatch[1]);
        const data = await body(req);
        const item = operations.find((candidate) => candidate.id === id);
        if (!item) return json(res, 404, { error: 'Операция не найдена' });
        item.allocations.push({ id: allocationId++, categoryId: Number(data.categoryId), amount: Number(data.amount) });
        addAudit('transaction.allocated', 'transaction', id, data);
        return json(res, 200, item);
    }

    const unallocationMatch = pathname.match(/^\/api\/v1\/accounting\/transactions\/(\d+)\/unallocate$/);
    if (req.method === 'POST' && unallocationMatch) {
        const id = Number(unallocationMatch[1]);
        const data = await body(req);
        const item = operations.find((candidate) => candidate.id === id);
        if (!item) return json(res, 404, { error: 'Операция не найдена' });
        const allocation = item.allocations.find((candidate) => candidate.categoryId === Number(data.categoryId));
        if (!allocation) return json(res, 404, { error: 'Распределение не найдено' });
        item.allocations = item.allocations.filter((candidate) => candidate !== allocation);
        addAudit('transaction.unallocated', 'transaction', id, { categoryId: Number(data.categoryId), amount: allocation.amount });
        return json(res, 200, item);
    }

    if (req.method === 'POST' && pathname === '/api/v1/accounting/categories') {
        const data = await body(req);
        const item = { id: Math.max(0, ...categories.map(({ id }) => id)) + 1, ...data, active: true, createdAt: new Date().toISOString(), createdBy: users[0] };
        categories.push(item); addAudit('category.created', 'category', item.id, { name: item.name });
        return json(res, 201, item);
    }

    const categoryMatch = pathname.match(/^\/api\/v1\/accounting\/categories\/(\d+)$/);
    if (categoryMatch) {
        const id = Number(categoryMatch[1]);
        const item = categories.find((candidate) => candidate.id === id);
        if (!item) return json(res, 404, { error: 'Статья не найдена' });
        if (req.method === 'PATCH') {
            Object.assign(item, await body(req)); addAudit('category.updated', 'category', id, { name: item.name });
            return json(res, 200, item);
        }
        if (req.method === 'DELETE') {
            categories = categories.filter((candidate) => candidate.id !== id); addAudit('category.deleted', 'category', id, { name: item.name });
            return json(res, 204, null);
        }
    }

    const restoreMatch = pathname.match(/^\/api\/v1\/accounting\/audit\/(\d+)\/restore$/);
    if (req.method === 'POST' && restoreMatch) {
        const entry = audit.find((candidate) => candidate.id === Number(restoreMatch[1]));
        const snapshot = entry?.payload?.snapshot;
        if (!entry || entry.restoredAt || !snapshot) return json(res, 409, { error: 'Операцию нельзя восстановить' });
        operations.unshift(snapshot); entry.restoredAt = new Date().toISOString();
        addAudit('transaction.restored', 'transaction', entry.entityId, { sourceAuditId: entry.id });
        return json(res, 200, snapshot);
    }

    return json(res, 404, { error: 'Маршрут локального просмотра не найден' });
}

const server = http.createServer(async (req, res) => {
    try {
        const url = new URL(req.url, `http://${req.headers.host}`);
        if (url.pathname.startsWith('/api/v1/accounting')) return await api(req, res, url);
        if (url.pathname.startsWith('/build/')) {
            const requested = path.resolve(buildRoot, `.${url.pathname.slice('/build'.length)}`);
            if (!requested.startsWith(buildRoot) || !fs.existsSync(requested)) { res.writeHead(404); return res.end(); }
            const ext = path.extname(requested);
            res.writeHead(200, { 'Content-Type': ext === '.css' ? 'text/css' : 'text/javascript', 'Cache-Control': 'no-store' });
            return fs.createReadStream(requested).pipe(res);
        }
        if (url.pathname === '/' || url.pathname.startsWith('/accounting')) {
            res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8', 'Cache-Control': 'no-store' });
            return res.end(html());
        }
        res.writeHead(404); res.end();
    } catch (error) {
        console.error(error);
        json(res, 500, { error: 'Ошибка локального просмотра' });
    }
});

server.listen(port, '127.0.0.1', () => {
    console.log(`Accounting preview: http://127.0.0.1:${port}/accounting`);
});
