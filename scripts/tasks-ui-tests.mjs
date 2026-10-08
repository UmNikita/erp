import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import vm from 'node:vm';
import ts from 'typescript';
const cache = new Map();
function load(file) {
    file = path.resolve(file);
    if (cache.has(file)) return cache.get(file);
    const module = { exports: {} }; cache.set(file, module.exports);
    const code = ts.transpileModule(fs.readFileSync(file, 'utf8'), { compilerOptions: { target: ts.ScriptTarget.ES2022, module: ts.ModuleKind.CommonJS } }).outputText;
    vm.runInThisContext(`(function(require,module,exports){${code}\n})`, { filename: file })(specifier => load(path.resolve(path.dirname(file), specifier + '.ts')), module, module.exports);
    return module.exports;
}
const { filterTasks } = load('assets/tasks/utils/tableFilters.ts');
const now = Date.parse('2026-10-08T20:00:00Z');
const person = id => ({ id, name: `User ${id}` });
const base = { description: '', status: 'open', priority: 'normal', assignees: [person(1)], dueAt: null, dutyShiftId: null, createdBy: person(2), requestedBy: null };
const tasks = [
    { ...base, id: 1, title: 'Single' },
    { ...base, id: 2, title: 'Shared', assignees: [person(1), person(2)], priority: 'high', dueAt: '2026-10-08T21:30:00Z' },
    { ...base, id: 3, title: 'Overdue', assignees: [person(2)], dueAt: '2026-10-07T12:00:00Z' },
    { ...base, id: 4, title: 'Duty', dutyShiftId: 1, dueAt: '2026-10-07T12:00:00Z' },
    { ...base, id: 5, title: 'Closed', status: 'done', dueAt: '2026-10-07T12:00:00Z' },
];
const filters = { userIds: [1], search: '', status: 'all', priority: 'all', source: 'all', requestedBy: 'all', deadline: 'all' };
const ids = patch => filterTasks(tasks, { ...filters, ...patch }, now).map(task => task.id);
assert.deepEqual(ids({}), [1, 2, 4, 5]);
assert.deepEqual(ids({ userIds: [] }), []);
assert.deepEqual(ids({ userIds: [2] }), [2, 3]);
assert.deepEqual(ids({ userIds: [1, 2] }), [1, 2, 3, 4, 5]);
assert.deepEqual(ids({ priority: 'high' }), [2]);
assert.deepEqual(ids({ source: 'duty' }), [4]);
assert.deepEqual(ids({ status: 'done' }), [5]);
assert.deepEqual(ids({ userIds: [1, 2], deadline: 'overdue' }), [3]);
assert.deepEqual(ids({ deadline: 'today' }), []); // 21:30Z is already the next Moscow day.
assert.deepEqual(ids({ deadline: 'week' }), [2]);
assert.deepEqual(ids({ deadline: 'none' }), [1]);
assert.deepEqual(ids({ search: ' SHARED ' }), [2]);
assert.deepEqual(ids({ requestedBy: '99' }), []);
const events = new Map(); let root = {}; let mounts = 0; let unmounts = 0;
globalThis.document = { readyState: 'complete', getElementById: () => root, addEventListener: (name, fn) => events.set(name, fn) };
const { mountModule } = load('assets/shared/mountModule.ts');
mountModule('tasks-app', () => ({ mount: () => mounts++, unmount: () => unmounts++ }));
assert.equal(mounts, 1);
events.get('turbo:load')(); assert.equal(mounts, 1);
for (let index = 0; index < 20; index++) {
    events.get('turbo:before-cache')(); root = null; events.get('turbo:load')();
    root = {}; events.get('turbo:load')();
}
assert.equal(mounts, 21); assert.equal(unmounts, 20);
delete globalThis.document;
console.log('PASS: 13 task-filter assertions and 20 Turbo restore cycles');
