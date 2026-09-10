<?php

return [
    'database_prefix' => env('TENANT_DATABASE_PREFIX', 'tenant_'),
    'central_database' => env('DB_DATABASE', 'vet_saas'),
    'default_tenant' => env('DEFAULT_TENANT', 'veterinaria1'),
    
    'roles' => [
        'super_admin' => 'Super Administrador',
        'admin' => 'Administrador',
        'veterinario' => 'Veterinario',
        'recepcionista' => 'Recepcionista',
        'cajero' => 'Cajero',
        'asistente' => 'Asistente',
    ],
    
    'permissions' => [
        'clientes' => [
            'view_any', 'create', 'view', 'update', 'delete'
        ],
        'mascotas' => [
            'view_any', 'create', 'view', 'update', 'delete'
        ],
        'citas' => [
            'view_any', 'create', 'view', 'update', 'delete', 'confirm', 'cancel'
        ],
        'ventas' => [
            'view_any', 'create', 'view', 'update', 'delete', 'process_payment'
        ],
        'inventario' => [
            'view_any', 'create', 'view', 'update', 'delete', 'adjust_stock'
        ],
        'reportes' => [
            'view_sales', 'view_inventory', 'view_financial', 'view_clients', 'export'
        ],
        'tenants' => [
            'view_any', 'create', 'view', 'update', 'delete'
        ],
    ],
    
    'payment_methods' => [
        'efectivo' => 'Efectivo',
        'tarjeta_credito' => 'Tarjeta de Crédito',
        'tarjeta_debito' => 'Tarjeta Débito',
        'transferencia_bancaria' => 'Transferencia Bancaria',
        'pasarela_pago' => 'Pasarela de Pago',
        'qr' => 'Pago QR',
    ],
    
    'taxes' => [
        'iva' => 19,
        'ica' => 0.8,
    ],
];