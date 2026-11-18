import { Input } from '@/components/ui/input';
import { router } from '@inertiajs/react';
import { Search } from 'lucide-react';
import { useState, type FormEvent } from 'react';

interface SearchInputProps {
    url: string;
    params: Record<string, unknown>;
    initial?: string;
    placeholder: string;
}

export function SearchInput({ url, params, initial = '', placeholder }: SearchInputProps) {
    const [value, setValue] = useState(initial);

    const submit = (event: FormEvent) => {
        event.preventDefault();
        router.get(url, { ...params, q: value.trim() || undefined }, { preserveState: true, preserveScroll: true });
    };

    return (
        <form onSubmit={submit} className="relative w-full sm:max-w-xs" role="search">
            <Search className="text-muted-foreground absolute top-1/2 left-3 size-4 -translate-y-1/2" />
            <Input value={value} onChange={(event) => setValue(event.target.value)} placeholder={placeholder} className="pl-9" maxLength={100} />
        </form>
    );
}
