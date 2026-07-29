import assert from 'node:assert/strict';
import test from 'node:test';

class HTMLInputElementMock {}
globalThis.HTMLInputElement = HTMLInputElementMock;

const {completionSnapshot, validationErrorCode} = await import('../../resources/js/analytics/tool-journey.js');

const field = ({value = '', valid = true, validity = {}, disabled = false} = {}) => ({
    value,
    validity,
    disabled,
    checkValidity: () => valid,
});

test('completionSnapshot counts only non-empty valid fields', () => {
    const snapshot = completionSnapshot([
        {element: field({value: '10'})},
        {element: field({value: ''})},
        {element: field({value: 'invalid', valid: false})},
        {element: field({value: '20'})},
    ]);

    assert.deepEqual(snapshot, {
        filled_fields: 2,
        total_fields: 4,
        completion_percentage: 50,
    });
});

test('validationErrorCode exposes semantic codes and never field values', () => {
    assert.equal(validationErrorCode(field({validity: {valueMissing: true}})), 'value_missing');
    assert.equal(validationErrorCode(field({validity: {rangeOverflow: true}})), 'range_overflow');
    assert.equal(validationErrorCode(field({validity: {customError: true}})), 'custom_error');
});
