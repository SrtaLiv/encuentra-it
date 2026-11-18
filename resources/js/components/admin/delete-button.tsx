import { Button } from '@/components/ui/button';
import { router } from '@inertiajs/react';
import { Trash2 } from 'lucide-react';

export function DeleteButton({ url, label }: { url: string; label: string }) {
    const destroy = () => {
        if (!window.confirm(`¿Seguro que querés borrar "${label}"? No se puede deshacer.`)) {
            return;
        }

        router.delete(url, { preserveScroll: true });
    };

    return (
        <Button variant="ghost" size="icon" onClick={destroy} aria-label={`Borrar ${label}`}>
            <Trash2 className="text-destructive" />
        </Button>
    );
}
