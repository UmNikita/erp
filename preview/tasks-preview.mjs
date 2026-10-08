import http from 'node:http';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const buildRoot = path.join(root, 'preview', 'tasks-build');
const dataFile = process.env.TASKS_PREVIEW_DATA_FILE || path.join(root, 'preview', 'tasks-preview-data.json');
const port = Number(process.env.TASKS_PREVIEW_PORT || 8043);
const users = [{ id: 1, name: 'Данил' }, { id: 2, name: 'Анна Петрова' }, { id: 3, name: 'Иван Иванов' }];
const today = new Date();
const at = (hour, offset = 0) => { const date = new Date(today); date.setDate(date.getDate() + offset); date.setHours(hour, 0, 0, 0); return date.toISOString(); };
const initial = {
    tasks: [
        { id: 1, title: 'Проверить новые заявки', description: 'Разобрать заявки, поступившие с сайта и из мессенджеров.', result: null, status: 'in_progress', priority: 'high', color: '#8a63ea', dueAt: at(18), assignee: users[0], createdBy: users[0], dutyShiftId: 1, createdAt: at(7), updatedAt: at(7) },
        { id: 2, title: 'Ответить на входящие звонки', description: 'Не оставлять пропущенные звонки без ответа.', result: null, status: 'open', priority: 'normal', color: '#8a63ea', dueAt: at(18), assignee: users[0], createdBy: users[0], dutyShiftId: 1, createdAt: at(7), updatedAt: at(7) },
        { id: 3, title: 'Согласовать макет главной страницы', description: 'Посмотреть обновлённый макет и дать обратную связь дизайнеру.', result: null, status: 'open', priority: 'normal', color: '#3d70f5', dueAt: at(16, 1), assignee: users[0], createdBy: users[1], dutyShiftId: null, createdAt: at(10, -1), updatedAt: at(10, -1) },
        { id: 4, title: 'Подготовить отчёт по рекламной кампании', description: 'Свести результаты недели.', result: 'Отчёт опубликован в общей папке.', status: 'done', priority: 'low', color: '#1faf79', dueAt: at(18, -1), assignee: users[0], createdBy: users[0], dutyShiftId: null, createdAt: at(9, -3), updatedAt: at(17, -1) },
        { id: 5, title: 'Проверить распределение оплат', description: 'Уточнить последние поступления.', result: null, status: 'open', priority: 'normal', color: '#ed9a32', dueAt: at(15, 1), assignee: users[2], createdBy: users[0], dutyShiftId: null, createdAt: at(9), updatedAt: at(9) },
    ],
    shifts: [
        { id: 1, title: 'Дежурство по входящим заявкам', assignee: users[0], startsAt: new Date(today.getTime() - 60 * 60000).toISOString(), endsAt: new Date(today.getTime() + 8 * 60 * 60000).toISOString(), checklist: ['Проверить новые заявки', 'Ответить на входящие звонки'] },
        { id: 2, title: 'Вечерняя смена', assignee: users[1], startsAt: at(18, 1), endsAt: at(23, 1), checklist: ['Проверить новые заявки', 'Передать необработанные обращения'] },
    ],
    schedules: [],
    messages: [{ id: 1, taskId: 1, author: users[0], body: 'Начал проверку заявок. Первые ответы отправлены.', createdAt: at(9) }],
    overdueReports: [],
};
let db;
try { db = JSON.parse(fs.readFileSync(dataFile, 'utf8')); }
catch { db = initial; }
db.schedules ??= [];
db.overdueReports ??= [];
db.feedback ??= [];
db.audit ??= [];
for (const report of db.overdueReports) {
    report.assigneeAtReport ??= report.author ?? db.tasks.find((task) => task.id === report.taskId)?.assignee ?? null;
    report.assigneeAtReportIds ??= report.assigneeAtReport ? [report.assigneeAtReport.id] : [];
}
for (const task of db.tasks) {
    task.assignees ??= task.assignee ? [task.assignee] : [];
    task.requestedBy ??= task.createdBy ?? null;
    task.completedAt ??= ['done', 'not_done'].includes(task.status) ? task.updatedAt : null;
    task.completedFor ??= task.completedAt ? task.assignee : null;
    task.completedForIds ??= task.completedFor ? [task.completedFor.id] : [];
}
for (const shift of db.shifts) { shift.scheduleId ??= null; shift.color ??= '#8a63ea'; shift.comment ??= null; }
function persist() { fs.writeFileSync(dataFile, JSON.stringify(db, null, 2)); }
function json(res, status, data) { res.writeHead(status, { 'Content-Type': 'application/json; charset=utf-8', 'Cache-Control': 'no-store' }); res.end(JSON.stringify(data)); }
async function payload(req) { let text = ''; for await (const chunk of req) text += chunk; return text ? JSON.parse(text) : {}; }
function person(id) { return users.find((user) => user.id === Number(id)) ?? users[0]; }
function assignees(ids) {
    if (!Array.isArray(ids) || !ids.length || ids.length > 30 || ids.some((id) => !users.some((user) => user.id === Number(id)))) return null;
    return [...new Set(ids.map(Number))].map(person);
}
function taskSnapshot(task) { return { title: task.title, description: task.description, result: task.result, status: task.status, priority: task.priority, dueAt: task.dueAt, assignees: task.assignees.map((user) => user.name), requestedBy: task.requestedBy?.name ?? null }; }
function auditTask(task, action, changes = {}, actor = users[0]) {
    db.audit.unshift({ id: Math.max(0, ...db.audit.map((entry) => entry.id)) + 1, taskId: task.id, taskTitle: task.title, actor, action, changes, createdAt: new Date().toISOString() });
}
function taskChanges(before, after) { return Object.fromEntries(Object.keys(after).filter((field) => JSON.stringify(before[field]) !== JSON.stringify(after[field])).map((field) => [field, { before: before[field], after: after[field] }])); }
function dashboard() {
    const moscowDate = (value) => new Intl.DateTimeFormat('en-CA', { timeZone: 'Europe/Moscow', year: 'numeric', month: '2-digit', day: '2-digit' }).format(new Date(value));
    const now = new Date();
    const days = Array.from({ length: 7 }, (_, index) => { const date = new Date(now); date.setDate(date.getDate() - 6 + index); return { date: moscowDate(date), done: 0, failed: 0 }; });
    const dayMap = new Map(days.map((item) => [item.date, item]));
    const start30 = new Date(now); start30.setDate(start30.getDate() - 29);
    const first30 = moscowDate(start30);
    const moved = new Set(db.overdueReports.map((item) => item.taskId));
    const summary = new Map(users.map((user) => [user.id, { person: user, done: 0, failed: 0, onTime: 0, dueDated: 0, moved: 0 }]));
    const personal = { done: 0, failed: 0, onTime: 0, dueDated: 0, moved: 0 };
    for (const task of db.tasks) {
        if (!task.completedAt || !task.completedForIds?.length || !['done', 'not_done'].includes(task.status)) continue;
        const day = moscowDate(task.completedAt);
        const kind = task.status === 'done' ? 'done' : 'failed';
        if (day >= first30) {
            for (const id of task.completedForIds) {
                const row = summary.get(id);
                if (!row) continue;
                row[kind]++;
                if (task.dueAt) { row.dueDated++; if (moved.has(task.id)) row.moved++; if (kind === 'done' && !moved.has(task.id) && new Date(task.completedAt) <= new Date(task.dueAt)) row.onTime++; }
            }
        }
        if (task.completedForIds.includes(users[0].id) && dayMap.has(day)) {
            dayMap.get(day)[kind]++;
            personal[kind]++;
            if (task.dueAt) { personal.dueDated++; if (moved.has(task.id)) personal.moved++; if (kind === 'done' && !moved.has(task.id) && new Date(task.completedAt) <= new Date(task.dueAt)) personal.onTime++; }
        }
    }
    personal.completionRate = personal.done + personal.failed ? Math.round(personal.done * 100 / (personal.done + personal.failed)) : null;
    personal.onTimeRate = personal.dueDated ? Math.round(personal.onTime * 100 / personal.dueDated) : null;
    personal.transferRate = personal.dueDated ? Math.round(personal.moved * 100 / personal.dueDated) : null;
    const ranking = [...summary.values()].map((row) => ({ ...row, score: row.done * 10 + row.onTime * 2 - row.failed * 4 - row.moved * 2, completionRate: row.done + row.failed ? Math.round(row.done * 100 / (row.done + row.failed)) : null })).sort((a, b) => b.score - a.score || b.done - a.done || a.person.name.localeCompare(b.person.name)).map((row, index) => ({ ...row, rank: index + 1 }));
    return { daily: days, personal, ranking };
}
function materializeToday() {
    const day = new Date();
    const weekday = (day.getDay() + 6) % 7;
    const date = `${day.getFullYear()}-${String(day.getMonth() + 1).padStart(2, '0')}-${String(day.getDate()).padStart(2, '0')}`;
    let changed = false;
    for (const schedule of db.schedules.filter((item) => item.active && item.effectiveFrom <= date)) {
        const rule = schedule.days[weekday];
        if (!rule?.enabled) continue;
        for (const id of rule.userIds) {
            if (db.shifts.some((shift) => shift.scheduleId === schedule.id && shift.occurrenceDate === date && shift.assignee.id === id)) continue;
            const [startH, startM] = rule.start.split(':').map(Number);
            const [endH, endM] = rule.end.split(':').map(Number);
            const start = new Date(day); start.setHours(startH, startM, 0, 0);
            const end = new Date(day); end.setHours(endH, endM, 0, 0); if (end <= start) end.setDate(end.getDate() + 1);
            const shift = { id: Math.max(0, ...db.shifts.map((item) => item.id)) + 1, title: schedule.title, color: schedule.color, comment: schedule.comment, assignee: person(id), startsAt: start.toISOString(), endsAt: end.toISOString(), checklist: schedule.taskTemplates.map((task) => task.title), scheduleId: schedule.id, occurrenceDate: date };
            db.shifts.push(shift);
            for (const template of schedule.taskTemplates) {
                const task = { id: Math.max(0, ...db.tasks.map((item) => item.id)) + 1, title: template.title, description: template.description || null, result: null, status: 'open', priority: template.priority, color: schedule.color, dueAt: shift.endsAt, assignee: shift.assignee, assignees: [shift.assignee], createdBy: null, dutyShiftId: shift.id, createdAt: new Date().toISOString(), updatedAt: new Date().toISOString(), completedForIds: [] };
                db.tasks.unshift(task); auditTask(task, 'created', {}, null);
            }
            changed = true;
        }
    }
    if (changed) persist();
}
function entry() { return JSON.parse(fs.readFileSync(path.join(buildRoot, '.vite', 'entrypoints.json'), 'utf8')).entryPoints.tasks; }
function html() {
    const task = entry();
    const css = task.css.map((url) => `<link rel="stylesheet" href="${url}">`).join('');
    const preload = (task.preload ?? []).map((url) => `<link rel="modulepreload" href="${url}">`).join('');
    const script = task.js.map((url) => `<script type="module" src="${url}"></script>`).join('');
    const railIcons = [
        '<path d="M3 11.5 12 4l9 7.5"/><path d="M5 10.5V20h14v-9.5"/>',
        '<rect x="3" y="4" width="18" height="16" rx="3"/><path d="M3 10h18M9 10v10M15 10v10"/>',
        '<path d="M17 2l4 4-4 4M3 11V9a3 3 0 0 1 3-3h15M7 22l-4-4 4-4"/>',
        '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18"/>',
        '<path d="M6 3h9l3 3v15H6zM14 3v4h4M8 11h8M8 15h8"/>',
        '<path d="M10 13a5 5 0 0 0 7.1 0l2-2a5 5 0 0 0-7.1-7.1l-1.1 1.1M14 11a5 5 0 0 0-7.1 0l-2 2a5 5 0 0 0 7.1 7.1l1.1-1.1"/>',
        '<path d="M4 19V5M4 19h16M8 16v-5M12 16V8M16 16v-9"/>',
        '<path d="M9 11l2 2 4-5"/><rect x="4" y="4" width="16" height="16" rx="3"/>',
        '<path d="M22 17v3a2 2 0 0 1-2 2C11 22 2 13 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2"/>',
        '<ellipse cx="12" cy="5" rx="7" ry="3"/><path d="M5 5v12c0 2 3 3 7 3s7-1 7-3V5M5 11c0 2 3 3 7 3s7-1 7-3"/>',
        '<rect x="5" y="7" width="14" height="12" rx="3"/><path d="M12 3v4M8 12h.01M16 12h.01"/>',
        '<path d="M4 5h16v14H4zM4 10h16M9 10v9M15 10v9"/>',
    ];
    const rail = railIcons.map((icon, index) => `<span class="rail-btn${index === 7 ? ' active' : ''}"><svg viewBox="0 0 24 24">${icon}</svg></span>`).join('');
    return `<!doctype html><html lang="ru"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Задачи — локальный просмотр</title>${css}${preload}<style>
    *{box-sizing:border-box}html,body{margin:0;min-height:100%;font-family:Inter,Arial,sans-serif;color:#101828}a{text-decoration:none}
    .crm-app{min-height:100vh;display:grid;margin-left:78px;width:calc(100vw - 78px)}
    .rail{position:fixed;inset:0 auto 0 0;width:78px;height:100vh;padding:14px 10px;background:#111824;display:flex;flex-direction:column;align-items:center;gap:12px;z-index:1000}
    .logo{width:40px;height:40px;border:1px solid rgba(22,184,217,.5);border-radius:13px;display:grid;place-items:center;color:#fff;font-weight:850;margin-bottom:10px}
    .rail-nav{width:100%;display:flex;flex-direction:column;align-items:center;gap:9px}.rail-btn{width:42px;height:42px;border-radius:13px;color:#b8c0cc;display:grid;place-items:center;position:relative}.rail-btn svg{width:21px;height:21px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}.rail-btn.active{background:#202b3a;color:#fff}.rail-btn.active:before{content:"";position:absolute;left:-10px;width:3px;height:22px;border-radius:99px;background:#16b8d9}.rail-bottom{margin-top:auto;padding-bottom:10px}.rail-user{width:42px;height:42px;border-radius:50%;background:linear-gradient(180deg,#1cc3e4,#109fbd);color:#fff;display:grid;place-items:center;font-size:13px;font-weight:800}
    </style></head><body><div class="crm-app"><aside class="rail"><div class="logo">RH</div><nav class="rail-nav">${rail}</nav><div class="rail-bottom"><div class="rail-user">Д</div></div></aside><div id="tasks-app"></div></div>${script}</body></html>`;
}
async function api(req, res, url) {
    const name = url.pathname;
    if (req.method === 'GET' && name === '/api/v1/tasks/bootstrap') { materializeToday(); return json(res, 200, { currentUser: users[0], canAdmin: true, users, ...db, dashboard: dashboard() }); }
    if (req.method === 'PUT' && name === '/api/v1/tasks/board-columns') {
        const data = await payload(req);
        const statuses = ['open', 'in_progress', 'review', 'done', 'not_done'];
        if (!Array.isArray(data.columns) || data.columns.length !== 5 || new Set(data.columns.map(c => c.status)).size !== 5 || data.columns.some(c => !statuses.includes(c.status) || !c.name?.trim() || c.name.length > 60 || !/^#[a-f0-9]{6}$/i.test(c.color))) return json(res, 422, { error: 'Проверьте названия, цвета и все пять статусов' });
        db.boardColumns = data.columns; persist(); return json(res, 200, { columns: db.boardColumns });
    }
    if (req.method === 'GET' && name === '/api/v1/tasks/audit') {
        const offset = Math.max(0, Number(url.searchParams.get('offset')) || 0);
        return json(res, 200, { items: db.audit.slice(offset, offset + 100), total: db.audit.length });
    }
    if (req.method === 'GET' && name === '/api/v1/tasks/feedback') return json(res, 200, db.feedback.filter(item => item.recipient.id === users[0].id).sort((a, b) => Date.parse(b.createdAt) - Date.parse(a.createdAt) || b.id - a.id).map(item => ({ ...item, readAt: item.readAt ?? null })));
    const readFeedbackMatch = name.match(/^\/api\/v1\/tasks\/feedback\/(\d+)\/read$/);
    if (req.method === 'POST' && readFeedbackMatch) {
        const item = db.feedback.find(item => item.id === Number(readFeedbackMatch[1]));
        if (!item) return json(res, 404, { error: 'Комментарий не найден' });
        if (item.recipient.id !== users[0].id) return json(res, 403, { error: 'Нет доступа' });
        item.readAt ??= new Date().toISOString(); persist(); return json(res, 200, item);
    }
    if (req.method === 'POST' && name === '/api/v1/tasks/feedback') {
        const data = await payload(req);
        if (!users.some((user) => user.id === Number(data.recipientId)) || !data.body?.trim() || data.body.length > 2000) return json(res, 422, { error: 'Выберите сотрудника и напишите комментарий' });
        const item = { id: Math.max(0, ...db.feedback.map((entry) => entry.id)) + 1, recipient: person(data.recipientId), author: users[0], body: data.body.trim(), createdAt: new Date().toISOString(), readAt: null };
        db.feedback.unshift(item); persist(); return json(res, 201, item);
    }
    const feedbackMatch = name.match(/^\/api\/v1\/tasks\/feedback\/(\d+)$/);
    if (req.method === 'DELETE' && feedbackMatch) {
        const index = db.feedback.findIndex((item) => item.id === Number(feedbackMatch[1]));
        if (index < 0) return json(res, 404, { error: 'Комментарий не найден' });
        db.feedback.splice(index, 1); persist(); res.writeHead(204); res.end(); return;
    }
    if (req.method === 'POST' && name === '/api/v1/tasks/schedules') {
        const data = await payload(req);
        const date = new Date();
        const schedule = { ...data, id: Math.max(0, ...db.schedules.map((item) => item.id)) + 1, effectiveFrom: `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}` };
        db.schedules.push(schedule); persist(); return json(res, 201, schedule);
    }
    const scheduleMatch = name.match(/^\/api\/v1\/tasks\/schedules\/(\d+)$/);
    if (req.method === 'PATCH' && scheduleMatch) {
        const schedule = db.schedules.find((item) => item.id === Number(scheduleMatch[1])); if (!schedule) return json(res, 404, { error: 'Дежурство не найдено' });
        const data = await payload(req); const tomorrow = new Date(); tomorrow.setDate(tomorrow.getDate() + 1);
        Object.assign(schedule, data, { effectiveFrom: `${tomorrow.getFullYear()}-${String(tomorrow.getMonth() + 1).padStart(2, '0')}-${String(tomorrow.getDate()).padStart(2, '0')}` });
        persist(); return json(res, 200, schedule);
    }
    if (req.method === 'POST' && name === '/api/v1/tasks') {
        const data = await payload(req); const id = Math.max(0, ...db.tasks.map((task) => task.id)) + 1;
        const owners = assignees(data.assigneeIds ?? [data.assigneeId ?? users[0].id]);
        if (!owners) return json(res, 422, { error: 'Выберите от 1 до 30 ответственных' });
        const item = { id, title: data.title, description: data.description || null, result: data.result || null, status: data.status || 'open', priority: data.priority || 'normal', color: data.color || '#3d70f5', dueAt: data.dueAt || null, assignee: owners[0], assignees: owners, createdBy: users[0], requestedBy: person(data.requestedById ?? users[0].id), dutyShiftId: null, createdAt: new Date().toISOString(), updatedAt: new Date().toISOString(), completedAt: ['done', 'not_done'].includes(data.status) ? new Date().toISOString() : null, completedFor: ['done', 'not_done'].includes(data.status) ? owners[0] : null, completedForIds: ['done', 'not_done'].includes(data.status) ? owners.map((user) => user.id) : [] };
        db.tasks.unshift(item); auditTask(item, 'created'); persist(); return json(res, 201, item);
    }
    const taskMatch = name.match(/^\/api\/v1\/tasks\/(\d+)$/);
    if (req.method === 'PATCH' && taskMatch) {
        const task = db.tasks.find((item) => item.id === Number(taskMatch[1])); if (!task) return json(res, 404, { error: 'Задача не найдена' });
        const data = await payload(req);
        if (!task.dutyShiftId && task.dueAt && new Date(task.dueAt) < new Date() && !['done', 'not_done'].includes(task.status) && ('dueAt' in data || 'assigneeId' in data || 'assigneeIds' in data || 'status' in data)) return json(res, 422, { error: 'Сначала укажите причину просрочки и новый срок в карточке задачи' });
        const owners = 'assigneeIds' in data ? assignees(data.assigneeIds) : 'assigneeId' in data ? assignees([data.assigneeId]) : null;
        if (('assigneeIds' in data || 'assigneeId' in data) && !owners) return json(res, 422, { error: 'Выберите от 1 до 30 ответственных' });
        const before = taskSnapshot(task);
        const oldStatus = task.status;
        const { assigneeIds, assigneeId, requestedById, ...fields } = data;
        Object.assign(task, fields, { updatedAt: new Date().toISOString() });
        if (owners) { task.assignee = owners[0]; task.assignees = owners; }
        if ('requestedById' in data) task.requestedBy = person(requestedById);
        if ('status' in data && data.status !== oldStatus) { task.completedAt = ['done', 'not_done'].includes(data.status) ? task.updatedAt : null; task.completedFor = task.completedAt ? task.assignee : null; task.completedForIds = task.completedAt ? task.assignees.map((user) => user.id) : []; }
        const changes = taskChanges(before, taskSnapshot(task));
        if (Object.keys(changes).length) auditTask(task, 'updated', changes);
        persist(); return json(res, 200, task);
    }
    const overdueMatch = name.match(/^\/api\/v1\/tasks\/(\d+)\/report-overdue$/);
    if (req.method === 'POST' && overdueMatch) {
        const task = db.tasks.find((item) => item.id === Number(overdueMatch[1])); if (!task) return json(res, 404, { error: 'Задача не найдена' });
        const data = await payload(req);
        if (task.dutyShiftId || !task.dueAt || new Date(task.dueAt) >= new Date() || !data.reason?.trim() || new Date(data.newDueAt) <= new Date()) return json(res, 422, { error: 'Укажите причину и новый срок просроченной задачи' });
        const report = { id: Math.max(0, ...db.overdueReports.map((item) => item.id)) + 1, taskId: task.id, taskTitle: task.title, author: users[0], assigneeAtReport: task.assignee, assigneeAtReportIds: task.assignees.map((user) => user.id), reason: data.reason.trim(), previousDueAt: task.dueAt, newDueAt: data.newDueAt, createdAt: new Date().toISOString() };
        db.overdueReports.unshift(report); task.dueAt = data.newDueAt; task.updatedAt = report.createdAt; auditTask(task, 'deadline_moved', { reason: report.reason, dueAt: { before: report.previousDueAt, after: report.newDueAt } }); persist(); return json(res, 201, report);
    }
    const messageMatch = name.match(/^\/api\/v1\/tasks\/(\d+)\/messages$/);
    if (req.method === 'POST' && messageMatch) {
        const data = await payload(req); const item = { id: Math.max(0, ...db.messages.map((message) => message.id)) + 1, taskId: Number(messageMatch[1]), author: users[0], body: data.body, createdAt: new Date().toISOString() };
        db.messages.push(item); const task = db.tasks.find((entry) => entry.id === item.taskId); if (task) auditTask(task, 'message_added'); persist(); return json(res, 201, item);
    }
    if (req.method === 'POST' && name === '/api/v1/tasks/shifts') {
        const data = await payload(req); const id = Math.max(0, ...db.shifts.map((shift) => shift.id)) + 1;
        const shift = { id, title: data.title, assignee: person(data.assigneeId), startsAt: data.startsAt, endsAt: data.endsAt, checklist: data.checklist };
        db.shifts.push(shift);
        for (const title of shift.checklist) {
            const task = { id: Math.max(0, ...db.tasks.map((item) => item.id)) + 1, title, description: null, result: null, status: 'open', priority: 'normal', color: '#8a63ea', dueAt: shift.endsAt, assignee: shift.assignee, assignees: [shift.assignee], createdBy: users[0], dutyShiftId: id, createdAt: new Date().toISOString(), updatedAt: new Date().toISOString(), completedForIds: [] };
            db.tasks.unshift(task); auditTask(task, 'created');
        }
        persist(); return json(res, 201, shift);
    }
    const shiftMatch = name.match(/^\/api\/v1\/tasks\/shifts\/(\d+)$/);
    if (req.method === 'PATCH' && shiftMatch) {
        const shift = db.shifts.find((item) => item.id === Number(shiftMatch[1])); if (!shift) return json(res, 404, { error: 'Дежурство не найдено' });
        const data = await payload(req); const old = shift.checklist; const additions = data.checklist.filter((item) => !old.includes(item));
        if (old.some((item) => !data.checklist.includes(item))) return json(res, 422, { error: 'Созданные задачи чек-листа нельзя убрать из дежурства' });
        Object.assign(shift, data); shift.assignee = person(data.assigneeId);
        for (const task of db.tasks.filter((task) => task.dutyShiftId === shift.id && !['done', 'not_done'].includes(task.status))) { const before = taskSnapshot(task); task.assignee = shift.assignee; task.assignees = [shift.assignee]; task.dueAt = shift.endsAt; const changes = taskChanges(before, taskSnapshot(task)); if (Object.keys(changes).length) auditTask(task, 'updated', changes); }
        for (const title of additions) {
            const task = { id: Math.max(0, ...db.tasks.map((item) => item.id)) + 1, title, description: null, result: null, status: 'open', priority: 'normal', color: '#8a63ea', dueAt: shift.endsAt, assignee: shift.assignee, assignees: [shift.assignee], createdBy: users[0], dutyShiftId: shift.id, createdAt: new Date().toISOString(), updatedAt: new Date().toISOString(), completedForIds: [] };
            db.tasks.unshift(task); auditTask(task, 'created');
        }
        persist(); return json(res, 200, shift);
    }
    return json(res, 404, { error: 'Маршрут не найден' });
}
const server = http.createServer(async (req, res) => {
    try {
        const url = new URL(req.url, `http://${req.headers.host}`);
        if (url.pathname.startsWith('/api/v1/tasks')) return await api(req, res, url);
        if (url.pathname.startsWith('/build/')) {
            const requested = path.resolve(buildRoot, `.${url.pathname.slice('/build'.length)}`);
            if (!(requested.startsWith(buildRoot + path.sep) || requested === buildRoot) || !fs.existsSync(requested)) { res.writeHead(404); return res.end(); }
            const ext = path.extname(requested);
            res.writeHead(200, { 'Content-Type': ext === '.css' ? 'text/css' : 'text/javascript', 'Cache-Control': 'no-store' });
            return fs.createReadStream(requested).pipe(res);
        }
        if (url.pathname === '/' || url.pathname.startsWith('/tasks')) { res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8', 'Cache-Control': 'no-store' }); return res.end(html()); }
        res.writeHead(404); res.end();
    } catch (error) { console.error(error); json(res, 500, { error: 'Ошибка локального просмотра' }); }
});
server.listen(port, '127.0.0.1', () => console.log(`Tasks preview: http://127.0.0.1:${port}/tasks`));
