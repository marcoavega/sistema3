<?php
// helpers/PermissionHelper.php

class PermissionHelper
{
    /**
     * Devuelve un array con permisos para los módulos.
     * Ajusta aquí la política centralizada.
     */
    public static function getPermissionsByLevel(int $level_user): array
    {
        // Default: solo ver/reportes
        $base = [
            'products' => [
                'view'   => true,
                'create' => false,
                'edit'   => false,
                'delete' => false,
                'report' => true, // permitimos descargar reportes por defecto
            ],
            // puedes añadir otros módulos aquí: categories, suppliers, etc.
        ];

        // Nivel 1 y 2: permisos totales
        if (in_array($level_user, [1, 2], true)) {
            $base['products'] = [
                'view'   => true,
                'create' => true,
                'edit'   => true,
                'delete' => true,
                'report' => true,
            ];
            return $base;
        }

        // Nivel 3: puede crear y editar, pero NO eliminar
        if ($level_user === 3) {
            $base['products'] = [
                'view'   => true,
                'create' => true,
                'edit'   => true,
                'delete' => false,
                'report' => true,
            ];
            return $base;
        }

        // Resto: solo consultar y reporte
        return $base;
    }
}
