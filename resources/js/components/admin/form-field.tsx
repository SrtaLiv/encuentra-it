import InputError from '@/components/input-error';
import { Label } from '@/components/ui/label';
import { type ReactNode } from 'react';

interface FormFieldProps {
    label: string;
    htmlFor?: string;
    error?: string;
    hint?: string;
    children: ReactNode;
}

export function FormField({ label, htmlFor, error, hint, children }: FormFieldProps) {
    return (
        <div className="grid gap-2">
            <Label htmlFor={htmlFor}>{label}</Label>
            {children}
            {hint && <p className="text-muted-foreground text-xs">{hint}</p>}
            <InputError message={error} />
        </div>
    );
}
