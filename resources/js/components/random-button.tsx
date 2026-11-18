import { Button } from '@/components/ui/button';
import { type RandomType } from '@/types';
import { Link } from '@inertiajs/react';
import { Dices } from 'lucide-react';
import { type ReactNode } from 'react';

export function RandomButton({ type, children }: { type: RandomType; children: ReactNode }) {
    return (
        <Button asChild variant="outline" size="sm">
            <Link href={route('random', { tipo: type })}>
                <Dices />
                {children}
            </Link>
        </Button>
    );
}
