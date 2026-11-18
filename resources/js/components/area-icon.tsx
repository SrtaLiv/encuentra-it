import { Brain, Briefcase, Bug, Code, Compass, Megaphone, Palette, Server, Shield, type LucideProps } from 'lucide-react';

export const areaIcons = {
    code: Code,
    brain: Brain,
    shield: Shield,
    server: Server,
    bug: Bug,
    palette: Palette,
    briefcase: Briefcase,
    megaphone: Megaphone,
    compass: Compass,
};

export function AreaIcon({ name, ...props }: LucideProps & { name: string }) {
    const Icon = areaIcons[name as keyof typeof areaIcons] ?? Compass;

    return <Icon {...props} />;
}
