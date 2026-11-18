import { LucideIcon } from 'lucide-react';

export interface Auth {
    user: User | null;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    url: string;
    icon?: LucideIcon | null;
    isActive?: boolean;
}

export interface Enums {
    codingLevels: Record<CodingLevel, string>;
    levels: Record<Level, string>;
    resourceTypes: Record<ResourceType, string>;
    aiModes: Record<AiMode, string>;
    aiModeHints: Record<AiMode, string>;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    flash: { success: string | null; error: string | null };
    enums: Enums;
    [key: string]: unknown;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    is_admin: boolean;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    [key: string]: unknown; // This allows for additional properties...
}

export type CodingLevel = 'none' | 'basic' | 'core';
export type Level = 'beginner' | 'intermediate' | 'advanced';
export type AiMode = 'none' | 'tutor' | 'allowed';
export type ResourceType = 'course' | 'guide' | 'roadmap' | 'repository' | 'challenge' | 'community' | 'job_board';

export interface Area {
    id: number;
    name: string;
    slug: string;
    description: string;
    icon: string;
    position: number;
    careers_count?: number;
}

export interface RoadmapStep {
    id: number;
    level: Level;
    title: string;
    description: string | null;
    position: number;
}

export interface Challenge {
    id: number;
    level: Level;
    ai_mode: AiMode;
    title: string;
    description: string;
    deliverable: string;
    position: number;
    guide: ChallengeGuide | null;
    has_solution?: boolean;
    // only present once the user completed the challenge
    solution?: string | null;
}

// a type (not an interface) so inertia's useForm accepts it as form data
export type ChallengeGuide = {
    before: string[];
    steps: string[];
    hints: string[];
};

export interface Reflection {
    decision: string;
    difficulty: string;
    next: string | null;
}

export interface ChallengeCompletion {
    evidence_url: string | null;
    reflection: Reflection | null;
}

export interface Resource {
    id: number;
    title: string;
    url: string;
    description: string | null;
    type: ResourceType;
    level: Level;
    language: 'es' | 'en';
    is_free: boolean;
    is_published: boolean;
    careers?: Pick<Career, 'id' | 'name' | 'slug'>[];
    careers_count?: number;
}

export interface Career {
    id: number;
    area_id: number;
    name: string;
    slug: string;
    summary: string;
    description: string;
    coding_level: CodingLevel;
    daily_tasks: string[];
    skills: string[];
    job_titles: string[];
    job_outlook: string | null;
    is_published: boolean;
    area?: Pick<Area, 'id' | 'name' | 'slug' | 'icon'>;
    steps?: RoadmapStep[];
    challenges?: Challenge[];
    resources?: Resource[];
    challenges_count?: number;
}

export type RandomType = 'carrera' | 'recurso' | 'desafio';

export type CareerSummary = Pick<Career, 'id' | 'name' | 'slug' | 'summary' | 'coding_level' | 'area' | 'challenges_count'>;

export interface Paginated<T> {
    data: T[];
    current_page: number;
    last_page: number;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}
