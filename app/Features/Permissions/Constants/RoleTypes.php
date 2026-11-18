<?php

namespace App\Features\Permissions\Constants;

final class RoleTypes
{
    public const ADMIN = 'Admin';

    public const USUARIO = 'Usuario';

    public const CUIDADOR = 'Cuidador';

    public const FAMILIAR = 'Familiar';

    public const SUPERVISOR = 'Supervisor';

    /**
     * @return list<string>
     */
    public static function mobile(): array
    {
        return [
            self::ADMIN,
            self::USUARIO,
            self::CUIDADOR,
            self::FAMILIAR,
            self::SUPERVISOR,
        ];
    }

    /**
     * @return list<string>
     */
    public static function fixedNames(): array
    {
        return self::mobile();
    }
}
