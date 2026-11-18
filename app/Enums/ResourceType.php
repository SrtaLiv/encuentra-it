<?php

namespace App\Enums;

enum ResourceType: string
{
    use HasOptions;

    case Course = 'course';
    case Guide = 'guide';
    case Roadmap = 'roadmap';
    case Repository = 'repository';
    case Challenge = 'challenge';
    case Community = 'community';
    case JobBoard = 'job_board';

    public function label(): string
    {
        return match ($this) {
            self::Course => 'Curso',
            self::Guide => 'Guía / Documentación',
            self::Roadmap => 'Roadmap',
            self::Repository => 'Repositorio',
            self::Challenge => 'Desafío / Práctica',
            self::Community => 'Comunidad',
            self::JobBoard => 'Bolsa de empleo',
        };
    }
}
