import AppLogo from '@/components/app-logo';
import { Button } from '@/components/ui/button';
import { Sheet, SheetContent, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import { cn } from '@/lib/utils';
import { type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { Menu } from 'lucide-react';
import { type ReactNode } from 'react';

const navItems = [
    { title: 'Test vocacional', url: '/test' },
    { title: 'Carreras', url: '/carreras' },
    { title: 'Recursos', url: '/recursos' },
    { title: 'Al azar', url: '/azar' },
];

function NavLinks({ className }: { className?: string }) {
    const { url } = usePage();

    return navItems.map((item) => (
        <Link
            key={item.url}
            href={item.url}
            className={cn(
                'text-muted-foreground hover:text-foreground text-sm font-bold transition-colors',
                url.startsWith(item.url) && 'text-foreground',
                className,
            )}
        >
            {item.title}
        </Link>
    ));
}

function AuthLinks() {
    const { auth } = usePage<SharedData>().props;

    if (auth.user) {
        return (
            <Button asChild size="sm">
                <Link href={route('dashboard')}>Mi panel</Link>
            </Button>
        );
    }

    return (
        <>
            <Button asChild variant="ghost" size="sm">
                <Link href={route('login')}>Ingresar</Link>
            </Button>
            <Button asChild size="sm">
                <Link href={route('register')}>Crear cuenta</Link>
            </Button>
        </>
    );
}

export default function PublicLayout({ children }: { children: ReactNode }) {
    return (
        <div className="bg-background flex min-h-svh flex-col">
            <header className="bg-background/90 sticky top-0 z-40 border-b backdrop-blur">
                <div className="mx-auto flex h-16 max-w-6xl items-center gap-6 px-4">
                    <Link href={route('home')} className="flex items-center gap-2">
                        <AppLogo />
                    </Link>

                    <nav className="hidden items-center gap-6 md:flex">
                        <NavLinks />
                    </nav>

                    <div className="ml-auto hidden items-center gap-2 md:flex">
                        <AuthLinks />
                    </div>

                    <Sheet>
                        <SheetTrigger asChild>
                            <Button variant="ghost" size="icon" className="ml-auto md:hidden" aria-label="Abrir menú">
                                <Menu className="size-5" />
                            </Button>
                        </SheetTrigger>
                        <SheetContent side="right" className="flex w-64 flex-col gap-6 p-6">
                            <SheetTitle className="sr-only">Menú</SheetTitle>
                            <nav className="flex flex-col gap-4 pt-6">
                                <NavLinks className="text-base" />
                            </nav>
                            <div className="flex flex-col gap-2">
                                <AuthLinks />
                            </div>
                        </SheetContent>
                    </Sheet>
                </div>
            </header>

            <main className="flex-1">{children}</main>

            <footer className="border-t">
                <div className="text-muted-foreground mx-auto flex max-w-6xl flex-col gap-2 px-4 py-8 text-sm sm:flex-row sm:justify-between">
                    <p>Encuentra IT · Hecho por y para la comunidad.</p>
                    <p>Todos los recursos pertenecen a sus autores.</p>
                </div>
            </footer>
        </div>
    );
}
