import { expect, test } from 'vitest';
import { buildSegments, moveAssignment } from '../composables/useSessionSplit.js';

const assignments = [
    { key: 1, task_id: 10, sprint_id: 100 },
    { key: 2, task_id: 20, sprint_id: 200 },
    { key: 3, task_id: 30, sprint_id: 300 },
];

test('moveAssignment moves a part later in the order', () => {
    expect(moveAssignment(assignments, 0, 2).map((assignment) => assignment.task_id)).toEqual([20, 30, 10]);
});

test('moveAssignment moves a part earlier in the order', () => {
    expect(moveAssignment(assignments, 2, 0).map((assignment) => assignment.task_id)).toEqual([30, 10, 20]);
});

test('moveAssignment keeps the task and sprint together', () => {
    expect(moveAssignment(assignments, 1, 0)[0]).toEqual({ key: 2, task_id: 20, sprint_id: 200 });
});

test('moveAssignment does not mutate the original list', () => {
    moveAssignment(assignments, 0, 2);

    expect(assignments.map((assignment) => assignment.task_id)).toEqual([10, 20, 30]);
});

test('moveAssignment returns an unchanged copy for out-of-range or identical indexes', () => {
    expect(moveAssignment(assignments, 0, 0)).toEqual(assignments);
    expect(moveAssignment(assignments, -1, 1)).toEqual(assignments);
    expect(moveAssignment(assignments, 0, 3)).toEqual(assignments);
    expect(moveAssignment(assignments, 1.5, 0)).toEqual(assignments);
});

test('reordering assignments keeps the times fixed to each position while task and sprint follow the order', () => {
    const startMs = 0;
    const endMs = 3 * 60 * 60 * 1000;
    const cutPoints = [30 * 60 * 1000, 2 * 60 * 60 * 1000];

    const before = buildSegments(cutPoints, startMs, endMs, assignments);
    const after = buildSegments(cutPoints, startMs, endMs, moveAssignment(assignments, 0, 2));

    expect(after.map(({ startMs, endMs }) => ({ startMs, endMs }))).toEqual(
        before.map(({ startMs, endMs }) => ({ startMs, endMs })),
    );
    expect(after.map(({ task_id, sprint_id }) => ({ task_id, sprint_id }))).toEqual([
        { task_id: 20, sprint_id: 200 },
        { task_id: 30, sprint_id: 300 },
        { task_id: 10, sprint_id: 100 },
    ]);
});
