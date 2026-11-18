import DataTable from 'datatables.net-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.css';

const DEFAULT_LANGUAGE = {
    processing: 'Cargando...',
    search: 'Buscar:',
    lengthMenu: 'Mostrar _MENU_ registros',
    info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
    infoEmpty: 'Mostrando 0 a 0 de 0 registros',
    infoFiltered: '(filtrado de _MAX_ registros)',
    emptyTable: 'No hay registros disponibles.',
    zeroRecords: 'No se encontraron registros.',
    paginate: {
        first: 'Primera',
        last: 'Última',
        next: 'Siguiente',
        previous: 'Anterior',
    },
};

export function createDataTable(target, columns, options = {}) {
    const table = resolveTable(target);

    if (!table) {
        console.warn('DataTable: no se encontró la tabla.', target);
        return null;
    }

    const {
        url = table.dataset.url,
        entityName = 'registros',
        language = {},
        ajax,
        ...dataTableOptions
    } = options;

    if (typeof ajax !== 'function' && !url) {
        console.warn('DataTable: la tabla no tiene una URL ni una función ajax.', table);
        return null;
    }

    return new DataTable(table, {
        processing: true,
        serverSide: true,
        pageLength: 15,
        order: [[0, 'asc']],
        columns,
        language: {
            ...DEFAULT_LANGUAGE,
            info: `Mostrando _START_ a _END_ de _TOTAL_ ${entityName}`,
            infoEmpty: `Mostrando 0 a 0 de 0 ${entityName}`,
            infoFiltered: `(filtrado de _MAX_ ${entityName})`,
            ...language,
        },
        ajax,
        ...dataTableOptions,
    });
}

function resolveTable(target) {
    if (target instanceof HTMLElement) {
        return target;
    }

    if (typeof target !== 'string') {
        return null;
    }

    if (target.startsWith('#') || target.startsWith('.') || target.includes('[')) {
        return document.querySelector(target);
    }

    return document.getElementById(target);
}
