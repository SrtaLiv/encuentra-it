import { Checkbox } from '@/components/ui/checkbox';
import { type Career } from '@/types';
import { useId } from 'react';

interface CareerPickerProps {
    careers: Pick<Career, 'id' | 'name'>[];
    selected: number[];
    onChange: (ids: number[]) => void;
}

export function CareerPicker({ careers, selected, onChange }: CareerPickerProps) {
    const prefix = useId();

    const toggle = (id: number, checked: boolean) => {
        onChange(checked ? [...selected, id] : selected.filter((selectedId) => selectedId !== id));
    };

    return (
        <div className="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
            {careers.map((career) => (
                <label key={career.id} htmlFor={`${prefix}-${career.id}`} className="flex cursor-pointer items-center gap-2 text-sm">
                    <Checkbox
                        id={`${prefix}-${career.id}`}
                        checked={selected.includes(career.id)}
                        onCheckedChange={(checked) => toggle(career.id, checked === true)}
                    />
                    {career.name}
                </label>
            ))}
        </div>
    );
}
