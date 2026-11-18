<?php

namespace App\Enums;

enum AiMode: string
{
    use HasOptions;

    case None = 'none';
    case Tutor = 'tutor';
    case Allowed = 'allowed';

    public function label(): string
    {
        return match ($this) {
            self::None => 'Sin IA',
            self::Tutor => 'IA como tutor',
            self::Allowed => 'IA permitida',
        };
    }

    public function hint(): string
    {
        return match ($this) {
            self::None => 'Hacelo con tu cabeza. Es un fundamento que vas a necesitar sin ayuda, por ejemplo en una entrevista.',
            self::Tutor => 'Podés pedirle pistas a la IA, pero no la solución. Copiá este prompt para que te guíe sin resolverlo por vos.',
            self::Allowed => 'Usala como en un trabajo real. Después vas a tener que explicar cada decisión con tus palabras.',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function hints(): array
    {
        $hints = [];

        foreach (self::cases() as $case) {
            $hints[$case->value] = $case->hint();
        }

        return $hints;
    }
}
