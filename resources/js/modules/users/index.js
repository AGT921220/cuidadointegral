import { createDataTable } from '../../shared/datatable.js';

$(function () {
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content') ?? '';

    createDataTable('users-table', [
        { data: 'name', name: 'users.name', orderSequence: ['asc', 'desc'] },
        { data: 'nickname', name: 'users.nickname', orderSequence: ['asc', 'desc'] },
        { data: 'email', name: 'users.email', orderSequence: ['asc', 'desc'] },
        { data: 'roles', name: 'roles', orderable: false, searchable: false },
        {
            data: null,
            name: 'actions',
            orderable: false,
            searchable: false,
            className: 'text-end',
            render(_data, _type, row) {
                return `
                    <div class="btn-list flex-nowrap">
                        <a href="/users/${row.id}/edit" class="btn btn-sm btn-outline-primary">
                            <i class="ti ti-pencil me-1"></i>
                            Editar
                        </a>
                        <form action="/users/${row.id}" method="POST" onsubmit="return confirm('¿Eliminar este usuario?')">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="ti ti-trash me-1"></i>
                                Eliminar
                            </button>
                        </form>
                    </div>
                `;
            },
        },
    ], {
        entityName: 'usuarios',
        pageLength: 10,
        ajax: async (data, callback) => {
            try {
                const params = new URLSearchParams({
                    offset: data.start,
                    limit: data.length,
                    draw: data.draw,
                });
                const order = data.order?.[0];

                if (order) {
                    const column = data.columns?.[order.column];

                    if (column?.name && column.name !== 'actions' && column.name !== 'roles' && ['asc', 'desc'].includes(order.dir)) {
                        params.set('order_by', column.name);
                        params.set('order_direction', order.dir);
                    }
                }

                const response = await fetch(`/user-headers?${params.toString()}`, {
                    headers: { Accept: 'application/json' },
                });

                if (!response.ok) {
                    throw new Error('No se pudieron cargar los usuarios.');
                }

                const payload = await response.json();

                callback({
                    draw: payload.draw ?? data.draw,
                    recordsTotal: payload.recordsTotal ?? 0,
                    recordsFiltered: payload.recordsFiltered ?? 0,
                    data: payload.data ?? [],
                });
            } catch (error) {
                console.error(error);
                callback({
                    draw: data.draw,
                    recordsTotal: 0,
                    recordsFiltered: 0,
                    data: [],
                });
            }
        },
    });
});
