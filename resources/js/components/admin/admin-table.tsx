import { type ReactNode } from 'react';

export function AdminTable({ headers, empty, children }: { headers: string[]; empty?: boolean; children: ReactNode }) {
    return (
        <div className="bg-card card-raised overflow-x-auto">
            <table className="w-full text-sm">
                <thead className="bg-muted/50 text-left">
                    <tr>
                        {headers.map((header, index) => (
                            <th key={index} className="px-4 py-3 font-bold whitespace-nowrap">
                                {header}
                            </th>
                        ))}
                    </tr>
                </thead>
                <tbody className="divide-y [&_td]:px-4 [&_td]:py-3">
                    {empty ? (
                        <tr>
                            <td colSpan={headers.length} className="text-muted-foreground text-center">
                                No hay nada cargado todavía.
                            </td>
                        </tr>
                    ) : (
                        children
                    )}
                </tbody>
            </table>
        </div>
    );
}
